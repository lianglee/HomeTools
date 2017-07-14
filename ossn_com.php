<?php
/**
 * Open Source Social Network
 *
 * @package    Homelancer Add-ons Manager for Ossn
 * @version    1.7
 * @author     Geocadin RA <ossn@homelancer.com>
 * @author_url http://www.homelancer.com/
 * @copyright  2017 Homelancer.com
 * @license    General Public Licence http://www.opensource-socialnetwork.org/licence
 * @link       http://www.opensource-socialnetwork.org/licence
 */
define('__HOMETOOLS__', ossn_route()->com . 'HomeTools/');

global $HomeTools,$HomeTool;
//Define HomeTool base
require_once(__HOMETOOLS__ . 'addons/hometool.php');
$HomeTool=new HomeTool();

/* Load HomeTools */
require_once(__HOMETOOLS__ . 'classes/hometools.php');
//Pass our component path to the class
$HomeTools=new HomeTools(__HOMETOOLS__);
ossn_register_callback('ossn', 'init', $HomeTools->init());

