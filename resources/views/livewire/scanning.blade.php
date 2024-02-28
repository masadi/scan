<div>
	<div class="flex-fill mb-2" x-data x-init="$refs.form_id.focus();"> <!-- this component needs to swap out -->
		<input class="form-control form-control-lg" wire:model="form_id" x-ref="form_id" id="form_id" type="text" placeholder="ID Peserta Didik/ID PTK">
	</div>
    <div class="scan">
		<div class="qrcode"></div>
		<h3>QR Code Scanning...</h3>
		<div class="border"></div>
	</div>
</div>
@push('styles')
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
@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", () => {
    $('#form_id').focus()
    document.addEventListener("click", function() {
      $('#form_id').focus()
    });
    $("#form_id").attr('maxlength','36');
    Livewire.hook('message.processed', (message, component) => {
      $("#form_id").attr('maxlength','36');
    })
  });
  window.addEventListener('toastr', event => {
        console.log(event.detail);
        toastr[event.detail.type](event.detail.message, event.detail.title, {
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
        new Audio("{{url('mp3')}}/"+event.detail.mp3).play();
    })
</script>
@endpush