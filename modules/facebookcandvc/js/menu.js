/**
 * Mitrocops LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mitrocops.com/LICENSE.txt
 *
 /*
 * 
 * @author    Mitrocops <developersaddons@gmail.com>
 * @category Others
 * @package facebookcandvc
 * @copyright Copyright (c) 2012 - 2014 Mitrocops LLC. (http://www.mitrocops.com)
 * @license   http://www.mitrocops.com/LICENSE.txt
 */

function return_default_img(type,text){
        	if(confirm(text))
        	{
        	if(type=="auth")
        		$('#imageauthpage').css('opacity',0.5);
        	if(type=="sign")
        		$('#imagesignin').css('opacity',0.5);	
        	
        	$.post('../modules/facebookcandvc/ajax/admin_image.php', {
        		action:'returnimage',
        		type : type
        	}, 
        	function (data) {
        		if (data.status == 'success') {
        			
        			if(type=="auth"){
                		$('#imageauthpage').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageauthpage').src = "";
            			document.getElementById('imageauthpage').src = "../modules/facebookcandvc/i/facebook.png?re=" + count;
            			$('#imageauthpage-click').remove();
        			}
        			if(type=="sign"){
                		$('#imagesignin').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagesignin').src = "";
            			document.getElementById('imagesignin').src = "../modules/facebookcandvc/i/facebook.png?re=" + count;
            			$('#imagesignin-click').remove();
        			}
        			
        			if(type=="login"){
                		$('#imagelogin').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagelogin').src = "";
            			document.getElementById('imagelogin').src = "../modules/facebookcandvc/i/facebook.png?re=" + count;
            			$('#imagelogin-click').remove();
        			}
                	
        		} else {
        			if(type=="auth")
                		$('#imageauthpage').css('opacity',1);
        			if(type=="sign")
                		$('#imagesignin').css('opacity',1);	
        			alert(data.message);
        		}
        		
        	}, 'json');
        	}

        }

function tabs_custom(id){
	
	for(i=0;i<100;i++){
		$('#tab-menu-'+i).removeClass('selected');
	}
	$('#tab-menu-'+id).addClass('selected');
	for(i=0;i<100;i++){
		$('#tabs-'+i).hide();
	}
	$('#tabs-'+id).show();
}

function init_tabs(id){
	$('document').ready( function() {
		for(i=0;i<100;i++){
			$('#tabs-'+i).hide();
		}
		$('#tabs-'+id).show();
		tabs_custom(id);
	});
}

init_tabs(1);