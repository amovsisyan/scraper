<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'results';

    protected $fillable = [
        'title', 'description', 'main_image', 'date_upload', 'url', 'updated_at', 'created_at'
    ];
}
