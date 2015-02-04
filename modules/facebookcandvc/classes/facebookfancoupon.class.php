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

class facebookfancoupon extends Module{
	
	private $_name = 'facebookcandvc';
	
	public function __construct(){
		$this->initContext();
	}
	
	public function initContext()
	{
	  if (version_compare(_PS_VERSION_, '1.5', '>'))
	    $this->context = Context::getContext();
	  else
	  {
	    global $cookie;
	    $this->context = new StdClass();
	    $this->context->cookie = $cookie;
	  }
	}
	
	private function idGuest(){
		$cookie = $this->context->cookie;
		
    	$id_guest = (int)$cookie->id_guest;
    	return $id_guest;
	}
	
	private function getDiscountData(){
		$cookie = $this->context->cookie;
		$id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;
		$id_guest = 0;
		if($id_customer){
			
			// if customer get discount as unregistered user
			$sql = 'SELECT id_discount 
					FROM `' . _DB_PREFIX_ . 'facebookfancoupon`
					WHERE `id_guest` != 0
					AND `ip_adress` = "'.$_SERVER['REMOTE_ADDR'].'"';
			$id_discount = Db::getInstance()->getValue($sql);
				
			if(!$id_discount){
				$sql = 'SELECT id_discount 
						FROM `' . _DB_PREFIX_ . 'facebookfancoupon`
						WHERE `id_customer` = '.$id_customer;
				$id_discount = Db::getInstance()->getValue($sql);
			}
			
		} else {
			$id_guest = $this->idGuest();
			
			// if customer get discount as unregistered user
			$sql = 'SELECT id_discount 
					FROM `' . _DB_PREFIX_ . 'facebookfancoupon`
					WHERE `id_guest` != 0
					AND `ip_adress` = "'.$_SERVER['REMOTE_ADDR'].'"';
			$id_discount = Db::getInstance()->getValue($sql);
			
			if(!$id_discount){	
				$sql = 'SELECT id_discount 
						FROM `' . _DB_PREFIX_ . 'facebookfancoupon`
						WHERE `id_guest` = '.$id_guest.'
						AND `id_customer` = '.$id_customer.'
						AND `ip_adress` = "'.$_SERVER['REMOTE_ADDR'].'"';
				$id_discount = Db::getInstance()->getValue($sql);
			}
		}
		
		return array('id_discount'=>(int)$id_discount,'id_guest'=>(int)$id_guest,'id_customer'=>(int)$id_customer);
	}
	
	
	public function isUseCoupon()
	{
		$data_discount = $this->getDiscountData();
		
		$id_discount = $data_discount['id_discount'];
		
		
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			$sql = 'SELECT COUNT(oct.id_order_cart_rule) 
					FROM ' . _DB_PREFIX_ . 'order_cart_rule oct
					WHERE oct.id_cart_rule = '.$id_discount;
		} else {
			$sql = 'SELECT COUNT(od.id_order_discount) 
					FROM ' . _DB_PREFIX_ . 'order_discount od
					WHERE od.id_discount = '.$id_discount;
		}
		
