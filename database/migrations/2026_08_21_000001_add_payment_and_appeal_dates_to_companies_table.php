<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->date('gewia_yuthu_dinaya')->nullable()->after('thinduwa_labadena_dinaya');
            $table->date('abiyachana_idiripath_kala_yuthu_dinaya')->nullable()->after('gewia_yuthu_dinaya');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn([
                'gewia_yuthu_dinaya',
                'abiyachana_idiripath_kala_yuthu_dinaya',
            ]);
        });
    }
};
