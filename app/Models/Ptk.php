<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ptk extends Model
{
    use HasFactory;
    public $incrementing = false;
    public $keyType = 'string';
	protected $table = 'ptk';
	protected $primaryKey = 'ptk_id';
	protected $guarded = [];
    
    public function rombongan_belajar()
    {
        return $this->hasOne(Rombongan_belajar::class, 'ptk_id', 'ptk_id')->where('semester_id', session('semester_aktif'));
    }
    public function user()
    {
        return $this->hasOne(User::class, 'ptk_id', 'ptk_id');
    }
    public function sekolah()
    {
        return $this->hasOne(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }
    public function alpa()
    {
        return $this->hasMany(Absen::class, 'ptk_id', 'ptk_id')->doesntHave('absen_masuk');
    }
    public function absen()
    {
        return $this->hasMany(Absen::class, 'ptk_id', 'ptk_id');
    }
    public function absen_masuk()
    {
        return $this->hasManyThrough(
            Absen_masuk::class,
            Absen::class,
            'ptk_id', // Foreign key on the absen table...
            'absen_id', // Foreign key on the Absen_masuk table...
            'ptk_id', // Local key on the projects table...
            'id' // Local key on the absen table...
        );
    }
    public function absen_pulang()
    {
        return $this->hasManyThrough(
            Absen_pulang::class,
            Absen::class,
            'ptk_id', // Foreign key on the absen table...
            'absen_id', // Foreign key on the Absen_masuk table...
            'ptk_id', // Local key on the projects table...
            'id' // Local key on the absen table...
        );
    }
    public function jam(){
        return $this->hasOneThrough(
            Jam::class,
            Jam_ptk::class,
            'ptk_id', // Foreign key on the absen table...
            'id', // Foreign key on the Absen_masuk table...
            'ptk_id', // Local key on the projects table...
            'jam_id' // Local key on the absen table...
        );
    }
    public function all_jam(){
        return $this->hasManyThrough(
            Jam::class,
            Jam_ptk::class,
            'ptk_id', // Foreign key on the absen table...
            'id', // Foreign key on the Absen_masuk table...
            'ptk_id', // Local key on the projects table...
            'jam_id' // Local key on the absen table...
        );
    }
    public function izin(){
        return $this->hasManyThrough(
            Izin::class,
            Absen::class,
            'ptk_id', // Foreign key on the absen table...
            'absen_id', // Foreign key on the Absen_masuk table...
            'ptk_id', // Local key on the projects table...
            'id' // Local key on the absen table...
        );
    }
    public function sakit()
    {
        return $this->izin()->where('izin.keterangan', 'sakit');
    }
    public function cuti()
    {
        return $this->izin()->where('izin.keterangan', 'cuti');
    }
    public function izin_harian()
    {
        return $this->hasOneThrough(
            Izin::class,
            Absen::class,
            'ptk_id', // Foreign key on the absen table...
            'absen_id', // Foreign key on the Absen_masuk table...
            'ptk_id', // Local key on the projects table...
            'id' // Local key on the absen table...
        );
    }
    /*public function izin()
    {
        return $this->hasMany(Izin::class, 'ptk_id', 'ptk_id')->where('keterangan', 'izin');
    }
    public function sakit()
    {
        return $this->hasMany(Izin::class, 'ptk_id', 'ptk_id')->where('keterangan', 'sakit');
    }
    public function all_izin()
    {
        return $this->hasMany(Izin::class, 'ptk_id', 'ptk_id');
    }
    */
}
