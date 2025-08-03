<!-- CSS -->
<link rel="stylesheet" href="{{ asset('calendar/nepali.datepicker.v3.2.min.css') }}">

<!-- JS -->
<script src="{{ asset('calendar/nepali.datepicker.v3.2.min.js') }}"></script>

<script>
    var currentDate;

    $(".calender").each(function() {
        cc_id = $(this).attr("id");
        const day = NepaliFunctions.GetCurrentBsDay();
        const month = NepaliFunctions.GetCurrentBsMonth();

        currentDate = NepaliFunctions.GetCurrentBsYear() +
            "-" +
            (month < 10 ? "0" + month : month) +
            "-" +
            (day < 10 ? "0" + day : day);


        if ((this.value == "" || this.value == undefined) && this.dataset.nodate == undefined) {
            // if ($("#" + cc_id).val() == '' && $("#" + cc_id).hasClass('foredit')) {
            //     $("#" + cc_id).val('');
            // } else {
            //     $(this).val(
            //         currentDate
            //     );
            // }
            $("#" + cc_id).val('');


        }


        if($("#" + cc_id).hasClass('nolimit')){
            $("#" + cc_id).nepaliDatePicker();
        }else{
            if($("#" + cc_id).hasClass('before')){
                $("#" + cc_id).nepaliDatePicker({
                    disableBefore: currentDate
                });
            }else{

                $("#" + cc_id).nepaliDatePicker({
                    disableAfter: currentDate
                });
            }
        }

    });
</script>
