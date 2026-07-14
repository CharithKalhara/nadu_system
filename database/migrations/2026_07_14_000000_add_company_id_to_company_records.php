<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('company_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
        });

        $companies = DB::table('companies')
            ->select(['id', 'table_name'])
            ->orderBy('id')
            ->get();

        if ($companies->count() === 1) {
            DB::table('documents')
                ->whereNull('company_id')
                ->update(['company_id' => $companies->first()->id]);
        }

        $companies->each(function (object $company): void {
            if (! preg_match('/^company_\d{4}_cases$/', $company->table_name)
                || ! Schema::connection('companies')->hasTable($company->table_name)) {
                return;
            }

            if (! Schema::connection('companies')->hasColumn($company->table_name, 'company_id')) {
                Schema::connection('companies')->table($company->table_name, function (Blueprint $table): void {
                    $table->unsignedBigInteger('company_id')->nullable()->after('id')->index();
                });
            }

            DB::connection('companies')
                ->table($company->table_name)
                ->whereNull('company_id')
                ->update(['company_id' => $company->id]);
        });
    }

    public function down(): void
    {
        DB::table('companies')
            ->select('table_name')
            ->each(function (object $company): void {
                if (preg_match('/^company_\d{4}_cases$/', $company->table_name)
                    && Schema::connection('companies')->hasTable($company->table_name)
                    && Schema::connection('companies')->hasColumn($company->table_name, 'company_id')) {
                    Schema::connection('companies')->table($company->table_name, function (Blueprint $table): void {
                        $table->dropIndex(['company_id']);
                        $table->dropColumn('company_id');
                    });
                }
            });

        Schema::table('documents', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
