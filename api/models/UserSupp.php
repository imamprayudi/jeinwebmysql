<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSupp extends Eloquent {

    protected $table = 'UserSupp';
    
    protected $primaryKey = null;
    
    public $incrementing = false;

    public $timestamps = false;

}