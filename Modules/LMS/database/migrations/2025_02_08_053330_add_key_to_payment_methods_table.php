<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            //
            $table->string('slug')->after('method_name');
            $table->string('currency')->after('slug');
            $table->float('conversation_rate')->nullable()->after('currency');
            $table->json('keys')->after('conversation_rate')->nullable();
            $table->integer('enabled_test_mode')->default(0)->after('keys');
        });
        // Supprimer tous les anciens modes de paiement
        DB::table('payment_methods')->truncate();

        DB::table('payment_methods')->insert([
            'method_name' => 'Paydunya',
            'slug' => 'paydunya',
            'logo' => 'paydunya.png', // Assurez-vous d'avoir le logo
            'currency' => 'XOF', // Franc CFA par défaut, ajustez selon vos besoins
            'conversation_rate' => 1.0,
            'keys' => json_encode([
                'master_key' => '',
                'private_key' => '',
                'token' => ''
            ]),
            'enabled_test_mode' => 1, // Mode test activé par défaut
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      /*  Schema::table('PaymentMethod', function (Blueprint $table) {
            //
            $table->dropColumn('slug');
            $table->dropColumn('currency');
            $table->dropColumn('conversation_rate');
            $table->dropColumn('keys');
            $table->dropColumn('enabled_test_mode');
        });*/
    }
};
