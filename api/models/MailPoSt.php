<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class MailPoSt extends Eloquent {

    protected $table = 'mailpost';
    
    protected $primaryKey = null;
    
    public $incrementing = false;

    public $timestamps = false;

}