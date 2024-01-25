<div>
    <div class="flex-fill mb-2">
        <input class="form-control form-control-lg" type="text" wire:model.live="form_id" id="form_id" placeholder="ID Peserta Didik/ID PTK" @if($isDisabled) disabled @endif>
    </div>
    <div class="scan">
        <div class="qrcode"></div>
        <h3>QR Code Scanning...</h3>
        <div class="border"></div>
    </div>
</div>
@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<style>
    .toast-error {background: #dc3545; font-size: 20px;}
    .toast-success {background: #198754}
    .toast-title, .toast-message {color: #fff !important; font-size: 25px;}
    .form-control:focus {   
      box-shadow: rgba(0, 0, 0, 0.25) 0px 54px 55px, rgba(0, 0, 0, 0.12) 0px -12px 30px, rgba(0, 0, 0, 0.12) 0px 4px 6px, rgba(0, 0, 0, 0.17) 0px 12px 13px, rgba(0, 0, 0, 0.09) 0px -3px 5px !important;
      /*border-color: #dc3545;
      box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(126, 239, 104, 0.6);
      outline: 0 none;*/
    }
</style>
@endpush
@script
<script>
    document.addEventListener('livewire:initialized', () => {
        console.log('livewire:initialized');
        document.getElementById("form_id").focus();
        document.addEventListener("click", function() {
            document.getElementById("form_id").focus();
        });
        window.addEventListener('toastr', event => {
            document.getElementById("form_id").focus();
            let getData = event.detail[0];
            toastr[getData.type](getData.message, getData.title, {
                "closeButton": true,
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
            new Audio("{{url('mp3')}}/"+getData.mp3).play();
        })
    })
  
</script>
@endscript