		return (bool)Db::getInstance()->getValue($sql);
	}
	
	public function deleteVoucher()
	{
		

		$data_discount = $this->getDiscountData();
		
		$id_discount = (int)$data_discount['id_discount'];
		
		$coupon = new Discount($id_discount);
		$coupon->delete();
		
		$sql_delete_discount = 'DELETE FROM `'._DB_PREFIX_.'facebookfancoupon` 
				WHERE id_discount = '.$id_discount;
		
		Db::getInstance()->Execute($sql_delete_discount);
	
	}   
	
	public function isExistsVoucherForCustomer()
	{
		$data_discount = $this->getDiscountData();
		
		$is_exist = $data_discount['id_discount'];
		$id_guest = $data_discount['id_guest'];
		$id_customer = $data_discount['id_customer']; 
		
		return array('is_exist'=>$is_exist,'id_guest'=>$id_guest,'id_customer'=>$id_customer);
	}
	
	public function expiriedVoucher($id_discount)
	{
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			$discount = new CartRule($id_discount);
		} else {
			$discount = new Discount($id_discount);
		}
		
		$is_expiried = 1;
		$current_time = strtotime('now');
		if ($current_time >= strtotime($discount->date_from) && $current_time < strtotime($discount->date_to))
			$is_expiried = 0;
			
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			$code_v = $discount->code;
		} else {
			$code_v = $discount->name;
		}
	    $different =  strtotime($discount->date_to);
	    
		return array('is_expiried'=>$is_expiried,'code_v'=>$code_v,'different'=>$different);
	}
	
	public function createVoucherFanCoupon(){
		
			$cookie = $this->context->cookie;
			$prefix_type_coupon = "fan";
			$name_module = $this->_name;
			
	    	$code_v = '';
	    	$different = strtotime(date('Y-m-d H:i:s'));
	    	$is_exists_voucher_for_customer = 1;
	    	$data_exists_voucher_for_customer = $this->isExistsVoucherForCustomer();
	    	$is_expiried_voucher = 0;
	    	
	    	$is_logged = isset($cookie->id_customer)?$cookie->id_customer:0;
	    	
	    	if(!$data_exists_voucher_for_customer['is_exist']){
	    	
	    	$is_exists_voucher_for_customer = 0;

	    	$cookie = $this->context->cookie;
		
	    	
	    	if(!$is_logged){
	    	$id_guest = $this->idGuest();
	    	
	    	// id_customer
	    	$sql_customer = 'SELECT id_customer FROM '._DB_PREFIX_.'guest WHERE id_guest='.(int)$id_guest;
			$uid = (int)Db::getInstance()->getValue($sql_customer);		
			} else {
				$uid = (int)$cookie->id_customer;
			}
			
	    	$id_currency = null;
	    	switch (Configuration::get($this->_name.'fdiscount_type'))
				{
					case 1:
						// percent
						$id_discount_type = 1;
						$value = Configuration::get($this->_name.'fpercentage_val');
						$id_currency = (int)$cookie->id_currency;
						break;
					case 2:
						// currency
						$id_discount_type = 2;
						$id_currency = (int)$cookie->id_currency;
						$value = Configuration::get('fsdamount_'.(int)$id_currency);
					break;
					default:
						$id_discount_type = 2;
						$id_currency = (int)$cookie->id_currency;
						$value = Configuration::get('fsdamount_'.(int)$id_currency);
				}
			
			
			$code_module = Configuration::get($this->_name.'fvouchercode');
			$prefix_social = '';
			
			$current_language = (int)$cookie->id_lang;
			
	    	$coupon = (version_compare(_PS_VERSION_, '1.5.0') != -1)? new CartRule() : new Discount();
    		
	    	$gen_pass = Tools::strtoupper(Tools::passwdGen(8));
	    	
	    	
	    	if(version_compare(_PS_VERSION_, '1.5', '>')){
		       	foreach (Language::getLanguages() AS $language){
		       		$coupon->name[(int)$language['id_lang']] = $code_module.$prefix_social.'-'.$gen_pass;
		       	}
		       	$coupon->description = Configuration::get($name_module.'fcoupondesc_'.$current_language);
		       	
	    	} else {
	    		
	    		foreach (Language::getLanguages() AS $language){
	    			$coupon->description[(int)$language['id_lang']] = Configuration::get($name_module.'fcoupondesc_'.(int)$language['id_lang']);
	    		}
	    	}
	    	
	    	$codename = $code_module.$prefix_social.'-'.$gen_pass;
	    	$category = explode(",",Configuration::get($name_module.$prefix_type_coupon.'catbox'));
    		
	    	if (version_compare(_PS_VERSION_, '1.5', '>')) {
				$coupon->code = $codename;
				$type = $id_discount_type == 2? 'reduction_amount' : 'reduction_percent';

				$coupon->$type = ($value);

				$coupon->reduction_currency = (int)($id_currency);
				if(Configuration::get($name_module.$prefix_type_coupon.'isminamount') == true || 
				   Configuration::get($name_module.$prefix_type_coupon.'isminamount') == 1){
					$coupon->minimum_amount = (int)(Configuration::get($prefix_type_coupon.'fsdminamount_'.(int)$id_currency));
					$coupon->minimum_amount_currency = (int)($id_currency);
				}

				if($id_discount_type == 2)
					$coupon->reduction_tax = (int)Configuration::get($name_module.'taxf');
				else
                    $coupon->reduction_tax = (int)Configuration::get($name_module.'taxper');
                    
                    
				if (sizeof($category)>0) {
						$coupon->product_restriction = 1;
						
						if($id_discount_type == 1){
							$coupon->reduction_product = -2;
						}
				}
					
					
					
			} else {
					$coupon->name = $codename;
					$coupon->id_discount_type = $id_discount_type == 2? 2 : 1;

					if (version_compare(_PS_VERSION_ , '1.3.0.4') != -1) {
						$coupon->id_currency = (int)($id_currency);
					}
					
					$coupon->cart_display = 0;
					
					// fo ps 1.3 - 1.4
		    		if(Configuration::get($name_module.$prefix_type_coupon.'isminamount') == true || 
		    		   Configuration::get($name_module.$prefix_type_coupon.'isminamount') == 1){
							if(!$id_currency) $id_currency = 1;
							$coupon->minimal = Configuration::get($prefix_type_coupon.'fsdminamount_'.(int)$id_currency);
					}
				}
			
	    	
			// shared data
			$coupon->value = ($value);
			$coupon->id_customer = $uid;
			$coupon->quantity = 1;
			$coupon->quantity_per_user = 1;
			
			// cumulable
			// for ps 1.5.6.0 
			if (version_compare(_PS_VERSION_, '1.5', '>')) 
	        	$coupon->cart_rule_restriction = ((Configuration::get($name_module.$prefix_type_coupon.'cumulativeother'))==0?1:0);
	        	 
			$coupon->cumulable = (int)(Configuration::get($name_module.$prefix_type_coupon.'cumulativeother'));
			
			$coupon->cumulable_reduction = (int)(Configuration::get($name_module.$prefix_type_coupon.'cumulativereduc'));
			// cumulable
			
			
			$coupon->active = 1;
			
			$start_date = date('Y-m-d H:i:s');
			$coupon->date_from = $start_date;
				
			$different = strtotime(date('Y-m-d H:i:s')) + Configuration::get($this->_name.'fsdvvalid')*24*60*60;
			$end_date = date('Y-m-d H:i:s',$different);
			$coupon->date_to = $end_date;
			
			
			$is_voucher_create = false;
	        if (version_compare(_PS_VERSION_, '1.5', '>')) {
	        	
	        	$is_voucher_create = $coupon->add(true, false);
	        	
	        	if ($is_voucher_create && sizeof($category)>0) 
	        	{
	        		// add a cart rule
					$is_voucher_create = $this->addProductRule($coupon->id, 1, 'categories', $category);
				}
	        } else {
	        	// create voucher and add a cart rule (if exists)
	        	$is_voucher_create = $coupon->add(true, false, (sizeof($category)>0?$category:null));
	        }
	        
			
			if (!$is_voucher_create){
			    Db::getInstance()->Execute('ROLLBACK');
			}
	         
	        // insert into facebookcustomer
	         $id_discount = $coupon->id;
	         $ip_address = $_SERVER['REMOTE_ADDR'];
	         $id_guest = $data_exists_voucher_for_customer['id_guest'];
	         $id_customer = $data_exists_voucher_for_customer['id_customer'];
	         $sql = 'INSERT into `'._DB_PREFIX_.'facebookfancoupon` SET
							   `id_discount` = '.$id_discount.', 
							   `ip_adress` = "'.$ip_address.'",
							   `id_guest` = '.$id_guest.',
							   `id_customer` = '.$id_customer.'
							   ';
			 Db::getInstance()->Execute($sql);
	        
			 $code_v = $codename;
			 
    		} else {
    			$id_discount = $data_exists_voucher_for_customer['is_exist'];
	    		
    			$data_expiried = $this->expiriedVoucher($id_discount);
    			if($data_expiried['is_expiried'] == 1){
    				$is_expiried_voucher = 1;
    				$code_v = $data_expiried['code_v'];
	    			$different =  $data_expiried['different'];
    			}
    			
    			if($is_expiried_voucher == 0){ 
	    			if(version_compare(_PS_VERSION_, '1.5', '>')){
						$discount = new CartRule($id_discount);
	    			} else {
						$discount = new Discount($id_discount);
	    			}
	    			
	    			if(version_compare(_PS_VERSION_, '1.5', '>')){
						$code_v = $discount->code;
					} else {
						$code_v = $discount->name;
					}
	    			$different =  strtotime($discount->date_to);
	    		}
	    		Db::getInstance()->Execute('ROLLBACK');
    		}
	         
    		Db::getInstance()->Execute('COMMIT');
    		
    		if($is_logged != 0 && $is_voucher_create){
    			
    		$data_voucher = array('voucher_code'=>$code_v,'date_until' => date('d/m/Y H:i:s',$different));
	        	
    		$customer_data = $this->getInfoAboutCustomer(array('id_customer'=>$is_logged));
		
    		$this->sendNotificationCreatedVoucher(
    													array(
    														  'email_customer'=>$customer_data['email'],
    														  'data_voucher'=>$data_voucher
    														  )
    												  );
			}
			
	        return array('voucher_code'=>$code_v,'date_until' => date('d/m/Y H:i:s',$different),
	        			 'is_exists_voucher_for_customer' => $is_exists_voucher_for_customer,
	        			 'is_expiried_voucher' => $is_expiried_voucher);
	}
	
