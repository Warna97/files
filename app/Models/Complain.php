<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    use HasFactory;
    protected $table = 'complains';

    protected $fillable = [
        'cname',
        'tele',
        'complain',
        'img1',
        'img2',
        'img3',
    ];

    public function complainAction()
    {
        return $this->hasOne(ComplainAction::class);
    }
}
/*
    1(Complain) : 1 (ComplainAction)
*/
