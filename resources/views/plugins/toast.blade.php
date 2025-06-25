<!-- notification -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
<div
    id="flash"
    data-message="{{ Session::get('message') }}"
    data-type="{{ Session::get('alert-type', 'info') }}">
</div>

<script
    type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const flash = document.getElementById("flash");
        if (!flash) return;

        const message = flash.dataset.message;
        const type = flash.dataset.type;

        if (message) {
            switch (type) {
                case "info":
                    toastr.info(message);
                    break;
                case "success":
                    toastr.success(message);
                    break;
                case "warning":
                    toastr.warning(message);
                    break;
                case "error":
                    toastr.error(message);
                    break;
            }
        }
    });
</script>