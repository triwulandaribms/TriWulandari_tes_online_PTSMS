<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use Notifiable, SoftDeletes;

    protected $table = 'tbl_user';

    protected $primaryKey = 'id';


    protected $hidden = ['password'];

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = [
        'username',
        'password',
        'role',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at',
    ];

    protected $attributes = [
        'role' => 'user',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
