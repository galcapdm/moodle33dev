define(['core/ajax', 'core/templates', 'core/notification', 'core/str'], function(ajax, templates, notification, str) {

    "use strict";

    var hidepos = 0;
    var userID = $('#userid').val();

    $(document).ready(function()
    // ------------------------
    {
        // Validate new message form - START.
        $('#form_new_message').each(function () {
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
                    //var data = $( form ).serialize();

                    $( '#new_message_submit' ).attr('disabled', true);

                    // Hide the form and show an information message as sending emails can cause a delay
                    toggleNewForm(0);

                    var newMsg = $('#message').val();
                    var newSub = $('#subject').val();
                    var catVal = $('#category').val();

                    var promises = ajax.call([
                        { methodname: 'local_capdmhelpdesk_save_message', args:{ userid: userID, category: catVal, subject: newSub, message: newMsg, updateby: userID, status: 0, readflag: 1, params: ''} }
                    ]);
                    promises[0].done(function(data) {
                        // We have the data - lets re-render the template with it.
                        templates.render('local_capdmhelpdesk/message_list', data).done(function(html, js) {
                            $('[data-region="capdmhelpdesk-msg-list"]').replaceWith(html);
                            // And execute any JS that was in the template.
                            templates.runTemplateJS(js);

                            $( '#msgid_'+data.newmsgid+' div.status').toggleClass('latestmsg');

                            $( '#capdmhelpdesk-new-msg-holder' ).animate({
                                left: hidepos
                            }, 750, function(){
                                // Show message list in full opacity.
                                $( '#capdmhelpdesk-msg-list').fadeTo(750, 1);
                                // Reset the values of the input/select boxes.
                                $('#message').val('');
                                $('#subject').val('');
                                $('#category').val('');
                                // Enable the submit button again
                                var validator = $( '#form_new_message' ).validate();
                                validator.resetForm();

                                $( '#new_message_submit' ).attr('disabled', false);

                                $( '#add_new_message' ).toggleClass( 'showingnew' );
                                $( '#add_new_message' ).toggleClass( 'fa-rotate-45' );

                                $.when(str.get_string('newmessageadded', 'local_capdmhelpdesk')).done(function(localizedEditString) {
                                    capdmhelpdesk_alert_msg(localizedEditString, 0);
                                });
                            });

                        }).fail(notification.exception);
                    }).fail(notification.exception);

                    // Always return false so the form does not submit as we want to use Ajax.
                    return false;
                }
            });
        });
        // Validate new message form - END.

        // Validate reply form - START.
        $('#form_reply_message').each(function () {

            console.log('did reply form validation - start');

            $(this).validate({
                rules: {
                    reply: "required",
                },
                messages: {
                    reply: "Please enter a description of the help you are needing",
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

                    var replyMsg = $('#form_reply_message #reply').val();
                    var replyTo = $('#form_reply_message #replyto').val();
                    var replierId = $('#form_reply_message #replierid').val();
                    var notify = $('#form_reply_message #notify').val();
                    var owner = $('#form_reply_message #owner').val();

                    var promises = ajax.call([
                        { methodname: 'local_capdmhelpdesk_save_reply', args:{ replyto: replyTo, message: replyMsg, replierid: replierId, notify: notify, owner: owner} },
                        { methodname: 'local_capdmhelpdesk_get_replies', args:{ replyto: replyTo } }
                    ]);
                    promises[0].done(function(data) {
                        // Submit the reply and feedback to the user
                        $( '#msgid_'+replyTo+'_reply_holder' ).hide(750);
                        $( '#msgid_'+replyTo+'_reply_holder #form_reply_message #reply' ).text('');

                        // Empty the contents of the reply textarea.
                        $( '#reply' ).val('');

                    }).fail(notification.exception);

                    // Now reload the replies for this message
                    promises[1].done(function(data) {
                        // We have the data - lets re-render the template with it.
                        templates.render('local_capdmhelpdesk/message_replies', data).done(function(html, js) {
                            $('[data-region="msgid_'+replyTo+'_replies"]').replaceWith(html);
                            // And execute any JS that was in the template.
                            templates.runTemplateJS(js);
                        }).fail(notification.exception);
                    }).fail(notification.exception);

                    // Always return false so the form does not submit as we want to use Ajax.
                    return false;
                }
            });
        });
        // Validate rely forom - END.
    });

    // Listeners.

    // Add new message button listener.
    // -------------------------------------------------------------------------
    $( '#add_new_message' ).on('click', function(e){
        var p = $( '#capdmhelpdesk-holder' );
        var pos = p.offset();
        var nmx = $( '#capdmhelpdesk-new-msg-holder' ).width();
        hidepos = ((pos.left + nmx)*-1) - 50;

        // Find the top position of the message list and align
        // the new message box to that.
        var p2 = $( "#capdmhelpdesk-msg-list" );
        var pos2 = p2.position();
        var posY = pos2.top;
        $( '#capdmhelpdesk-new-msg-holder' ).css('top', posY);

        // Hide any expanded replies sections
        $('.messageholder.replies').hide(750);

        if(! $( this ).hasClass('showingnew') ){
            $( '#capdmhelpdesk-msg-list').fadeTo(500, 0.25, function(){
                $( '#capdmhelpdesk-new-msg-holder' ).animate({
                    left: 0
                }, 750);
                $( '#subject' ).focus();
            });
        } else {
            $( '#capdmhelpdesk-new-msg-holder' ).animate({
                left: hidepos
            }, 750, function(){
                // Show message list in full opacity.
                $( '#capdmhelpdesk-msg-list').fadeTo(750, 1);
            });
        }
        $( this ).toggleClass( 'showingnew' );
        $( this ).toggleClass( 'fa-rotate-45' );
    });


    /*
     *  Click handler to listen for a click on a message.
     *  This will use AJAX to call for the relies to this message ID
     *  and use a template to replace the contents.
     */
    $( 'body' ).on( 'click', '.messageholder-main', function(e){
        var msgid = $( this ).attr('id');
        var id = msgid.split('_')[1];

        if(! $(this).hasClass('opened') ){

            // Show this message actions buttons
            $( '#action_buttons_'+id ).show(250);

            $( this ).find('.messagedetails').animate({
                marginLeft: '+=30'
            }, 300, function(){
                // Callback.
                $( '#closeicon_'+id ).fadeIn(300);
            });

            // Hide all messageholder div's except this one
            $( '.messageholder-main').not(this).hide(500);

            // Show the replies section and the original message.
            $( '#msgid_'+id+'_replies').show(750);
            $( '#origmessage_'+id).show(750);
            $( this ).addClass('opened');

            var msgid = $( '#msgid' ).val();

            // First - reload the data for the page.
            var promises = ajax.call([
                { methodname: 'local_capdmhelpdesk_get_replies', args:{ replyto: id } },
                { methodname: 'local_capdmhelpdesk_update_message', args:{ msgid: id, field: 'readflag', val: 1 } }
            ]);
            promises[0].done(function(data) {
                // We have the data - lets re-render the template with it.
                templates.render('local_capdmhelpdesk/message_replies', data).done(function(html, js) {
                    $('[data-region="msgid_'+id+'_replies"]').replaceWith(html);
                    // And execute any JS that was in the template.
                    templates.runTemplateJS(js);
                }).fail(notification.exception);
            }).fail(notification.exception);

            promises[1].done(function(data) {
                $( '#msgid_'+id+'_details' ).find('p.unread').removeClass('unread');
            }).fail(notification.exception);

        } else {
            $( '#action_buttons_'+id ).hide(250);
            $( '#closeicon_'+id ).fadeOut(200);
            $( this ).find('.messagedetails').animate({
                marginLeft: '-=30'
            }, 300, function(){
                // Callback.
            });
            // Hide the replies section and the original message.
            $( '#msgid_'+id+'_replies').hide(750);
            $( '#origmessage_'+id).hide(750);
            $( this ).removeClass('opened');
            // Now show all the other messageholder div's.
            $( '.messageholder-main').not(this).show(500);
        }

    });

    // Show only the selected types of message based on status.
    $( 'body' ).on( 'click', '.helpdesk-control-button', function(e){
        var btnid = $( this ).attr('id');
        var show = btnid.split( '_' )[1];

        // Hide any action buttons if shown
        $( '.capdmhelpdesk-action-buttons' ).hide(300);
        $( '.messageholder-replies' ).hide(300);
        // Close any opened messages.
        $( '.opened div.messagedetails' ).animate({
            marginLeft: '-=30'
        }, 300, function(){
            // Callback.
        });
        var openelement = $( '.opened' ).attr( 'id' );
        if( openelement ){
            $( '.opened' ).removeClass( 'opened' );
            $( '#closeicon_'+openelement.split( '_' )[1] ).fadeOut(200);
        }

        switch(show){
            case 'all':
                $( '.holder_status_0' ).show(500);
                $( '.holder_status_1' ).show(500);
                break;
            case 'open':
                $( '.holder_status_0' ).show(500);
                $( '.holder_status_1' ).hide(500);
                break;
            case 'closed':
                $( '.holder_status_0' ).hide(500);
                $( '.holder_status_1' ).show(500);
                break;
            case 'age':
                switch(btnid.split( '_' )[2]){
                    case '0':
                        $( '.holder_age_1' ).show(300);
                        $( '.holder_age_6' ).show(300);
                        $( '.holder_age_12' ).show(300);
                        $( '.holder_age_24' ).show(300);
                        $( '.holder_age_48' ).show(300);
                        break;
                    case '1':
                        $( '.holder_age_1' ).show(300);
                        $( '.holder_age_6' ).hide(300);
                        $( '.holder_age_12' ).hide(300);
                        $( '.holder_age_24' ).hide(300);
                        $( '.holder_age_48' ).hide(300);
                        break;
                    case '6':
                        $( '.holder_age_1' ).hide(300);
                        $( '.holder_age_6' ).show(300);
                        $( '.holder_age_12' ).hide(300);
                        $( '.holder_age_24' ).hide(300);
                        $( '.holder_age_48' ).hide(300);
                        break;
                    case '12':
                        $( '.holder_age_1' ).hide(300);
                        $( '.holder_age_6' ).hide(300);
                        $( '.holder_age_12' ).show(300);
                        $( '.holder_age_24' ).hide(300);
                        $( '.holder_age_48' ).hide(300);
                        break;
                    case '24':
                        $( '.holder_age_1' ).hide(300);
                        $( '.holder_age_6' ).hide(300);
                        $( '.holder_age_12' ).hide(300);
                        $( '.holder_age_24' ).show(300);
                        $( '.holder_age_48' ).hide(300);
                        break;
                    case '48':
                        $( '.holder_age_1' ).hide(300);
                        $( '.holder_age_6' ).hide(300);
                        $( '.holder_age_12' ).hide(300);
                        $( '.holder_age_24' ).hide(300);
                        $( '.holder_age_48' ).show(300);
                        break;
                }
                break;
        }

    });

    // Reload this user's messages.
    $( 'body' ).on( 'click', '#reload', function(){

        $( '#capdmhelpdesk-msg-list').fadeTo(300, 0.25, function(){

            $.when(str.get_string('reloading', 'local_capdmhelpdesk')).done(function(localizedEditString) {
                capdmhelpdesk_alert_msg(localizedEditString, 0);
            });

            var promises = ajax.call([
                { methodname: 'local_capdmhelpdesk_reload_messages', args:{ userid: userID } }
            ]);
            promises[0].done(function(data) {
                // We have the data - lets re-render the template with it.
                templates.render('local_capdmhelpdesk/message_list', data).done(function(html, js) {
                    $('[data-region="capdmhelpdesk-msg-list"]').replaceWith(html);
                    // And execute any JS that was in the template.
                    templates.runTemplateJS(js);
                    // Fade back to normal.
                    setTimeout(function(){
                        $( '#capdmhelpdesk-msg-list').fadeTo(300, 1, function(){
                            capdmhelpdesk_alert_msg('', -1);
                        });
                    }, 750);
                }).fail(notification.exception);
            }).fail(notification.exception);
        });
    });

    // Action buttons
    $( 'body' ).on('click', '.capdmhelpdesk-action-button', function(){
        var btnid = $( this ).attr('id');
        var action = btnid.split('_')[1];
        var id = btnid.split('_')[2];

        // Check what is being requested.
        switch(action){
            case 'reply':   // Show the reply form.
                $( '#msgid_'+id+'_reply_holder' ).show(250);
                // Set focus to the reply textarea.
                $( '#msgid_'+id+'_reply_holder form textarea#reply' ).focus();
                break;
            case 'close':
                // Set the status of this message to closed.
                var promises = ajax.call([
                    { methodname: 'local_capdmhelpdesk_update_message', args:{ msgid: id, field: 'status', val: 1 } }
                ]);
                promises[0].done(function(data) {
                // We have the data - lets re-render the template with it.
                //templates.render('local_capdmhelpdesk/message_replies', data).done(function(html, js) {
//                    $('[data-region="msgid_'+id+'_replies"]').replaceWith(html);
  //                  // And execute any JS that was in the template.
//                    templates.runTemplateJS(js);
console.log(data);
                    $.when(str.get_string('messageclosed', 'local_capdmhelpdesk')).done(function(localizedEditString) {
                        capdmhelpdesk_alert_msg(localizedEditString, 0);
                    });
                    $( '#msgid_'+id+' div.status' ).removeClass('status_0');
                    $( '#msgid_'+id+' div.status' ).addClass('status_1');
                }).fail(notification.exception);
                break;
        }

    });

    $( 'body' ).on('click', '#test', function(){

        toggleNewForm(0);

    });

    // End of listeners.

    // Functions.
    //--------------------------------------------------------------------------

    // Function to toggle visibility of new message holding message
    function toggleNewForm(direction){

        var msg = $( '#new_message_waiting' );
        var form = $( '#new_message_form' );

        switch(direction){
            case 0:
                form.hide(300);
                msg.show(300);
                break;
            case 1:
                msg.hide(300);
                form.show(300);
                break;
        }
    }

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

    return {
        init: function() {
            console.log('Init of capdmhelpdesk');
        }
    };
});