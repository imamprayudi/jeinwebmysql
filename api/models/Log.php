<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class Log extends Eloquent {

    protected $table = 'LOG1';
    
    protected $primaryKey = null;
    
    public $incrementing = false;

    public $timestamps = false;

}