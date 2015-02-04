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

{if !$facebookcandvcislogged}
{if $facebookcandvcsigninfacebook_pos == "left"}

<div class="block products_block facebook-block" >
	<h4><img src="{$base_dir_ssl|escape:'html'}modules/facebookcandvc/i/btn/ico-facebook.gif" alt="{l s='Facebook' mod='facebookcandvc'}" />{$facebookcandvcblocktitletxt|escape:'html'}</h4>
			 
			 
	<div class="block_content">
	{if $facebookcandvcvis_on == 1 && $facebookcandvcblockauthis_on == 1}
		<p {if $facebookcandvcis15 == 0}class="fb-txt-block"{/if}>{$facebookcandvcadvtext|escape:'html'}</p>
	{/if}
		<p {if $facebookcandvcis15 == 0}class="fb-img-block"{/if}>
			<a href="javascript://" onclick="facebooklogin('block');">
				<img id="fb-block-img" src="{$facebookcandvcblockimg|escape:'html'}" alt="{l s='Facebook' mod='facebookcandvc'}" />
			</a>
		</p>
	</div>
</div>

{/if}
{/if}


{if $facebookcandvc_psleftColumn == "psleftColumn" && $facebookcandvcviscoupon_on == 1}

<div class="block products_block facebook-block" id="facebook-fan-coupon-block">
	<h4><img src="{$base_dir_ssl|escape:'html'}modules/facebookcandvc/i/btn/ico-facebook.gif" alt="{l s='Facebook' mod='facebookcandvc'}" />{$facebookcandvcblockfantitletxt|escape:'html'}</h4>

	<div class="block_content">
		<p>{$facebookcandvcblockfanadvtxt|escape:'html'}</p>
      
       <div id="container_fb" style="z-index:1000;">
              	
<fb:like href="{$facebookcandvcfanpageurl}" show_faces="{if $facebookcandvcshow_face==1}true{else}false{/if}"  width="{$facebookcandvcfwidth|escape:'html'}" height="{$facebookcandvcfheight|escape:'html'}" layout="{$facebookcandvclikelayout|escape:'html'}"></fb:like>
       
       </div>
       		
	</div>	
</div>
{/if}