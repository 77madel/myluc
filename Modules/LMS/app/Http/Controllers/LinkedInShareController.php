<?php

namespace Modules\LMS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Modules\LMS\Models\Certificate\UserCertificate;

class LinkedInShareController extends Controller
{
    /**
     * Rediriger vers LinkedIn pour autorisation OAuth
     */
    public function authorize(Request $request)
    {
        $request->validate([
            'certificate_id' => 'required|integer',
            'message' => 'required|string',
        ]);

        // Stocker les données en session
        session([
            'linkedin_certificate_id' => $request->certificate_id,
            'linkedin_message' => $request->message,
            'linkedin_return_url' => url()->previous(),
        ]);

        // Configuration LinkedIn
        $clientId = config('services.linkedin.client_id');
        $redirectUri = route('linkedin.callback');
        $state = bin2hex(random_bytes(16));

        session(['linkedin_state' => $state]);

        // Scopes LinkedIn (OpenID Connect pour publication)
        $scopes = 'openid profile w_member_social';

        // URL d'autorisation LinkedIn
        $authUrl = 'https://www.linkedin.com/oauth/v2/authorization?'.http_build_query([
            'response_type' => 'code',
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'state' => $state,
            'scope' => $scopes,
        ]);

        return redirect($authUrl);
    }

    /**
     * Callback après autorisation LinkedIn
     */
    public function callback(Request $request)
    {
        try {
            \Log::info('🔵 [LinkedIn] Callback reçu', [
                'has_code' => $request->has('code'),
                'has_state' => $request->has('state'),
                'has_error' => $request->has('error'),
                'error' => $request->error,
                'error_description' => $request->error_description,
            ]);

            // Vérifier si l'utilisateur a refusé
            if ($request->has('error')) {
                \Log::warning('⚠️ [LinkedIn] Autorisation refusée', [
                    'error' => $request->error,
                    'description' => $request->error_description,
                ]);

                return redirect()->route('student.certificate.index')
                    ->with('warning', '⚠️ Autorisation LinkedIn annulée. Vous pouvez utiliser l\'option "Copier le message".');
            }

            // Vérifier le state
            if ($request->state !== session('linkedin_state')) {
                \Log::error('❌ [LinkedIn] State invalide', [
                    'request_state' => $request->state,
                    'session_state' => session('linkedin_state'),
                ]);
                throw new \Exception('Invalid state parameter');
            }

            // Échanger le code contre un access token
            $tokenResponse = Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
                'grant_type' => 'authorization_code',
                'code' => $request->code,
                'redirect_uri' => route('linkedin.callback'),
                'client_id' => config('services.linkedin.client_id'),
                'client_secret' => config('services.linkedin.client_secret'),
            ]);

            if (! $tokenResponse->successful()) {
                throw new \Exception('Failed to get access token');
            }

            $accessToken = $tokenResponse->json()['access_token'];

            \Log::info('✅ [LinkedIn] Access token obtenu');

            // Récupérer les informations de l'utilisateur LinkedIn avec la nouvelle API
            $userResponse = Http::withToken($accessToken)
                ->withHeaders(['LinkedIn-Version' => '202401'])
                ->get('https://api.linkedin.com/v2/userinfo');

            if (! $userResponse->successful()) {
                \Log::error('❌ [LinkedIn] Erreur userinfo', [
                    'status' => $userResponse->status(),
                    'body' => $userResponse->body(),
                ]);
                throw new \Exception('Failed to get user info: '.$userResponse->body());
            }

            $userData = $userResponse->json();
            \Log::info('✅ [LinkedIn] User info récupéré', [
                'has_sub' => isset($userData['sub']),
                'has_name' => isset($userData['name']),
            ]);

            // L'ID utilisateur est dans 'sub' avec la nouvelle API OpenID
            $linkedinUserId = $userData['sub'];

            // Récupérer les données de la session
            $certificateId = session('linkedin_certificate_id');
            $message = session('linkedin_message');

            // Récupérer le certificat
            $certificate = UserCertificate::with(['user.userable', 'course'])->findOrFail($certificateId);
            $publicUrl = route('certificate.public', $certificate->public_uuid);

            // Le certificat HTML est dans la DB, on le convertit en image
            if ($certificate->certificate_content && extension_loaded('imagick')) {
                \Log::info('🎨 [LinkedIn] Utilisation du certificat HTML existant');

                // Convertir le HTML en PDF puis en image
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($certificate->certificate_content);
                $pdf->setPaper('A4', 'landscape');
                $pdfContent = $pdf->output();

                // Convertir le PDF en image
                $imageContent = $this->convertPdfToImage($pdfContent);

                if (!$imageContent) {
                    // Fallback image simple
                    \Log::warning('⚠️ [LinkedIn] Conversion échouée, utilisation image simple');
                    $imageContent = $this->generateSimpleCertificateImage($certificate);
                }
            } else {
                // Pas de HTML ou Imagick non disponible : image simple
                \Log::info('📸 [LinkedIn] Génération image simple');
                $imageContent = $this->generateSimpleCertificateImage($certificate);
            }

