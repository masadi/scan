<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Libur extends Model
{
    use HasFactory;
    protected $table = 'libur';
	protected $guarded = [];
    protected $appends = ['start', 'end'];
    /**
     * Get the kategori_libur that owns the Libur
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kategori_libur()
    {
        return $this->belongsTo(Kategori_libur::class, 'kategori_id', 'id');
    }
    public function getStartAttribute()
	{
		return Carbon::parse($this->attributes['mulai_tanggal'])->format('Y-m-d');
	}
    public function getEndAttribute()
	{
		return Carbon::parse($this->attributes['sampai_tanggal'])->format('Y-m-d');
	}
    public function libur_ptk()
    {
        return $this->hasMany(Libur_ptk::class, 'libur_id', 'id');
    }
    public function getMulaiAttribute()
	{
		return Carbon::parse($this->attributes['mulai_tanggal'])->format('d/m/Y');
	}
    public function getSampaiAttribute()
	{
		return Carbon::parse($this->attributes['sampai_tanggal'])->format('d/m/Y');
	}
}
