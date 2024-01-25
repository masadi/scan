<div>
    <div class="container-fluid mt-2">
        <div class="row justify-content-lg-between mb-2">
            <div class="col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-0">SCAN MASUK</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">NO</th>
                                    <th class="text-center align-middle">NAMA</th>
                                    <th class="text-center align-middle">KELAS/UNIT</th>
                                    <th class="text-center align-middle">WAKTU SCAN MASUK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($collection_siswa_masuk as $item_masuk)
                                    <tr @if($loop->iteration == 1) class="table-success" @endif>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$item_masuk['peserta_didik']['nama']}}</td>
                                        <td>{{($item_masuk['peserta_didik']['kelas']) ? $item_masuk['peserta_didik']['kelas']['nama'] : '-'}}</td>
                                        <td class="text-center">{{$item_masuk->created_at->format('H:i:s')}}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="4">Tidak ada data untuk ditampilkan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-0">TERLAMBAT SCAN MASUK</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">NO</th>
                                    <th class="text-center align-middle">NAMA</th>
                                    <th class="text-center align-middle">KELAS/UNIT</th>
                                    <th class="text-center align-middle">WAKTU SCAN MASUK</th>
                                    <th class="text-center align-middle">WAKTU TERLAMBAT (MENIT)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($collection_siswa_terlambat as $terlambat)
                                <tr>
                                    <td class="text-center">{{$loop->iteration}}</td>
                                    <td>{{$terlambat->peserta_didik->nama}}</td>
                                    <td class="text-center">{{($terlambat->peserta_didik->kelas) ? $terlambat->peserta_didik->kelas->nama : '-'}}</td>
                                    <td class="text-center">{{$terlambat->created_at->format('H:i:s')}}</td>
                                    <td class="text-center">{{$terlambat->absen_masuk->terlambat}}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-center" colspan="5">Tidak ada data untuk ditampilkan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-lg-between mb-2">
            <div class="col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-0">TIDAK SCAN MASUK</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">NO</th>
                                    <th class="text-center">NAMA</th>
                                    <th class="text-center">KELAS/UNIT</th>
                                    <th class="text-center">KETERANGAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($collection_tidak_scan_masuk as $tidak_scan)
                                    <tr @if($loop->iteration == 1) class="table-success" @endif>
                                        <td class="text-center">{{$loop->iteration}}</td>
                                        <td>{{$tidak_scan->peserta_didik->nama}}</td>
                                        <td>{{($tidak_scan->peserta_didik->kelas) ? $tidak_scan->peserta_didik->kelas->nama : '-'}}</td>
                                        <td>
                                            @if($tidak_scan->izin)
                                            {{$tidak_scan->izin->keterangan}}
                                            @endif
                                            @if($tidak_scan->izin)
                                            {{$tidak_scan->izin->keterangan}}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="4">Tidak ada data untuk ditampilkan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-0">SCAN PULANG CEPAT</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center">NO</th>
                                    <th class="text-center">NAMA</th>
                                    <th class="text-center">KELAS/UNIT</th>
                                    <th class="text-center">KETERANGAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($collection_pulang_cepat as $pulang_cepat)
                                <tr>
                                    <td class="text-center">{{$loop->iteration}}</td>
                                    <td>{{$pulang_cepat->peserta_didik->nama}}</td>
                                    <td>{{($pulang_cepat->peserta_didik->kelas) ? $pulang_cepat->peserta_didik->kelas->nama : '-'}}</td>
                                    <td>{{$pulang_cepat->absen_pulang->keterangan}}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="text-center" colspan="4">Tidak ada data untuk ditampilkan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<style>
