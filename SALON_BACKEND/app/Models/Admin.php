<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Admin extends Model
{
    use HasApiTokens, Notifiable;

    protected $table = 'admin';
    protected $primaryKey = 'id_admin';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = ['nama', 'email', 'password', 'role'];

    // Sanctum token relationship
    public function tokens()
    {
        return $this->morphMany('Laravel\Sanctum\PersonalAccessToken', 'tokenable', 'tokenable_type', 'tokenable_id', $this->primaryKey);
    }
}
