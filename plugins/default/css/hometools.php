/*****************************************************************
Add admin styles here. 
Defined at hometools.php 
 init() 		
Use following for non-admin
 ossn_extend_view('css/ossn.default', 'css/hometools');  
*****************************************************************/

#htao-addons-list {
overflow:hidden;
position:relative;
}
#htao-addons-list .cb{
	width:1%;
	min-width:10px;
}
#htao-addons-list .addon-name{
	text-align:left;
	max-width:250px;
}
#htao-addons-list .addon-active{
	text-align:right;
	width:10px;
}

#htao-addons-list tr.sortable-row{
cursor:move;cursor:grab;
}
#htao-addons-list td.cb{
cursor:pointer;cursor:hand;
}

#htao-addons-list .addon-name{
	position:relative;
    cursor: pointer;
}
#htao-addons-list .addon-more{
    position: absolute;
    top: 5;
    text-align: center;
    width: 20px;
    height: 30px;
    font-size: 20px;
}

#htao-addons-list tr.ui-sortable-helper{
left:0px !important;
display:block;
cursor:grabbing;
}
