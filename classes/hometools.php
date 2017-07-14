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
if (!class_exists ('HomeTools')) {
class HomeTools {
	public $compath;
	public $plugin_view_type='';
	public $vars=array();
	public $addons=array();
	
    public function __construct($compath='')
    {
		if (empty($compath)){
			$compath=str_replace('\\','/',dirname(__FILE__)).'/';
		}
		$this->compath=$compath;
		$this->vars=array();
    }


	/**
	* Initialize component
	*
	* called from by ossn_register_callback('ossn', 'init', $thisClass->init());
	*
	* @return void
	*/
	function init() { 
		$this->checkTable();
		//ossn_add_hook('user', 'default:fields', array($this,'snatch_hook'));	
		ossn_register_callback('ossn', 'init', array($this,'system_init'), 301);

		ossn_extend_view('css/ossn.admin.default', 'css/hometools');
		ossn_extend_view('js/opensource.socialnetwork', 'js/hometools');
		
		if(ossn_isAdminLoggedin()) {
			ossn_register_com_panel('hometools', 'settings');
			ossn_register_admin_sidemenu('addons', 'addons:manager', ossn_site_url('administrator/addons'), ossn_print('addons:menu'));			
			ossn_add_hook('halt', 'view:themes/goblue/plugins/default/menus/topbar_admin', array($this,'snatch_hook'));	
			ossn_register_action('hometools/post', $this->compath . 'actions/post.php');		
		}
		
		return;
		
	}
	
	/**
	* Will try to load this routine as last initialization
	*
	* called from by ossn_register_callback('ossn', 'init', $thisClass->system_init());
	*
	* @return void
	*/
	function system_init() { 
		if(ossn_isAdminLoggedin()) {
			//Remove the 'Premium' menu item
			//ossn_unregister_menu_item('support','support','topbar_admin');
			
			//Register the admin page handler for add ons
			ossn_register_page('administrator', array($this,'ossn_administrator_pagehandler'));
			
			//Register the addons page handler 
			ossn_register_page('addons', array($this,'addons_pagehandler'));
			
			$this->loadAddons();
		}
		if(ossn_isLoggedin()) {
			//
		}

		
	}
	
	/**
	* Load and activate the add-ons
	*/
	function loadAddons(){
		$this->addons=array('all'=>$this->getAddons('all','name'));
		$this->addons['active']=array();
		foreach($this->addons['all'] as $name=>$addon){
			if ($addon->active=='1'){
				$classdata=$this->loadAddon($name);
				if ($classdata){
					$this->addons['active'][$name]=$classdata;
				}
			}
		}
		ksort($this->addons['active']);
	}
	
	
	/**
	* Process the hooks
	*/
	function snatch_hook($hook, $type, $params=null, $returnvalue=null){
		$hooktype=$hook.':'.$type;
		switch ($hooktype){
		case 'halt:view:themes/'.ossn_site_settings('theme').'/plugins/default/menus/topbar_admin':
			$params=$returnvalue;
			$configmenu=$params['menu']['configure'];
			foreach($configmenu as $i=>$menu){
				if ($menu['name']=='hometools'){
					unset($configmenu[$i]);
					break;
				}
			}
			$params['menu']['configure']=$configmenu;
			$path = ossn_route()->www . substr($type,5);
			$file = ossn_include($path . '.php', $params);
			return $file;
			break;
		}
		return;
	}
	
	/**
	* Register a page handler for addons
	* @pages:
	*       addons/*
	*
	* @return boolean|null
	*/
	function addons_pagehandler($pages) {
		if(!ossn_isLoggedin()) {
			ossn_error_page();
		}
		$page = $pages[0];
		if(empty($page)) {
			$page = 'addons';
		}
		switch($page) {
		case 'anything at all':
			break;
		default:
			//ossn_error_page();
			break;
		}
	}
	
	
	/**
	* Register a page handler for administrator;
	* @pages:
	*       administrator/addons,
	*       administrator/addon,
	*       administrator/{else} -- pass it to ossn_administrator_pagehandler($pages)
	* @return boolean|null
	*/
	function ossn_administrator_pagehandler($pages) {
		global $Ossn;
		$page = $pages[0];
		$name = false;
		if(isset($pages[1])){
			$name=$pages[1];
			if ($name=='hometools'){
				$page='hometools';
			}
		}
		switch($page) {
		case 'hometools':
			//execute as component
			if(isset($name) && in_array($name, ossn_registered_com_panel())) {
				$com['com']           = OssnComponents::getCom($name);
				$com['settings']      = ossn_components()->getComSettings($name);
				$title                = $com['com']->name;
				$contents['contents'] = ossn_plugin_view("settings/administrator/{$pages[1]}/{$Ossn->com_panel[$name]}", $com);
				$contents['title']    = $title;
				$content              = ossn_set_page_layout('administrator/administrator', $contents);
				echo ossn_view_page($title, $content, 'administrator');
			}
		case 'addons':
			switch($pages[1]){
			case 'settings':
				$id = $pages[2];
				if(!empty($id)) {
					$addon=$this->getAddon($id);
					echo ossn_view_form('settings', array(
						'action' => ossn_site_url() . 'action/hometools/post',
						'component' => 'hometools',
						'class' => 'htao-form',
						'params' => $addon,
					), false);
					break;
				} else {
					ossn_error_page();
				}
				break;
			default:
				$title                = ossn_print('admin:addons');
				$contents['contents'] = ossn_plugin_view('admin/list');
				$contents['title']    = $title;
				$content              = ossn_set_page_layout('administrator/administrator', $contents);
				echo ossn_view_page($title, $content, 'administrator');
				break;
			}
			break;
		case 'addon':
			global $Ossn;
			if( isset($name) && in_array($name, array_keys($this->addons['active'])) ) {
				$addon['addon']       = $this->getAddon($name,'name');
				$addon['settings']    = $addon['addon']->settings;
				$title                = $addon['addon']->title;
				//verify correct location for contents
				$contents['contents'] = ossn_plugin_view("settings/administrator/{$name}/{$Ossn->addon_panel[$name]}", $addon);
				$contents['title']    = $title;
				$content              = ossn_set_page_layout('administrator/administrator', $contents);
				echo ossn_view_page($title, $content, 'administrator');
			}
			break;
			
		default:
			ossn_administrator_pagehandler($pages);
			break;					
		}
	}

    /**
     * Get the addons.
     *
     * @return object;
     */
    public function getAddons($scope='all',$orderby='order'){
		$addons=array();
	
		$OssnDb=new OssnDatabase;
		$params['from']   = 'ossn_addons';
		$params['orderby'] = array( 
			$orderby
		);
		if ($scope!='all'){
			$params['wheres'] = $scope;
		}
		$items=$OssnDb->select($params, true);
		$count=count($items);
		foreach($items as $i=>$item){
			$order=$item->order;
			if (isset($addons[$order])){
				$item->order=$count+$i;
			}
			$data=$this->getAddonXML($item->name);
			if ($data){
				$data->id=$item->id;
				$data->order=$item->order;
				$data->active=$item->active;
				$data->settings=unserialize($item->settings);
				$addons[$item->$orderby]=$data;
			}
		}
		ksort($addons);
		return $addons;
    }
	
	/**
	 * Save add-on data by add-on id
	 *
	 * @params integer $id ID of add-on
	 * @params array $addon data of add-on
	 *
	 * @return object|false
	 */
	function setAddon($id,$addon){
		$names=array();
		$values=array();
		foreach($addon as $key=>$val){
			$names[]=$key;
			$values[]=$val;
		}
	
		$OssnDb=new OssnDatabase;
		$params['table'] = 'ossn_addons';
		$params['names']  = $names;
		$params['values'] = $values;
		$params['wheres'] = array(
			"id='{$id}'"
		);
		if($OssnDb->update($params)) {
			return true;
		}
		return false;
	}

	/**
	 * Delete add-on data and files by id
	 *
	 * @params integer $id ID of add-on
	 *
	 * @return object|false
	 */
	function deleteAddon($value,$field='id'){
		$data=$this->getAddon($value,$field);
		$name=$data->name;
		$OssnDb=new OssnDatabase;
		$params['from'] = 'ossn_addons';
		$params['wheres'] = array(
			"$field='{$value}'"
		);
		if($OssnDb->delete($params)) {
			if (isset($this->addons['active'][$name])){
				unset($this->addons['active'][$name]);
			}
			if (isset($this->addons['all'][$name])){
				unset($this->addons['all'][$name]);
			}
			OssnFile::DeleteDir($this->compath.'addons/'.$data->name);		
			return true;
		}
		return false;
	}
	
	/**
	 * Load add-on by id
	 *
	 * @params $name of add-on
	 *
	 * @return object|false
	 */
	function loadAddon($name,$regged=true){
		$classname='hometool_'.$name;
		if ($regged){
			$addon=$this->addons['all'][$name];
			$id=$addon->id;
			$active=$addon->active;
			if ((empty($name)) ||
				(empty($id)) ||
				(empty($addon)) || 
				(!$active) || 
				($active==0) ||
				(class_exists($classname)) 
				){ return false; }
		}
		$path=$this->compath.'addons';
		$pathname="$path/$name/hometool.php";
		if (file_exists($pathname)){
			include_once($pathname);
			$class=new $classname;
			if ($regged) $class->load();
			return $class;
		}
		return false;
	}

		/**
	 * Get add-on XML by name
	 *
	 * @params $name,$path of add-on
	 *
	 * @return object|false
	 */
	public function getAddonXML($name,$path=false){
		if (!$name){ return false; }
		if (!$path){
			$path=$this->compath.'addons';
		}
		$pathname="$path/$name/hometool.xml";
		if (file_exists($pathname)){
			$data=(array)simplexml_load_file($pathname); //force to array
			$data=(object)$data; //force back to stdClass object
			if ($data->settings){
				$data->settings=(array)$data->settings;
			}
			return $data; 
		}
		return false;
	}

	/**
	 * Get add-on by field/value
	 *
	 * @params $value,$field of add-on
	 *
	 * @return object|false
	 */
	function getAddon($value,$field='id'){
		$OssnDb=new OssnDatabase;
		$params['from']   = 'ossn_addons';
		$params['wheres'] = array(
			"$field='{$value}'"
		);
		$item = $OssnDb->select($params,false); //single item
		if($item) {
			$data=$this->getAddonXML($item->name);
			if ($data){
				$data->id=$item->id;
				$data->name=$item->name;
				$data->order=$item->order;
				$data->active=$item->active;
				$data->settings=unserialize($item->settings);
			}
			return $data;
		}
		return false;
	}
	

	/**
	 * Insert a new add-on to hometools
	 *
	 * @return boolean
	 */
	public function newAddon($name) {
		if(!empty($name) && is_dir($this->compath.'addons/'.$name)) {
			//check if addon is in the database table
			$item = $this->getAddon($name,'name');
			if ($item){
				ossn_trigger_message(ossn_print('addon:exists',$name), 'error');
				return false;
			}
			$data=$this->getAddonXML($name);
			$item=array(
				'id'=>0,
				'name'=>$name,
				'order'=>count($this->addons)+9,
				'active'=>0,
				'settings'=>serialize((array)$data->settings)
			);
			$OssnDb=new OssnDatabase;
			$params['into'] = 'ossn_addons';
			$params['names']=array();
			$params['values']=array();
			foreach($item as $field=>$value){
				$params['names'][]=$field;
				$params['values'][]=$value;
			}
			if($OssnDb->insert($params)) {
				if (!in_array($name,$this->addons['all'])){
					$this->addons['all'][$name]=(object)$item;
					ksort($this->addons['all']);
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * Upload addon
	 *
	 * @return boolean
	 *
	 */
	function upload() {
		//initial return value
		$return=false; 
		$archive  = new ZipArchive;
		$data_dir = ossn_get_userdata('tmp/addons');
		if(!is_dir($data_dir)) {
			mkdir($data_dir, 0755, true);
		}
		$zip = $_FILES['addon_file'];
		$newfile = "{$data_dir}/{$zip['name']}";
		if(move_uploaded_file($zip['tmp_name'], $newfile)) {
			if($archive->open($newfile) === TRUE) {
				$translit = OssnTranslit::urlize($zip['name']);
								
				$archive->extractTo($data_dir . '/' . $translit);
				$dirctory = scandir($data_dir . '/' . $translit, 1);
				$dirctory = $dirctory[0];
								
				$files = $data_dir . '/' . $translit . '/' . $dirctory . '/';
				$archive->close();
				if(is_dir($files) && is_file("{$files}hometool.php") && is_file("{$files}hometool.xml")) {
					$hometool_xml = simplexml_load_file("{$files}hometool.xml");
					if(isset($hometool_xml->name) && !empty($hometool_xml->name)) {
						//move to addons folder
						$addons_folder=$this->compath.'addons/'.$hometool_xml->name.'/';
						if(OssnFile::moveFiles($files, $addons_folder)) {
							//add new addon to hometools
							$return=$this->newAddon((string)$hometool_xml->name);
						}
					}
				}
			}
			//remove temporary folder
			OssnFile::DeleteDir($data_dir);
			
		}
		return $return;
	}
	

	
	/*******************************************************************************************
	*
	* 
	*
	*******************************************************************************************/
	protected function checkTable(){
		//make sure addons table exists
		$OssnDb=new OssnDatabase;
		$query='CREATE TABLE IF NOT EXISTS `ossn_addons` (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`name` varchar(20) NOT NULL,
			`order` bigint(20) NOT NULL,
			`active` int(1) NOT NULL,
			`settings` TEXT NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
		';
		$OssnDb->statement($query);
		$OssnDb->execute();
	}
	
} //End class
} //End ifclass

