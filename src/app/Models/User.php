<?php

namespace App\Models;

use Core\Database\Model;

class User extends Model
{
    protected $table = 'users';

    protected $attributes = [
        'name',
        'email',
        'password',
        self::CREATED_AT,
        self::UPDATED_AT,
    ];
}
