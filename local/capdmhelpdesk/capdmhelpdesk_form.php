<?php

require_once($CFG->libdir.'/formslib.php');
require_once('locallib.php');

class local_capdmcontactus_form extends moodleform {
    function definition() {

                global $COURSE, $DB, $CFG;

                $crseFullname = null;
                
                $mform = $this->_form;
                
                if($this->_customdata['cid']){
                    $crseFullname = $DB->get_field_sql('select crse.fullname from {capdmorder_course} cc inner join {course} crse on cc.course_id = crse.id where cc.id = :id', array('id'=>$this->_customdata['cid']));
                }
                
                if(isset($_GET["msgID"])){
                    $msg = $DB->get_record('capdmcontactus_config', array('id'=>$_GET["msgID"], 'type'=>'msg'));
                    if($msg){
                            $mform->setDefault('subject', $msg->label);
                            $mform->setDefault('message', $msg->content);
                    }
                }

                $mform->addElement('html', '<div id="capdmcontactus_form">');

                // fname
                $mform->addElement('text', 'fname', get_string('fname', 'local_capdmcontactus'), array('size'=>'64', 'maxlength'=>'100'));
		$mform->setType('fname', PARAM_TEXT);
                $mform->addRule('fname', null, 'required', null, 'client');
                $mform->addRule('fname', get_string('maximumchars'), 'maxlength', '100', 'client');
                // lname
                $mform->addElement('text', 'lname', get_string('lname', 'local_capdmcontactus'), array('size'=>'64', 'maxlength'=>'100'));
		$mform->setType('lname', PARAM_TEXT);
                $mform->addRule('lname', null, 'required', null, 'client');
                $mform->addRule('lname', get_string('maximumchars'), 'maxlength', '100', 'client');
                // email
                $mform->addElement('text', 'email', get_string('email', 'local_capdmcontactus'), array('size'=>'64', 'maxlength'=>'100'));
		$mform->setType('email', PARAM_TEXT);
                $mform->addRule('email', null, 'required', null, 'client');
                $mform->addRule('email', get_string('maximumchars'), 'maxlength', '100', 'client');
                $mform->addRule('email', get_string('invalid_email', 'local_capdmcontactus'), 'email', '', 'client');
                // tel
                $mform->addElement('text', 'tel', get_string('tel', 'local_capdmcontactus'), array('size'=>'64', 'maxlength'=>'45'));
		$mform->setType('tel', PARAM_TEXT);
                $mform->addRule('tel', get_string('maximumchars'), 'maxlength', '45', 'client');
                $mform->addRule('tel', get_string('numbersonly', 'local_capdmcontactus'), 'numeric', '', 'client');

		$mform->addElement('text', 'user_id', get_string('capdmcontactus_userid', 'local_capdmcontactus'), array('size'=>'64', 'maxlength'=>'45'));
                $mform->setType('user_id', PARAM_TEXT);


                // callme
//                $mform->addElement('advcheckbox', 'callme', get_string('callme', 'local_capdmcontactus'), get_string('callme_info','local_capdmcontactus'), null, array(0,1));
                // subject
                $mform->addElement('text', 'subject', get_string('subject', 'local_capdmcontactus'), array('size'=>'128', 'maxlength'=>'255'));
		if($crseFullname){
                    $mform->setDefault('subject', get_string('course_enquiry_subject', 'local_capdmcontactus', array('fullname'=>$crseFullname)));
                }
                $mform->setType('subject', PARAM_TEXT);
                $mform->addRule('subject', null, 'required', null, 'client');
                $mform->addRule('subject', get_string('maximumchars'), 'maxlength', '255', 'client');
                // message
                $mform->addElement('textarea', 'message', get_string('message', 'local_capdmcontactus'), array('wrap'=>'virtual', 'rows'=>'10', 'cols'=>'66'));
		$mform->setType('message', PARAM_TEXT);
                $mform->addRule('message', null, 'required', null, 'client');
                // recaptcha
                if (!empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey)) {
                        $mform->addElement('recaptcha', 'recaptcha_element');
                        $mform->addRule('recaptcha_element', null, 'required', null, 'client');
                }

