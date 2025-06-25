<script type="text/javascript">
    $(document).ready(function() {
        $('[image]').change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(document).find('[show-image]').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files[0]);
        });
    });
</script>