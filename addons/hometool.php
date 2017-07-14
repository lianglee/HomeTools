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

if (!class_exists ('HomeTool')) {
class HomeTool{
	public $name='';
	public $homepath='';
	public $vars=array();
	
	
	/**
	* Setup system data
	*
	* @return void
	*/
	function __construct() { 
		$classname=get_class($this);
		$name=substr($classname,9);
		$path=str_replace('\\','/',dirname(__FILE__)).'/';
		$this->homepath=$path.$name.'/';
		$this->name=$name;
		$this->vars=array();
		return;
	}
	
	/**
	* Load and activate the add-on
	*
	* @return void 
	*/
	function load() { }
	
	
	/**
	 * Get add-on settings from the database and default settings from the XML
	 *
	 * @return object|false
	 */
	function getSettings(){
		global $HomeTools;
		$addon=$HomeTools->getAddon($this->name,'name');
		$settings=$addon->settings;
		$pathname=$this->homepath.'hometool.xml';
		if (file_exists($pathname)){
			$data=(array)simplexml_load_file($pathname); //force to array
			$data=(object)$data; //force back to stdClass object
			if ($data->settings){
				$data->settings=(array)$data->settings;
			}
			$settings=array_merge($data->settings,$settings);
		}
		return $settings; 
	}
	
	/**
	 * Show add-configuration panel
	 *
	 * @params array $params of add-on settings
	 *
	 * @return object|false
	 */
	function settings($params=array()){
		return false;
	}

} //End class
} //End ifclass

