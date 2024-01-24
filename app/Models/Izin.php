<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Izin extends Model
{
    use HasFactory;
    protected $table = 'izin';
	protected $guarded = [];
    /*public function getTanggalStartAttribute()
    {
        return Carbon::createFromFormat('Y-m-d', $this->attributes['tanggal_start'])->format('d/m/Y');
    }
    public function getTanggalEndAttribute()
    {
        return Carbon::createFromFormat('Y-m-d', $this->attributes['tanggal_end'])->format('d/m/Y');
    }*/
    public function ptk(){
		return $this->hasOne(Ptk::class, 'ptk_id', 'ptk_id');
	}
	public function pd(){
		return $this->hasOne(Peserta_didik::class, 'peserta_didik_id', 'peserta_didik_id');
	}
    public function absen()
    {
        return $this->belongsTo(Absen::class);
    }
}
