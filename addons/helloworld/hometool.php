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
 
 
/***********************************************************************
This file must be always named as 'hometool.php' 
while the XML file always named as 'hometool.xml'

These two files must always be zipped under the folder using the 
name declared in the 'hometool.xml'

The zipfile name can be anything you want.

You need to declare the class starting with 'HomeTool_' 
then the name specified at the 'hometool.xml' and 'extends HomeTool'
must always extend the base class.

The public functions load() must always be defined. This function 
contains what the addon will be doing.

If you have settings to include, declare the settings names at the 
hometool.xml inside the '<settings></settings>' and declare the
public settings($params) and the code for editing the settings.

Take note of the naming of the input fields which must always be
settings[{variable}] for single variable and
settings[{variable}][] for array variables
***********************************************************************/ 
 
class HomeTool_helloworld extends HomeTool {
	public function load(){
		$settings=$this->getSettings();
		ossn_trigger_message($settings['message'], 'success');
	}
	
	function settings($params){
		//get our settings
		$settings=$params;
		
		//set up our configuration.settings page
		?><h4>My Settings</h4>
		<table class="table"><tbody>
        <tr class="panel-heading action-item">
			<th>Message</th>
			<td>
				<!-- Take note of the naming of the input name="settings[{field}]" -->
				<input type="text" name="settings[message]" value="<?php echo $settings['message'];?>" />
			</td>
		</tr>
        <tr class="panel-heading action-item">
			<th>Sample Setting</th>
			<td>
				<!-- Take note of the naming of the input name="settings[{field}]" -->
				<input type="text" name="settings[anyvar]" value="<?php echo $settings['anyvar'];?>" />
			</td>
		</tr>
        <tr class="panel-heading action-item">
			<th>Sample Array of Settings</th>
			<td>
				<table><tbody>
				<?php foreach ($settings['lotsavars'] as $var){?>
				<tr>
					<!-- Take note of the naming of the array input name="settings[{field}][]" -->
					<input type="text" name="settings[lotsavars][]" value="<?php echo $var;?>" />
				</tr>
				<?php } ?>
				</tbody></table>
			</td>
		</tr>
		</tbody></table>
	<?php
	}
	
}
?>