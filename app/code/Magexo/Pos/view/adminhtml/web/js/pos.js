require(['jquery', 'jquery/ui'], function($){
    (function () {
        //if need be to ajax refresh data on page
        // jQuery(window).load(loadPos);

        jQuery('.delete-pos').click(function(){
            // var route = '/eibach/pointofsalefront/' + jQuery(this).val() + '/index';
            var route = '/pointofsalefront/' + jQuery(this).val() + '/index';
            var id = jQuery(this).attr('id');
            var url = window.location.origin + route;
            if (confirm("Are you sure you want to delete this POS?")){
                jQuery.ajax({
                    url:  url,
                    method: 'post',
                    data: {'id': id},
                }).done(function (returnResult) {
                    location.reload();
                    returnResult = JSON.parse(returnResult);
                    jQuery('.message-reporting').append('<h2 class="pos-message">' + returnResult + '</h2>');
                    setTimeout(function(){ jQuery('.pos-message').hide() }, 2000);
                })
            } else {
                event.preventDefault();
            }
        });

        jQuery('.update-pos').click(function(){
            jQuery(".pos-update-div").css('display','none');
            jQuery(".pos-create-div").css('display','none');
            jQuery('.pos-create-div').find('input:text').val('');
            // var route = '/eibach/pointofsalefront/' + jQuery(this).val() + '/index';
            var route = '/pointofsalefront/' + jQuery(this).val() + '/index';
            var id = jQuery(this).attr('id');
            var url = window.location.origin + route;
                jQuery.ajax({
                    url:  url,
                    method: 'post',
                    dataType:"json",
                    data: {'id': id},
                }).done(function (returnResult) {
                    jQuery('#pos-update input[name ="pos_id"]').val(returnResult.pos_id);
                    jQuery('#pos-update input[name ="name"]').val(returnResult.name);
                    jQuery('#pos-update input[name ="address"]').val(returnResult.address);
                    jQuery("select").val(returnResult.is_available).attr('selected','selected');
                    jQuery(".pos-update-div").css('display','block');

                })
        });

        jQuery('.create').click(function(){
            jQuery(".pos-create-div").css('display','block');
        });

        jQuery("#submit").on('click', function(){
            // var route = '/eibach/pointofsalefront/update/index';
            var route = '/pointofsalefront/update/index';
            var url = window.location.origin + route;
            if (confirm("Are you sure you want to update this POS?")){
                if (jQuery.trim(jQuery("#pos_id").val()) === "" || jQuery.trim(jQuery("#name").val()) === "" || jQuery.trim(jQuery("#address").val()) === "") {
                    alert('You did not fill out one or more fields');
                    return false;
                } else {
                    jQuery.ajax({
                        url:  url,
                        method: 'post',
                        data : jQuery("#pos-update").serialize(),
                    }).done(function (returnResult) {
                        location.reload();
                        returnResult = JSON.parse(returnResult);
                        jQuery(".pos-update-div").css('display','none');
                        jQuery('.message-reporting').append('<h2 class="pos-message">' + returnResult + '</h2>');
                        setTimeout(function(){ jQuery('.pos-message').hide() }, 2000);
                    })
                }
            } else {
                event.preventDefault();
            }
        });

        jQuery("#submit2").on('click', function(){
            // var route = '/eibach/pointofsalefront/update/index';
            var route = '/pointofsalefront/update/index';
            var url = window.location.origin + route;
            if (confirm("Are you sure you want to Create new POS?")){
                if (jQuery.trim(jQuery("#create_pos_id").val()) === "" || jQuery.trim(jQuery("#create_name").val()) === "" || jQuery.trim(jQuery("#create_address").val()) === "") {
                    alert('You did not fill out one or more fields');
                    return false;
                } else {
                    jQuery.ajax({
                        url:  url,
                        method: 'post',
                        data : jQuery("#pos-create").serialize(),
                    }).done(function (returnResult) {
                        // location.reload();
                        returnResult = JSON.parse(returnResult);
                        jQuery(".pos-create-div").css('display','none');
                        jQuery('.pos-create-div').find('input:text').val('');
                        jQuery('.message-reporting').append('<h2 class="pos-message">' + returnResult + '</h2>');
                        setTimeout(function(){ jQuery('.pos-message').hide() }, 2000);
                    })
                }
            } else {
                event.preventDefault();
            }
        });

        function loadPos() {
            // var route = '/eibach/pointofsalefront/update/index';
            var route = '/pointofsalefront/update/index';
            var url = window.location.origin + route;
            jQuery.ajax({
                url:  url,
                method: 'post',
                dataType:"json",
                data : {type:'read'},
            }).done(function (returnResult) {
                jQuery("#tbody").empty();
                returnResult.forEach(function (singleResult) {
                    jQuery('#tbody').append('<tr>' +
                        '<td>' + singleResult.id + '</td><td>' + singleResult.name +'</td><td>' + singleResult.address + '</td><td>' + singleResult.is_available +
                        '</td><td><button id=' + singleResult.id +' value="pos" class="update-pos" style="display: inline-block">Update</button>' +
                        '</td><td><button id=' + singleResult.id +' value="delete" class="delete-pos" style="display: inline-block">Delete</button></td><td></td>' +
                        '</tr>')
                })
            })
        }
    })();
});
