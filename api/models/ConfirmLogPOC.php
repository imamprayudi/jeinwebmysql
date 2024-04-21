<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class ConfirmLogPOC extends Eloquent {

    protected $table = 'confirm_log_poc';
    
    protected $primaryKey = 'id';
    
    public $incrementing = true;

    public $timestamps = false;


}