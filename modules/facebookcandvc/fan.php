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

$name_module = "facebookcandvc";
if(Configuration::get($name_module.'viscoupon_on') == 0)
	exit;

include(dirname(__FILE__).'/facebookcandvc.php');
include(dirname(__FILE__).'/classes/facebookfancoupon.class.php');

$facebookcandvc = new facebookcandvc();
$data_translate = $facebookcandvc->translateFanCoupon();


$facebookfancoupon = new facebookfancoupon();


$like = (int)Tools::getValue('like');
$unlike = (int)Tools::getValue('unlike');

$_http_host = _PS_BASE_URL_.__PS_BASE_URI__;




if($unlike == 0){
	
	if($facebookfancoupon->isUseCoupon()){
		echo '<h4>';
		echo '<img src="'.$_http_host.'modules/'.$name_module.'/i/logo-16x16.gif"/>&nbsp;';
		echo $data_translate['use_coupon'];
		echo '</h4>';
		exit;
	}
	
	$data_voucher = $facebookfancoupon->createVoucherFanCoupon();
	
	if($like == 1
	   && $data_voucher['is_exists_voucher_for_customer'] == 0){
	   	
	    echo '<h4>';
		echo '<img src="'.$_http_host.'modules/'.$name_module.'/i/logo-16x16.gif"/>&nbsp;';
		echo $data_translate['firsttext'].' '.$data_translate['discountvalue'];
		echo '</h4>';
		echo '<br/>';
		echo '<div style="font-weight:normal;font-size:12px">'.$data_translate['secondtext'].': &nbsp;<b>'.$data_voucher['voucher_code'].'</b></div>';
		echo '<br/>';
		echo '<div style="font-weight:normal;font-size:12px">'.$data_translate['threetext'].': &nbsp;<b>'.$data_voucher['date_until'].'</b></div>';
	} elseif($like == 1
	   		 && $data_voucher['is_exists_voucher_for_customer'] == 1 && $data_voucher['is_expiried_voucher']==0) {
		echo '<h4>';
		echo '<img src="'.$_http_host.'modules/'.$name_module.'/i/logo-16x16.gif"/>&nbsp;';
		echo $data_translate['already_get_coupon'];
		echo '</h4>';
		echo '<br/>';
		echo '<div style="font-weight:normal;font-size:12px">'.$data_translate['secondtext'].': &nbsp;<b>'.$data_voucher['voucher_code'].'</b></div>';
		echo '<br/>';
		echo '<div style="font-weight:normal;font-size:12px">'.$data_translate['threetext'].': &nbsp;<b>'.$data_voucher['date_until'].'</b></div>';
        exit;
	} elseif($like == 1
	   		 && $data_voucher['is_exists_voucher_for_customer'] == 1 && $data_voucher['is_expiried_voucher']==1) {
		echo '<h4>';
		echo '<img src="'.$_http_host.'modules/'.$name_module.'/i/logo-16x16.gif"/>&nbsp;';
		echo $data_translate['expiried_voucher'];
		echo '</h4>';
		echo '<br/>';
		echo '<div style="font-weight:normal;font-size:12px">'.$data_translate['secondtext'].': &nbsp;<b>'.$data_voucher['voucher_code'].'</b></div>';
		echo '<br/>';
		echo '<div style="font-weight:normal;font-size:12px">'.$data_translate['threetext'].': &nbsp;<b>'.$data_voucher['date_until'].'</b></div>';
        exit;
	}
	
} 

/*elseif($unlike == 1){
		// delete voucher	
		$facebookfancoupon->deleteVoucher();
		echo '<h4>';
		echo '<img src="'.$_http_host.'modules/'.$name_module.'/i/logo-16x16.gif"/>&nbsp;';
		echo $data_translate['delete_coupon'];
		echo '</h4>';
		
}*/
	 
exit();
