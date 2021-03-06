<?php

namespace App\Models;

use Core\Database\Model;

class Link extends Model
{
    protected $table = 'links';

    protected $attributes = [
        'original',
        'short',
        self::CREATED_AT,
        self::UPDATED_AT,
    ];
}
