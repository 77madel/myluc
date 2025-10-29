<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use ZipArchive;

class UpdateGeoIpDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geoip:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Télécharger et mettre à jour la base de données MaxMind GeoLite2-City';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌍 Mise à jour de la base de données GeoIP...');
        
        // 1. Vérifier la License Key
        $licenseKey = env('MAXMIND_LICENSE_KEY');
        
        if (empty($licenseKey)) {
            $this->error('❌ MAXMIND_LICENSE_KEY non définie dans le fichier .env');
            $this->warn('💡 Ajoutez MAXMIND_LICENSE_KEY=votre_clé dans le fichier .env');
            $this->warn('💡 Obtenez une clé gratuite sur : https://www.maxmind.com/en/geolite2/signup');
            return Command::FAILURE;
        }
        
        // 2. Construire l'URL de téléchargement
        $url = "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key={$licenseKey}&suffix=tar.gz";
        
        // 3. Créer le dossier temporaire
        $tempDir = storage_path('app/temp');
        $geoipDir = storage_path('app/geoip');
        
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }
        
        if (!File::exists($geoipDir)) {
            File::makeDirectory($geoipDir, 0755, true);
        }
        
        $downloadPath = $tempDir . '/GeoLite2-City.tar.gz';
        
        // 4. Télécharger le fichier
        $this->info('📥 Téléchargement de GeoLite2-City...');
        
        try {
            $response = Http::timeout(300)->get($url);
            
            if ($response->failed()) {
                $this->error('❌ Erreur lors du téléchargement : ' . $response->status());
                return Command::FAILURE;
            }
            
            File::put($downloadPath, $response->body());
            
            $fileSize = File::size($downloadPath);
            $this->info("✅ Téléchargement terminé ! Taille : " . round($fileSize / 1024 / 1024, 2) . " MB");
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur de téléchargement : ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        // 5. Décompresser avec tar (si disponible)
        $this->info('📦 Décompression...');
        
        try {
            // Méthode 1 : Utiliser tar natif de Windows (si disponible)
            $extractPath = $tempDir . '/extracted';
            
            if (!File::exists($extractPath)) {
                File::makeDirectory($extractPath, 0755, true);
            }
            
            // Commande tar
            $tarCommand = "tar -xzf \"{$downloadPath}\" -C \"{$extractPath}\"";
            
            exec($tarCommand, $output, $returnCode);
            
            if ($returnCode === 0) {
                $this->info('✅ Décompression réussie avec tar');
                
                // Trouver le fichier .mmdb
                $mmdbFiles = File::glob($extractPath . '/*/GeoLite2-City.mmdb');
                
                if (count($mmdbFiles) > 0) {
                    $mmdbFile = $mmdbFiles[0];
                    $destinationPath = $geoipDir . '/GeoLite2-City.mmdb';
                    
                    // Sauvegarder l'ancienne base (backup)
                    if (File::exists($destinationPath)) {
                        $backupPath = $geoipDir . '/GeoLite2-City.mmdb.backup';
                        File::move($destinationPath, $backupPath);
                        $this->info('💾 Ancienne base sauvegardée');
                    }
                    
                    // Copier la nouvelle base
                    File::copy($mmdbFile, $destinationPath);
                    $this->info('✅ Base de données installée : ' . $destinationPath);
                    
                    // Nettoyer les fichiers temporaires
                    File::deleteDirectory($tempDir);
                    $this->info('🧹 Fichiers temporaires supprimés');
                    
                    // Vérifier l'installation
                    if (File::exists($destinationPath)) {
                        $size = File::size($destinationPath);
                        $this->info("✅ Vérification OK - Taille : " . round($size / 1024 / 1024, 2) . " MB");
                        $this->info('🎉 Mise à jour terminée avec succès !');
                        return Command::SUCCESS;
                    }
                } else {
                    $this->error('❌ Fichier .mmdb non trouvé dans l\'archive');
                    return Command::FAILURE;
                }
            } else {
                // tar non disponible, afficher les instructions manuelles
                $this->warn('⚠️ La commande tar n\'est pas disponible sur ce système');
                $this->info('');
                $this->info('📋 INSTALLATION MANUELLE :');
                $this->info('1. Téléchargez le fichier depuis :');
                $this->info('   https://www.maxmind.com/en/accounts/current/geoip/downloads');
                $this->info('2. Décompressez avec 7-Zip (téléchargez sur https://www.7-zip.org/)');
                $this->info('3. Copiez GeoLite2-City.mmdb vers :');
                $this->info('   ' . $geoipDir . '/GeoLite2-City.mmdb');
                $this->info('');
                $this->info('💡 Le fichier téléchargé est disponible ici :');
                $this->info('   ' . $downloadPath);
                
                return Command::FAILURE;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur de décompression : ' . $e->getMessage());
            $this->info('');
            $this->info('📋 Le fichier téléchargé est disponible ici :');
            $this->info('   ' . $downloadPath);
            $this->info('💡 Décompressez-le manuellement avec 7-Zip');
            
            return Command::FAILURE;
        }
    }
}