public function sendNotificationCreatedVoucher($data = null){
		
			include_once(dirname(__FILE__).'/../facebookcandvc.php');
			$obj = new facebookcandvc();
			$data_translate = $obj->translateFB(array('fan'=>1));
			
			$email_customer = $data['email_customer'];
			
			$firsttext = $data_translate['firsttext'];
			$discountvalue = $data_translate['discountvalue'];
			
			$secondtext = $data_translate['secondtext'];
			$threetext = $data_translate['threetext'];
			$voucher_code = $data['data_voucher']['voucher_code'];
			$date_until = $data['data_voucher']['date_until'];
			
			$cookie = $this->context->cookie;
			
			/* Email generation */
			$templateVars = array(
				'{firsttext}' => $firsttext,
				'{discountvalue}' => $discountvalue,
				'{secondtext}' => $secondtext,
				'{threetext}' => $threetext,
				'{voucher_code}' => $voucher_code,
				'{date_until}' => $date_until
			);
			$id_lang = (int)($cookie->id_lang);	

			
			
			/* Email sending */
			Mail::Send($id_lang, 'voucher', $data_translate['review_voucher'], $templateVars, 
				$email_customer, 'Voucher Form', NULL, NULL,
				NULL, NULL, dirname(__FILE__).'/../mails/');
		
		
	}
	
  public function getIdShop(){
		$id_shop = 0;
		if(version_compare(_PS_VERSION_, '1.5', '>'))
			$id_shop = Context::getContext()->shop->id;
		return $id_shop;
	} 
    
