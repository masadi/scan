<?php
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Peserta_didik;
use App\Models\Ptk;
use App\Models\Jam_ptk;
use App\Models\Jam_pd;
use App\Models\Jam_hari;
use App\Models\Absen;
use App\Models\Semester;
use App\Models\Libur;
use App\Models\Izin;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\File;

function jam($menit){
    return $menit / 60;//intdiv($menit, 60).':'. ($menit % 60);
}
function jam_angka($menit){
    return intdiv($menit, 60).'.'. ($menit % 60);
}
function jam_aktif_pd($peserta_didik_id){
    $jam_pd = Jam_pd::with(['jam'])->where('peserta_didik_id', $peserta_didik_id)->first();
    $from = Carbon::now()->format('H:i:s');
    $to = Carbon::createFromFormat('H:i:s', $jam_pd->jam->jam_pulang);
    return $to->diffInHours($from);
}
function hari($menit){
    $hid = 24; // Hours in a day - could be 24, 8, etc
    $days = ($menit) ? jam_angka($menit)/$hid : 0;//round(jam_angka($menit)/$hid);
    return $days;
}
function tidak_hadir_pd($peserta_didik_id, $start, $end){
    $period = CarbonPeriod::between($start, $end)->addFilter(function ($date) {
        return $date->isMonday() || $date->isTuesday() || $date->isWednesday() || $date->isThursday() || $date->isFriday();
    });
    $libur = Libur::select('mulai_tanggal')->where(function($query) use ($period, $start, $end){
        $query->whereDate('mulai_tanggal', '>=', $start);
        $query->whereDate('sampai_tanggal', '<=', $end);
        $query->where(function($q) use($period) {
            collect($period->map(function (Carbon $date) use ($q){
                $q->whereDay('mulai_tanggal', '=', $date->format('d'), 'or');
                $q->whereDay('sampai_tanggal', '=', $date->format('d'), 'or');
            }));
        });
    })->get();
    $hari_libur = NULL;
    foreach ($libur as $value) {
        $hari_libur[] = date('Y-m-d', strtotime($value->mulai_tanggal));
    }
    $tidak_hadir = Absen::where(function($query) use ($peserta_didik_id, $start, $end, $hari_libur){
        $query->doesntHave('absen_masuk');
        $query->whereDoesntHave('izin', function($query){
            $query->where('jenis', '<>', 'Sekolah');
        });
        $query->where('peserta_didik_id', $peserta_didik_id);
        $query->whereDate('created_at', '>=', $start);
        $query->whereDate('created_at', '<=', $end);
        if($hari_libur){
            $query->whereNotIn('created_at', $hari_libur);
        }
    })->count();
    return $tidak_hadir;//jml_hari_aktif($start, $end) - $jml_hadir;
}
function total_hadir_ptk($ptk_id, $start, $end){
    return Absen::where(function($query) use ($ptk_id, $start, $end){
        $query->whereHas('absen_masuk');
        $query->where('ptk_id', $ptk_id);
        $query->whereDate('created_at', '>=', $start);
        $query->whereDate('created_at', '<=', $end);
        $query->orwhereHas('izin', function($query){
            $query->where('jenis', 'Sekolah');
        });
        $query->where('ptk_id', $ptk_id);
        $query->whereDate('created_at', '>=', $start);
        $query->whereDate('created_at', '<=', $end);
    })->count();
}
function total_hadir_pd($peserta_didik_id, $start, $end){
    return Absen::where(function($query) use ($peserta_didik_id, $start, $end){
        $query->whereHas('absen_masuk');
        $query->where('peserta_didik_id', $peserta_didik_id);
        $query->whereDate('created_at', '>=', $start);
        $query->whereDate('created_at', '<=', $end);
        $query->orwhereHas('izin', function($query){
            $query->where('jenis', 'Sekolah');
        });
        $query->where('ptk_id', $ptk_id);
        $query->whereDate('created_at', '>=', $start);
        $query->whereDate('created_at', '<=', $end);
    })->count();
}
function tidak_hadir_ptk($ptk_id, $start, $end, $total = TRUE){
    $period = CarbonPeriod::between($start, $end)->addFilter(function ($date) {
        return $date->isMonday() || $date->isTuesday() || $date->isWednesday() || $date->isThursday() || $date->isFriday();
    });
    $libur = Libur::where(function($query) use ($period, $start, $end){
        $query->whereDate('mulai_tanggal', '>=', $start);
        $query->whereDate('sampai_tanggal', '<=', $end);
        $query->where(function($q) use($period) {
            collect($period->map(function (Carbon $date) use ($q){
                $q->whereDay('mulai_tanggal', '=', $date->format('d'), 'or');
                $q->whereDay('sampai_tanggal', '=', $date->format('d'), 'or');
            }));
        });
    })->get();
    $hari_libur = NULL;
    foreach ($libur as $value) {
        $hari_libur[] = date('Y-m-d', strtotime($value->mulai_tanggal));
    }
    $tidak_hadir = Absen::where(function($query) use ($ptk_id, $start, $end, $hari_libur, $total){
        $query->doesntHave('absen_masuk');
        $query->whereDoesntHave('izin', function($query) use ($total){
            if($total){
                $query->where('jenis', 'Sekolah');
            }
        });
        $query->where('ptk_id', $ptk_id);
        $query->whereDate('created_at', '>=', $start);
        $query->whereDate('created_at', '<=', $end);
        if($hari_libur){
            $query->whereNotIn('created_at', $hari_libur);
        }
    })->count();
    return $tidak_hadir;
}
function jml_hari_aktif($start = NULL, $end = NULL){
    $semester = Semester::find(session('semester_aktif'));
    if($end){
        $from = Carbon::createFromFormat('Y-m-d', $start);
        $to = Carbon::createFromFormat('Y-m-d', $end);
    } else {
        $from = Carbon::createFromFormat('Y-m-d', $semester->tanggal_mulai);
        $to = Carbon::createFromFormat('Y-m-d', $semester->tanggal_selesai);
    }
    $libur = Libur::where(function($query) use ($from, $to){
        $query->whereDate('created_at', '>=', $from);
        $query->whereDate('created_at', '<=', $to);
    })->count();
    $jml_hari_aktif = $to->diffInDays($from) - $libur;
    return $jml_hari_aktif;
}
function jml_hari_aktif_ptk($sekolah_id, $ptk_id, $start = NULL, $end = NULL){
    if($end){
        $from = Carbon::createFromFormat('Y-m-d', $start);
        $to = Carbon::createFromFormat('Y-m-d', $end);
    } else {
        $semester = Semester::find(session('semester_aktif'));
        $from = Carbon::createFromFormat('Y-m-d', $semester->tanggal_mulai);
        $to = Carbon::createFromFormat('Y-m-d', $semester->tanggal_selesai);
    }
    $jam_ptk = Jam_ptk::whereHas('jam', function($query){
        $query->where('semester_id', session('semester_aktif'));
    })->where('ptk_id', $ptk_id)->get();
    $nama_hari = [];
    if($jam_ptk){
        foreach($jam_ptk as $jjm){
            foreach($jjm->jam->hari as $hari){
                $nama_hari[$hari->nama] = $hari->nama;
            }
        }
        $period = CarbonPeriod::between($start, $end)
        ->addFilter(function ($date) use ($nama_hari){
            return in_array($date->translatedFormat('l'), $nama_hari);
        });
        $libur = jml_hari_libur($sekolah_id, $start, $end, $period, $nama_hari);
        $jml_hari_aktif = $period->count() - $libur;
    } else {
        $jml_hari_aktif = 0;
        $libur = 0;
    }
    return [
        'jml_hari_aktif' => $jml_hari_aktif,
        'libur' => $libur,
    ];
}
function jml_hari_aktif_pd($sekolah_id, $peserta_didik_id, $start = NULL, $end = NULL){
    if($end){
        $from = Carbon::createFromFormat('Y-m-d', $start);
        $to = Carbon::createFromFormat('Y-m-d', $end);
    } else {
        $semester = Semester::find(session('semester_aktif'));
        $from = Carbon::createFromFormat('Y-m-d', $semester->tanggal_mulai);
        $to = Carbon::createFromFormat('Y-m-d', $semester->tanggal_selesai);
    }
    $jam_pd = Jam_pd::whereHas('jam', function($query){
        $query->where('semester_id', session('semester_aktif'));
    })->where('peserta_didik_id', $peserta_didik_id)->first();
    if($jam_pd){
        $nama_hari = [];
        foreach($jam_pd->jam->hari as $hari){
            $nama_hari[] = $hari->nama;
        }
        $period = CarbonPeriod::between($start, $end)
        ->addFilter(function ($date) use ($nama_hari){
            return in_array($date->translatedFormat('l'), $nama_hari);
        });
        $libur = jml_hari_libur($sekolah_id, $start, $end, $period, $nama_hari);
        $jml_hari_aktif = $period->count() - $libur;
    } else {
        $libur = 0;
        $jml_hari_aktif = 0;
    }
    return [
        'jml_hari_aktif' => $jml_hari_aktif,
        'libur' => $libur,
    ];
}
function jml_hari_libur($sekolah_id, $from, $to, $period, $nama_hari){
    $libur = Libur::where(function($query) use ($from, $to, $period, $sekolah_id){
        $query->whereHas('kategori_libur', function($query) use ($sekolah_id){
            $query->where('sekolah_id', $sekolah_id);
            $query->orWhereNull('sekolah_id');
        });
        $query->whereDate('mulai_tanggal', '>=', $from);
        $query->whereDate('sampai_tanggal', '<=', $to);
        $query->where(function($q) use($period) {
            collect($period->map(function (Carbon $date) use ($q){
                $q->whereDay('mulai_tanggal', '=', $date->format('d'), 'or');
                $q->whereDay('sampai_tanggal', '=', $date->format('d'), 'or');
            }));
        });
    })->get();
    $jml = 0;
    foreach($libur as $li){
        $jml += CarbonPeriod::between($li->mulai_tanggal, $li->mulai_tanggal)
        ->addFilter(function ($date) use ($nama_hari){
            return in_array($date->translatedFormat('l'), $nama_hari);
        })->count();
    }
    return $jml;
}
function izin_ptk($data, $jenis_izin, $start, $end){
    return $data->izin()->where(function($query) use ($jenis_izin, $start, $end){
        $query->where('izin.keterangan', $jenis_izin);
        $query->whereHas('absen', function($query) use ($start, $end){
            $query->whereDate('created_at', '>=', $start);
            $query->whereDate('created_at', '<=', $end);
        });
    })->count();
}
function izin_pd($data, $jenis_izin, $start, $end){
    return $data->izin()->where(function($query) use ($jenis_izin, $start, $end){
        $query->where('keterangan', $jenis_izin);
        $query->whereHas('absen', function($query) use ($start, $end){
            $query->whereDate('created_at', '>=', $start);
            $query->whereDate('created_at', '<=', $end);
        });
    })->count();
}
function generate_qrcode($id){
    $folder = storage_path('app/public/qrcodes');
    if (!File::isDirectory($folder)) {
        //MAKA FOLDER TERSEBUT AKAN DIBUAT
        File::makeDirectory($folder, 0777, true, true);
    }
    if(!File::exists(storage_path('app/public/qrcodes/'.$id.'.svg'))){
        QrCode::size(200)->generate($id, storage_path('app/public/qrcodes/'.$id.'.svg'));
    }
}
function detik_ini(){
    return strtotime(Carbon::now()->format('H:i:s'));
}
function check_scan_masuk_start($jam){
    if(detik_ini() >= strtotime($jam)){
        return true;
    }
    return false;
}
function check_scan_masuk_end($jam){
    if(detik_ini() > strtotime($jam)){
        return false;
    }
    return true;
}
function check_scan_pulang_start($jam){
    if(detik_ini() >= strtotime($jam)){
        return true;
    }
    return false;
}
function check_scan_pulang_end($jam){
    if(detik_ini() > strtotime($jam)){
        return false;
    }
    return true;
}
function insert_absen($peserta_didik_id, $semester_id){
    $absen = Absen::where(function($query) use ($peserta_didik_id, $semester_id){
        $query->whereDate('created_at', Carbon::today());
        $query->where('peserta_didik_id', $peserta_didik_id);
        $query->where('semester_id', $semester_id);
    })->first();
    if($absen){
        $absen->updated_at = now();
        $absen->save();
    } else {
        $absen = Absen::create([
            'peserta_didik_id' => $peserta_didik_id,
            'semester_id' => $semester_id,
        ]);
    }
    return $absen;
}
function insert_absen_ptk($ptk_id, $semester_id){
    $absen = Absen::where(function($query) use ($ptk_id, $semester_id){
        $query->whereDate('created_at', Carbon::today());
        $query->where('ptk_id', $ptk_id);
        $query->where('semester_id', $semester_id);
    })->first();
    if($absen){
        $absen->updated_at = now();
        $absen->save();
    } else {
        $absen = Absen::create([
            'ptk_id' => $ptk_id,
            'semester_id' => $semester_id,
        ]);
    }
    return $absen;
}
function cek_libur($tanggal){
    return Libur::where(function($query) use ($tanggal){
        $query->whereDate('mulai_tanggal', '>=', $tanggal);
        $query->whereDate('sampai_tanggal', '<=', $tanggal);
    })->first();
}
function clean($string){
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}
