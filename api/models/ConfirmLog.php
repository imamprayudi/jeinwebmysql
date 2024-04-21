<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class ConfirmLog extends Eloquent {

    protected $table = 'confirm_log';
    
    protected $primaryKey = 'id';
    
    public $incrementing = true;

    public $timestamps = false;


}