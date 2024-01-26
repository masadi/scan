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
        $siswa_masuk = Absen::with(['peserta_didik' => function($query){
            $query->with(['kelas' => function($query){
                $query->where('anggota_rombel.semester_id', $this->getSemester()->semester_id);
            }]);
        }])->where(function($query){
            $query->whereDate('created_at', Carbon::today());
            $query->has('peserta_didik');
        })->whereHas('absen_masuk', function($query){
            $query->where('terlambat', 0);
        })->orderBy('created_at', 'DESC')->limit(10)->get();
        $siswa_terlambat = Absen::with(['peserta_didik' => function($query){
            $query->with(['kelas' => function($query){
                $query->where('anggota_rombel.semester_id', $this->getSemester()->semester_id);
            }]);
        }])->where(function($query){
            $query->whereDate('created_at', Carbon::today());
            $query->has('peserta_didik');
        })->whereHas('absen_masuk', function($query){
            $query->where('terlambat', '>', 0);
        })->orderBy('created_at', 'DESC')->limit(10)->get();
        $this->collection_siswa_masuk = $siswa_masuk;
        $this->collection_siswa_terlambat = $siswa_terlambat;
        $this->collection_tidak_scan_masuk = Absen::where(function($query){
            $query->whereDate('created_at', Carbon::today());
            //$query->has('absen_pulang');
            $query->has('peserta_didik');
        })->doesntHave('absen_masuk')->orderBy('created_at', 'DESC')->limit(10)->get();
        $this->collection_pulang_cepat = Absen::where(function($query){
            $query->whereDate('created_at', Carbon::today());
            //$query->has('absen_pulang');
            $query->has('peserta_didik');
        })->whereHas('absen_pulang', function($query){
            $query->where('pulang_cepat', '>', 0);
        })->orderBy('created_at', 'DESC')->limit(10)->get();
        $this->jml_siswa = Anggota_rombel::where('semester_id', $this->getSemester()->semester_id)->count();
        $this->jml_terlambat = Absen::where(function($query){
            $query->whereDate('created_at', Carbon::today());
            //$query->has('absen_pulang');
            $query->has('peserta_didik');
        })->whereHas('absen_masuk', function($query){
            $query->where('terlambat', '>', 0);
        })->count();
        $this->jml_tidak_hadir = Absen::where(function($query){
            $query->whereDate('created_at', Carbon::today());
            //$query->has('absen_pulang');
            $query->has('peserta_didik');
        })->doesntHave('absen_masuk')->count();
        $this->jml_hadir = Absen::where(function($query){
            $query->whereDate('created_at', Carbon::today());
            //$query->has('absen_pulang');
            $query->has('peserta_didik');
        })->has('absen_masuk')->count();
        return view('livewire.display')->title('Monitoring Presensi Yayasan Ariya Metta');
    }
    private function getSemester(){
        return Semester::where('periode_aktif', 1)->first();
    }
    public function scanMasukSiswa($params){
        $this->reset(['collection_siswa_terlambat']);
        if(count($this->collection_siswa_masuk) == 10){
            if ($this->collection_siswa_masuk instanceof \Illuminate\Database\Eloquent\Collection) {
                $this->collection_siswa_masuk->pop();
            }
        }
        $collection = collect($this->collection_siswa_masuk);
        $merged = $collection->merge([$params]);
        $sorted = $merged->sortByDesc('updated_at');
        $this->collection_siswa_masuk = $sorted->values()->all();
        $this->collection_siswa_terlambat = $this->collection_siswa_terlambat();
    }
    public function scanPulangSiswa($params){
        $this->reset(['collection_pulang_cepat']);
        if(count($this->collection_tidak_scan_masuk) == 10){
            $this->collection_tidak_scan_masuk->pop();
        }
        $collection = collect($this->collection_tidak_scan_masuk);
        $merged = $collection->merge([$params]);
        $sorted = $merged->sortByDesc('updated_at');
        $this->collection_tidak_scan_masuk = $sorted->values()->all();
        $this->collection_pulang_cepat = $this->collection_pulang_cepat();
    }
    private function collection_siswa_terlambat(){
        $collection_siswa_terlambat = Absen::where(function($query){
            $query->whereDate('created_at', Carbon::today());
            //$query->has('absen_pulang');
            $query->has('peserta_didik');
        })->whereHas('absen_masuk', function($query){
            $query->where('terlambat', '>', 0);
        })->orderBy('created_at', 'DESC')->limit(10)->get();
        /*$collection_siswa_terlambat = Rombongan_belajar::whereHas('anggota_rombel', function($query){
            $query->whereHas('peserta_didik', function($query){
                $query->whereHas('absen_masuk', function($query){
                    $query->whereDate('absen_masuk.created_at', Carbon::today());
                });
            });
        })->withCount([
            'anggota_rombel as hadir' => function($query){
                $query->whereHas('peserta_didik', function($query){
                    $query->whereHas('absen_masuk', function($query){
                        $query->whereDate('absen_masuk.created_at', Carbon::today());
                    });
                });
            },
            'anggota_rombel as belum' => function($query){
                $query->whereHas('peserta_didik', function($query){
                    $query->whereDoesntHave('absen_masuk', function($query){
                        $query->whereDate('absen_masuk.created_at', Carbon::today());
                    });
                });
            }
        ])->limit(10)->get();*/
        return $collection_siswa_terlambat;
    }
    private function collection_pulang_cepat(){
        $collection_pulang_cepat = Rombongan_belajar::whereHas('anggota_rombel', function($query){
            $query->whereHas('peserta_didik', function($query){
                $query->whereHas('absen_pulang', function($query){
                    $query->whereDate('absen_pulang.created_at', Carbon::today());
                });
            });
        })->withCount([
            'anggota_rombel as pulang' => function($query){
                $query->whereHas('peserta_didik', function($query){
                    $query->whereHas('absen_pulang', function($query){
                        $query->whereDate('absen_pulang.created_at', Carbon::today());
                    });
                });
            },
            'anggota_rombel as belum' => function($query){
                $query->whereHas('peserta_didik', function($query){
                    $query->whereDoesntHave('absen_pulang', function($query){
                        $query->whereDate('absen_pulang.created_at', Carbon::today());
                    });
                });
            }
        ])->limit(10)->get();
        return $collection_pulang_cepat;
    }
    public function showDiv(){
        $this->showDiv++;
        if($this->showDiv == 4){
            $this->showDiv = 1;
        }
    }
}
