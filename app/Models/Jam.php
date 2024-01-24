<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Cviebrock\EloquentSluggable\Sluggable;

class Jam extends Model
{
    use HasFactory, Sluggable;
    protected $table = 'jam';
	protected $guarded = [];
    /*public function setNamaAttribute($value)
    {
        $this->attributes['nama'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }*/
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'nama'
            ]
        ];
    }
    /*
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'slug_or_title',
            ],
        ];
    }
    */
    public function sekolah()
    {
        return $this->hasOne(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }
    public function ptk(){
		return $this->hasMany(Jam_ptk::class, 'jam_id', 'id');
	}
    public function hari(){
		return $this->hasMany(Jam_hari::class, 'jam_id', 'id');
	}
    public function semester()
    {
        return $this->hasOne(Semester::class, 'semester_id', 'semester_id');
    }
    public function jam_ptk(){
		return $this->hasMany(Jam_ptk::class, 'jam_id', 'id');
	}
    public function jam_pd(){
		return $this->hasMany(Jam_pd::class, 'jam_id', 'id');
	}
    public function jam_hari(){
		return $this->hasMany(Jam_hari::class, 'jam_id', 'id');
	}
    public function data_ptk(){
        return $this->hasManyThrough(
            Ptk::class,
            Jam_ptk::class,
            'jam_id', // Foreign key on the environments table...
            'ptk_id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'ptk_id' // Local key on the environments table...
        );
    }
    public function data_pd(){
        return $this->hasManyThrough(
            Peserta_didik::class,
            Jam_pd::class,
            'jam_id', // Foreign key on the environments table...
            'peserta_didik_id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'peserta_didik_id' // Local key on the environments table...
        );
    }
}
