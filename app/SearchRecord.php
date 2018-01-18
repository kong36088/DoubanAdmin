<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchRecord extends Model
{
    protected $table = 'search_record';
    protected $primaryKey = 'search';
    protected $keyType = 'string';
    const CREATED_AT = 'create_time';
}
