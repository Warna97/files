<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_year',
        'application_month',
        'name_en',
        'name_si',
        'name_ta',
        'file_path_en',
        'file_path_si',
        'file_path_ta',
    ];
}


