<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Peserta_didik extends Model
{
    use HasFactory;
    public $incrementing = false;
    public $keyType = 'string';
	protected $table = 'peserta_didik';
	protected $primaryKey = 'peserta_didik_id';
	protected $guarded = [];
    public function user()
    {
        return $this->hasOne(User::class, 'peserta_didik_id', 'peserta_didik_id');
    }
    public function sekolah()
    {
        return $this->hasOne(Sekolah::class, 'sekolah_id', 'sekolah_id');
    }
    public function alpa()
    {
        return $this->hasMany(Absen::class, 'peserta_didik_id', 'peserta_didik_id')->doesntHave('absen_masuk');
    }
    public function absen()
    {
        return $this->hasMany(Absen::class, 'peserta_didik_id', 'peserta_didik_id');
    }
    public function absen_masuk()
    {
        return $this->hasManyThrough(
            Absen_masuk::class,
            Absen::class,
            'peserta_didik_id', // Foreign key on the absen table...
            'absen_id', // Foreign key on the Absen_masuk table...
            'peserta_didik_id', // Local key on the projects table...
            'id' // Local key on the absen table...
        );
    }
    public function absen_pulang()
    {
        return $this->hasManyThrough(
            Absen_pulang::class,
            Absen::class,
            'peserta_didik_id', // Foreign key on the absen table...
            'absen_id', // Foreign key on the Absen_masuk table...
            'peserta_didik_id', // Local key on the projects table...
            'id' // Local key on the absen table...
        );
    }
    public function anggota_rombel()
    {
        return $this->hasOne(Anggota_rombel::class, 'peserta_didik_id', 'peserta_didik_id');
    }
    public function kelas(){
        return $this->hasOneThrough(
            Rombongan_belajar::class,
            Anggota_rombel::class,
            'peserta_didik_id', // Foreign key on the cars table...
            'rombongan_belajar_id', // Foreign key on the owners table...
            'peserta_didik_id', // Local key on the mechanics table...
            'rombongan_belajar_id' // Local key on the cars table...
        );
    }
    public function jam(){
        return $this->hasOneThrough(
            Jam::class,
            Jam_pd::class,
            'peserta_didik_id', // Foreign key on the absen table...
            'id', // Foreign key on the Absen_masuk table...
            'peserta_didik_id', // Local key on the projects table...
            'jam_id' // Local key on the absen table...
        );
    }
    public function all_jam(){
        return $this->hasManyThrough(
            Jam::class,
            Jam_pd::class,
            'peserta_didik_id', // Foreign key on the absen table...
            'id', // Foreign key on the Absen_masuk table...
            'peserta_didik_id', // Local key on the projects table...
            'jam_id' // Local key on the absen table...
        );
    }
    public function izin()
    {
        return $this->hasManyThrough(
            Izin::class,
            Absen::class,
            'peserta_didik_id', // Foreign key on the absen table...
            'absen_id', // Foreign key on the Absen_masuk table...
            'peserta_didik_id', // Local key on the projects table...
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
            'peserta_didik_id', // Foreign key on the absen table...
            'absen_id', // Foreign key on the Absen_masuk table...
            'peserta_didik_id', // Local key on the projects table...
            'id' // Local key on the absen table...
        );
    }
    public function getNamaAttribute()
	{
		return strtoupper($this->attributes['nama']);
	}
    public function getTetalaAttribute()
	{
        return strtoupper($this->attributes['tempat_lahir']).', '.Carbon::parse($this->attributes['tanggal_lahir'])->translatedFormat('d F Y');
	}
    public function pelanggaran()
    {
        //return $this->hasMany(Pelanggaran::class, 'anggota_rombel_id', 'anggota_rombel_id');
        return $this->hasManyThrough(
            Pelanggaran::class,
            Anggota_rombel::class,
            'peserta_didik_id', // Foreign key on the cars table...
            'anggota_rombel_id', // Foreign key on the owners table...
            'peserta_didik_id', // Local key on the mechanics table...
            'anggota_rombel_id' // Local key on the cars table...
        );
    }
}
