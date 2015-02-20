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

class facebookcandvchelp extends Module{

  private $_width = 400;
  private $_height = 400;
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
      global $smarty, $cookie;
      $this->context = new StdClass();
      $this->context->smarty = $smarty;
      $this->context->cookie = $cookie;
    }
  }

  public function login($data)
    {
    $appid = $data['appid'];
    $secretkey = $data['secretkey'];

        $me = null;

        $cookie = $this->get_facebook_cookie($appid, $secretkey);

        $me = Tools::jsonDecode($this->getFbData('https://graph.facebook.com/me?access_token=' . $cookie['access_token']));

        $me = (array)$me;

      if(empty($me['email'])){
          die('You don\'t have primary email in your Facebook Account. Go to Facebook -> Settings -> General -> Email and set Primary email!');
        }

        if(version_compare(_PS_VERSION_, '1.5', '>')){
          $id_shop = Context::getContext()->shop->id;
        } else {
          $id_shop = 0;
        }

        if (is_array($me)) {
      $sql= 'SELECT `customer_id`
          FROM `'._DB_PREFIX_.'facebook_customer_fb`
          WHERE `fb_id` = '.$me['id'].' AND `id_shop` = '.$id_shop.'
          LIMIT 1';
      $result = Db::getInstance()->ExecuteS($sql);

      if(sizeof($result)>0)
        $customer_id = $result[0]['customer_id'];
      else
        $customer_id = 0;
    }

    $exists_mail = 0;
    //chek for dublicate
    if(!empty($me['email'])){
      if(version_compare(_PS_VERSION_, '1.5', '>')){
      $sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer`
              WHERE `active` = 1 AND `email` = \''.pSQL($me['email']).'\'
              AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").' AND `id_shop` = '.$id_shop.'';
      } else {
      $sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer`
              WHERE `active` = 1 AND `email` = \''.pSQL($me['email']).'\'
              AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").'';
      }
      $result_exists_mail = Db::getInstance()->GetRow($sql);
      if($result_exists_mail)
        $exists_mail = 1;
    }

    $auth = 0;
       if(($customer_id && $exists_mail) || ($exists_mail == 1)){
      $auth = 1;
    }


    if((!isset($customer_id) &&  $exists_mail) || ($exists_mail == 1)){
      $sql = 'SELECT * FROM `'._DB_PREFIX_.'facebook_customer_fb`
              WHERE customer_id = '.$result_exists_mail['id_customer'].'
              AND fb_id = '.$me['id'].'
               AND id_shop = '.$id_shop.'';

      $result_exists_fbcustomer = Db::getInstance()->GetRow($sql);
      if(!$result_exists_fbcustomer){
        // insert record into customerXfacebook table
        $sql = 'INSERT into `'._DB_PREFIX_.'facebook_customer_fb` SET
                 customer_id = '.$result_exists_mail['id_customer'].',
                 fb_id = '.$me['id'].',
                 id_shop = '.$id_shop.' ';

          $result = Db::getInstance()->Execute($sql);

      }
      $auth = 1;
    }
    $data_voucher = array();
    if($auth){
      $cookie = $this->context->cookie;
      //  authentication
      if(version_compare(_PS_VERSION_, '1.5', '>')){
      $sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer`
              WHERE `active` = 1 AND `email` = \''.pSQL($me['email']).'\'
              AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").' AND `id_shop` = '.$id_shop.'
              ';
      } else {
      $sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer`
              WHERE `active` = 1 AND `email` = \''.pSQL($me['email']).'\'
              AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").'
              ';
      }
      $result = Db::getInstance()->GetRow($sql);

      if ($result){
        $customer = new Customer();

        $customer->id = $result['id_customer'];
          foreach ($result AS $key => $value)
              if (key_exists($key, $customer))
                  $customer->{$key} = $value;
        }

        $cookie->id_customer = (int)($customer->id);
        $cookie->customer_lastname = $customer->lastname;
        $cookie->customer_firstname = $customer->firstname;
        $cookie->logged = 1;
        $cookie->passwd = $customer->passwd;
        $cookie->email = $customer->email;
        if (Configuration::get('PS_CART_FOLLOWING') AND (empty($cookie->id_cart)
          OR Cart::getNbProducts($cookie->id_cart) == 0))
            $cookie->id_cart = (int)(Cart::lastNoneOrderedCart((int)($customer->id)));
        if(version_compare(_PS_VERSION_, '1.5', '>')){
          Hook::exec('authentication');
        } else {
          Module::hookExec('authentication');
        }
      } else {
        $fb_id = $me['id'];

        //// create new user ////
        $gender = ($me['gender'] == 'male')?1:2;
        $id_default_group = 1;
        $firstname = pSQL($me['first_name']);
        $lastname = pSQL($me['last_name']);
        $email = $me['email'];

        // generate passwd
        srand((double)microtime()*1000000);
        $passwd = Tools::substr(uniqid(rand()),0,12);
        $real_passwd = $passwd;
        $passwd = md5(pSQL(_COOKIE_KEY_.$passwd));

        $last_passwd_gen = date('Y-m-d H:i:s', strtotime('-'.Configuration::get('PS_PASSWD_TIME_FRONT').'minutes'));
        $secure_key = md5(uniqid(rand(), true));
        $active = 1;
        $date_add = date('Y-m-d H:i:s'); //'2011-04-04 18:29:15';
        $date_upd = $date_add;

        if(Tools::strlen($me['first_name'])==0 || Tools::strlen($me['last_name']) == 0){
          die('Empty First Name and Last Name!');
        }

        if(version_compare(_PS_VERSION_, '1.5', '>')){

          $id_shop_group = Context::getContext()->shop->id_shop_group;

          $sql = 'insert into `'._DB_PREFIX_.'customer` SET
                id_shop = '.$id_shop.', id_shop_group = '.$id_shop_group.',
                id_gender = '.$gender.', id_default_group = '.$id_default_group.',
                firstname = \''.$firstname.'\', lastname = \''.$lastname.'\',
                email = \''.$email.'\', passwd = \''.$passwd.'\',
                last_passwd_gen = \''.$last_passwd_gen.'\',
                secure_key = \''.$secure_key.'\', active = '.$active.',
                optin = \'1\', newsletter = \'1\', newsletter_date_add = \''.$date_add.'\',
                date_add = \''.$date_add.'\', date_upd = \''.$date_upd.'\' ';

        } else {

        $sql = 'insert into `'._DB_PREFIX_.'customer` SET
                 id_gender = '.$gender.', id_default_group = '.$id_default_group.',
                 firstname = \''.$firstname.'\', lastname = \''.$lastname.'\',
                 email = \''.$email.'\', passwd = \''.$passwd.'\',
                 last_passwd_gen = \''.$last_passwd_gen.'\',
                 secure_key = \''.$secure_key.'\', active = '.$active.',
                 date_add = \''.$date_add.'\', date_upd = \''.$date_upd.'\' ';

        }

        $result = Db::getInstance()->Execute($sql);
        $insert_id = Db::getInstance()->Insert_ID();



        // insert record in customer group
        $id_group = 1;
        $sql = 'INSERT into `'._DB_PREFIX_.'customer_group` SET
                 id_customer = '.$insert_id.', id_group = '.$id_group.' ';
        $result = Db::getInstance()->Execute($sql);



        // insert record into customerXfacebook table
        $sql_exists= 'SELECT `customer_id`
            FROM `'._DB_PREFIX_.'facebook_customer_fb`
            WHERE `fb_id` = '.$me['id'].' AND `id_shop` = '.$id_shop.'
            LIMIT 1';
        $result_exists = Db::getInstance()->ExecuteS($sql_exists);
        if(sizeof($result_exists)>0)
          $customer_id = $result_exists[0]['customer_id'];
        else
          $customer_id = 0;

        if($customer_id){
          $sql_del = 'DELETE FROM `'._DB_PREFIX_.'facebook_customer_fb` WHERE `customer_id` = '.$customer_id.' AND `id_shop` = '.$id_shop.'';
          $result = Db::getInstance()->Execute($sql_del);

        }

          $sql = 'INSERT into `'._DB_PREFIX_.'facebook_customer_fb` SET
                   customer_id = '.$insert_id.', fb_id = '.$fb_id.', id_shop = '.$id_shop.' ';
          $result = Db::getInstance()->Execute($sql);


        //// end create new user ///


        // auth customer
        $cookie = $this->context->cookie;

        $customer = new Customer();
            $authentication = $customer->getByEmail(trim($email), trim($real_passwd));
            if (!$authentication OR !$customer->id) {
              die('Authentication failed!');
            }
            else
            {
                $cookie->id_customer = (int)($customer->id);
                $cookie->customer_lastname = $customer->lastname;
                $cookie->customer_firstname = $customer->firstname;
                $cookie->logged = 1;
                $cookie->passwd = $customer->passwd;
                $cookie->email = $customer->email;
                if (Configuration::get('PS_CART_FOLLOWING') AND (empty($cookie->id_cart) OR Cart::getNbProducts($cookie->id_cart) == 0))
                    $cookie->id_cart = (int)(Cart::lastNoneOrderedCart((int)($customer->id)));

              if(version_compare(_PS_VERSION_, '1.5', '>')){
            Hook::exec('authentication');
          } else {
                  Module::hookExec('authentication');
          }
            }


        @Mail::Send((int)($cookie->id_lang), 'account', $this->l('Welcome!'),
                  array('{firstname}' => $customer->firstname,
                      '{lastname}' => $customer->lastname,
                      '{email}' => $customer->email,
                      '{passwd}' => $real_passwd),
                      $customer->email,
                      $customer->firstname.' '.$customer->lastname);


          if(Configuration::get('facebookcandvcvis_on') == 1){
            $data_voucher = $this->createVoucher(array('customer_id'=>(int)($customer->id)));


            $this->sendNotificationCreatedVoucher(
                                array(
                                    'email_customer'=>$customer->email,
                                    'data_voucher'=>$data_voucher
                                    )
                                );
          }


      }

      return array('data'=>$data_voucher,'auth'=>$auth);

    }

  public function sendNotificationCreatedVoucher($data = null){

      include_once(dirname(__FILE__).'/../facebookcandvc.php');
      $obj = new facebookcandvc();
      $data_translate = $obj->translateFB();

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

   public function createVoucher($data){


        $prefix_type_coupon = "";
      $name_module = $this->_name;

        $uid = $data['customer_id'];

        $cookie = $this->context->cookie;

      $id_currency = null;
        switch (Configuration::get($name_module.'discount_type'))
        {
          case 1:
            // percent
            $id_discount_type = 1;
            $value = Configuration::get($name_module.'percentage_val');
            $id_currency = (int)$cookie->id_currency;
            break;
          case 2:
            // currency
            $id_discount_type = 2;
            $id_currency = (int)$cookie->id_currency;
            $value = Configuration::get('sdamount_'.(int)$id_currency);
          break;
          default:
            $id_discount_type = 2;
            $id_currency = (int)$cookie->id_currency;
            $value = Configuration::get('sdamount_'.(int)$id_currency);
        }



      $code_module = Configuration::get($name_module.'vouchercode');
      $prefix_social = '';

      $current_language = (int)$cookie->id_lang;

        $coupon = (version_compare(_PS_VERSION_, '1.5.0') != -1)? new CartRule() : new Discount();

        $gen_pass = Tools::strtoupper(Tools::passwdGen(8));


        if(version_compare(_PS_VERSION_, '1.5', '>')){
            foreach (Language::getLanguages() AS $language){
              $coupon->name[(int)$language['id_lang']] = $code_module.$prefix_social.'-'.$gen_pass;
            }
            $coupon->description = Configuration::get($name_module.'coupondesc_'.$current_language);

        } else {

          foreach (Language::getLanguages() AS $language){
            $coupon->description[(int)$language['id_lang']] = Configuration::get($name_module.'coupondesc_'.(int)$language['id_lang']);
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
          $coupon->reduction_tax = (int)Configuration::get($name_module.'taxc');
        else
                    $coupon->reduction_tax = (int)Configuration::get($name_module.'taxpercc');

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

      $different = strtotime(date('Y-m-d H:i:s')) + Configuration::get($this->_name.'sdvvalid')*24*60*60;
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

           $code_v = $codename;

        Db::getInstance()->Execute('COMMIT');



        return array('voucher_code'=>$code_v,'date_until' => date('d/m/Y H:i:s',$different));


   }



  private function get_facebook_cookie($app_id, $app_secret)
    {
        if ($_COOKIE['fbsr_' . $app_id] != '') {
            return $this->get_new_facebook_cookie($app_id, $app_secret);
        } else {
            return $this->get_old_facebook_cookie($app_id, $app_secret);
        }
    }

   private function get_old_facebook_cookie($app_id, $app_secret)
    {
        $args = array();
        parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
        ksort($args);
        $payload = '';
        foreach ($args as $key => $value) {
            if ($key != 'sig') {
                $payload .= $key . '=' . $value;
            }
        }
        if (md5($payload . $app_secret) != $args['sig']) {
            return array();
        }
        return $args;
    }

    private function get_new_facebook_cookie($app_id, $app_secret)
    {
        $signed_request = $this->parse_signed_request($_COOKIE['fbsr_' . $app_id], $app_secret);
        // $signed_request should now have most of the old elements
        $signed_request['uid'] = $signed_request['user_id']; // for compatibility
        if (!is_null($signed_request)) {
            // the cookie is valid/signed correctly
            // lets change "code" into an "access_token"
            $url = "https://graph.facebook.com/oauth/access_token?client_id=$app_id&redirect_uri=&client_secret=$app_secret&code=$signed_request[code]";
            $access_token_response = $this->getFbData($url);
      parse_str($access_token_response);
      $signed_request['access_token'] = $access_token;
      $signed_request['expires'] = time() + $expires;
        }

        return $signed_request;
    }

    private function parse_signed_request($signed_request, $secret)
    {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        // decode the data
        $sig = $this->base64_url_decode($encoded_sig);
        $data = Tools::jsonDecode($this->base64_url_decode($payload), true);

        if (Tools::strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
            echo('Unknown algorithm. Expected HMAC-SHA256');
            return null;
        }

        // check sig
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
            echo('Bad Signed JSON signature!');
            return null;
        }

        return $data;
    }

    private function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

  private function getFbData($url)
  {
    $data = null;

    if (ini_get('allow_url_fopen') && function_exists('file_get_contents')) {
      $data = file_get_contents($url);
    } else {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $data = curl_exec($ch);
    }
    return $data;
  }

  public function getImages($data = null){
      $admin = isset($data['admin'])?$data['admin']:0;

      $smarty = $this->context->smarty;

      if(version_compare(_PS_VERSION_, '1.5', '>')){
            $id_shop = Context::getContext()->shop->id;
           } else {
            $id_shop = 0;
           }

           if(!$admin){

           $_http_host = '';
           if(defined('_MYSQL_ENGINE_')){
        $_http_host = isset($smarty->tpl_vars['base_dir_ssl']->value)?$smarty->tpl_vars['base_dir_ssl']->value:$smarty->tpl_vars['base_dir']->value;
       } else {
          $_http_host = isset($smarty->_tpl_vars['base_dir_ssl'])?$smarty->_tpl_vars['base_dir_ssl']:$smarty->_tpl_vars['base_dir'];
       }

      if($_http_host == 'http://' || $_http_host == 'http:///'
           || $_http_host == 'https://' || $_http_host == 'https:///'){
            if (Configuration::get('PS_SSL_ENABLED') == 1)
          $type_url = "https://";
        else
          $type_url = "http://";
           $_http_host = $type_url.$_SERVER['HTTP_HOST']."/";
           }

           } else {
            $_http_host = "../";
           }
      // image in block "Sign in with Facebook"

      $sql = 'SELECT * FROM `'._DB_PREFIX_   .'facebook_img`
              WHERE `type` = 1 AND `id_shop` = '.$id_shop.'';
      $data_block_sign_in_with_facebook = Db::getInstance()->GetRow($sql);
      $img_block = (isset($data_block_sign_in_with_facebook['img'])?$data_block_sign_in_with_facebook['img']:'');
      $img_block_path = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR."facebookcandvc".DIRECTORY_SEPARATOR.$img_block;

      $uploaded_img = 0;
      if(Tools::strlen($img_block)>0){
        if(@filesize($img_block_path)>0){
          $uploaded_img = 1;
        }
      }
      if($uploaded_img){
        $block_sign_in_with_facebook = $_http_host."upload/facebookcandvc/".$img_block;
      } else {
        $block_sign_in_with_facebook = $_http_host.'modules/facebookcandvc/i/facebook.png';
      }


      // image on Authentication page

      $sql = 'SELECT * FROM `'._DB_PREFIX_   .'facebook_img`
              WHERE `type` = 2 AND `id_shop` = '.$id_shop.'';
      $data_auth = Db::getInstance()->GetRow($sql);
      $img_blockauth = (isset($data_auth['img'])?$data_auth['img']:'');
      $img_block_pathauth = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR."facebookcandvc".DIRECTORY_SEPARATOR.$img_blockauth;

      $uploaded_imgauth = 0;
      if(Tools::strlen($img_blockauth)>0){
        if(@filesize($img_block_pathauth)>0){
          $uploaded_imgauth = 1;
        }
      }
      if($uploaded_imgauth){
        $block_auth = $_http_host."upload/facebookcandvc/".$img_blockauth;
      } else {
        $block_auth = $_http_host.'modules/facebookcandvc/i/facebook_auth.png';
      }

      // image in the block with a link Log In

      $sql = 'SELECT * FROM `'._DB_PREFIX_   .'facebook_img`
              WHERE `type` = 3 AND `id_shop` = '.$id_shop.'';
      $data_login = Db::getInstance()->GetRow($sql);
      $img_blocklogin = (isset($data_login['img'])?$data_login['img']:'');
      $img_block_pathlogin = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR."facebookcandvc".DIRECTORY_SEPARATOR.$img_blocklogin;

      $uploaded_imglogin = 0;
      if(Tools::strlen($img_blocklogin)>0){
        if(@filesize($img_block_pathlogin)>0){
          $uploaded_imglogin = 1;
        }
      }
      if($uploaded_imglogin){
        $block_login = $_http_host."upload/facebookcandvc/".$img_blocklogin;
      } else {
        $block_login = $_http_host.'modules/facebookcandvc/i/facebook_login.png';
      }

      return array('block_sign_in_with_facebook'=>$block_sign_in_with_facebook,'img_block'=>$img_block,
             'block_auth'=>$block_auth, 'img_blockauth'=>$img_blockauth,
             'block_blocklogin'=>$block_login, 'img_blocklogin'=>$img_blocklogin);
  }


  public function saveImage($data = null){

    $error = 0;
    $error_text = '';
    $custom_type_img = $data['type'];

    $files = $_FILES['post_image_'.$custom_type_img];

    ############### files ###############################
    if(!empty($files['name']))
      {
          if(!$files['error'])
          {
          $type_one = $files['type'];
          $ext = explode("/",$type_one);

          if(strpos('_'.$type_one,'image')<1)
          {
            $error_text = $this->l('Invalid file type, please try again!');
            $error = 1;

          }elseif(!in_array($ext[1],array('png','x-png','gif','jpg','jpeg','pjpeg'))){
            $error_text = $this->l('Wrong file format, please try again!');
            $error = 1;

          } else {



              $data_img = $this->getImages(array('admin'=>1));
              if($custom_type_img == "block"){
                $type_page = 1;
                $img_old_del = $data_img['img_block'];

              } elseif($custom_type_img == "auth"){
                $type_page = 2;
                $img_old_del = $data_img['img_auth'];
              } elseif($custom_type_img == "login"){
                $type_page = 3;
                $img_old_del = $data_img['img_blocklogin'];
              }

              if(Tools::strlen($img_old_del)>0){
                // delete old img
                unlink(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$img_old_del);
              }


              srand((double)microtime()*1000000);
            $uniq_name_image = uniqid(rand());
            $type_one = Tools::substr($type_one,6,Tools::strlen($type_one)-6);
            $filename = $uniq_name_image.'.'.$type_one;

            move_uploaded_file($files['tmp_name'], dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$filename);

            /*$this->copyImage(array('dir_without_ext'=>dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$uniq_name_image,
                        'name'=>dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$filename)
                    );*/


            $img_return = $uniq_name_image.'.jpg';
            $img_return = $filename;

              $this->_updateImgDB(array('type_page' => $type_page,
                            'img' => $img_return
                             )
                        );

          }
        }

      }

    return array('error' => $error,
           'error_text' => $error_text);


  }

  private function _updateImgDB($data = null){

    $type_page = $data['type_page'];
    $img = $data['img'];

    if(version_compare(_PS_VERSION_, '1.5', '>')){
          $id_shop = Context::getContext()->shop->id;
      } else {
          $id_shop = 0;
      }


    $sql = 'SELECT count(*) as count FROM `'._DB_PREFIX_   .'facebook_img`
              WHERE `type` = '.$type_page.' AND `id_shop` = '.$id_shop.'';
    $data_exists = Db::getInstance()->GetRow($sql);

    if($data_exists['count']){
      // delete and insert
      $sql = 'DELETE FROM `'._DB_PREFIX_.'facebook_img`
               WHERE `type` = '.$type_page.'
               AND `id_shop` = '.$id_shop.'';
        Db::getInstance()->Execute($sql);

    } else {
      // only insert new
    }
    // insert
    $sql = 'INSERT INTO `'._DB_PREFIX_.'facebook_img`
               SET `type` = '.$type_page.',
                   `id_shop` = '.$id_shop.',
                   `img` = \''.pSQL($img).'\'
                   ';

      Db::getInstance()->Execute($sql);

  }

  public function deleteImage($data){
    $type = $data['type'];

    if(version_compare(_PS_VERSION_, '1.5', '>')){
            $id_shop = Context::getContext()->shop->id;
        } else {
            $id_shop = 0;
        }

       $sql = 'DELETE FROM `'._DB_PREFIX_.'facebook_img`
               WHERE `type` = '.$type.'
               AND `id_shop` = '.$id_shop.'';
        Db::getInstance()->Execute($sql);

  }

  public function getCustomers($data){

      $start = $data['start'];
    $step = $data['step'];
    if(version_compare(_PS_VERSION_, '1.5', '>')){
            $id_shop = Context::getContext()->shop->id;
            $name_shop = Context::getContext()->shop->name;
        } else {
            $id_shop = 0;
            $name_shop = '';
        }


    $sql = 'SELECT customer_id as user_id
                 FROM `'. _DB_PREFIX_ . 'facebook_customer_fb`
                 WHERE id_shop = '.$id_shop.'
                 ORDER BY `customer_id` DESC LIMIT '.$start.' ,'.$step.'';
    $_data_ids = Db::getInstance()->ExecuteS($sql);

    $user_data = array();
    foreach($_data_ids as $_item_id){
      $uid = $_item_id['user_id'];

      // get info about user //
      $sql = 'SELECT c.id_customer as id,
               c.firstname,
               c.lastname
               FROM  `'. _DB_PREFIX_ . 'customer` c
               WHERE c.id_customer = '.$uid;
      $info_user = Db::getInstance()->ExecuteS($sql);

      $user_data[] = $info_user[0];
    }

    $_data_tmp = $user_data;

    $_data = array();

    foreach($_data_tmp as $_item){
      $_id_customer = $_item['id'];

      $sql_is_exist = 'select COUNT(*) as count from `'. _DB_PREFIX_ . 'facebook_customer_fb`
                  where customer_id = '.$_id_customer.' AND id_shop = '.$id_shop.'';

      $data_exist_user = Db::getInstance()
      ->getRow($sql_is_exist);

      if($data_exist_user['count']>0){
      $_item['name_shop'] = $name_shop;
      $_data[] = $_item;
      }

    }

    $sql_count = 'SELECT distinct c.id_customer,
             c.firstname,
             c.lastname
             FROM  `'. _DB_PREFIX_ . 'customer` c';
    $_data_tmp = Db::getInstance()->ExecuteS($sql_count);
    $count_all = 0;
    foreach($_data_tmp as $_item){
      $_id_customer = $_item['id_customer'];

      $data_exist_user = Db::getInstance()
      ->getRow('select COUNT(*) as count from `'. _DB_PREFIX_ . 'facebook_customer_fb`
                  where customer_id = '.$_id_customer.' AND id_shop = '.$id_shop.'');

      if($data_exist_user['count']>0){
        $count_all++;
      }

    }
    return array('data' => $_data, 'count_all' => $count_all );


    }

  public function getCustomersSearch($data){

      $search_query = trim(htmlspecialchars(strip_tags($data['search_query'])));

      if(version_compare(_PS_VERSION_, '1.5', '>')){
            $id_shop = Context::getContext()->shop->id;
            $name_shop = Context::getContext()->shop->name;
        } else {
            $id_shop = 0;
            $name_shop = '';
        }


      // get info about user //

      if(version_compare(_PS_VERSION_, '1.5', '>')){

    $sql = 'SELECT c.id_customer as id
             FROM  `'. _DB_PREFIX_ . 'customer` c
             WHERE c.active = 1 AND c.deleted = 0 AND c.id_shop =  '.$id_shop.' AND
             (
              LOWER(c.lastname) LIKE BINARY LOWER(\'%'.$search_query.'%\')
             OR
                LOWER(c.firstname) LIKE BINARY LOWER(\'%'.$search_query.'%\')
                )';
    } else {
        $sql = 'SELECT c.id_customer as id
             FROM  `'. _DB_PREFIX_ . 'customer` c
             WHERE c.active = 1 AND c.deleted = 0 AND
             (
              LOWER(c.lastname) LIKE BINARY LOWER(\'%'.$search_query.'%\')
             OR
                LOWER(c.firstname) LIKE BINARY LOWER(\'%'.$search_query.'%\')
                )';
      }
    $info_ids = Db::getInstance()->ExecuteS($sql);
    $ids_exists = array();
    foreach($info_ids as $_v_ids)
    $ids_exists[] = $_v_ids['id'];
    $ids_exists = implode(",",$ids_exists);
    if(Tools::strlen($ids_exists)==0)
      $ids_exists = 0;

    $sql = 'SELECT customer_id as user_id
                 FROM `'. _DB_PREFIX_ . 'facebook_customer_fb`
                 WHERE `customer_id` IN('.$ids_exists.') AND id_shop = '.$id_shop.'
                 ORDER BY `customer_id` DESC';
    $_data_ids = Db::getInstance()->ExecuteS($sql);

    $user_data = array();
    foreach($_data_ids as $_item_id){
      $uid = $_item_id['user_id'];

      // get info about user //
      $sql = 'SELECT c.id_customer as id,
               c.firstname,
               c.lastname
               FROM  `'. _DB_PREFIX_ . 'customer` c
               WHERE c.id_customer = '.$uid;
      $info_user = Db::getInstance()->ExecuteS($sql);

      $user_data[] = $info_user[0];
    }

    $_data_tmp = $user_data;

    $_data = array();

    foreach($_data_tmp as $_item){
      $_id_customer = $_item['id'];

      $sql_is_exist = 'select COUNT(*) as count from `'. _DB_PREFIX_ . 'facebook_customer_fb`
                  where customer_id = '.$_id_customer.' AND id_shop = '.$id_shop.'';

      $data_exist_user = Db::getInstance()
      ->getRow($sql_is_exist);

      if($data_exist_user['count']>0){
      $_item['name_shop'] = $name_shop;

      $_data[] = $_item;
      }

    }

    $sql_count = 'SELECT distinct c.id_customer,
             c.firstname,
             c.lastname
             FROM  `'. _DB_PREFIX_ . 'customer` c
             WHERE c.id_customer IN('.$ids_exists.')';
    $_data_tmp = Db::getInstance()->ExecuteS($sql_count);
    $count_all = 0;
    foreach($_data_tmp as $_item){
      $_id_customer = $_item['id_customer'];

      $data_exist_user = Db::getInstance()
      ->getRow('select COUNT(*) as count from `'. _DB_PREFIX_ . 'facebook_customer_fb`
                  where customer_id = '.$_id_customer.' AND id_shop = '.$id_shop.'');

      if($data_exist_user['count']>0){
        $count_all++;
      }

    }
    return array('data' => $_data, 'count_all' => $count_all );


    }

  public function PageNav($start,$count,$step, $_data =null )
  {

    $res = '';
    $currentIndex = $_data['currentIndex'];
    $item = $_data['item'];
    $token = $_data['token'];
    $text_page = $_data['text_page'];
    $start1 = $start;


    $res .= '<span>';
       if($start > 0){
         $res .= '<input type="image" onclick="window.location.href=\''.$currentIndex.'&page'.$item.$token.'\'" src="'._PS_ADMIN_IMG_.'list-prev2.gif">
            &nbsp;';

         $res .= '<input type="image" onclick="window.location.href=\''.$currentIndex.'&page'.$item.'='.((int)$start - (int)$step).$token.'\'" src="'._PS_ADMIN_IMG_.'list-prev.gif">&nbsp;&nbsp;';
        }

        $res .= ''.$text_page.' <b>'.((int)($start1 / $step) + 1).'</b> / '.ceil($count/$step).'';

         if($start + $step < $count) {
            $res .= '&nbsp;&nbsp;<input type="image" onclick="window.location.href=\''.$currentIndex.'&page'.$item.'='.((int)$start + (int)$step).$token.'\'" src="'._PS_ADMIN_IMG_.'list-next.gif">
            &nbsp;
            <input type="image" onclick="window.location.href=\''.$currentIndex.'&page'.$item.'='.((ceil($count/$step)*$step)-$step).$token.'\'" src="'._PS_ADMIN_IMG_.'list-next2.gif">';
         }
    $res .= '</span>';


    return $res;
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
