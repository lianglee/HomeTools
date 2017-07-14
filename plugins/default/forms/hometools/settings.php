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
defined at addons_pagehandler at classes/hometools.php
  echo ossn_view_form('{param1}', array(
        'action' => ossn_site_url() . '{param2[1]}',
        'component' => '{param2[2]}',
    ), false);
actual defined at plugins/default/admin/settings.php	
  echo ossn_view_form('settings', array(
        'action' => ossn_site_url() . 'action/hometools/post',
        'component' => 'hometools',
  ), false);
also called by 
  /administrator/component/{component}  
also called from 
  plugins/default/admin/settings.php
submits the form to
  actions/post.php (or see above defined {param2[1]})
****************************************************************************************/ 
global $HomeTools;
if (!$params){
	$params = $HomeTools->getAddon($_REQUEST['id']);
}
if(empty($params->id)){
	redirect(REF);
}
if ($params->active=='1'){
	$status='enable';
	$active='disable';
} else {
	$status='disable';
	$active='enable';
}
?>
<input type="hidden" name="id" value="<?php echo $params->id;?>" />
<input type="hidden" name="active" value="<?php echo $params->active;?>" />
<input type="hidden" name="order" value="<?php echo $params->order;?>" />
<input type="hidden" name="form-action" id="settings-form-action" value=""/>
<table class="table margin-top-10"><tbody>
<?php 
$fields=array(
	'name',
	'title',
	'version',
	'description',
	'author',
	'author_url',
);
foreach($fields as $field){?>
	<tr>
		<th scope="row"><?php echo ossn_print('addon:'.$field); ?></th>
    	<td><?php echo $params->$field;?></td>
 	</tr>
<?php } ?>
 	<tr>
		<th scope="row"><?php echo ossn_print('addon:requires'); ?></th>
		<td>OSSN version <?php echo $params->ossn_version;?></td>
	</tr>                                                      
 	<tr>
		<th scope="row"><?php echo ossn_print('addon:status'); ?></th>
		<td><?php echo ossn_print($status).'d';?></td>
	</tr>   
 	<tr><td colspan="2">
	<?php 
	$regged=false;
	if (isset($config)) unset($config);
	if (isset($HomeTools->addons['active'][$params->name])){
		$config=$HomeTools->addons['active'][$params->name];
		$regged=true;
	} else {
		$classname=$HomeTools->LoadAddon($params->name,false);
		$config=new $classname;
	}
	//show configuration panel directly from the HomeTool add-on class
	$config->settings($params->settings);
	
	//clean up temporary class
	if (!$regged) unset($config);
	?>
	</td></tr>
</tbody></table>
<br/>
<?php

?>
<div class="row" style="margin-left:10px;">
	<input class="btn btn-success inline-block left" type="submit" value="<?php echo ossn_print('save'); ?>"  onClick="htao_formAction('save');"/>
	<input class="btn btn-warning inline-block left" type="submit" value="<?php echo ossn_print($active);?>"  style="margin-left:10px;" onClick="htao_formAction('<?php echo $active;?>');"/>
	<input class="btn btn-danger ossn-com-delete-button inline-block left" type="submit" value="<?php echo ossn_print('delete'); ?>"  style="margin-left:10px;" onClick="return htao_formAction('delete');"/>
</div>

<script type="text/javascript">
function htao_formAction(action){
	$("#settings-form-action").val(action);
	if (action=='delete') {
		return confirm('<?php echo ossn_print('addon:delete:confirm');?>');
	}
}
</script>
<style>
.addon-field{
   border: 1px solid #eee;
    border-radius: 2px;
    color: #333;
    display: block;
    font-size: 13px;
    margin-bottom: 10px;
    outline: medium none;
    padding: 12px 14px;
    transition: all 0.4s ease-in-out 0s;
    width: 100%;
}
</style>