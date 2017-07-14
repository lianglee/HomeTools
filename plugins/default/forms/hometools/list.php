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
defined at plugins/default/admin/list.php
  echo ossn_view_form('{param1}', array(
        'action' => ossn_site_url() . '{param2[1]}',
        'component' => '{param2[2]}',
    ), false);
actual defined at plugins/default/admin/list.php	
  echo ossn_view_form('list', array(
        'action' => ossn_site_url() . 'action/hometools/post',
        'component' => 'hometools',
  ), false);
called by 
  /administrator/component/{component}  
called from 
  plugins/default/settings/admin/list.php
submits the form to
  actions/post.php (or see above defined {param2[1]})
****************************************************************************************/ 
global $HomeTools;
$pagination = new OssnPagination;
$addons=$HomeTools->getAddons();
$pagination->setItem($addons);
//$data=$HomeTools->getAddonXML('demo');
//echo '<pre>';print_r($data);echo '</pre>';
//echo '<pre>serialize<br/>';print_r(serialize((array)$data->settings));echo '</pre>';
?>
<div class="row margin-bottom-10">
	<div class="inline-block left">
		<em class="left">Drag and drop to reorder the loading of the add-ons</em>
	</div>
    <div class="inline-block right">
		<input type="submit" class="btn btn-primary inline-block left" value="<?php echo ossn_print('addons:reorder'); ?>" style="margin-right:10px;" onClick="htao_setAction('reorder');" />
		<a class="btn btn-success inline-block left" href="#insertaddon" style="margin-right:10px;"><?php echo ossn_print('add'); ?></a>
		<input type="submit" class="btn btn-danger inline-block left" value="<?php echo ossn_print('delete'); ?>" style="margin-right:10px;" onClick="return htao_setAction('delete');" />
    </div>
	<input id="form-action" type="hidden" name="form-action" value=""/>
</div>
<div class="row">
	<table class="table panel-group" id="htao-addons-list" data-id='0'>
	<tbody class="panel panel-default">
    <?php
    $addons = $pagination->getItem();

    if ($addons) {
        foreach ($addons as $i=>$addon) { ?>
            <tr class='sortable-row panel-heading addon-item' id="addon-item-<?php echo $addon->id; ?>" data-id="<?php echo $addon->id; ?>">
                <td class="cb">
					<input type="checkbox" name="guids[]" value="<?php echo $addon->id; ?>"/>
					<input id="list-order" type="hidden" name="order[<?php echo $i;?>]" value="<?php echo $addon->id; ?>"/>
				</td>
                <td class="addon-name sort-handle">
					<?php echo $addon->title.' '.$addon->version; ?>
					<i class="fa fa-sort-desc addon-more"></i>					
				</td>
                <td class="addon-description sort-handle"><?php echo $addon->description; ?></td>
                <td class="addon-active sort-handle">
					<?php if ($addon->active==1){ ?>
						<i title="Enabled" class="component-title-icon component-title-check fa fa-check-circle"></i>				
					<?php } else { ?>
						<i title='Disabled' class="component-title-icon component-title-delete fa fa-times-circle-o"></i>
					<?php } ?>
				</td>

            </tr>
        <?php
        }
    } else {?>
		<tr><td colspan="6"><h4 style="text-align:center;"><?php echo ossn_print('addons:foundnone');?></h4></td></tr>
	<?php } ?>
    </tbody>
</table>
<?php echo $pagination->pagination(); ?>
</div>
<div class="row" id="insertaddon">
<h3><?php echo ossn_print("addon:install");?></h3>
<div class="margin-top-10">
	<input type="file" name="addon_file"/>
</div>
<div class="margin-top-10">
	<input type="submit" class="btn btn-primary" value="<?php echo ossn_print('upload'); ?>" onClick="htao_setAction('upload');"/>
</div>
<div class="alert alert-info page-botton-notice">
    <?php echo ossn_print('addon:install:notice'); ?>
</div>
</div>
<script type="text/javascript">
$( document ).ready(function() {
    $("#htao-addons-list tbody").sortable({
		axis:'y',
		containment:'parent',
		handle:'.sort-handle',
		items:'tr.sortable-row',
		update:function(e,ui){
			$(this).children().each(function(index) {
				$(this).find('td input#list-order').attr('name','order['+index+']');
			})
		}
	});
    $("#htao-addons-list tbody").disableSelection();
	
	$(".addon-item").on("click",".addon-name", function(){
		var id=$(this).closest(".addon-item").attr('data-id');
		var topId=$("#htao-addons-list").attr('data-id');
		if ( (topId>0) && (topId!=id) ){
			$('#addon-item-'+topId).removeClass('expanded')			
			$('#addon-settings-'+topId).remove();
			$("#htao-addons-list").attr('data-id','0');
		}
		if ($('#addon-item-'+id).hasClass('expanded')){
			$('#addon-item-'+id).removeClass('expanded')			
			$('#addon-settings-'+id).remove();
			$("#htao-addons-list").attr('data-id','0');
		} else {
			$('#addon-item-'+id).after('<tr id="addon-settings-'+id+'"><td colspan="6"></td></tr>');
			$('#addon-item-'+id).addClass('expanded')			
			$("#htao-addons-list").attr('data-id',id);
			htao_addonSettings(id);			
		}

	});
	
});

function htao_addonSettings($id) {
    Ossn.PostRequest({
        url: Ossn.site_url + "administrator/addons/settings/" + $id,
        action: false,
        callback: function(callback) {
            $('#addon-settings-' + $id+' td').html(callback);
        }
    });
};

function htao_setAction(action){
	$("#form-action").val(action);
	if (action=='delete') {
		return confirm('<?php echo ossn_print('addon:delete:confirm');?>');
	}
}

</script>
