<div class="overflow-x-auto scrollbar-table">
    <table
        class="table-auto w-full whitespace-nowrap text-left text-gray-500 dark:text-dark-text font-medium leading-non">
        <thead class="text-primary-500">
            <tr>
                <th
                    class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                    {{ translate('Certificate') }}
                </th>
                <th
                    class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                    {{ translate('Certificate ID') }}
                </th>
                <th
                    class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                    {{ translate('Certificate Type') }}
                </th>

                <th
                    class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                    {{ translate('Certificate Date') }}
                </th>

                <th
                    class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right w-10">
                    {{ translate('Action') }}
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-dark-border-three">
            @foreach ($certificates as $certificate)
                <tr>
                    <td class="items-center gap-2 px-3.5 py-4">
                        <h6 class="leading-none text-heading dark:text-white font-bold mb-1.5 line-clamp-1">
                            {{ $certificate->subject ?? '' }}
                        </h6>
                    </td>
                    <td class="px-3.5 py-4">
                        {{ $certificate->certificate_id ?? 0 }}
                    </td>
                    <td class="px-3.5 py-4">
                        {{ $certificate?->type }}
                    </td>
                    <td class="px-3.5 py-4">
                        {{ customDateFormate($certificate->certificated_date, format: 'm D  Y') }}
                    </td>
                    <td class="px-3.5 py-4">
                        <div class="flex items-center gap-2 flex-wrap">
                            <!-- 1️⃣ View (icône œil uniquement) -->
                            <a href="{{ route('student.certificate.view', $certificate->id) }}" target="_blank"
                                class="btn-icon btn-info-icon-light size-8" 
                                title="{{ translate('Voir le certificat') }}">
                                <i class="ri-eye-line text-inherit text-base"></i>
                            </a>
                            
                            <!-- 2️⃣ Download -->
                            @if($certificate->isDownloaded())
                                <!-- Badge téléchargé (compact) -->
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200" title="Téléchargé le {{ $certificate->downloaded_at->format('d/m/Y') }}">
                                    <i class="ri-check-line mr-1"></i>
                                    {{ translate('Téléchargé') }}
                                </span>
                            @else
                                <!-- Bouton Télécharger -->
                                <a href="{{ route('student.certificate.download', $certificate->id) }}"
                                    class="btn-icon btn-success-icon-light size-8" 
                                    title="{{ translate('Télécharger le certificat') }}">
                                    <i class="ri-download-2-line text-inherit text-base"></i>
                                </a>
                            @endif
                            
                            <!-- 3️⃣ Réseaux sociaux -->
                            @php
                                $publicUrl = route('certificate.public', $certificate->public_uuid);
                                $courseName = $certificate->subject;
                                $certNumber = $certificate->certificate_id;
                                $certDate = $certificate->certificated_date ? $certificate->certificated_date->format('d/m/Y') : date('d/m/Y');
                                $platformName = config('app.name', 'MyLMS');
                                
                                // Message LinkedIn
                                $linkedinMessage = "🎓 Je suis fier(e) d'annoncer que j'ai obtenu mon certificat pour le cours '{$courseName}' !\n\nCette formation sur {$platformName} m'a permis d'acquérir de nouvelles compétences.\n\nCertificat N° : {$certNumber}\nDate d'obtention : {$certDate}\n\n#Formation #Certificat #ApprentissageContinue";
                                
                                // Message Facebook
                                $fbMessage = "🎉 Je viens d'obtenir mon certificat pour le cours '{$courseName}' !\n\n📅 Date : {$certDate}\n🎓 Certificat N° : {$certNumber}\n🏫 Plateforme : {$platformName}\n\n#Formation #Certificat";
                                // Facebook utilise les meta tags Open Graph, pas le paramètre quote
                                $facebookUrl = "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($publicUrl);
                                
                                // Message Twitter (optimisé pour 280 caractères)
                                $twitterMessage = "🎓 Certificat obtenu pour '{$courseName}' sur {$platformName} ! 🎉\n\nDate : {$certDate}\nN° : {$certNumber}\n\n#Formation #Certificat #Apprentissage #Réussite";
                                $twitterUrl = "https://twitter.com/intent/tweet?text=" . urlencode($twitterMessage) . "&url=" . urlencode($publicUrl);
                            @endphp
                            
                            <button 
                                onclick="openShareModal({{ $certificate->id }}, '{{ addslashes($courseName) }}', '{{ $publicUrl }}', `{{ addslashes($linkedinMessage) }}`)"
                                class="btn-icon btn-primary-icon-light size-8" 
                                title="{{ translate('Partager sur LinkedIn') }}">
                                <i class="ri-linkedin-box-fill text-inherit text-base"></i>
                            </button>
                            
                            <button 
                                onclick="openFacebookModal({{ $certificate->id }}, '{{ addslashes($courseName) }}', '{{ $publicUrl }}', `{{ addslashes($fbMessage) }}`)"
                                class="btn-icon btn-info-icon-light size-8" 
                                title="{{ translate('Partager sur Facebook') }}">
                                <i class="ri-facebook-box-fill text-inherit text-base"></i>
                            </button>
                            
                            <button 
                                onclick="shareToTwitter({{ $certificate->id }}, '{{ addslashes($courseName) }}', '{{ $publicUrl }}', `{{ addslashes($twitterMessage) }}`)"
                                class="btn-icon btn-secondary-icon-light size-8" 
                                title="{{ translate('Partager sur Twitter') }}">
                                <i class="ri-twitter-x-fill text-inherit text-base"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal de Partage Facebook -->
