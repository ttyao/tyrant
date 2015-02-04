<?php
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

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

include(dirname(__FILE__).'/facebookcandvc.php');
include(dirname(__FILE__).'/classes/facebookcandvchelp.class.php');
global $smarty;
$facebookcandvc = new facebookcandvc();
$data_translate = $facebookcandvc->translateFB();

$name_module = 'facebookcandvc';
$data = array('appid'=>Configuration::get($name_module.'appid'),
			  'secretkey'=>Configuration::get($name_module.'secretkey'),
			  );

$obj = new facebookcandvchelp();

$data_voucher = $obj->login($data);

if(Configuration::get($name_module.'vis_on') == 1 && $data_voucher['auth'] == 0){	
if(defined('_MYSQL_ENGINE_')){
	$_http_host = $smarty->tpl_vars['base_dir_ssl']->value;
} else {
	$_http_host = $smarty->_tpl_vars['base_dir_ssl'];
}
echo '<h4>';
echo '<img src="'.$_http_host.'modules/facebookcandvc/i/logo-16x16.gif"/>&nbsp;';
echo $data_translate['firsttext'].' '.$data_translate['discountvalue'];
echo '</h4>';
echo '<br/>';
echo '<div style="font-weight:normal;font-size:12px">'.$data_translate['secondtext'].': &nbsp;<b>'.$data_voucher['data']['voucher_code'].'</b></div>';
echo '<br/>';
echo '<div style="font-weight:normal;font-size:12px">'.$data_translate['threetext'].': &nbsp;<b>'.$data_voucher['data']['date_until'].'</b></div>';
} else {
	echo "auth";
}
exit();
