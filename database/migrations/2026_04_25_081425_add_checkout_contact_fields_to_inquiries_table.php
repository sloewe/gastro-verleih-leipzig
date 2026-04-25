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
        Schema::table('inquiries', function (Blueprint $table) {
            $table->string('salutation')->nullable()->after('id');
            $table->string('street')->nullable()->after('company');
            $table->string('postal_code', 32)->nullable()->after('street');
            $table->string('city')->nullable()->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            $table->dropColumn([
                'salutation',
                'street',
                'postal_code',
                'city',
            ]);
        });
    }
};
