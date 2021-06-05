<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class promo_code extends Model
{
    use HasFactory;

    protected $fillable =[
        'code_name','valid_to','amount','status','address','latitude','longitude','radius'
    ];
}
