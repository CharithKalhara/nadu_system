<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->string('journal_nadu_ankaya_format')->nullable();
            $table->string('journal_teeraka')->nullable();
            $table->string('journal_samithiya')->nullable();
            $table->string('journal_samithiya_lipinaya')->nullable();
            $table->string('journal_niyojithaya')->nullable();
            $table->string('journal_niyojithaya_lipinaya_1')->nullable();
            $table->string('journal_niyojithaya_lipinaya_2')->nullable();
            $table->string('journal_niyojithaya_lipinaya_3')->nullable();
            $table->date('journal_first_sithasiya_date')->nullable();
            $table->string('journal_first_sithasiya_post_office')->nullable();
            $table->string('journal_first_sithasiya_receipt_no')->nullable();
            $table->date('journal_second_sithasiya_date')->nullable();
            $table->string('journal_second_sithasiya_post_office')->nullable();
            $table->string('journal_second_sithasiya_receipt_no')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn([
                'journal_nadu_ankaya_format', 'journal_teeraka', 'journal_samithiya', 'journal_samithiya_lipinaya',
                'journal_niyojithaya', 'journal_niyojithaya_lipinaya_1', 'journal_niyojithaya_lipinaya_2',
                'journal_niyojithaya_lipinaya_3', 'journal_first_sithasiya_date', 'journal_first_sithasiya_post_office',
                'journal_first_sithasiya_receipt_no', 'journal_second_sithasiya_date',
                'journal_second_sithasiya_post_office', 'journal_second_sithasiya_receipt_no',
            ]);
        });
    }
};
