<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = array_filter([
            Schema::hasColumn('companies', 'apal_pa') ? 'apal_pa' : null,
            Schema::hasColumn('companies', 'apal_vi') ? 'apal_vi' : null,
        ]);

        if ($columns !== []) {
            Schema::table('companies', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->integer('apal_pa')->nullable();
            $table->integer('apal_vi')->nullable();
        });
    }
};
