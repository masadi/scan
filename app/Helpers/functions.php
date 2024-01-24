<?php
//functions.php
use App\Models\Absen;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

function check_scan_masuk_start($jam){
    if(detik_ini() >= strtotime($jam)){
        return true;
    }
    return false;
}
function detik_ini(){
    return strtotime(Carbon::now()->format('H:i:s'));
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