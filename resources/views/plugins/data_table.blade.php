<link
    href="{{ asset('backend/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}"
    rel="stylesheet" />
<script src="{{ asset('backend/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $("[data-table]").DataTable();
    });
</script>