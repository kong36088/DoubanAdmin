<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupMark extends Model
{
    protected $table = 'group_mark';
    protected $primaryKey = 'url,user_id,type';
    protected $keyType = 'string';
    public $timestamps = false;
}
