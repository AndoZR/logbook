<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';

    protected $fillable = [
        'nama',
        'tingkat',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