            // Publier sur LinkedIn
            $this->publishToLinkedIn($accessToken, $linkedinUserId, $message, $publicUrl, $imageContent, false);

            // Enregistrer le partage
            \DB::table('certificate_shares')->insert([
                'user_certificate_id' => $certificateId,
                'user_id' => auth()->id(),
                'platform' => 'linkedin',
                'custom_message' => $message,
                'shared_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Nettoyer la session
            session()->forget([
                'linkedin_certificate_id',
                'linkedin_message',
                'linkedin_state',
            ]);

            return redirect()->route('student.certificate.index')
                ->with('success', '🎉 Certificat partagé sur LinkedIn avec succès !');

        } catch (\Exception $e) {
            \Log::error('Erreur partage LinkedIn: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            session()->forget([
                'linkedin_certificate_id',
                'linkedin_message',
                'linkedin_state',
            ]);

            return redirect()->route('student.certificate.index')
                ->with('error', '❌ Erreur lors du partage sur LinkedIn. Veuillez réessayer.');
        }
    }

    /**
     * Publier sur LinkedIn via API UGC Posts
     */
    private function publishToLinkedIn($accessToken, $linkedinUserId, $message, $articleUrl, $fileContent, $isPdf = false)
    {
        \Log::info('📤 Publication sur LinkedIn', [
            'user_id' => $linkedinUserId,
            'message_length' => strlen($message),
            'file_size' => strlen($fileContent),
            'file_type' => $isPdf ? 'PDF' : 'PNG',
        ]);

        // LinkedIn n'accepte que les images, pas les PDF
        // Si c'est un PDF, on doit le convertir en image
        if ($isPdf) {
            \Log::info('🔄 [LinkedIn] Conversion PDF en image nécessaire');
            $convertedImage = $this->convertPdfToImage($fileContent);

            // Si la conversion échoue, utiliser une image simple
            if ($convertedImage === null) {
                \Log::warning('⚠️ [LinkedIn] Conversion PDF échouée, génération image simple');
                // On ne peut pas obtenir le certificat ici, donc on renvoie NULL
                // et on laisse l'erreur se propager
                throw new \Exception('Failed to convert PDF to image. Imagick is required.');
            }

            $fileContent = $convertedImage;
        }

        // Vérifier que nous avons bien un contenu
        if (empty($fileContent)) {
            throw new \Exception('No image content to upload');
        }

        // Uploader l'image
        $mediaUrn = $this->uploadImageToLinkedIn($accessToken, $linkedinUserId, $fileContent);

        \Log::info('📸 [LinkedIn] Image uploadée', [
            'media_urn' => $mediaUrn,
            'user_id' => $linkedinUserId,
        ]);

        // Créer le post avec image - Format UGC simplifié
        $postData = [
            'author' => 'urn:li:person:'.$linkedinUserId,
            'lifecycleState' => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary' => [
                        'text' => $message."\n\n".$articleUrl,
                    ],
                    'shareMediaCategory' => 'IMAGE',
                    'media' => [
                        [
                            'status' => 'READY',
                            'description' => [
                                'text' => 'Certificat de formation',
                            ],
                            'media' => $mediaUrn,
                            'title' => [
                                'text' => 'Mon Certificat',
                            ],
                        ],
                    ],
                ],
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
            ],
        ];

        \Log::info('📤 [LinkedIn] Envoi du post', [
            'author' => 'urn:li:person:'.$linkedinUserId,
            'visibility' => 'PUBLIC',
            'message_preview' => substr($message, 0, 50).'...',
        ]);

        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'X-Restli-Protocol-Version' => '2.0.0',
                'LinkedIn-Version' => '202401',
            ])
            ->post('https://api.linkedin.com/v2/ugcPosts', $postData);

        if (! $response->successful()) {
            \Log::error('❌ Erreur publication LinkedIn', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Failed to publish post: '.$response->body());
        }

        \Log::info('✅ Post LinkedIn publié avec succès', [
            'response' => $response->json(),
        ]);

        return $response->json();
    }

    /**
     * Uploader une image sur LinkedIn
     */
    private function uploadImageToLinkedIn($accessToken, $linkedinUserId, $imageContent)
    {
        \Log::info('📸 Upload image LinkedIn', ['image_size' => strlen($imageContent)]);

        // 1. Enregistrer l'upload
        $registerData = [
            'registerUploadRequest' => [
                'recipes' => ['urn:li:digitalmediaRecipe:feedshare-image'],
                'owner' => 'urn:li:person:'.$linkedinUserId,
                'serviceRelationships' => [
                    [
                        'relationshipType' => 'OWNER',
                        'identifier' => 'urn:li:userGeneratedContent',
                    ],
                ],
            ],
        ];

        $registerResponse = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post('https://api.linkedin.com/v2/assets?action=registerUpload', $registerData);

        if (! $registerResponse->successful()) {
            throw new \Exception('Failed to register upload: '.$registerResponse->body());
        }

        $registerResult = $registerResponse->json();
        $uploadUrl = $registerResult['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'];
        $assetUrn = $registerResult['value']['asset'];

        // 2. Uploader vers LinkedIn (l'image est déjà en mémoire)
        $uploadResponse = Http::withToken($accessToken)
            ->withBody($imageContent, 'application/octet-stream')
            ->put($uploadUrl);

        if (! $uploadResponse->successful()) {
            throw new \Exception('Failed to upload image: '.$uploadResponse->body());
        }

        \Log::info('✅ Image uploadée', ['asset' => $assetUrn]);

        return $assetUrn;
    }

    /**
     * Convertir un PDF en image PNG
     */
    private function convertPdfToImage($pdfContent)
    {
        try {
            // Sauvegarder temporairement le PDF
            $tempPdf = storage_path('app/temp_linkedin_' . uniqid() . '.pdf');
            file_put_contents($tempPdf, $pdfContent);

            // Convertir avec Imagick (si disponible)
            if (extension_loaded('imagick')) {
                $imagick = new \Imagick();
                $imagick->setResolution(150, 150);
                $imagick->readImage($tempPdf . '[0]'); // Première page
                $imagick->setImageFormat('png');

                // Redimensionner pour LinkedIn (1200x630)
                $imagick->thumbnailImage(1200, 0);

                // Crop au centre pour avoir 1200x630
                $currentHeight = $imagick->getImageHeight();
                if ($currentHeight > 630) {
                    $cropY = ($currentHeight - 630) / 2;
                    $imagick->cropImage(1200, 630, 0, $cropY);
                }

                $imageContent = $imagick->getImageBlob();
                $imagick->destroy();

                // Supprimer le fichier temporaire
                @unlink($tempPdf);

                \Log::info('✅ [LinkedIn] PDF converti en image (Imagick)', ['size' => strlen($imageContent)]);

                return $imageContent;
            }

            // Fallback si Imagick n'est pas disponible
            @unlink($tempPdf);
            \Log::warning('⚠️ [LinkedIn] Imagick non disponible, utilisation image simple');

            // Retourner une image simple à la place
            throw new \Exception('Imagick not available');

        } catch (\Exception $e) {
            \Log::error('❌ [LinkedIn] Erreur conversion PDF', [
                'error' => $e->getMessage(),
            ]);

            // Retourner NULL pour forcer l'utilisation de l'image simple
            return null;
        }
    }

    /**
     * Générer l'image du certificat avec GD (COPIE EXACTE de downloadPdf)
     */
    private function generateSimpleCertificateImage($certificate)
    {
        // === COPIE 100% EXACTE DU CODE DE downloadPdf() ===
        $user = $certificate->user;
        $course = $certificate->course;

        // Formater la date
        $moisFr = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        if ($certificate->certificated_date) {
            $jour = $certificate->certificated_date->format('d');
            $mois = $moisFr[(int)$certificate->certificated_date->format('m')];
            $annee = $certificate->certificated_date->format('Y');
            $completion_date = $jour . ' ' . $mois . ' ' . $annee;
        } else {
            $jour = date('d');
            $mois = $moisFr[(int)date('m')];
            $annee = date('Y');
            $completion_date = $jour . ' ' . $mois . ' ' . $annee;
        }

        $course_title = $course ? $course->title : ($certificate->subject ?? 'Formation');
        $instructor_name = 'Instructeur';

        if ($course && $course->instructors->count() > 0) {
            $instructor = $course->instructors->first();
            if ($instructor && $instructor->userable) {
                $instructor_name = ($instructor->userable->first_name ?? '').' '.($instructor->userable->last_name ?? '');
            }
        }

        // NOUVELLE APPROCHE: Créer une image complète avec GD
        $imagePath = base_path('Modules/LMS/storage/app/public/lms/certificates/lms-B7ZmOUUgXO.jpeg');
        $studentName = ($user->userable->first_name ?? 'Utilisateur') . ' ' . ($user->userable->last_name ?? '');

        // Charger l'image de fond et la REDIMENSIONNER à une taille plus gérable
        $originalImage = imagecreatefromjpeg($imagePath);
        $originalWidth = imagesx($originalImage);
        $originalHeight = imagesy($originalImage);

        // Nouvelle taille: 1200×850px (plus facile à gérer, ratio similaire)
        $width = 1200;
        $height = 850;

        // Créer une nouvelle image redimensionnée
        $image = imagecreatetruecolor($width, $height);

        // Copier et redimensionner l'image originale
        imagecopyresampled($image, $originalImage, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
        imagedestroy($originalImage);

        // Définir les couleurs
        $colorBlue = imagecolorallocate($image, 26, 58, 82);
        $colorDarkBlue = imagecolorallocate($image, 44, 82, 130);
        $colorPurple = imagecolorallocate($image, 87, 37, 113);
        $colorBlack = imagecolorallocate($image, 0, 0, 0);

        // Redimensionner ENCORE pour que taille 5 soit visible
        $finalImage = imagecreatetruecolor(800, 600);
        imagecopyresampled($finalImage, $image, 0, 0, 0, 0, 800, 600, $width, $height);
        imagedestroy($image);
        $image = $finalImage;
        $width = 800;
        $height = 600;

        // Redéfinir les couleurs sur la nouvelle image
        $colorBlue = imagecolorallocate($image, 26, 58, 82);
        $colorDarkBlue = imagecolorallocate($image, 44, 82, 130);
        $colorPurple = imagecolorallocate($image, 87, 37, 113);
        $colorBlack = imagecolorallocate($image, 0, 0, 0);

        // ============ MÉTHODE IMAGESTRING (sans police TTF) ============

        // 1. NOM ÉTUDIANT: left: 50%, top: 40%
        $x = 400 - (strlen($studentName) * 4);
        $y = 240;
        imagestring($image, 5, $x, $y, utf8_decode($studentName), $colorBlue);

        // 2. TITRE: left: 50%, top: 50% (sur plusieurs lignes si long)
        $maxCharsPerLine = 45;
        $courseTitleDecoded = utf8_decode($course_title);

        // Découper le titre en plusieurs lignes si nécessaire
        if (strlen($courseTitleDecoded) > $maxCharsPerLine) {
            // Titre long - afficher sur 2 lignes
            $words = explode(' ', $courseTitleDecoded);
            $line1 = '';
            $line2 = '';
            $currentLine = 1;

            foreach ($words as $word) {
                if ($currentLine == 1 && strlen($line1 . ' ' . $word) <= $maxCharsPerLine) {
                    $line1 .= ($line1 ? ' ' : '') . $word;
                } else {
                    $currentLine = 2;
                    $line2 .= ($line2 ? ' ' : '') . $word;
                }
            }

            // Afficher ligne 1
            $x = 400 - (strlen($line1) * 3);
            $y = 290;
            imagestring($image, 4, $x, $y, $line1, $colorDarkBlue);

            // Afficher ligne 2
            if ($line2) {
                $x = 400 - (strlen($line2) * 3);
                $y = 310;
                imagestring($image, 4, $x, $y, $line2, $colorDarkBlue);
            }
        } else {
            // Titre court - une seule ligne
            $x = 400 - (strlen($courseTitleDecoded) * 3);
            $y = 300;
            imagestring($image, 4, $x, $y, $courseTitleDecoded, $colorDarkBlue);
        }

        // 3. N° CERTIFICAT: left: 525px, top: 524px
        $x = 493 - (strlen($certificate->certificate_id) * 2);
        $y = 524;
        imagestring($image, 2, $x, $y, $certificate->certificate_id, $colorBlack);

        // 4. DATE: left: 60%, bottom: 33% = top: 402px (67% de 600)
        $dateText = 'Fait a Bamako, le ' . $completion_date;
        $x = 460;
        $y = 385;
        imagestring($image, 4, $x, $y, utf8_decode($dateText), $colorPurple);

        // 5. INSTRUCTEUR: au niveau de "Formateur" (à droite)
        $x = 330;
        $y = 410;
        imagestring($image, 4, $x, $y, utf8_decode($instructor_name), $colorBlack);

        // Retourner l'image PNG
        ob_start();
        imagepng($image, null, 9);
        $imageContent = ob_get_clean();
        imagedestroy($image);

        \Log::info('✅ [LinkedIn] Certificat généré (même méthode que téléchargement)', ['size' => strlen($imageContent)]);

        return $imageContent;
    }
}