                // hidden fields
                $mform->addelement('hidden', 'thisaction', $this->_customdata['thisaction']);
		$mform->setType('thisaction', PARAM_INT);
                // add button
                $buttonarray=array();
                $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('sendmessage', 'local_capdmcontactus'));
                $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
                $mform->closeHeaderBefore('buttonar');

                $mform->addElement('html', '</div>');
    }

    function validation($data, $files) {
        global $CFG, $DB;
        $errors = parent::validation($data, $files);

        if (!empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey)) {
            $recaptcha_element = $this->_form->getElement('recaptcha_element');
            if (!empty($this->_form->_submitValues['recaptcha_challenge_field'])) {
                $challenge_field = $this->_form->_submitValues['recaptcha_challenge_field'];
                $response_field = $this->_form->_submitValues['recaptcha_response_field'];
                if (true !== ($result = $recaptcha_element->verify($challenge_field, $response_field))) {
                    $errors['recaptcha'] = $result;
                }
            } else {
                $errors['recaptcha'] = get_string('missingrecaptchachallengefield');
            }
        }

        if($errors){
			foreach($errors as $e=>$v){
            	switch($e){
                	case 'recaptcha':
                    	echo capdmcontactus_message(get_string('captchaproblem', 'local_capdmcontactus'));
                    	break;
                }
			}
        }

        return $errors;

    }

}

class local_capdmcontactus_config_form extends moodleform {
    function definition() {

		global $COURSE, $DB;
        $mform = $this->_form;

		if($this->_customdata['mode'] == 'edit'){
			$conf = $DB->get_record('capdmcontactus_config', array('id'=>$this->_customdata['entryID']));
			$mform->setDefault('type', $conf->type);
			$mform->setDefault('label', $conf->label);
			$mform->setDefault('content', $conf->content);
			$mform->setDefault('display', $conf->display);
			$mform->addElement('hidden', 'id', $this->_customdata['entryID']);
			$mform->setType('id', PARAM_INT);
			$mform->addElement('hidden', 'thisaction', 5);
			$mform->setType('thisaction', PARAM_INT);
		} else {
			$mform->addElement('hidden', 'thisaction', 2);
			$mform->setType('thisaction', PARAM_INT);
		}

        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'general', get_string('capdmcontactus_add_config', 'local_capdmcontactus'));

		$types = array('email'=>'Email', 'tel'=>'Tel', 'post'=>'Post', 'admins'=>'Admins', 'msg'=>'Standard message');
		$mform->addElement('select', 'type', get_string('itemtype', 'local_capdmcontactus'), $types);
		
		$mform->addElement('text', 'label', get_string('capdmcontactus_label', 'local_capdmcontactus'), array('size'=>'64', 'maxlength'=>'80'));
		$mform->setType('label', PARAM_TEXT);
//		$mform->addRule('label', null, 'required', null, 'client');
		$mform->addRule('label', get_string('maximumchars'), 'maxlength', '80', 'client');

		$mform->addElement('textarea', 'content', get_string('capdmcontactus_content', 'local_capdmcontactus'), array('wrap'=>'virtual', 'rows'=>'10', 'cols'=>'66'));
		$mform->setType('content', PARAM_TEXT);
		$mform->addRule('content', null, 'required', null, 'client');
		$mform->addRule('content', get_string('maximumchars'), 'maxlength', '400', 'client');

		$mform->addElement('advcheckbox', 'display', get_string('capdmcontactus_display', 'local_capdmcontactus'), null, array('group' => 1), array(0, 1));
		
		// add standard buttons, common to all modules
		$this->add_action_buttons();
		
    }
}


class local_capdmcontactus_message_form extends moodleform {
    function definition() {

		global $COURSE, $DB;
        $mform = $this->_form;


    }
}

class local_capdmcontactus_checkout_coupon_form extends moodleform {
    function definition() {

		global $COURSE, $DB;
        $mform = $this->_form;
	
		$couponInstructions = html_writer::start_tag('div', array('id'=>'couponinstrucitons'));
		$couponInstructions .= html_writer::tag('p', get_string('couponinstructions','local_capdmcontactus'));
		$couponInstructions .= html_writer::end_tag('div');


		$mform->addElement('text', 'coupon', get_string('capdmcontactus_discount_code', 'local_capdmcontactus'), array('size'=>'25', 'maxlength'=>'25'));

		$mform->addElement('html', $couponInstructions);
		
		$mform->addElement('hidden', 'orderid', $this->_customdata['orderid']);
		$mform->addElement('hidden', 'thisaction', $this->_customdata['thisaction']);
		$mform->addElement('hidden', 'step', 2);

		$this->add_action_buttons(false, get_string($this->_customdata['buttonstring'],'local_capdmcontactus'));


    }
}

