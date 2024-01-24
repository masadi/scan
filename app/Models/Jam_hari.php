<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jam_hari extends Model
{
    use HasFactory;
    protected $table = 'jam_hari';
	protected $guarded = [];
    public function jam_pd(){
        return $this->hasManyThrough(
            Jam_pd::class,
            Jam::class,
            'id', // Foreign key on the absen table...
            'jam_id', // Foreign key on the Absen_masuk table...
            'jam_id', // Local key on the projects table...
            'id' // Local key on the absen table...
        );
    }
    public function jam_ptk(){
        return $this->hasManyThrough(
            Jam_ptk::class,
            Jam::class,
            'id', // Foreign key on the absen table...
            'jam_id', // Foreign key on the Absen_masuk table...
            'jam_id', // Local key on the projects table...
            'id' // Local key on the absen table...
        );
    }
}
