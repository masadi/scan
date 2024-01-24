<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota_rombel extends Model
{
    use HasFactory;
    public $incrementing = false;
    public $keyType = 'string';
	protected $table = 'anggota_rombel';
	protected $primaryKey = 'anggota_rombel_id';
	protected $guarded = [];
    
    public function peserta_didik()
    {
        return $this->hasOne(Peserta_didik::class, 'peserta_didik_id', 'peserta_didik_id');
    }
    
    public function rombongan_belajar()
    {
        return $this->hasOne(Rombongan_belajar::class, 'rombongan_belajar_id', 'rombongan_belajar_id');
    }
    public function pelanggaran()
    {
        return $this->hasMany(Pelanggaran::class, 'anggota_rombel_id', 'anggota_rombel_id');
    }
}
