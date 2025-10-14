<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class technical_person extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'field', 'address'];
}
