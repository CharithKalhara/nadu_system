<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasForeignKey('documents', 'documents_nadu_id_foreign')) {
            return;
        }

        Schema::table('documents', function (Blueprint $table): void {
            $table->dropForeign(['nadu_id']);
        });
    }

    public function down(): void
    {
        // Nadu records are stored in company-specific tables. Recreating a
        // foreign key to the legacy `cases` table would corrupt that design.
    }
};
