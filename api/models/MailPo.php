<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailPo extends Eloquent {

    protected $table = 'mailpo';
    
    protected $primaryKey = null;
    
    public $incrementing = false;

    public $timestamps = false;

    public function mailpost(){
        return $this->belongsTo('mailpost');
    }

}