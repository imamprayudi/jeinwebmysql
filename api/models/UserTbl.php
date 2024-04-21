<?php

use Illuminate\Database\Eloquent\Model as Eloquent;

class UserTbl extends Eloquent {

    protected $table = 'UserTbl';
    
    protected $primaryKey = 'UserId';
    
    public $incrementing = false;

    public $timestamps = false;

}