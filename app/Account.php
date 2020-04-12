<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public function loans()
    {
        return $this->hasMany('App\Loan');
    }
}