class local_capdmcontactus_buy_form extends moodleform {
    function definition() {

//		global $COURSE, $DB;
        $mform = $this->_form;

		$mform->addElement('hidden', 'itemid', $this->_customdata['itemid']);
		// thisaction 1 = buy an item
		$mform->addElement('hidden', 'thisaction', 1);
		$mform->addElement('hidden', 'courseprice', $this->_customdata['courseprice']);

		$this->add_action_buttons(false, get_string('buycourse','local_capdmcontactus'));


    }
}

class local_capdmcontactus_paynow_form extends moodleform {
    function definition() {

//		global $COURSE, $DB;
        $mform = $this->_form;

		$mform->addElement('hidden', 'orderid', $this->_customdata['orderid']);
		$mform->addElement('hidden', 'userid', $this->_customdata['userid']);
		$mform->addElement('hidden', 'thisaction', 4);
		

        $mform->addElement('checkbox', 'agreeterms', get_string('agreetoterms','local_capdmcontactus'), html_writer::link('#', get_string('viewtandc','local_capdmcontactus'), array('class'=>'lightlink')));
        $mform->addRule('agreeterms', get_string('agreetotermsprompt','local_capdmcontactus'), 'required', null, 'client');

		$this->add_action_buttons(false, get_string('proceed2','local_capdmcontactus'));


    }
}

class local_capdmcontactus_showcart_form extends moodleform {
    function definition() {
	
		global $DB;
		
		$params = array($this->_customdata['user_id']);
		$cart = $DB->get_record_sql('SELECT o.id, count(i.id) as count_of_items FROM mdl_capdmcontactus_order o inner join mdl_capdmcontactus_order_item i on o.id = i.order_id where order_status = \'p\' and user_id = ?', $params);

		$arrCount = array('count'=>$cart->count_of_items);
						
        $mform = $this->_form;
		// thisaction 1 = buy an item
		$mform->addElement('hidden', 'thisaction', 2);

		$buttonarray=array();
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton', $cart->count_of_items, array('class'=>'cartbutton', 'title'=>get_string('vieworder','local_capdmcontactus', $arrCount)));
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
		
//		$this->add_action_buttons(false, get_string('vieworder','local_capdmcontactus', $arrCount));


    }
}

class local_capdmcontactus_previousorders_form extends moodleform {
    function definition() {
	
		global $DB;
		
		$params = array($this->_customdata['user_id']);
		$cart = $DB->get_record_sql('SELECT count(o.id) as count_of_orders FROM mdl_capdmcontactus_order o where order_status = \'c\' and user_id = ?', $params);

		$arrCount = array('count'=>$cart->count_of_orders);
						
        $mform = $this->_form;
		$mform->addElement('hidden', 'nooforders', $cart->count_of_orders);
		$mform->addElement('hidden', 'thisaction', 90);

		$buttonarray=array();
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton', $cart->count_of_orders, array('class'=>'cartorder', 'title'=>get_string('viewopreviousrderslabel','local_capdmcontactus', $arrCount)));
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }
}

class local_capdmcontactus_list_order_form extends moodleform {
    function definition() {
	
        $mform = $this->_form;
		$order_no = $this->_customdata['orderid'];
		$mform->addElement('hidden', 'ordernumber', $order_no);
		$mform->addElement('hidden', 'thisaction', $this->_customdata['thisaction']);
		
		$arrString = array('orderno'=>$order_no);

		$buttonarray=array();
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton', $order_no, array('class'=>'vieworder', 'title'=>get_string('vieworder','local_capdmcontactus', $order_no)));
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }
}


class local_courselist_form extends moodleform {
    function definition() {
	
        $mform = $this->_form;
		// thisaction 1 = buy an item
		$mform->addElement('hidden', 'thisaction', -1);

		$buttonarray=array();
		$buttonarray[] = &$mform->createElement('submit', 'submitbutton', null, array('class'=>'cartorder', 'title'=>get_string('showcourseslabel','local_capdmcontactus')));
		$mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }
}

class local_capdmcontactus_delete_item_form extends moodleform {
    function definition() {

        $mform = $this->_form;

		$mform->addElement('hidden', 'orderid', $this->_customdata['orderid']);
		$mform->addElement('hidden', 'delid', $this->_customdata['delid']);

		$this->add_action_buttons(false, get_string('remove','local_capdmcontactus'));


    }
}
