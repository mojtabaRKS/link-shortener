<?php

namespace App\Models;

use Core\Database\Model;

class Link extends Model
{
    protected $table = 'links';

    protected $attributes = [
        'url',
        'domain',
        self::CREATED_AT,
        self::UPDATED_AT,
    ];
}
