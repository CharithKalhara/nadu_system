<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'companies';

    public function up(): void
    {
        Schema::connection('companies')->create('company_template_cases', function (Blueprint $table) {

            $table->id();

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

            $table->decimal('awasan_mudal_bandima', 15, 2)->nullable();
            $table->integer('dina_ganana')->nullable();

            $table->decimal('mul_mudala', 15, 2)->nullable();
            $table->decimal('poliya', 15, 2)->nullable();
            $table->decimal('nadu_gasthu', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('companies')->dropIfExists('company_template_cases');
    }
};