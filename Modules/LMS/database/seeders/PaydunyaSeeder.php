<?php


namespace Modules\LMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaydunyaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Supprimer toutes les anciennes méthodes de paiement
        DB::table('payment_methods')->truncate();

        // Insérer Paydunya comme seule méthode de paiement
        DB::table('payment_methods')->insert([
            'method_name' => 'Paydunya',
            'slug' => 'paydunya',
            'logo' => 'paydunya.png',
            'currency' => config('paydunya.currency', 'XOF'),
            'conversation_rate' => 1.0,
            'keys' => json_encode([
                'master_key' => config('paydunya.master_key', ''),
                'private_key' => config('paydunya.private_key', ''),
                'token' => config('paydunya.token', ''),
            ]),
            'enabled_test_mode' => config('paydunya.test_mode', true) ? 1 : 0,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Paydunya payment method seeded successfully!');
    }
}
