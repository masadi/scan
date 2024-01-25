<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Peserta_didik;
use App\Models\Ptk;
use App\Models\Absen;
use App\Models\Absen_masuk;
use App\Models\Absen_pulang;
use App\Models\Semester;
use App\Models\Jam_pd;
use App\Models\Jam_ptk;
use App\Models\Izin;
use App\Models\Libur;
use Carbon\Carbon;
use Pusher\Pusher;
use App\Events\StatusLiked;

class Counter extends Component
{
    public $isDisabled = 0;
    public $form_id = '';
    public $peserta_didik_id;
    public $ptk_id;
    public function render()
    {
        return view('livewire.counter')->title('Presensi Yayasan Ariya Metta');
    }
    public function updatedFormId()
    {
        $this->isDisabled = 1;
        $id = Str::isUuid($this->form_id);
        if($id){
            $peserta_didik = Peserta_didik::find($this->form_id);
            if($peserta_didik){
                if($this->check_libur($peserta_didik->sekolah_id)){
                    $this->toastr('error', 'Absen Gagal', 'Hari ini '. Carbon::now()->translatedFormat('d F Y').', Libur!!!', 'Dong.mp3');
                } else {
                    $this->peserta_didik_id = $this->form_id;
                    $this->proses_absen_pd();
                }
            } else {
                $ptk = Ptk::find($this->form_id);
                if($ptk){
                    if($this->check_libur($ptk->sekolah_id)){
                        $this->toastr('error', 'Absen Gagal', 'Hari ini '. Carbon::now()->translatedFormat('d F Y').', Libur!!!', 'Dong.mp3');
                    } else {
                        $this->ptk_id = $this->form_id;
                        $this->proses_absen_ptk();
                    }
                } else {
                    $this->toastr('error', 'Absen Gagal', 'Data Peserta Didik/PTK tidak ditemukan!', 'Dong.mp3');
                }
            }
            
        } else {
            $this->toastr('error', 'Absen Gagal', 'Data Peserta Didik/PTK tidak ditemukan!', 'Dong.mp3');
        }
        $this->reset(['form_id', 'isDisabled']);
    }
    public function check_libur($sekolah_id){
        $libur = Libur::where(function($query) use ($sekolah_id){
            $query->whereHas('kategori_libur', function($query) use ($sekolah_id){
                $query->where('sekolah_id', $sekolah_id);
            });
            $query->where('mulai_tanggal', '<=', Carbon::now()->format('Y-m-d'));
            $query->where('sampai_tanggal', '>=', Carbon::now()->format('Y-m-d'));
            $query->orWhereHas('kategori_libur', function($query){
                $query->whereNull('sekolah_id');
            });
            $query->where('mulai_tanggal', '<=', Carbon::now()->format('Y-m-d'));
            $query->where('sampai_tanggal', '>=', Carbon::now()->format('Y-m-d'));
        })->first();
        return $libur;
    }
    private function pusher(){
        $options = array(
            'cluster' => 'ap1',
            'encrypted' => true
        );
        $pusher = new Pusher(
            'bc531acdb4578535bf7a',
            '9cec3f1e5c2a5cca6cbf',
            1442785, 
            $options
        );
        return $pusher;
    }
    public function notif_pusher($type, $title, $message, $mp3){
        $data = ['type' => $type,  'title' => $title, 'message' => $message, 'mp3' => $mp3];
        /*
            public function toastr($type, $title, $message, $mp3){
            $this->dispatchBrowserEvent('toastr', ['type' => $type,  'title' => $title, 'message' => $message, 'mp3' => $mp3]);
        }
        */
        $pusher = $this->pusher();
        $pusher->trigger('aksi-scan', 'App\\Events\\Notify', $data);
    }
    public function notif_masuk_guru($absen)
    {
        $pusher = $this->pusher();
        $pusher->trigger('guru-masuk', 'App\\Events\\Notify', $absen);
 
    }
    public function notif_pulang_guru($absen)
    {
        $pusher = $this->pusher();
        $pusher->trigger('guru-pulang', 'App\\Events\\Notify', $absen);
 
    }
    public function notif_masuk_siswa($absen)
    {
        broadcast(new StatusLiked($absen))->toOthers();
        $pusher = $this->pusher();
        $pusher->trigger('siswa-masuk', 'App\\Events\\Notify', $absen);
 
    }
    public function notif_pulang_siswa($absen)
    {
        $pusher = $this->pusher();
        $pusher->trigger('siswa-pulang', 'App\\Events\\Notify', $absen);
 
    }
    private function getSemester(){
        return Semester::where('periode_aktif', 1)->first();
    }
    public function jam_sekarang(){
        return Carbon::now();
    }
    public function toastr($type, $title, $message, $mp3){
        //$this->notif_pusher($type, $title, $message, $mp3);
        $this->dispatch('toastr', ['type' => $type,  'title' => $title, 'message' => $message, 'mp3' => $mp3]);
    }
    private function proses_absen_pd(){
        $jam_pd = Jam_pd::with(['jam', 'pd' => function($query){
            $query->with([
                'izin_harian' => function($query){
                    $query->where('izin.created_at', Carbon::now()->format('Y-m-d'));
                },
                'kelas' => function($query){
                    $query->where('anggota_rombel.semester_id', $this->getSemester()->semester_id);
                }
            ]);
        }])->where(function($query){
            $query->where('peserta_didik_id', $this->peserta_didik_id);
            $query->whereHas('jam', function($query){
                $query->whereHas('semester', function($query){
                    $query->where('semester_id', $this->getSemester()->semester_id);
                    $query->where('tanggal_mulai', '<=', Carbon::now()->format('Y-m-d'));
                    $query->where('tanggal_selesai', '>=', Carbon::now()->format('Y-m-d'));
                });
            });
            $query->whereHas('hari', function($query){
                $query->where('jam_hari.nama', Carbon::now()->translatedFormat('l'));
            });
        })->first();
        if($jam_pd){
            if(!$jam_pd->pd->izin_harian){
                if(check_scan_masuk_start($jam_pd->jam->scan_masuk_start)){
                    if(check_scan_masuk_end($jam_pd->jam->scan_masuk_end)){
                        $absen = insert_absen($this->peserta_didik_id, $this->getSemester()->semester_id);
                        $absen_masuk = Absen_masuk::where('absen_id', $absen->id)->first();
                        if($absen_masuk){
                            $this->toastr('error', 'Absen Gagal', 'Absen masuk '.$jam_pd->pd->nama.' untuk hari ini sudah terekam', 'Dong.mp3');
                        } else {
                            $from = Carbon::createFromFormat('H:i:s', $jam_pd->jam->waktu_akhir_masuk);
                            $to = Carbon::createFromFormat('H:i:s', $this->jam_sekarang()->format('H:i:s'));
                            Absen_masuk::updateOrCreate(
                                [
                                    'absen_id' => $absen->id,
                                ],
                                [
                                    'terlambat' => (Str::contains($from->diffInMinutes($to, false), '-')) ? 0 : $from->diffInMinutes($to, false),
                                ]
                            );
                            $this->toastr('success', 'Absen Masuk hari ini berhasil disimpan', 'Selamat Datang '.$jam_pd->pd->nama, 'Ding.mp3');
                            $absen->peserta_didik = $jam_pd->pd;
                            $this->notif_masuk_siswa($absen);
                        }
                    } elseif(check_scan_pulang_start($jam_pd->jam->scan_pulang_start)){
                        if(check_scan_pulang_end($jam_pd->jam->scan_pulang_end)){
                            $this->proses_absen_pulang_pd($jam_pd);
                        } else {
                            $this->toastr('error', 'Absen Gagal', 'Waktu scan pulang telah berakhir', 'Dong.mp3');
                        }
                    } else {
                        if(check_scan_pulang_end($jam_pd->jam->scan_pulang_end)){
                            $this->proses_absen_pulang_pd($jam_pd);
                        } else {
                            $this->toastr('error', 'Absen Gagal', 'Waktu scan pulang telah berakhir', 'Dong.mp3');
                        }
                    }
                } else {
                    $this->toastr('error', 'Absen Gagal', 'Belum waktunya scan masuk!', 'Dong.mp3');
                }
            } else {
                $this->toastr('error', 'Absen Gagal', 'Terdeteksi Anda memiliki izin untuk hari ini!', 'Dong.mp3');
            }
        } else {
            $this->toastr('error', 'Absen Gagal', 'Setting JAM PD tidak ditemukan!', 'Dong.mp3');
        }
        $this->reset(['peserta_didik_id']);
    }
    private function proses_absen_pulang_pd($jam_pd){
        $absen = insert_absen($this->peserta_didik_id, $this->getSemester()->semester_id);
        $absen_masuk = Absen_masuk::where('absen_id', $absen->id)->first();
        if($absen_masuk){
            $absen_pulang = Absen_pulang::where('absen_id', $absen->id)->first();
            if($absen_pulang){
                $this->toastr('error', 'Absen Gagal', 'Absen pulang '.$jam_pd->pd->nama.' untuk hari ini sudah terekam', 'Dong.mp3');
            } else {
                $from = $this->jam_sekarang()->format('H:i:s');
                $to = Carbon::createFromFormat('H:i:s', $jam_pd->jam->scan_pulang_start);
                Absen_pulang::updateOrCreate(
                    [
                        'absen_id' => $absen->id,
                    ],
                    [
                        //'pulang_cepat' => (Str::contains($to->diffInMinutes($from, false), '-')) ? 0 : $to->diffInMinutes($from, false),
                        'pulang_cepat' => (Str::contains($to->diffInMinutes($from, false), '-')) ? $to->diffInMinutes($from) : 0,
                        //$to->diffInMinutes($from),
                    ]
                );
                $this->toastr('success', 'Absen Pulang berhasil disimpan', $jam_pd->pd->nama. ' telah scan pulang', 'Ding.mp3');
                $absen->peserta_didik = $jam_pd->pd;
                $this->notif_pulang_siswa($absen);
            }
        } else {
            $this->toastr('error', 'Absen Gagal', $jam_pd->pd->nama.' hari ini belum absen masuk', 'Dong.mp3');
        }
    }
    private function proses_absen_pulang_ptk($jam_ptk){
        $absen = insert_absen_ptk($this->ptk_id, $this->getSemester()->semester_id);
        $absen_masuk = Absen_masuk::where('absen_id', $absen->id)->first();
        if($absen_masuk){
            $absen_pulang = Absen_pulang::where('absen_id', $absen->id)->first();
            if($absen_pulang){
                $this->toastr('error', 'Absen Gagal', 'Absen pulang '.$jam_ptk->ptk->nama.' untuk hari ini sudah terekam', 'Dong.mp3');
            } else {
                $from = $this->jam_sekarang()->format('H:i:s');
                $to = Carbon::createFromFormat('H:i:s', $jam_ptk->jam->scan_pulang_start);
                $absen_pulang = Absen_pulang::updateOrCreate(
                    [
                        'absen_id' => $absen->id,
                    ],
                    [
                        //'pulang_cepat' => (Str::contains($to->diffInMinutes($from, false), '-')) ? 0 : $to->diffInMinutes($from, false),
                        'pulang_cepat' => (Str::contains($to->diffInMinutes($from, false), '-')) ? $to->diffInMinutes($from) : 0,
                        //'pulang_cepat' => $to->diffInMinutes($from),
                    ]
                );
                $this->toastr('success', 'Absen Pulang '.$jam_ptk->ptk->nama.' berhasil disimpan', 'Terima kasih', 'Ding.mp3');
                $absen->absen_pulang = $absen_pulang;
                $absen->ptk = $jam_ptk->ptk;
                $this->notif_pulang_guru($absen);
            }           
        } else {
            $this->toastr('error', 'Absen Gagal', $jam_ptk->ptk->nama.' hari ini belum absen masuk', 'Dong.mp3');
        }
    }
    private function proses_absen_ptk(){
        $jam_ptk = Jam_ptk::with(['jam', 'ptk.izin_harian' => function($query){
            $query->where('izin.created_at', Carbon::now()->format('Y-m-d'));
        }])->where(function($query){
            $query->where('ptk_id', $this->ptk_id);
            $query->whereHas('jam', function($query){
                $query->whereHas('semester', function($query){
                    $query->where('semester_id', $this->getSemester()->semester_id);
                    $query->where('tanggal_mulai', '<=', Carbon::now()->format('Y-m-d'));
                    $query->where('tanggal_selesai', '>=', Carbon::now()->format('Y-m-d'));
                });
            });
            $query->whereHas('hari', function($query){
                $query->where('jam_hari.nama', Carbon::now()->translatedFormat('l'));
            });
        })->first();
        if($jam_ptk){
            if($jam_ptk->ptk->izin_harian){
                $keterangan = $jam_ptk->ptk->izin_harian->keterangan;
                $alasan = ($jam_ptk->ptk->izin_harian->alasan) ? ' dengan alasan '.$jam_ptk->ptk->izin_harian->alasan : '';
                if($keterangan == 'izin'){
                    $notif = 'izin tidak masuk'.$alasan;
                } else {
                    $notif = 'izin '.ucwords($keterangan).''.$alasan;
                }
                $this->toastr('error', 'Absen Gagal', 'Hari ini Anda terdeteksi memiliki '.$notif, 'Dong.mp3');
            } else {
                if(check_scan_masuk_start($jam_ptk->jam->scan_masuk_start)){
                    if(check_scan_masuk_end($jam_ptk->jam->scan_masuk_end)){
                        $absen = insert_absen_ptk($this->ptk_id, $this->getSemester()->semester_id);
                        $absen_masuk = Absen_masuk::where('absen_id', $absen->id)->first();
                        if($absen_masuk){
                            $this->toastr('error', 'Absen Gagal', 'Absen masuk '.$jam_ptk->ptk->nama.' untuk hari ini sudah terekam', 'Dong.mp3');
                        } else {
                            $from = Carbon::createFromFormat('H:i:s', $jam_ptk->jam->waktu_akhir_masuk);
                            $to = Carbon::createFromFormat('H:i:s', $this->jam_sekarang()->format('H:i:s'));
                            Absen_masuk::updateOrCreate(
                                [
                                    'absen_id' => $absen->id,
                                ],
                                [
                                    'terlambat' => (Str::contains($from->diffInMinutes($to, false), '-')) ? 0 : $from->diffInMinutes($to, false),
                                ]
                            );
                            $this->toastr('success', 'Absen Masuk hari ini berhasil disimpan', 'Selamat Datang '.$jam_ptk->ptk->nama, 'Ding.mp3');
                            $absen->ptk = $jam_ptk->ptk;
                            $this->notif_masuk_guru($absen);
                        }
                    } elseif(check_scan_pulang_start($jam_ptk->jam->scan_pulang_start)){
                        if(check_scan_pulang_end($jam_ptk->jam->scan_pulang_end)){
                            $this->proses_absen_pulang_ptk($jam_ptk);
                        } else {
                            $this->toastr('error', 'Absen Gagal', 'Waktu scan pulang telah berakhir', 'Dong.mp3');
                        }
                    } else {
                        if(check_scan_pulang_end($jam_ptk->jam->scan_pulang_end)){
                            $this->proses_absen_pulang_ptk($jam_ptk);
                        } else {
                            $this->toastr('error', 'Absen Gagal', 'Waktu scan pulang telah berakhir', 'Dong.mp3');
                        }
                    }
                } else {
                    $this->toastr('error', 'Absen Gagal', 'Belum waktunya scan masuk!', 'Dong.mp3');
                }
            }
        } else {
            $this->toastr('error', 'Absen Gagal', 'Setting JAM PTK tidak ditemukan!', 'Dong.mp3');
        }
        $this->reset(['ptk_id']);
    }
}
