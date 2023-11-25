$(document).ready(function() {
    $("#salon_field_input").hide();
    $("#errorMessagesdiv").hide();
    $("#service_details_div").hide();
    $("#slot_booking_date_and_time_div").hide();
    // $("#ProceedtoBooking").hide();
    $("#ProceedtoBookingDiv").hide();
    $("#selected_cust_addr").hide();



    var base_url = window.location.origin;
    var date = $('#slot_dt').val();
    // var selected    =   new Date(date);


    // Convert to 24 Hr format from 12hr format
    function Convert_to_24hr(format, str) {
        var hours = Number(str.match(/^(\d+)/)[1]);
        var minutes = Number(str.match(/:(\d+)/)[1]);
        var AMPM = str.match(/\s?([AaPp][Mm]?)$/)[1];
        var pm = ['P', 'p', 'PM', 'pM', 'pm', 'Pm'];
        var am = ['A', 'a', 'AM', 'aM', 'am', 'Am'];
        if (pm.indexOf(AMPM) >= 0 && hours < 12) hours = hours + 12;
        if (am.indexOf(AMPM) >= 0 && hours == 12) hours = hours - 12;
        var sHours = hours.toString();
        var sMinutes = minutes.toString();
        if (hours < 10) sHours = "0" + sHours;
        if (minutes < 10) sMinutes = "0" + sMinutes;
        if (format == '0000') {
            return (sHours + sMinutes);
        } else if (format == '00:00') {
            return (sHours + ":" + sMinutes);
        } else {
            return false;
        }
    }

    function convert_to_12hr(time) {
        time_to_12 = time.split(':');
        time_hr = time_to_12[0];
        time_min = time_to_12[1];
        if (time_hr > '12') {
            time_12_hr = time_hr - 12;
            time_format = 'pm';
        } else if (time_hr < '12') {
            time_12_hr = time_hr;
            time_format = 'am';
        } else if (time_hr == '00') {
            time_12_hr = 12;
            time_format = 'am';
        }
        time_in_12_hr = time_12_hr + ':' + time_min + time_format;

        return time_in_12_hr;
    }


    $(".date-flatpickr").flatpickr({
        inline: true,
        dateFormat: "d-m-Y",
        minDate: "today",
        "firstDayOfWeek": 1,
        defaultDate: date,
    });


    function show_time_slot_function(timeslots) {
        time_slots_html = '';
        $.each(timeslots, function(e, t) {
            time_slots_html += '<div id="timeframe_' + e + '" class="btn-group-toggle SelectTimeFromBtn" data-toggle="buttons"><label class="btn btn-secondary showactivetime">';
            time_slots_html += '     <input type="checkbox" class="TimeCheckBox" name="start_time[]" value="' + t + '" data-toggle="tooltip" data-placement="bottom" title="">' + t + '</label>';
            time_slots_html += '</div>';
        });
        $("#time_intervals").html(time_slots_html);
    }

    function show_time_slot_function1(timeslots) {
        // console.log(timeslots);
        var start_time;
        var today = new Date();

        var slot_dt = $("#slot_dt").val();

        if (slot_dt == today) {
            // console.log("yes it's it today");
            var hrs = today.getHours();
            var mins = today.getMinutes();

            if (mins > '30') {
                hrs = hrs + 1;
                mins = '30';
            } else {
                hrs = hrs + 1;
                mins = '00';
            }
            start_time = (hrs > 12) ? (hrs - 12 + ':' + mins + ' PM') : (hrs + ':' + mins + ' AM');
        } else {
            start_time = convert_to_12hr(timeslots.start_time);
        }
        var end_time = convert_to_12hr(timeslots.end_time);

        $('.timepicker_start').timepicker({
            timeFormat: 'H:i',
            step: 30,
            minTime: start_time,
            maxTime: end_time,
            // 'showDuration': true,
            dynamic: true,
            dropdown: true,
            scrollbar: true,
        });

    }

    $("body").on("change", "#select_category", function() {
        var catvalue = $(this).val();
        if (catvalue != '') {
            // console.log(catvalue);
            $.ajax({
                headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
                type: "POST",
                url: base_url + "/mdadmin/bookingslots/salonlistfromcat",
                data: { 'categoryId': catvalue },
                dataType: "JSON",
                success: function(msg) {
                    if (msg['status'] == true) {
                        html = '';
                        html += '<option value="">Please Select Salon</option>';
                        $.each(msg['data'], function(e, t) {
                            html += '<option value="' + t.id + '">' + t.name + '</option>';
                        });
                        $("#salon_field_input").show();
                        $("#salonlists").html(html);
                        $("#errorMessagesdiv").hide();
                        $("#service_details_div").hide();
                        $("#slot_booking_date_and_time_div").hide();

                    } else {
                        $("#salon_field_input").hide();
                        $("#errorMessagesdiv").show();
                        $("#errmessage").html(msg['message']);
                        $("#slot_booking_date_and_time_div").hide();

                    }
                }
            });
        } else {
            $("#salon_field_input").hide();
            $("#errorMessagesdiv").show();
            $("#service_details_div").hide();
            $("#errmessage").html("Please Select Category to Continue");
        }
    });

    $("body").on("change", "#salonlists", function() {
        var salonid = $(this).val();
        var select_category = $("#select_category").val();
        // console.log(select_category);
        $.ajax({
            headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
            type: "POST",
            url: base_url + "/mdadmin/bookingslots/selectsalonservices",
            data: { 'salonId': salonid },
            dataType: "JSON",
            success: function(msg) {
                if (msg['status'] == true) {
                    html = '';
                    $.each(msg['data'], function(e, t) {
                        html += '<tr class>';
                        html += '<td class="select_service_class" data-selected_service_id="' + t.id + '"';
                        html += 'align="center">';
                        html += '<input type="checkbox" name="service_name_id[' + t.id + ']" id="service_id_' + t.id + '" value="' + t.id + '" data-selected_service_name="' +
                            t.service + '" data-selected_service_time="' + t.time + '" data-selected_service_amount="' + t.amount + '" data-selected_guests="1" data-selected_service_type="1" /></td>';
                        html += '<td>' + t.service;
                        if (t.category_id == select_category) {
                            html += ' (Primary)</td>';
                        } else {
                            html += '</td>';
                        }
                        html += '<td>' + t.time + '</td>';
                        html += '<td>' + t.amount + '</td>';
                        html += '<td><select class="form-control no_change_guest" data-service_id="' + t.id + '" name="service[' + t.id + ']["guests"]" id="select_guest_id_' + t.id + '">';
                        html += ' <option value="1">1</option><option value="2">2</option></select></td>';
                        html += ' <td><select class="form-control CheckServiceType" data-service_id="' + t.id + '" name="service[' + t.id + ']["service_type"]" id="select_servicetype_id_' + t.id + '">';
                        html += ' <option value="1" selected>BackToBack</option></select></td>';
                        html += '</tr>';
                    });

                    time_slots_html = '';
                    show_time_slot_function1(msg['timeslots']);
                    $("#service_details_div").show();
                    $("#services_based_on_salon").html(html);
                    $("#errorMessagesdiv").hide();
                    $("#slot_booking_date_and_time_div").hide();

                } else {
                    $("#salonlists").val('');
                    $("#errorMessagesdiv").show();
                    $("#service_details_div").hide();
                    $("#errmessage").html(msg['message']);
                    $("#slot_booking_date_and_time_div").hide();

                }
            }
        });
    });

    $("body").on("change", ".no_change_guest", function() {
        var service_id = $(this).attr("data-service_id");
        var selected_val = $(this).val();
        if (selected_val == '2') {
            $("#select_servicetype_id_" + service_id + "").html('<option value="1">BackToBack</option><option value="2">At the Same Time</option>');
        } else {
            $("#select_servicetype_id_" + service_id + "").html('<option value="1">BackToBack</option>');
        }
        $('#service_id_' + service_id + '').attr('data-selected_guests', selected_val);
        $('.select_service_class').trigger("change");
    });

    $("body").on("change", ".CheckServiceType", function() {
        var service_id = $(this).attr("data-service_id");
        var selected_val = $(this).val();
        $('#service_id_' + service_id + '').attr('data-selected_service_type', selected_val);
        $('.select_service_class').trigger("change");
    });

    $("body").on("change", '.select_service_class', function() {
        var checkboxes = $('.select_service_class input[type="checkbox"]');
        var countCheckedCheckboxes = checkboxes.filter(':checked').length;
        var selected_services = [];
        var totaltime = 0;
        var totalamount = 0;
        if (countCheckedCheckboxes > 0) {
            checkboxes.filter(':checked').each(function() {
                getamount = this.getAttribute("data-selected_service_amount");
                getname = this.getAttribute("data-selected_service_name");
                gettime = this.getAttribute("data-selected_service_time");
                get_guestcount = this.getAttribute("data-selected_guests");
                get_service_type = this.getAttribute("data-selected_service_type");
                service = {
                    'service_id': this.value,
                    'service_name': getname,
                    'service_amt': getamount,
                    'service_time': gettime,
                    'service_guest': get_guestcount,
                    'service_type': get_service_type
                }
                totalamount = totalamount + getamount * get_guestcount;
                totaltime = totaltime + (gettime * get_guestcount) / get_service_type;
                selected_services.push(service);
            });

            $("#slot_booking_date_and_time_div").show();

            $("#calculated_no_of_service").html(countCheckedCheckboxes);
            $("#calculated_service_time").html(totaltime);
            $("#calculated_service_amou").html(totalamount);
            $("#totalservicetime").val(totaltime);
            $("#totalserviceamount").val(totalamount);
            $("#selectedservice_details").val(JSON.stringify(selected_services));
            $("#selected_serive_time").html(totaltime);
        } else {
            $("#slot_booking_date_and_time_div").hide();
            // alert("please Check to contine");
        }
    });

    $("body").on("change", "#slot_dt", function() {
        selected_date = $(this).val();
        selected_salon = $("#salonlists").val();
        $.ajax({
            headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
            type: "POST",
            url: base_url + "/mdadmin/bookingslots/changeservicedate",
            data: { 'selected_date': selected_date, 'selected_salon': selected_salon },
            dataType: "JSON",
            success: function(msg) {
                if (msg['status'] == true) {
                    show_time_slot_function1(msg['timeslots']);
                } else {
                    $("#errorMessagesdiv").show();
                    $("#errmessage").html(msg['message']);
                }
            }
        });

    });


    $('body').on('change', '.timepicker_start', function() {
        // selected_date = $("#slot_dt").val();
        selected_time = $(this).val();
        var totalMinutes = $("#totalservicetime").val();
        // get_24_time = Convert_to_24hr("00:00", selected_time);
        // getdatetime = selected_date + ' ' + selected_time;
        var NewEndTime = moment(selected_time, "hh:mm a")
            .add(totalMinutes, 'minutes')
            .format('hh:mm a');
        console.log(NewEndTime);
        converted_endtime = Convert_to_24hr("00:00", NewEndTime);
        //
        // converted_endtime = convert_to_12hr(NewEndTime);

        $(".end_time_booking").val(converted_endtime);

    });

    // $("body").on("click",'.SelectTimeFromBtn',function()
    // {
    //     var checkboxes = $('.SelectTimeFromBtn input[type="checkbox"]');
    //     var activboxes = $('.SelectTimeFromBtn .showactivetime');
    //     $(".TimeCheckBox").prop("checked",false);
    //     activboxes.removeClass('active');
    //     var get_id                      = $(this).attr('id');
    //     var fields                      = get_id.split('_');
    //     var id_num                      = fields[1];
    //     var totalservtime               = $("#totalservicetime").val();
    //     var no_of_slots_to_be_filled    = totalservtime / 30;
    //     if (totalservtime % 30 === 0) {
    //         no_of_slots = no_of_slots_to_be_filled;
    //     }
    //     else {
    //         no_of_slots = parseInt(no_of_slots_to_be_filled) + 1;
    //     }
    //     console.log(id_num);
    //     for (let index = id_num; index <= no_of_slots; index++) {
    //         console.log(index);
    //         $('#'+fields[0]+'_'+index+' input[type="checkbox"]').attr("checked",true);
    //         $('#'+fields[0]+'_'+index+' .showactivetime').addClass('active');
    //     }
    // });


    // $("body").on("click","#testBtn",function()
    // {
    //     $(".SelectTimeFromBtn input[type='checkbox']").filter(':checked').each(function ()
    //     {
    //         console.log(this.value);
    //         var get_id                      = $(this).attr('id');
    //         var fields                      = get_id.split('_');
    //         var id_num                      = fields[1];
    //         var totalservtime               = $("#totalservicetime").val();
    //         var no_of_slots_to_be_filled    = totalservtime / 30;
    //         if (totalservtime % 30 === 0) {
    //             no_of_slots = no_of_slots_to_be_filled;
    //         }
    //         else {
    //             no_of_slots = parseInt(no_of_slots_to_be_filled) + 1;
    //         }

    //         for (let index = id_num; index <= no_of_slots; index++) {
    //             $('#'+fields[0]+'_'+index+' input[type="checkbox"]').attr("checked",true);
    //             $('#'+fields[0]+'_'+index+' .showactivetime').addClass('active');
    //         }
    //     });
    //     // var totalservtime               = $("#totalservicetime").val();
    //     // var no_of_slots_to_be_filled    = totalservtime / 30;
    //     // if (totalservtime % 30 === 0) {
    //     //     no_of_slots = no_of_slots_to_be_filled;
    //     // }
    //     // else {
    //     //     no_of_slots = parseInt(no_of_slots_to_be_filled) + 1;
    //     // }

    //     // console.log(no_of_slots);
    // });

    $("body").on("click", "#check_slots", function() {
        // var checkbooking    = $("#BlockPostForm").serialize();
        // console.log(checkbooking);
        var formdata = {};
        formdata.selected_date = $("#slot_dt").val();
        formdata.selected_salon = $("#salonlists").val();
        // formdata.totaltime           =   $("#totalservicetime").val();
        // formdata.totalamount         =   $("#totalserviceamount").val();
        formdata.selected_services = JSON.parse($("#selectedservice_details").val());
        formdata.booking_start_time = $('.timepicker_start').val();
        formdata.booking_end_time = $(".end_time_booking").val();
        console.log(formdata);
        // formdata = { 'selected_date'    : selected_date, 'selected_salon': selected_salon };
        $.ajax({
            headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
            type: "POST",
            url: base_url + "/mdadmin/bookingslots/CheckAvailability",
            data: formdata,
            dataType: "JSON",
            success: function(msg) {
                if (msg['status'] == true) {
                    $("#availability_check_div").html('<p>Hoorey, You can continue your booking</p>');
                    // $("#check_slots").hide();
                    // $("#ProceedtoBooking").show();
                    $("#assinged_staffs_to_go").val(JSON.stringify(msg['selected_staffs']));
                    $("#CheckAvailabilityBooking").hide();
                    $("#ProceedtoBookingDiv").show();
                    var servicedet = JSON.parse($("#selectedservice_details").val());
                    html = '';
                    $.each(servicedet,
                        function(e, t) {
                            console.log(t);
                            html += '<tr>';
                            html += '   <td>' + t.service_name + '</td>';
                            html += '   <td>' + t.service_time + '</td>';
                            html += '   <td>' + t.service_amt + '</td>';
                            html += '   <td>' +
                                t.service_guest + '</td>';
                            html += '   <td>';
                            if (t.service_type == 1)
                                html += 'Back 2 Back';
                            else
                                html += 'At the Same time';
                            html += '</td>';
                            html += '</tr>';
                        });
                    $("#SelectedServiceDetails").html(html);
                    $("#selected_booking_dt").html($("#slot_dt").val());
                    $("#selected_bookign_time").html($('.timepicker_start').val() + ' - ' + $(".end_time_booking").val());
                    $("#total_booking_amt").html($("#totalserviceamount").val());
                } else {
                    $("#availability_check_div").html('<p>' + msg['message'] + '</p>');
                    $("#check_slots").show();
                    $("#CheckAvailabilityBooking").show();
                    $("#ProceedtoBookingDiv").hide();
                    // $("#ProceedtoBooking").hide();
                }
            }
        });
    });


    $("body").on("change", "#cutomer_to_b_booked", function() {
        $.ajax({
            headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
            type: "POST",
            url: base_url + "/mdadmin/bookingslots/GetCustomerAddress",
            data: { 'customer_id': $(this).val() },
            dataType: "JSON",
            success: function(msg) {
                if (msg['status'] == true) {
                    html = '';
                    html += '<option value="">Please Select Salon</option>';
                    $.each(msg['data'], function(e, t) {
                        html += '<option value="' + t.id + '">' + t.address + '</option>';
                    });
                    $("#selected_cust_addr").show();

                    $("#user_address_options").html(html);
                } else {
                    $("#errorMessagesdiv").show();
                    $("#selected_cust_addr").hide();
                    $("#errmessage").html("No address found for Customer");
                }
            }
        });
    });


    $("body").on('click', '#BacktoEditBooking', function() {
        $("#CheckAvailabilityBooking").show();
        $("#ProceedtoBookingDiv").hide();
    });


    $("body").on('click', '#AddNewBooking', function() {
        var formdata = {};
        formdata.selected_date = $("#slot_dt").val();
        formdata.selected_salon = $("#salonlists").val();
        formdata.totaltime = $("#totalservicetime").val();
        formdata.totalamount = $("#totalserviceamount").val();
        formdata.selected_services = JSON.parse($("#selectedservice_details").val());
        formdata.booking_start_time = $('.timepicker_start').val();
        formdata.booking_end_time = $(".end_time_booking").val();
        formdata.selected_staffs = JSON.parse($("#assinged_staffs_to_go").val());
        formdata.blockbooking = 1;
        formdata.customer_id = $("#cutomer_to_b_booked").val();
        formdata.address_id = $("#user_address_options").val();
        $.ajax({
            headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
            type: "POST",
            url: base_url + "/mdadmin/bookingslots/MakeBooking",
            data: formdata,
            dataType: "JSON",
            success: function(msg) {
                if (msg['status'] == true) {
                    window.location.href = base_url + "/mdadmin/booking/";
                    console.log("Booked Successfully");
                } else {
                    $("#availability_check_div").html('<p>' + msg['message'] + '</p>');
                    $("#check_slots").show();
                    $("#CheckAvailabilityBooking").show();
                    $("#ProceedtoBookingDiv").hide();
                    // $("#ProceedtoBooking").hide();
                }
            }
        });
    });


});
