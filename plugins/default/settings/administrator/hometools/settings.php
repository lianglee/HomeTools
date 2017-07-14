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
defined at {component}.php 
  ossn_register_com_panel('{component}', '{settings}');  
called by 
  {ossn_site_url}/administrator/component/{component}
****************************************************************************************/ 
$settings = input('settings');
if (empty($settings)) {
    $settings = 'list';
}
switch ($settings) {
    case 'list':
		//located at plugins/default/{param}.php
        echo ossn_plugin_view('admin/list');
        break;
    case 'settings':
		//located at plugins/default/{param}.php
        echo ossn_plugin_view('admin/settings');
        break;
    default:
        break;

}
?>
