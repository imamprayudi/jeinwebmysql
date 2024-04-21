<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class MailPoConfirmation extends Eloquent {

    protected $table = 'mailpo_confirmation';
    
    protected $primaryKey = 'id';
    
    public $incrementing = true;

    public $timestamps = false;


}