public function getInfoAboutCustomer($data=null){
		$id_customer = (int) $data['id_customer'];
		//get info about customer
		if(version_compare(_PS_VERSION_, '1.5', '>')){
		$sql = '
	        	SELECT * FROM `'._DB_PREFIX_.'customer` 
		        WHERE `active` = 1 AND `id_customer` = \''.$id_customer.'\'  
		        AND `deleted` = 0 AND id_shop = '.$this->getIdShop().'  '.(defined(_MYSQL_ENGINE_)?"AND `is_guest` = 0":"").'
		        ';
		} else {
		$sql = '
	        	SELECT * FROM `'._DB_PREFIX_.'customer` 
		        WHERE `active` = 1 AND `id_customer` = \''.$id_customer.'\'  
		        AND `deleted` = 0 '.(defined(_MYSQL_ENGINE_)?"AND `is_guest` = 0":"").'
		        ';
		}
		$result = Db::getInstance()->GetRow($sql);
		$email = '';
		if($result){
		$lastname = Tools::strtoupper(Tools::substr($result['lastname'],0,1));
		$firstname = $result['firstname'];
		$customer_name = $firstname . " " . $lastname;
		$email = $result['email'];
		} else {
			$customer_name = "Guest";
		}

		return array('customer_name' => $customer_name,'email'=>$email);
	}
    
public function addProductRule($iCartRuleId, $iQuantity, $sType, array $aIds)
	{
		$bInsert = false;

		// set transaction
		Db::getInstance()->Execute('BEGIN');

		$sQuery = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule_group (id_cart_rule, quantity) VALUES('
			. $iCartRuleId . ', ' . $iQuantity . ')';

		// only if group rule is added
		if (Db::getInstance()->Execute($sQuery)) {

			$sQuery = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule (id_product_rule_group, type) VALUES('
				. Db::getInstance()->Insert_ID() . ', "' . $sType . '")';

			// only if product rule is added
			if (Db::getInstance()->Execute($sQuery)) {

				if (!empty($aIds)) {
					$bInsert = true;

					$iLastInsertId = Db::getInstance()->Insert_ID();

					foreach ($aIds as $iId) {
						$sQuery = 'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule_value (id_product_rule, id_item) VALUES('
							. $iLastInsertId . ', ' . $iId . ')';

						if (!Db::getInstance()->Execute($sQuery)) {
							$bInsert = false;
						}
					}
				}
			}
		}
		// commit or rollback transaction
		$bInsert = ($bInsert)? Db::getInstance()->Execute('COMMIT') : Db::getInstance()->Execute('ROLLBACK');

		return $bInsert;
	}
}