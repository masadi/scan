<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absen extends Model
{
    use HasFactory;
    protected $table = 'absen';
	protected $guarded = [];
	public $appends = ['jam_masuk'];
	public function absen_masuk(){
		return $this->hasOne(Absen_masuk::class, 'absen_id', 'id');
	}
	public function absen_pulang(){
		return $this->hasOne(Absen_pulang::class, 'absen_id', 'id');
	}
	public function izin(){
		return $this->hasOne(Izin::class, 'absen_id', 'id');
	}
	public function ptk(){
		return $this->hasOne(Ptk::class, 'ptk_id', 'ptk_id');
	}
	public function pd(){
		return $this->hasOne(Peserta_didik::class, 'peserta_didik_id', 'peserta_didik_id');
	}
	public function peserta_didik(){
		return $this->hasOne(Peserta_didik::class, 'peserta_didik_id', 'peserta_didik_id');
	}
	public function getUpdatedAtAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['updated_at'])->format('d/m/Y H:i:s');
    }
	public function getJamMasukAttribute()
    {
		return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['updated_at'])->format('H:i:s');
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'])->format('d/m/Y H:i:s');
    }
}
