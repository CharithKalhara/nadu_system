<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'company_name',
        'table_name',
        'status',
        'nadu_ankaya_format',
        'teeraka',
        'teeraka_name_with_initials',
        'karyalaya',
        'wibhaga_dinaya',
        'welawa',
        'journal_nadu_ankaya_format',
        'journal_teeraka',
        'journal_samithiya',
        'journal_samithiya_lipinaya',
        'journal_niyojithaya',
        'journal_niyojithaya_lipinaya_1',
        'journal_niyojithaya_lipinaya_2',
        'journal_niyojithaya_lipinaya_3',
        'journal_first_sithasiya_date',
        'journal_first_sithasiya_post_office',
        'journal_first_sithasiya_receipt_no',
        'journal_second_sithasiya_date',
        'journal_second_sithasiya_post_office',
        'journal_second_sithasiya_receipt_no',
    ];
}
