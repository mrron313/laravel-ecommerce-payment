<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountKit extends Model
{
    /**
     * @var string
     */
    protected $table = 'fb_account_kit';

    /**
     * @var array
     */
    protected $fillable = ['code', 'country_prefix', 'number', 'verified'];
}