<div id="facebook-share-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 16px; max-width: 550px; width: 90%; max-height: 90vh; overflow-y: auto;" class="dark:bg-dark-card">
        <div style="padding: 24px; border-bottom: 1px solid #E5E7EB;" class="dark:border-gray-700">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 20px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px;" class="text-heading dark:text-white">
                    <i class="ri-facebook-box-fill" style="color: #1877F2; font-size: 28px;"></i>
                    {{ translate('Partager sur Facebook') }}
                </h3>
                <button onclick="closeFacebookModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6B7280;" class="dark:text-gray-400">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div style="margin-top: 8px;">
                <p style="font-size: 14px; color: #6B7280;" class="dark:text-gray-400" id="fb-modal-course-title"></p>
            </div>
        </div>
        
        <div style="padding: 24px;">
            <!-- Message à copier -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;" class="text-heading dark:text-white">
                    {{ translate('Message à partager :') }}
                </label>
                <textarea id="facebook-message" rows="8" readonly
                    style="width: 100%; padding: 12px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 14px; font-family: inherit; background: #F9FAFB;" 
                    class="dark:bg-dark-card-two dark:border-gray-700 dark:text-white"></textarea>
            </div>
            
            <!-- Instructions -->
            <div style="padding: 16px; background: #F0F9FF; border: 2px solid #1877F2; border-radius: 12px; margin-bottom: 20px;" class="dark:bg-blue-900/20">
                <h4 style="margin: 0 0 12px 0; font-weight: 700; color: #1877F2; display: flex; align-items: center; gap: 8px;">
                    <i class="ri-information-fill"></i>
                    {{ translate('Comment partager :') }}
                </h4>
                <ol style="margin: 0; padding-left: 20px; font-size: 14px; color: #1e40af;" class="dark:text-blue-300">
                    <li style="margin-bottom: 8px;">{{ translate('Cliquez sur "Copier le Message"') }}</li>
                    <li style="margin-bottom: 8px;">{{ translate('Cliquez sur "Ouvrir Facebook"') }}</li>
                    <li style="margin-bottom: 8px;">{{ translate('Créez un nouveau post sur Facebook') }}</li>
                    <li style="margin-bottom: 8px;">{{ translate('Collez le message (Ctrl+V)') }}</li>
                    <li>{{ translate('Publiez !') }}</li>
                </ol>
            </div>
            
            <!-- Actions -->
            <div style="display: flex; gap: 12px;">
                <button onclick="copyFacebookMessage()" style="flex: 1; padding: 12px; background: white; color: #374151; border: 1px solid #D1D5DB; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;" class="dark:bg-dark-card dark:border-gray-600 dark:text-white">
                    <i class="ri-file-copy-line"></i>
                    {{ translate('Copier le Message') }}
                </button>
                <button onclick="openFacebookNow()" style="flex: 1; padding: 12px; background: #1877F2; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                    <i class="ri-facebook-box-fill"></i>
                    {{ translate('Ouvrir Facebook') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Partage LinkedIn -->
<div id="linkedin-share-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center;">
    <div style="background: white; border-radius: 16px; max-width: 650px; width: 90%; max-height: 90vh; overflow-y: auto;" class="dark:bg-dark-card">
        <div style="padding: 24px; border-bottom: 1px solid #E5E7EB;" class="dark:border-gray-700">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 20px; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px;" class="text-heading dark:text-white">
                    <i class="ri-linkedin-box-fill" style="color: #0A66C2; font-size: 28px;"></i>
                    {{ translate('Partager sur LinkedIn') }}
                </h3>
                <button onclick="closeShareModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6B7280;" class="dark:text-gray-400">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div style="margin-top: 8px;">
                <p style="font-size: 14px; color: #6B7280;" class="dark:text-gray-400" id="modal-course-title"></p>
            </div>
        </div>
        
        <div style="padding: 24px;">
            <!-- Message personnalisable -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px;" class="text-heading dark:text-white">
                    {{ translate('Votre message (modifiable) :') }}
                </label>
                <textarea id="linkedin-message" rows="10" 
                    style="width: 100%; padding: 12px; border: 1px solid #E5E7EB; border-radius: 8px; font-size: 14px; font-family: inherit; resize: vertical;" 
                    class="dark:bg-dark-card-two dark:border-gray-700 dark:text-white"></textarea>
                <small style="font-size: 12px; color: #6B7280; margin-top: 4px; display: block;" class="dark:text-gray-400">
                    💡 {{ translate('Vous pouvez modifier ce message avant de partager') }}
                </small>
            </div>
            
            <!-- Option 1: OAuth (Recommandé) -->
            <div style="margin-bottom: 20px; padding: 16px; background: #F0F9FF; border: 2px solid #0A66C2; border-radius: 12px;" class="dark:bg-blue-900/20">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <i class="ri-flashlight-fill" style="color: #0A66C2; font-size: 24px;"></i>
                    <h4 style="margin: 0; font-weight: 700; color: #0A66C2;">
                        {{ translate('Option 1 : Publication Automatique') }}
                        <span style="background: #0A66C2; color: white; font-size: 10px; padding: 2px 8px; border-radius: 4px; margin-left: 8px; font-weight: 700;">
                            {{ translate('RECOMMANDÉ') }}
                        </span>
                    </h4>
                </div>
                <p style="font-size: 13px; color: #1e40af; margin-bottom: 12px;" class="dark:text-blue-300">
                    ⚡ {{ translate('Le plus rapide ! Votre message et l\'image du certificat seront publiés directement sur LinkedIn.') }}
                </p>
                <form id="linkedin-oauth-form" method="POST" action="{{ route('student.linkedin.authorize') }}">
                    @csrf
                    <input type="hidden" id="oauth-certificate-id" name="certificate_id">
                    <input type="hidden" id="oauth-message" name="message">
                    <button type="submit" style="width: 100%; padding: 12px; background: #0A66C2; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 15px;">
                        <i class="ri-linkedin-box-fill"></i>
                        {{ translate('Connecter LinkedIn et Publier') }}
                    </button>
                </form>
            </div>
            
            <!-- Divider -->
            <div style="display: flex; align-items: center; gap: 12px; margin: 20px 0;">
                <div style="flex: 1; height: 1px; background: #E5E7EB;"></div>
                <span style="font-size: 12px; color: #9CA3AF; font-weight: 600;">{{ translate('OU') }}</span>
                <div style="flex: 1; height: 1px; background: #E5E7EB;"></div>
            </div>
            
            <!-- Option 2: Manuel -->
            <div style="padding: 16px; background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 12px;" class="dark:bg-dark-card-two dark:border-gray-700">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <i class="ri-clipboard-line" style="color: #6B7280; font-size: 24px;"></i>
                    <h4 style="margin: 0; font-weight: 700;" class="text-heading dark:text-white">
                        {{ translate('Option 2 : Partage Manuel') }}
                    </h4>
                </div>
                <p style="font-size: 13px; color: #6B7280; margin-bottom: 12px;" class="dark:text-gray-400">
                    📋 {{ translate('Copiez le message et collez-le manuellement sur LinkedIn.') }}
                </p>
                <div style="display: flex; gap: 8px;">
                    <button onclick="copyMessage()" style="flex: 1; padding: 10px; background: white; color: #374151; border: 1px solid #D1D5DB; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;" class="dark:bg-dark-card dark:border-gray-600 dark:text-white">
                        <i class="ri-file-copy-line"></i>
                        {{ translate('Copier le Message') }}
                    </button>
                    <button onclick="openLinkedInManual()" style="flex: 1; padding: 10px; background: #0A66C2; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="ri-external-link-line"></i>
                        {{ translate('Ouvrir LinkedIn') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Variables globales
let currentCertificateId = null;
let currentCertificateUrl = null;

// Ouvrir le modal de partage LinkedIn
function openShareModal(certificateId, courseName, certificateUrl, message) {
    currentCertificateId = certificateId;
    currentCertificateUrl = certificateUrl;
    
    // Mettre à jour le modal
    document.getElementById('modal-course-title').textContent = courseName;
    document.getElementById('linkedin-message').value = message;
    document.getElementById('oauth-certificate-id').value = certificateId;
    document.getElementById('oauth-message').value = message;
    
    // Afficher le modal
    document.getElementById('linkedin-share-modal').style.display = 'flex';
}

// Fermer le modal
function closeShareModal() {
    document.getElementById('linkedin-share-modal').style.display = 'none';
}

// Mettre à jour le message dans le form OAuth quand l'utilisateur modifie
document.getElementById('linkedin-message')?.addEventListener('input', function() {
    document.getElementById('oauth-message').value = this.value;
});

// Copier le message
function copyMessage() {
    const message = document.getElementById('linkedin-message').value;
    navigator.clipboard.writeText(message + '\n\n' + currentCertificateUrl).then(() => {
        if (typeof toastr !== 'undefined') {
            toastr.success('{{ translate("Message copié !") }}');
        } else {
            alert('{{ translate("Message copié dans le presse-papier !") }}');
        }
        trackShare(currentCertificateId, 'linkedin');
    }).catch(err => {
        console.error('Erreur copie:', err);
        alert('{{ translate("Erreur lors de la copie") }}');
    });
}

// Ouvrir LinkedIn manuellement
function openLinkedInManual() {
    window.open('https://www.linkedin.com/feed/', '_blank');
    trackShare(currentCertificateId, 'linkedin');
    closeShareModal();
}

// Variables globales Facebook
let currentFacebookCertificateId = null;
let currentFacebookUrl = null;

// Ouvrir le modal Facebook
function openFacebookModal(certificateId, courseName, certificateUrl, message) {
    currentFacebookCertificateId = certificateId;
    currentFacebookUrl = certificateUrl;
    
    // Mettre à jour le modal
    document.getElementById('fb-modal-course-title').textContent = courseName;
    document.getElementById('facebook-message').value = message + '\n\n' + certificateUrl;
    
    // Afficher le modal
    document.getElementById('facebook-share-modal').style.display = 'flex';
}

// Fermer le modal Facebook
function closeFacebookModal() {
    document.getElementById('facebook-share-modal').style.display = 'none';
}

// Copier le message Facebook
function copyFacebookMessage() {
    const message = document.getElementById('facebook-message').value;
    navigator.clipboard.writeText(message).then(() => {
        if (typeof toastr !== 'undefined') {
            toastr.success('✅ {{ translate("Message copié ! Collez-le sur Facebook.") }}');
        } else {
            alert('{{ translate("Message copié dans le presse-papier !") }}');
        }
    }).catch(err => {
        console.error('Erreur copie:', err);
        alert('{{ translate("Erreur lors de la copie") }}');
    });
}

// Ouvrir Facebook
function openFacebookNow() {
    window.open('https://www.facebook.com/', 'facebook-share', 'width=800,height=600');
    trackShare(currentFacebookCertificateId, 'facebook');
    closeFacebookModal();
}

// Fermer le modal Facebook en cliquant en dehors
document.getElementById('facebook-share-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeFacebookModal();
    }
});

// Fonction pour partager sur Twitter avec message personnalisé
function shareToTwitter(certificateId, courseName, certificateUrl, message) {
    // Ouvrir Twitter avec le message pré-rempli
    const twitterUrl = 'https://twitter.com/intent/tweet?text=' + encodeURIComponent(message) + '&url=' + encodeURIComponent(certificateUrl);
    
    // Afficher un message informatif
    if (typeof toastr !== 'undefined') {
        toastr.info('💡 {{ translate("Le message est pré-rempli sur Twitter !") }}', '', {timeOut: 3000});
    }
    
    // Ouvrir Twitter
    window.open(twitterUrl, 'twitter-share', 'width=600,height=600');
    
    // Enregistrer le partage
    trackShare(certificateId, 'twitter');
}

// Fonction pour enregistrer le partage
function trackShare(certificateId, platform) {
    fetch('{{ route("student.certificate.track-share") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            certificate_id: certificateId,
            platform: platform,
            custom_message: null
        })
    }).then(response => response.json())
      .then(data => {
          console.log('Partage enregistré:', data);
      })
      .catch(error => {
          console.log('Erreur tracking:', error);
      });
}

// Fermer le modal en cliquant en dehors
document.getElementById('linkedin-share-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeShareModal();
    }
});
</script>
