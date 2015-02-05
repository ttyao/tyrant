{*
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
*}

{if $facebookcandvcis15 == 0}
<link href="{$base_dir_ssl|escape:'html'}modules/facebookcandvc/css/facebookcandvc.css" rel="stylesheet" type="text/css" media="all" />
{/if}



{literal}
<script type="text/javascript" src="{/literal}{$facebookcandvcfbliburl}{literal}"></script>
{/literal}

{literal}
<script type="text/javascript"><!--
//<![CDATA[

$(document).ready(function(){


{/literal}{if !$facebookcandvcislogged}{literal}

{/literal}{if $facebookcandvcfauthis_on == 1}{literal}

var ph = '<div class="facebook-auth-page-clear"></div><div class="facebook-auth-page box">'+
{/literal}{if $facebookcandvcvis_on == 1 && $facebookcandvcadvauthis_on == 1}{literal}
'<p>{/literal}{$facebookcandvcadvtextauth|escape:"htmlall":"UTF-8"}{literal}</p>'+
{/literal}{/if}{literal}
'<a href="javascript:void(0)" onclick="facebooklogin(\'auth\');" title="{/literal}{l s='Facebook' mod='facebookcandvc'}{literal}">'+
  '<img id="fb-auth-img" src="{/literal}{$facebookcandvcauthimg}{literal}" alt="{/literal}{l s='Facebook' mod='facebookcandvc'}{literal}" {/literal}{if $facebookcandvcvis_on == 0 || $facebookcandvcadvauthis_on == 0}{literal}class="fb-no-advertise"{/literal}{/if}{literal} />'+
'<\/a>'+
'<\/div>';

$('#create-account_form').before(ph);

{/literal}{/if}{literal}



{/literal}{if $facebookcandvcfloginauthis_on == 1}{literal}
var log_in_button = '<a {/literal}{if $facebookcandvcis15 == 1}style="padding-left:10px"{else}style="padding-left:5px"{/if}{literal}href="javascript:void(0)" onclick="facebooklogin(\'welcome\');" title="{/literal}{l s='Facebook' mod='facebookcandvc'}{literal}">'+
  '<img id="fb-welcome-img" src="{/literal}{$facebookcandvcloginimg}{literal}" alt="{/literal}{l s='Facebook' mod='facebookcandvc'}{literal}" {/literal}{if $facebookcandvcvis_on == 0 || $facebookcandvcadvauthis_on == 0}{literal}class="fb-no-advertise"{/literal}{/if}{literal} />'+
'<\/a>';

if($('#header_user_info a'))
 	$('#header_user_info a').after(log_in_button);

//for PS 1.6 >
// if($('.header_user_info'))
//   $('.header_user_info').after('<div class="header_user_info_ps16">'+log_in_button+'<\/div>');

if($('#header_nav'))
  $('#header_nav').append('<li>'+log_in_button+'</li>');

{/literal}{/if}{literal}


{/literal}{/if}{literal}

});


{/literal}{if !$facebookcandvcislogged}{literal}

