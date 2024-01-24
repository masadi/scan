<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jam_pd extends Model
{
    use HasFactory;
    protected $table = 'jam_pd';
	protected $guarded = [];
    /**
     * Get the jam that owns the Jam_pd
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
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
    public function pd()
    {
        return $this->hasOne(Peserta_didik::class, 'peserta_didik_id', 'peserta_didik_id');
    }
}
