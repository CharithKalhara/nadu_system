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
        'thinduwa_labadena_dinaya',
        'gewia_yuthu_dinaya',
        'abiyachana_idiripath_kala_yuthu_dinaya',
        'welawa',
        'samithiya_lipinaya',
        'niyojithaya',
        'niyojithaya_lipinaya_1',
        'niyojithaya_lipinaya_2',
        'niyojithaya_lipinaya_3',
        'first_sithasiya_date',
        'first_sithasiya_post_office',
        'first_sithasiya_receipt_no',
        'second_sithasiya_date',
        'second_sithasiya_post_office',
        'second_sithasiya_receipt_no',
        'wibaga_sthanaya',
    ];
}
