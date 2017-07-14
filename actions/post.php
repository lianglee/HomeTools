<?php
/**
 * Open Source Social Network
 *
 * @package    Homelancer Add-ons Manager for Ossn
 * @version    1.7
 * @author     Geocadin RA <ossn@homelancer.com>
 * @author_url http://www.homelancer.com/
 * @copyright  2017 Homelancer.com
 * @license    General Public Licence http://www.homelancer.com/license
 * @link       http://www.homelancer.com/license
 */
/****************************************************************************************
process the actions called from the form located at plugins/default/forms/{param2[2]}/{param1}.php

defined at {component}.php 
  ossn_register_action('hometools/post', $this->compath . 'actions/post.php');		
called thru 
  {ossn_site_url}/action/hometools/post  
called from 
  plugins/default/forms/hometools/{form}.php
****************************************************************************************/ 
global $HomeTools;
$formAction = (input('form-action'))?input('form-action'):$_REQUEST['form-action'];
$formName = (input('form-name'))?input('form-name'):$_REQUEST['form-name'];
switch($formAction){
//plugins/default/forms/hometools/list.php
case 'reorder':
	$orders = $_REQUEST['order'];
	$fail=false;
	foreach($orders as $order=>$id){
		$data=array('order'=>$order);
		if (!$HomeTools->setAddon((int)$id, $data)){
			$fail=true;
			ossn_trigger_message(ossn_print('addon:reorder:fail'), 'error');
		}
	}
	if (!$fail){
		ossn_trigger_message(ossn_print('addon:reorder:success'), 'success');
	}
	break;
case 'delete':
	//$guids = $_REQUEST['guids'];
	$guids = (input('guids'))?input('guids'):input('id');
	//single $id at plugins/default/forms/hometools/settings.php
	if (!is_array($guids)){
		$guids=array(input('id'));
	}
	foreach($guids as $id){
		$addon=$HomeTools->getAddon((int)$id);
		if(empty($addon->id)){
			ossn_trigger_message(ossn_print('addon:invalid'), 'error');
		} else {
			if (!$HomeTools->deleteAddon($id)) {
				ossn_trigger_message(ossn_print('addon:delete:fail'), 'error');
			} else {
				ossn_trigger_message(ossn_print('addon:deleted', array($addon->title.' ('.$addon->name.')') ), 'success');  
			}	   
		}
	}
	break;
case 'upload':
	if ($HomeTools->upload()) {
		ossn_trigger_message(ossn_print('addon:installed'), 'success');
	} else {
		ossn_trigger_message(ossn_print('addon:install:error'), 'error');
	}
	break;
//plugins/default/forms/hometools/settings.php
case 'save':
	$settings=input('settings');
	if ($settings){
		//input() strips off our multi-array
		$settings=$_REQUEST['settings'];
	} else {
		$settings=array('settings',array());
	}
	$data['settings']=serialize($settings);
	break;
case 'enable':
	$data=array('active'=>'1');
	//make sure we only enable
	unset($data['settings']);
	break;
case 'disable':
	$data=array('active'=>'0');
	//make sure we only disable
	unset($data['settings']);
	break;
}
if (in_array($formAction,array('save','enable','disable'))){
	$id=input('id');
	$addon=$HomeTools->getAddon((int)$id);
	if(empty($addon->id)){
		ossn_trigger_message(ossn_print('addon:invalid'),'error');
	} else {
		if ($HomeTools->setAddon((int)$id, $data)){
			ossn_trigger_message(ossn_print('addon:'.$formAction,$addon->title.' ('.$addon->name.')'), 'success');
		} else {
			ossn_trigger_message(ossn_print($formAction.':fail',$addon->title.' ('.$addon->name.')'), 'error');
		}
	}
}
redirect(REF);