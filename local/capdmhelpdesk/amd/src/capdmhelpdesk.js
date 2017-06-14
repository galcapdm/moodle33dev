define(['core/ajax'], function(ajax) {

    "use strict";

    $(document).ready(function()
    // ------------------------
    {

        $('.form_validate').each(function () {
            $(this).validate({
                rules: {
                    subject: "required",
                    message: "required",
                    category: "required",
                },
                messages: {
                    subject: "Please enter a subject line",
                    message: "Please enter a description of the help you are needing",
                    category: "Please enter a value for the category",
                },
                errorElement: "em",
                errorPlacement: function ( error, element ) {
                    // Add the `help-block` class to the error element
                    error.addClass( "help-block" );

                    // Add `has-feedback` class to the parent div.form-group
                    // in order to add icons to inputs
                    element.parents( ".col-sm-5" ).addClass( "has-feedback" );

                    if ( element.prop( "type" ) === "checkbox" ) {
                            error.insertAfter( element.parent( "label" ) );
                    } else {
                            error.insertAfter( element );
                    }

                    // Add the span element, if doesn't exists, and apply the icon classes to it.
                    if ( !element.next( "span" )[ 0 ] ) {
                        $( "<span class='icon fa fa-times form-control-feedback'></span>" ).insertAfter( element );
                    }
                },
                success: function ( label, element ) {
                    // Add the span element, if doesn't exists, and apply the icon classes to it.
                    if ( !$( element ).next( "span" )[ 0 ] ) {
                        $( "<span class='icon fa fa-check form-control-feedback'></span>" ).insertAfter( $( element ) );
                    }
                },
                highlight: function ( element, errorClass, validClass ) {
                    $( element ).parents( ".col-sm-5" ).addClass( "has-error" ).removeClass( "has-success" );
                    $( element ).next( "span" ).addClass( "fa-times" ).removeClass( "fa-check" );
                },
                unhighlight: function ( element, errorClass, validClass ) {
                    $( element ).parents( ".col-sm-5" ).addClass( "has-success" ).removeClass( "has-error" );
                    $( element ).next( "span" ).addClass( "fa-check" ).removeClass( "fa-times" );
                },
                submitHandler: function ( form ) {
                    var data = $( form ).serialize();
                    console.log(data);

                    var newMsg = $('#message').val();
                    var newSub = $('#subject').val();
                    var userID = $('#userid').val();
                    var catVal = $('#category').val();
                    var timestamp = Date.now();
                    var params = {userid:userID, msg: newMsg, subject: newSub, timestamp:timestamp, cat_value: catVal};
                    var ret = capdmhelpdeskio('new', params);




                    // Always return false so the form does not submit as we want to use Ajax.
                    return false;
                }
            });
        });
    });

    // Listeners.

    // Add new message button listener.
    // -------------------------------------------------------------------------
    $( '#add_new_message').on('click', function(e){
        var p = $( '#capdmhelpdesk-holder' );
        var pos = p.offset();
        var nmx = $( '#capdmhelpdesk-new-msg-holder' ).width();
        var hidepos = ((pos.left + nmx)*-1) - 50;

        // Find the top position of the message list and align
        // the new message box to that.
        var p2 = $( "#capdmhelpdesk-msg-list" );
        var pos2 = p2.position();
        var posY = pos2.top;
        $( '#capdmhelpdesk-new-msg-holder' ).css('top', posY);

        // Hide any expanded replies sections
        $('.messageholder.replies').hide(750);

        if(! $( this ).hasClass('showingnew') ){
            $( '#capdmhelpdesk-new-msg-holder' ).animate({
                left: 0
            }, 750);
            $( '#subject' ).focus();
        } else {
            $( '#capdmhelpdesk-new-msg-holder' ).animate({
                left: hidepos
            }, 750);
        }
        $( this ).toggleClass( 'showingnew' );
        $( this ).toggleClass( 'fa-rotate-45' );
    });

    $( '.messageholder' ).on('click', function(e){
        var msgid = $( this ).attr('id');
        var id = msgid.split('_')[1];

        $( '#msgid_'+id+'_replies').toggle(500);
    });

    // End of listeners.

    // Functions.
    //--------------------------------------------------------------------------

    // Function to set and display a helpdesk feedback message
    function capdmhelpdesk_alert_msg(msg, opt){

        $('#capdmhelpdesk-feedback').hide(500);

        switch(opt){
            case -1:
                    $('#capdmhelpdesk-feedback').hide(500);
                    break;
            default:
                    $('#capdmhelpdesk-feedback').html(msg);
                    $('#capdmhelpdesk-feedback').show(500);
        }
    }


    function capdmhelpdeskio(whatType, params){

            params = JSON.stringify(params);

            // to keep the ajax io simple then will separate the differnt types of call
            switch(whatType){

                    case 'new':
                            $.ajax({type: 'POST',
                                    data: {op:'helpdeskmsg', type: whatType, parameters: params},
                                    url: "ajax.php",
                                    success: function(result){
                                            capdmhelpdesk_alert_msg('Thank you. Your message has been saved and course or site administrators have been notified and will be in touch soon. Please reload this page to view your new request in the helpdesk request list.', 0);
                                    },
                                    failure: function(){capdmhelpdesk_alert_msg('problem inserting a record'); refresh = false;
                                            console.log(result);
                                            console.log('this is from the capdmuser jquery code in the error section of a new helpdesk ticket FAILURE');
                                    },
                                    error: function(result){
                                            console.log(result);
                                            console.log('this is from the capdmuser jquery code in the error section of a new helpdesk ticket ERROR');
                                    }
                            });
                            break;
                    case 'close':
                            $.ajax({type: 'POST',
                                    data: {op:'helpdeskmsg', type: whatType, parameters: params},
                                    url: "database2.php",
                                    success: function(result){
                                            capdmhelpdesk_alert_msg('Message closed', 0);
                                            $('#capdmhelpdesk_message_holder').slideToggle(500);
                                            //obj = JSON.parse(params);
                                            //$('#help_msg_'+obj.msgid).remove();
                                            // amend the message count indicator
                                            //newMsgCount = $('#admin_msg_count').attr('count') - 1;
                                            //$('#admin_msg_count').attr('count', newMsgCount);
                                            //$('#admin_msg_count').html('('+newMsgCount+')');

                                            refresh = true; iores = result;
                                            },
                                    failure: function(){capdmhelpdesk_alert_msg('problem inserting a record'); refresh = false;}
                            });
                            break;

                    case 'reply':
                            $.ajax({type: 'POST',
                                    data: {op:'helpdeskmsg', type: whatType, parameters: params},
                                    url: "database2.php",
                                    success: function(result){
                                            capdmhelpdesk_alert_msg('Thank you.  Your reply has been received and where necessary course or site administrators have been notified.', 0);
                                            var arrPars = $.parseJSON(params);
                                            // divide by 100 as it gets multiplied by 100 in the function to handle dates out of MySQL an dPHP
                                            var submitDate = Date.now()/1000;
                                            $('#capdmhelpdesk_message_replies').prepend('<p class="alert bg-danger"><span class="capdmhelpdesk_msgdate smalltext">'+capdmhelpdesk_dateformat(submitDate, 1, 1)+'</span>'+arrPars['msg']+'<br /></p>');
                                            $('#capdmhelpdesk_reply').prop('disabled', false);
                                            $('#capdmhelpdesk_reply').html('Submit');
                                            $('#capdmhelpdesk_replycomment').val('');
                                            $('#capdmhelpdesk_message_reply').slideToggle(500);
                                            refresh = true;
                                            iores = result},
                                    failure: function(){capdmhelpdesk_alert_msg('problem inserting a record'); refresh = false;}
                            });
                            break;
                    case 'ticket':
                            $.ajax({type: 'POST',
                                    data: {op:'helpdeskmsg', type: whatType, parameters: params},
                                    url: "database2.php",
                                    success: function(result){
//						var arrReply = $.parseJSON(result);
                                            var rec = $.parseJSON(result.data);
                                            var origUser = rec['firstname']+' '+rec['lastname'];
                                            var updateBy = rec['updatedbyname'];
                                            var subDate = rec['formatted_submitdate'];    //capdmhelpdesk_dateformat(rec['submitdate'], 1, 1);
                                            var upDate = rec['formatted_updatedate'];     //capdmhelpdesk_dateformat(rec['updatedate'], 1, 1);
                                            var cat = rec['category'];
                                            var recStatus = rec['statusdesc'];

                                            $('#capdmhelpdesk_message_cat').html(rec.fullname);
                                            $('#capdmhelpdesk_message_subject').html(rec.subject);
                                            $('#capdmhelpdesk_message_details').html('<p class="smalltext">User - '+origUser+'</p><p class="smalltext">Date submitted - '+subDate+'</p><p class="smalltext">Date last updated - '+upDate+' by <strong>'+updateBy+'</strong></p><p class="smalltext status_'+rec['status']+'">Status - '+recStatus+'</p>');
                                            $('#capdmhelpdesk_message_msg').html('<p>'+rec.message+'</p>');
                                            $('#btn-capdmhelpdesk_message_close').attr('msgID', rec.id);
                                            $('#capdmhelpdesk_orig_userid').val(rec.userid);
                                            $('#capdmhelpdesk_cat_value').val(rec.category);
                                    },
                                    failure: function(){capdmhelpdesk_alert_msg('problem getting a record'); refresh = false;}
                            });
                            break;
                    case 'replies':
                            var result = '';
                            $.ajax({type: 'POST',
                                    data: {op:'helpdeskmsg', type: whatType, parameters: params},
                                    url: "database2.php",
                                    success: function(result){
                                            $('#capdmhelpdesk_message_replies').children('p').remove();
//                                                var arrReply = $.parseJSON(result);
//                                                var subDate = capdmhelpdesk_dateformat(rec['submitdate'], 0, 0);
//                                                var upDate = capdmhelpdesk_dateformat(rec['updatedate'], 1, 0);

                                            if(result.data.length > 2){
                                                    $.each($.parseJSON(result.data), function(i, o){
                                                            $('#capdmhelpdesk_message_replies').prepend('<p class="alert bg-info"><span class="capdmhelpdesk_msgdate smalltext">'+capdmhelpdesk_dateformat(o.submitdate, 1, 1)+' by '+o.firstname+' '+o.lastname+'</span>'+o.message+'</p>');
                                                    });
                                            } else {
                                                    $('#capdmhelpdesk_message_replies').prepend('<p class="alert bg-info">No replies</p>');
                                            }
                                    },
                                    complete: function(){capdmhelpdesk_wait(0)},
                                    failure: function(){capdmhelpdesk_alert_msg('problem getting a record'); refresh = false;}
                            });
                            break;
                    case 'unread':
                             var result = '';
                            $.ajax({type: 'POST',
                                    data: {op:'helpdeskmsg', type: whatType, parameters: params},
                                    url: "database2.php",
                                    success: function(result){},
                                    complete: function(){},
                                    failure: function(){capdmhelpdesk_alert_msg('problem getting a record'); refresh = false;}
                            });
                            break;
            }
    }



    /*
                        $.ajax({
                        url: "ajax.php",
                        data: {
                            parameters: data,
                            op: 'add'
                        },
                        cache: false,
                        processData: false,
                        contentType: false,
                        method: 'POST',
                        beforeSend: function (data) {
                            console.log('before send');
                        },
                        success: function (dataofconfirm) {
                            // do something with the result
                            console.log(dataofconfirm);
                        },
                        error: function (data) {
                            console.log('failure');
                        },
                        complete: function () {
                            console.log('complete');
                        }
                    });

*/

    return {
        init: function() {
            console.log('Init of capdmhelpdesk');
        }
    };
});