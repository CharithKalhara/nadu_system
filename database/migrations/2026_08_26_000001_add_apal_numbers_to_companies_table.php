<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->integer('apal_pa')->nullable()->after('abiyachana_idiripath_kala_yuthu_dinaya');
            $table->integer('apal_vi')->nullable()->after('apal_pa');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn(['apal_pa', 'apal_vi']);
        });
    }
};