function facebooklogin(type){
	if({/literal}{$facebookcandvcis_ok_fill_data}{literal} == 0){
		alert("{/literal}{l s='Error: Please fill Facebook App Id and Facebook Secret Key in settings with module Facebook Connect, Fan Coupon, Coupon for registration' mod='facebookcandvc'}{literal}");
		return;
	}

	FB.init({appId: '{/literal}{$facebookcandvcappid}{literal}',
		 status: true,
		 cookie: true,
		 xfbml: true,
       	 oauth: true});

	FB.login(function(response) {
        if (response.status == 'connected') {

        	if(type == "auth"){
        		$('#fb-auth-img').css('opacity',0.5);
        	}
        	if(type == "block"){
        		$('#fb-block-img').css('opacity',0.5);
        	}
        	if(type == "welcome"){
        		$('#fb-welcome-img').css('opacity',0.5);
        	}
        	// connect with facebook
        	$.ajax({
        		type: 'POST',
        		url: baseDir+'modules/facebookcandvc/facebook.php',
        		async: true,
        		cache: false,
        		data: '',
        		success: function(data)
        		{
        		if(type == "auth"){
            		$('#fb-auth-img').css('opacity',1);
            	}
            	if(type == "block"){
            		$('#fb-block-img').css('opacity',1);
            	}
            	if(type == "welcome"){
            		$('#fb-welcome-img').css('opacity',1);
            	}

				if(data != "auth"){
        		{/literal}{if $facebookcandvcvis_on == 1}{literal}
	        		if ($('div#fb-con-wrapper').length == 0)
	        		{
	        			//conwrapper = $('<div>', {'id':'fb-con-wrapper'});
	        			conwrapper = '<div id="fb-con-wrapper"><\/div>';
	        			$('body').append(conwrapper);
	        		}

	        		if ($('div#fb-con').length == 0)
					{
						//condom = $('<div>', {'id':'fb-con'});
	        			condom = '<div id="fb-con"><\/div>';
						$('body').append(condom);
					}

					$('div#fb-con').fadeIn(function(){

						$(this).css('filter', 'alpha(opacity=70)');
						$(this).bind('click dblclick', function(){
						$('div#fb-con-wrapper').hide();
						$(this).fadeOut();
						window.location.reload();
						});
					});

					//$('div#fb-con-wrapper').html(data).fadeIn();
					$('div#fb-con-wrapper').html('<a id="button-close" style="display: inline;"><\/a>'+data).fadeIn();

					$("a#button-close").click(function() {
		        		$('div#fb-con-wrapper').hide();
		        		$('div#fb-con').fadeOut();

		        		{/literal}{if $facebookcandvcorder_page == 1}{literal}
							var url = "{/literal}{$base_dir_ssl}{$facebookcandvcuri|urldecode}{literal}";
							window.location.href= url;
						{/literal}{else}{literal}
							window.location.reload();
						{/literal}{/if}{literal}

		        	});


				{/literal}{else}{literal}

					{/literal}{if $facebookcandvcorder_page == 1}{literal}
						var url = "{/literal}{$base_dir_ssl|escape:'html'}{$facebookcandvcuri|urldecode}{literal}";
						window.location.href= url;
					{/literal}{else}{literal}
						window.location.reload();
					{/literal}{/if}{literal}


				{/literal}{/if}{literal}
				} else {
					{/literal}{if $facebookcandvcorder_page == 1}{literal}
						var url = "{/literal}{$base_dir_ssl|escape:'html'}{$facebookcandvcuri|urldecode}{literal}";
						window.location.href= url;
					{/literal}{else}{literal}
						window.location.reload();
					{/literal}{/if}{literal}
				}
        		}

        		});
        } else {
        	if(type == "auth"){
        		$('#fb-auth-img').css('opacity',1);
        	}
        	if(type == "block"){
        		$('#fb-block-img').css('opacity',1);
        	}
        	if(type == "welcome"){
        		$('#fb-welcome-img').css('opacity',1);
        	}
            // user is not logged in
            window.location.reload();
        }
    }, {scope:'email'});
    return false;

}

 {/literal}{/if}{literal}
--></script>
{/literal}
<!-- Module Facebook Connect + Coupon for registration -->

{if $facebookcandvcviscoupon_on == 1}

{literal}
                <script type="text/javascript">
                $(document).ready(function(){



                    // like
                    FB.Event.subscribe("edge.create", function(targetUrl) {
                       if(targetUrl == '{/literal}{$facebookcandvcfanpageurl}{literal}'){

                    	  $('#facebook-fan-coupon-block').css('opacity',0.5);
                    	  $.ajax({
                          		type: 'POST',
                          		url: baseDir+'modules/facebookcandvc/fan.php',
                          		async: true,
                          		cache: false,
                          		data: 'like=1',
                          		success: function(data)
                          		{
                    		  		  $('#facebook-fan-coupon-block').css('opacity',1);
                    		  		  if(data.length==0) return;
		                    		  if ($('div#fb-con-wrapper').length == 0)
		            	        		{
		            	        			conwrapper = '<div id="fb-con-wrapper"><\/div>';
		            	        			$('body').append(conwrapper);
		            	        		}

		            	        		if ($('div#fb-con').length == 0)
		            					{
		            						condom = '<div id="fb-con"><\/div>';
		            						$('body').append(condom);
		            					}

		            					$('div#fb-con').fadeIn(function(){

		            						$(this).css('filter', 'alpha(opacity=70)');
		            						$(this).bind('click dblclick', function(){
		            						$('div#fb-con-wrapper').hide();
		            						$(this).fadeOut();
		            						});
		            					});

		            					//$('div#fb-con-wrapper').html(data).fadeIn();

		            					$('div#fb-con-wrapper').html('<a id="button-close" style="display: inline;"><\/a>'+data).fadeIn();

		            					$("a#button-close").click(function() {
		            		        		$('div#fb-con-wrapper').hide();
		            		        		$('div#fb-con').fadeOut();

		            		        	});

		                          }

                      		});


                       }
                    });

                    // unlike
                    FB.Event.subscribe("edge.remove", function(targetUrl) {

                    });

                });

		</script>
		{/literal}

{/if}