.card {
  box-shadow:0 4px 24px 0 rgba(34,41,47,.1);
  margin-bottom:2rem;
  transition:all .3s ease-in-out,background 0s,color 0s,border-color 0s
}
.card .card {
  box-shadow:none!important
}
.card .card-title {
  font-size:1.285rem;
  font-weight:500;
  margin-bottom:1.53rem
}
.card .card-bordered {
  border:0 solid rgba(34,41,47,.125)
}
.card .card-img {
  -o-object-fit:cover;
  object-fit:cover
}
.card .card-img-overlay {
  border-radius:.428rem
}
.card.card-fullscreen {
  bottom:0;
  display:block;
  height:100%!important;
  left:0;
  overflow:auto;
  position:fixed;
  right:0;
  top:0;
  width:100%!important;
  z-index:9999
}
.card .card-body[class*=border-bottom-] {
  border-bottom-width:2px!important
}
.card .card-img-overlay.bg-overlay {
  background:rgba(34,41,47,.45)
}
.card .card-img-overlay .text-muted {
  color:#1e1e1e!important
}
.card.card-minimal {
  border:none;
  box-shadow:none
}
.card .card-header {
  align-items:center;
  display:flex;
  flex-wrap:wrap;
  justify-content:space-between;
  position:relative
}
.card .card-header .card-title {
  margin-bottom:0
}
.card .card-header .heading-elements {
  position:relative;
  top:-1px
}
.card .card-header .heading-elements li:not(:first-child) a {
  margin-left:.75rem
}
.card .card-header .heading-elements a.btn {
  padding-bottom:6px;
  padding-top:6px
}
.card .card-header .heading-elements a i,
.card .card-header .heading-elements a svg {
  font-size:1rem;
  height:1rem;
  width:1rem
}
.card .card-header .heading-elements a[data-action=collapse] i,
.card .card-header .heading-elements a[data-action=collapse] svg {
  display:inline-block;
  transition:all .25s ease-out
}
.card .card-header .heading-elements a[data-action=collapse].rotate i,
.card .card-header .heading-elements a[data-action=collapse].rotate svg {
  transform:rotate(-180deg)
}
.card .card-header+.card-body,
.card .card-header+.card-content>.card-body:first-of-type {
  padding-top:0
}
.card .card-footer {
  background-color:transparent;
  border-top:1px solid #dae1e7
}
.card-group {
  margin-bottom:.75rem
}
.card-head-inverse,
.card-head-inverse .heading-elements i,
.card-head-inverse .heading-elements svg {
  color:#fff
}
.card-transparent {
  background-color:transparent
}
.card .table {
  border-bottom-left-radius:.357rem;
  border-bottom-right-radius:.357rem;
  margin-bottom:0
}
.card .table tbody tr:last-child>* {
  border-bottom-width:0
}
.card .table tbody tr:last-child td:first-child {
  border-bottom-left-radius:.357rem
}
.card .table tbody tr:last-child td:last-child {
  border-bottom-right-radius:.357rem
}
.table-success {
    border-color: #c2e0d0;
}
.toast-error {background: #dc3545; font-size: 20px;}
.toast-success {background: #198754}
.toast-title, .toast-message {color: #fff !important; font-size: 25px;}
.form-control:focus {   
    box-shadow: rgba(0, 0, 0, 0.25) 0px 54px 55px, rgba(0, 0, 0, 0.12) 0px -12px 30px, rgba(0, 0, 0, 0.12) 0px 4px 6px, rgba(0, 0, 0, 0.17) 0px 12px 13px, rgba(0, 0, 0, 0.09) 0px -3px 5px !important;
}
</style>
@endpush
@script
<script>
    console.log('asd');
    Pusher.logToConsole = true;

    var pusher = new Pusher('bc531acdb4578535bf7a', {
      cluster: 'ap1'
    });
    var aksi_scan = pusher.subscribe('aksi-scan');
    aksi_scan.bind('App\\Events\\StatusLiked', function(data) {
      console.log('aksi-scan');
      console.log(data);
      toastr[data.type](data.message, data.title, {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            //"positionClass": "toast-top-full-width",
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "3000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        });
        new Audio("{{url('mp3')}}/"+data.mp3).play();
    });
</script>
@endscript