<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->renameColumn('journal_samithiya_lipinaya', 'samithiya_lipinaya');
            $table->renameColumn('journal_niyojithaya', 'niyojithaya');
            $table->renameColumn('journal_niyojithaya_lipinaya_1', 'niyojithaya_lipinaya_1');
            $table->renameColumn('journal_niyojithaya_lipinaya_2', 'niyojithaya_lipinaya_2');
            $table->renameColumn('journal_niyojithaya_lipinaya_3', 'niyojithaya_lipinaya_3');
            $table->renameColumn('journal_first_sithasiya_date', 'first_sithasiya_date');
            $table->renameColumn('journal_first_sithasiya_post_office', 'first_sithasiya_post_office');
            $table->renameColumn('journal_first_sithasiya_receipt_no', 'first_sithasiya_receipt_no');
            $table->renameColumn('journal_second_sithasiya_date', 'second_sithasiya_date');
            $table->renameColumn('journal_second_sithasiya_post_office', 'second_sithasiya_post_office');
            $table->renameColumn('journal_second_sithasiya_receipt_no', 'second_sithasiya_receipt_no');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->renameColumn('samithiya_lipinaya', 'journal_samithiya_lipinaya');
            $table->renameColumn('niyojithaya', 'journal_niyojithaya');
            $table->renameColumn('niyojithaya_lipinaya_1', 'journal_niyojithaya_lipinaya_1');
            $table->renameColumn('niyojithaya_lipinaya_2', 'journal_niyojithaya_lipinaya_2');
            $table->renameColumn('niyojithaya_lipinaya_3', 'journal_niyojithaya_lipinaya_3');
            $table->renameColumn('first_sithasiya_date', 'journal_first_sithasiya_date');
            $table->renameColumn('first_sithasiya_post_office', 'journal_first_sithasiya_post_office');
            $table->renameColumn('first_sithasiya_receipt_no', 'journal_first_sithasiya_receipt_no');
            $table->renameColumn('second_sithasiya_date', 'journal_second_sithasiya_date');
            $table->renameColumn('second_sithasiya_post_office', 'journal_second_sithasiya_post_office');
            $table->renameColumn('second_sithasiya_receipt_no', 'journal_second_sithasiya_receipt_no');
        });
    }
};
