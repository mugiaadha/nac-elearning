<!--========== Start of add multiple class with ajax ==============-->
<div style="visibility: hidden">
    <div class="whole_extra_item_add" id="whole_extra_item_add">
        <div class="whole_extra_item_delete" id="whole_extra_item_delete">
            <div class="container mt-2">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="goals">Goals</label>
                        <input type="text" name="course_goals[]" id="goals" class="form-control" placeholder="Goals  ">
                    </div>
                    <div class="form-group col-md-6" style="padding-top: 20px">
                        <span class="btn btn-success btn-sm addeventmore"><span class="fa fa-plus-circle">Add</span></span>
                        <span class="btn btn-danger btn-sm removeeventmore"><span class="fa fa-minus-circle">Remove</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var counter = 0;
        $(document).on("click", ".addeventmore", function() {
            var whole_extra_item_add = $("#whole_extra_item_add").html();
            $(this).closest(".add_item").append(whole_extra_item_add);
            counter++;
        });
        $(document).on("click", ".removeeventmore", function(event) {
            $(this).closest("#whole_extra_item_delete").remove();
            counter -= 1
        });
    });
</script>