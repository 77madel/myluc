<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->timestamp('enrolled_at')->nullable()->after('status');
            $table->timestamp('course_due_at')->nullable()->after('enrolled_at');
            $table->timestamp('grace_due_at')->nullable()->after('course_due_at');
            $table->enum('enrollment_status', ['in_progress','grace','expired','completed'])->default('in_progress')->after('grace_due_at');
            $table->index(['user_id','course_id']);
            $table->index(['enrollment_status']);
            $table->index(['course_due_at','grace_due_at']);
        });
    }

    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->dropIndex(['user_id','course_id']);
            $table->dropIndex(['enrollment_status']);
            $table->dropIndex(['course_due_at','grace_due_at']);
            $table->dropColumn(['enrolled_at','course_due_at','grace_due_at','enrollment_status']);
        });
    }
};



