<?php

global $DB, $USER;

require_once('../../config.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/capdmcontactus_form.php');
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');

$pageparams = array();

$error = '';
$info = '';
$output = '';

$id = optional_param('id', 0, PARAM_INT); // what are we wanting to show on the screen?
$action = optional_param('thisaction', 0, PARAM_INT);  // required by Moodle to work but notionally order course code
$dowhat = optional_param('dowhat', -1, PARAM_INT);  // required by Moodle to work but notionally order course code
$showlist = optional_param('showlist', 1, PARAM_INT); // boolean to show the list of courses or not
	
$context = context_system::instance();

$pluginname = get_string('pluginname', 'local_capdmcontactus');

$thisurl = new moodle_url('/local/capdmcontactus/', $pageparams);

$PAGE->set_url($thisurl);
$PAGE->set_context($context);
//$PAGE->set_pagelayout('admin');
$PAGE->set_pagelayout('capdmpage');
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title($SITE->fullname. ': ' . $pluginname);

$PAGE->navbar->add($pluginname);

require_login();

require_capability('moodle/site:config', $context);

$tmpdir = $CFG->tempdir . '/backup';
if (!check_dir_exists($tmpdir, true, true)) {
    throw new restore_controller_exception('cannot_create_backup_temp_dir');
}

print $OUTPUT->header();
print html_writer::tag('h2', $pluginname);

if($error){
	print html_writer::tag('p', $error);
}
if($info){
	print html_writer::tag('p', $info, array('class'=>'highlight'));
}

if(capdmcontactus_is_admin($USER->username)){

	$output .= html_writer::start_tag('div', array('id'=>'capdmcontactus_button_bar'));
	$output .= html_writer::link('index.php', html_writer::tag('button', get_string('view_list','local_capdmcontactus'), array('class'=>'btn btn-default')));
	$output .= html_writer::link('?thisaction=1&menuid=1', html_writer::tag('button', get_string('add_new','local_capdmcontactus'), array('class'=>'btn btn-default')));
	$output .= html_writer::end_tag('div');
	
	// set the data array
	$data = $_POST;

	switch($action){
		case 1:	// add form
				$form_config = new local_capdmcontactus_config_form(null);		
				$form_config->display();		
			break;
		case 2:	// save item
			// if the form was cancelled then do nothing
			if(!array_key_exists('cancel', $data)){
				if($data){
						$record = new stdClass();
						$record->type = $data['type'];
						$record->label = $data['label'];
						$record->content = $data['content'];
						$record->display = $data['display'];
						$record->cdate = time();
						
						$res = $DB->insert_record('capdmcontactus_config', $record);
				
						if($res > 0){
							$info = get_string('capdmcontactus_record_added','local_capdmcontactus');
						} else {
							$info = get_string('capdmcontactus_record_added_error','local_capdmcontactus');
						}
					}
				}
			break;
		case 3:	// delete an entry
					$res = $DB->delete_records('capdmcontactus_config', array('id'=>$_GET['entryID'])) ;
					if($res > 0){
						$info = get_string('capdmcontactus_record_deleted','local_capdmcontactus');
					} else {
						$info = get_string('capdmcontactus_record_deleted_error','local_capdmcontactus');
					}
			break;
		case 4:	// add form
				$form_config = new local_capdmcontactus_config_form(null, array('mode'=>'edit', 'entryID'=>$_GET['entryID']));
				$form_config->display();		
			break;
		case 5:	// update form
			if(!array_key_exists('cancel', $data)){
				if($data){
						$record = new stdClass();
						$record->id = $data['id'];
						$record->type = $data['type'];
						$record->label = $data['label'];
						$record->content = $data['content'];
						$record->display = $data['display'];
						$record->cdate = time();
						
						$res = $DB->update_record('capdmcontactus_config', $record);
				
						if($res > 0){
							$info = get_string('capdmcontactus_record_updated','local_capdmcontactus');
						} else {
							$info = get_string('capdmcontactus_record_updated_error','local_capdmcontactus');
						}
					}
				} else {
					$info = get_string('capdmcontactus_cancelled','local_capdmcontactus');
				}
			break;

	}

	switch($id){
		case 0: // show list of config options
	
				
	
				$configs = $DB->get_records_sql('select  id, type, label, content, case display when 0 then \'No\' else \'Yes\' end as display, cdate from {capdmcontactus_config} order by `{capdmcontactus_config}`.`type`');
	
				$output .= html_writer::start_tag('div', array('id' => 'capdmcontactus_config_list'));			
				
				if($configs){
					
					$output .= html_writer::tag('p', get_string('capdmcontactus_list_of_configs','local_capdmcontactus'));
			
					$table = new html_table();
					$table->attributes = array('class'=>'capdmcontactus_summary_table');
					$table->head = array(get_string('edit','local_capdmcontactus'),get_String('type','local_capdmcontactus'), get_string('label', 'local_capdmcontactus'), get_string('value','local_capdmcontactus'), get_string('capdmcontactus_display','local_capdmcontactus'));
		
					foreach($configs as $c){
		
						$row = new html_table_row();
			
						$del = html_writer::link('?thisaction=3&entryID='.$c->id.'&menuid=1', get_string('capdmcontactus_del', 'local_capdmcontactus'));
						$edit = html_writer::link('?thisaction=4&entryID='.$c->id.'&menuid=1', get_string('capdmcontactus_edit', 'local_capdmcontactus'));
			
						$cell1 = new html_table_cell();
						$cell1->text = html_writer::tag('p', $del.'<br />'.$edit);
						$cell1->attributes = array('class'=>'oddcell', 'align'=>'left');
						
						$cell2 = new html_table_cell();
						$cell2->text = html_writer::tag('p', $c->type);
						$cell2->attributes = array('class'=>'evencell', 'align'=>'left');
			
						$cell3 = new html_table_cell();
						$cell3->text = html_writer::tag('p', $c->label);
						$cell3->attributes = array('class'=>'evencell', 'align'=>'left');
			
						$cell4 = new html_table_cell();
						$cell4->text = html_writer::tag('p', $c->content);
						$cell4->attributes = array('class'=>'evencell', 'align'=>'left');

						$cell5 = new html_table_cell();
						$cell5->text = html_writer::tag('p', $c->display);
						$cell5->attributes = array('class'=>'evencell', 'align'=>'left');
			
						$row->cells = array($cell1, $cell2, $cell3, $cell4, $cell5);
						$table->data[] = $row;
					}
					
					$output .= html_writer::table($table);
				} else {
					$output .= 'no configs available';
				}
				// course list close
				$output .= html_writer::end_tag('div');
			break;
	}


	if($info){
		$output .= capdmcontactus_message($info);
	}
} else {
	echo('You are not in the admins list - '.$USER->username);	
}

print $output;

print $OUTPUT->footer();
