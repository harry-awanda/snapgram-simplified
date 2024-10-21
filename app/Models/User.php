<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
  // use Notifiable;
  protected $primaryKey = 'userID';
  protected $fillable = ['username', 'email', 'password', 'namaLengkap', 'alamat'];
  protected $hidden = ['password', 'remember_token'];
}