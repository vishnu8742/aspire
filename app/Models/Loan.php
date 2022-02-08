<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    function payments(){
        return $this->hasMany('App\Models\LoanPayment');
    }

    function user(){
        return $this->belongsTo('App\Models\User');
    }

    function logs(){
        return $this->hasMany('App\Models\LoanLog');
    }

    function notes(){
        return $this->hasMany('App\Models\LoanLog')->where('action', 'note');
    }
}
