<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailPoChange extends Eloquent {

    protected $table = 'mailpoc';
    
    protected $primaryKey = null;
    
    public $incrementing = false;

    public $timestamps = false;

    public function mailpost(){
        return $this->belongsTo('mailpocst');
    }

}