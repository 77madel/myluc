<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // NULL si visiteur anonyme
            $table->string('session_id', 100)->unique();
            
            // Données techniques
            $table->enum('device_type', ['desktop', 'mobile', 'tablet'])->default('desktop');
            $table->string('os', 50)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('browser_version', 20)->nullable();
            $table->integer('screen_width')->nullable();
            $table->integer('screen_height')->nullable();
            
            // Géolocalisation
            $table->string('ip_address', 45)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('country_code', 2)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('timezone', 50)->nullable();
            
            // Source de trafic
            $table->text('referrer')->nullable();
            $table->string('traffic_source', 50)->nullable(); // direct, organic, social, referral
            $table->string('search_engine', 50)->nullable(); // google, bing, yahoo
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            
            // Données démographiques (si connecté)
            $table->integer('age')->nullable();
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable();
            $table->string('profession', 100)->nullable();
            
            // Timestamps
            $table->timestamp('first_visit')->useCurrent();
            $table->timestamp('last_visit')->useCurrent();
            
            $table->index('user_id');
            $table->index('session_id');
            $table->index('country_code');
            $table->index('traffic_source');
            $table->index('first_visit');
        });
        
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100);
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->text('page_url');
            $table->string('page_title', 255)->nullable();
            $table->text('referrer_url')->nullable();
            
            // Temps passé
            $table->integer('time_on_page')->default(0); // en secondes
            $table->integer('scroll_depth')->default(0); // pourcentage (0-100)
            
            $table->timestamp('visited_at')->useCurrent();
            
            $table->index('session_id');
            $table->index('user_id');
            $table->index('visited_at');
        });
        
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100)->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            
            // Durée de session
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration')->default(0); // en secondes
            
            // Statistiques
            $table->integer('pages_visited')->default(0);
            $table->integer('actions_performed')->default(0);
            
            // Conversion
            $table->boolean('converted')->default(false);
            $table->string('conversion_type', 50)->nullable(); // signup, purchase, enroll
            
            $table->index('session_id');
            $table->index('user_id');
            $table->index('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('user_analytics');
    }
};
