{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- Block user information module HEADER -->
<div id="header_user" {if $PS_CATALOG_MODE}class="header_user_catalog"{/if}>
	<ul id="header_nav">
		{if $logged}
			<li id="header_link_sitemap">
				<a href="{$link->getModuleLink('blockwishlist', 'mywishlist', array(), true)|addslashes}" title="My Wishlist">{l s='my wishlist' mod='blockuserinfo'}</a>
			</li>
		{/if}
		<li class="user_status">
			{if $logged}
				<a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='My Account' mod='blockuserinfo'}" class="account" rel="nofollow">
					{$cookie->customer_firstname} {$cookie->customer_lastname}
				</a>
			{else}
				<a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='Log in to your customer account' mod='blockuserinfo'}" class="login" rel="nofollow">{l s='Sign in' mod='blockuserinfo'}</a>
			{/if}
		</li>
		{if $logged}
			<li class="user_status">
				<a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}" title="{l s='Log me out' mod='blockuserinfo'}" class="logout" rel="nofollow">{l s='Sign out' mod='blockuserinfo'}</a>
			</li>
		{/if}
	</ul>
</div>
<!-- /Block user information module HEADER -->
