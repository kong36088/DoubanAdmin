<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'group';
    protected $primaryKey = 'url';
    protected $keyType = 'string';
    const CREATED_AT = 'create_time';
}
