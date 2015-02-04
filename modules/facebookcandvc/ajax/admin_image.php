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

include(dirname(__FILE__).'/../../../config/config.inc.php');
include(dirname(__FILE__).'/../../../init.php');
ob_start(); 
$status = 'success';
$message = '';

include_once(dirname(__FILE__).'/../classes/facebookcandvchelp.class.php');
$obj_facebookcandvchelp = new facebookcandvchelp();

$action = Tools::getValue('action');

switch ($action){
	case 'returnimage':
		$type = Tools::getValue('type');
		if($type == "auth"){
			// delete image for auth page
			$obj_facebookcandvchelp->deleteImage(array('type'=>2));
		} elseif($type == "login") {
			$obj_facebookcandvchelp->deleteImage(array('type'=>3));
			// delete custom image for block sign in with facebook 
		} else {
			$obj_facebookcandvchelp->deleteImage(array('type'=>1));
			// delete custom image for block sign in with facebook 
		}
	break;
	default:
		$status = 'error';
		$message = 'Unknown parameters!';
	break;
}


$response = new stdClass();
$content = ob_get_clean();
$response->status = $status;
$response->message = $message;	
$response->params = array('content' => $content);

echo Tools::jsonEncode($response);

?>