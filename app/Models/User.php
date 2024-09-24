<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
  // use Notifiable;
  protected $primaryKey = 'userID';
  protected $fillable = ['username', 'email', 'password', 'namaLengkap', 'alamat'];
  protected $hidden = ['password', 'remember_token'];
  // Relasi dengan album
  public function albums() {
    return $this->hasMany(Album::class, 'userID');
  }
  // Relasi dengan foto
  public function photos() {
    return $this->hasMany(Photo::class, 'userID');
  }
  // Relasi dengan komentar
  public function comments() {
    return $this->hasMany(Comment::class, 'userID');
  }
  // Relasi dengan like
  public function likes() {
    return $this->hasMany(Like::class, 'userID');
  }
}