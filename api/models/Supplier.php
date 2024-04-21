<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Eloquent {

    protected $table = 'Supplier';
    
    protected $primaryKey = 'SuppCode';
    
    public $incrementing = false;

    public $timestamps = false;

}