<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jam_ptk extends Model
{
    use HasFactory;
    protected $table = 'jam_ptk';
	protected $guarded = [];
    public function Ptk()
    {
        return $this->hasOne(Ptk::class, 'ptk_id', 'ptk_id')->select('ptk_id', 'nama');
    }
    public function jam()
    {
        return $this->belongsTo(Jam::class);
    }
    public function hari(){
        return $this->hasOneThrough(
            Jam_hari::class,
            Jam::class,
            'id', // Foreign key on the cars table...
            'jam_id', // Foreign key on the owners table...
            'jam_id', // Local key on the mechanics table...
            'id' // Local key on the cars table...
        );
        return $this->hasOneThrough(Jam_hari::class, Jam::class);
    }
}
