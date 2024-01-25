<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Rombongan_belajar;
use App\Models\Anggota_rombel;
use App\Models\Semester;
use App\Models\Absen;
use Carbon\Carbon;

class Display extends Component
{
    public $jml_siswa = 0;
    public $jml_terlambat = 0;
    public $jml_tidak_hadir = 0;
    public $jml_hadir = 0;
    public $collection_siswa_masuk = [];
    public $collection_siswa_terlambat = [];
    public $collection_tidak_scan_masuk = [];
    public $collection_pulang_cepat = [];
    public $showDiv = 1;
    public function render()
    {
        return view('livewire.display')->title('Monitoring Presensi Yayasan Ariya Metta');
    }
}
