<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->string('nadu_ankaya_format')->nullable()->after('company_name');
            $table->string('teeraka')->nullable()->after('nadu_ankaya_format');
            $table->string('karyalaya')->nullable()->after('teeraka');
            $table->date('wibhaga_dinaya')->nullable()->after('karyalaya');
            $table->time('welawa')->nullable()->after('wibhaga_dinaya');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn([
                'nadu_ankaya_format',
                'teeraka',
                'karyalaya',
                'wibhaga_dinaya',
                'welawa',
            ]);
        });
    }
};
