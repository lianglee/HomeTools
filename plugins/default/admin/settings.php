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
defined at plugins/default/settings/admin/{component}/{settings}.php
  echo ossn_plugin_view('admin/list');
called by 
  {ossn_site_url}/administrator/component/{component}  
called from 
  plugins/default/settings/administrator/{component}/{settings}.php
shows the form located at 
  plugins/default/forms/{param2[2]}/{param1}.php

****************************************************************************************/ 
echo ossn_view_form('settings', array(
    'action' => ossn_site_url() . 'action/hometools/post',
    'component' => 'hometools',
    'class' => 'htao-form',
	'params' => $params,
), false);
