<script>
    $(document).ready(function() {
        $('select[name="course_category_id"]').on('change', function() {
            var category_id = $(this).val();
            if (category_id) {
                $.ajax({
                    url: "{{ url('/instructor/sub-category/category-id/') }}/" + category_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('select[name="course_sub_category_id"]').html('');
                        var d = $('select[name="course_sub_category_id"]').empty();
                        $.each(data, function(key, value) {
                            $('select[name="course_sub_category_id"]').append('<option value="' + value.id + '">' + value.sub_category_name + '</option>');
                        });
                    },
                });
            } else {
                alert('danger');
            }
        });
    });
</script>