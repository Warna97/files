<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplainAction extends Model
{
    use HasFactory;
    protected $fillable = [
        'complain_id',
        'action',
    ];

    public function complain()
    {
        return $this->belongsTo(Complain::class);
    }
}
/*
    1(Complain) : 1 (ComplainAction)
*/
