<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;

class CaseTableSchema
{
    public static function define(Blueprint $table): void
    {
        $table->id();
        $table->unsignedBigInteger('company_id')->index();

        $table->string('nadu_ankaya')->nullable();

        $table->string('nayakaru1_nama')->nullable();
        $table->string('nayakaru1_samajika_ankaya')->nullable();
        $table->string('nayakaru1_lipinaya1')->nullable();
        $table->string('nayakaru1_lipinaya2')->nullable();
        $table->string('nayakaru1_lipinaya3')->nullable();

        $table->string('nayakaru2_nama')->nullable();
        $table->string('nayakaru2_samajika_ankaya')->nullable();
        $table->string('nayakaru2_lipinaya1')->nullable();
        $table->string('nayakaru2_lipinaya2')->nullable();
        $table->string('nayakaru2_lipinaya3')->nullable();

        $table->string('aepakaru1_nama')->nullable();
        $table->string('aepakaru1_samajika_ankaya')->nullable();
        $table->string('aepakaru1_lipinaya1')->nullable();
        $table->string('aepakaru1_lipinaya2')->nullable();
        $table->string('aepakaru1_lipinaya3')->nullable();

        $table->string('aepakaru2_nama')->nullable();
        $table->string('aepakaru2_samajika_ankaya')->nullable();
        $table->string('aepakaru2_lipinaya1')->nullable();
        $table->string('aepakaru2_lipinaya2')->nullable();
        $table->string('aepakaru2_lipinaya3')->nullable();

        $table->decimal('arawul_mudala', 15, 2)->nullable();
        $table->decimal('dun_naya_mudala', 15, 2)->nullable();

        $table->date('dun_dinaya')->nullable();
        $table->string('kalaya')->nullable();
        $table->decimal('poli_prathishathaya', 5, 2)->nullable();

        $table->decimal('awasan_mudal_bendima', 15, 2)->nullable();
        $table->integer('dina_ganuna')->nullable();

        $table->decimal('mul_mudala', 15, 2)->nullable();
        $table->decimal('poliya', 15, 2)->nullable();
        $table->decimal('nadu_gasthu', 15, 2)->nullable();
        $table->decimal('total', 15, 2)->nullable();

        $table->timestamps();
    }
}
