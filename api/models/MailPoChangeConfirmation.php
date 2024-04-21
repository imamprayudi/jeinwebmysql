<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class MailPoChangeConfirmation extends Eloquent {

    protected $table = 'mailpoc_confirmation';
    
    protected $primaryKey = 'id';
    
    public $incrementing = true;

    public $timestamps = false;


}