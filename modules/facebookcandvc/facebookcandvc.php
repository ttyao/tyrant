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

class facebookcandvc extends Module
{
  private $_step = 25;
  private $_is15;
  private $standart_text;
  private $title_standart_text;
  private $_http_referer;
  private $_is16;
  private $_multiple_lang;
  private $_like_button_settings;

  public function __construct()
  {
    $this->name = 'facebookcandvc';
    $this->version = '1.0.7';
    $this->tab = 'social_networks';
    $this->author = 'mitrocops.com';
    $this->module_key = '2b5601ea13369a214cfaf69a84ea5ea9';
    $this->confirmUninstall = $this->l('Are you sure you want to remove it ? Be careful, all your configuration and your data will be lost');

    if(version_compare(_PS_VERSION_, '1.5', '>'))
      $this->_is15 = 1;
    else
      $this->_is15 = 0;

    if(version_compare(_PS_VERSION_, '1.6', '>')){
      $this->_is16 = 1;
    } else {
      $this->_is16 = 0;
    }

    if(version_compare(_PS_VERSION_, '1.6', '>')){
      if(sizeof(Language::getLanguages())>1){
        $this->_multiple_lang = 1;
      } else {
        $this->_multiple_lang = 0;
      }
    } else {

      // ps 1.3
      if(version_compare(_PS_VERSION_, '1.4', '<'))
        $this->_multiple_lang = 0;
      else
        $this->_multiple_lang = 1;

    }

    $this->_like_button_settings = 1;

    $this->_http_referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';

    parent::__construct();
    $this->page = basename(__FILE__, '.php');
    $this->displayName = $this->l('Facebook Connect, Fan Coupon, Coupon for registration');
    $this->description = $this->l('Facebook Connect, Fan Coupon, Coupon for registration');

    $this->standart_text = $this->l('Sign in with Facebook and get voucher for discount');
    $this->title_standart_text = $this->l('Sign in with Facebook');

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

  public function install()
  {
    ### fan coupon ###
    // cumulable
    Configuration::updateValue($this->name.'fancumulativeother', 0);
    Configuration::updateValue($this->name.'fancumulativereduc', 0);
    // cumulable

    // categories
    Configuration::updateValue($this->name.'fancatbox', $this->getIdsCategories());
    // categories
    ### fan coupon ###




    ### voucher settings ###
    // cumulable
    Configuration::updateValue($this->name.'cumulativeother', 0);
    Configuration::updateValue($this->name.'cumulativereduc', 0);
    // cumulable

    // categories
    Configuration::updateValue($this->name.'catbox', $this->getIdsCategories());
    // categories
    ### voucher settings ###


    // fan coupon settings

    Configuration::updateValue($this->name.'likelayout', "box_count");
    Configuration::updateValue($this->name.'show_face', 'false');
      Configuration::updateValue($this->name.'fwidth', 500);
      Configuration::updateValue($this->name.'fheight', 500);

    Configuration::updateValue($this->name.'viscoupon_on', 1);

    if($this->_is16==1){
      Configuration::updateValue($this->name.'_psleftColumn', 'psleftColumn');
    } else{
    Configuration::updateValue($this->name.'_psrightColumn', 'psrightColumn');
    }
    Configuration::updateValue($this->name.'_psproductActions', 'psproductActions');
    Configuration::updateValue($this->name.'_pscheckoutPage', 'pscheckoutPage');


    $languages = Language::getLanguages(false);
      foreach ($languages as $language){
        $i = $language['id_lang'];
        $iso = Tools::strtoupper(Language::getIsoById($i));

        $fanblocktitletext = $this->l('Be Fan');
      Configuration::updateValue($this->name.'blockfantitletxt_'.$i, $fanblocktitletext);
    }



    Configuration::updateValue($this->name.'fanpageurl', "https://www.facebook.com/pages/Mitrocops-Prestashop-modules/617248764999822");

    $languages = Language::getLanguages(false);
      foreach ($languages as $language){
        $i = $language['id_lang'];
        $iso = Tools::strtoupper(Language::getIsoById($i));

        $fanblockadvtext = $this->l('Be fan of our Facebook page and get voucher for discount');
      Configuration::updateValue($this->name.'blockfanadvtxt_'.$i, $fanblockadvtext);
    }

    $languages = Language::getLanguages(false);
      foreach ($languages as $language){
        $i = $language['id_lang'];
        $iso = Tools::strtoupper(Language::getIsoById($i));

        $coupondesc = $this->l('Fan Coupon');
      Configuration::updateValue($this->name.'fcoupondesc_'.$i, $coupondesc);
    }
    Configuration::updateValue($this->name.'fvouchercode', "FAN");

    Configuration::updateValue($this->name.'fdiscount_type', 2);
    Configuration::updateValue($this->name.'fpercentage_val', 1);
    if($this->_is16)
        $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
      else
        $cur = Currency::getCurrencies();

    foreach ($cur AS $_cur){
        if(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency']){
          Configuration::updateValue('fsdamount_'.(int)$_cur['id_currency'], 1);
        }
    }

    Configuration::updateValue($this->name.'taxf', 1);
    Configuration::updateValue($this->name.'taxpercf', 1);


    Configuration::updateValue($this->name.'fsdvvalid', 365);


    // voucher settings

    Configuration::updateValue($this->name.'vis_on', 1);

    $languages = Language::getLanguages(false);
      foreach ($languages as $language){
        $i = $language['id_lang'];
        $iso = Tools::strtoupper(Language::getIsoById($i));

        $coupondesc = $this->displayName;
      Configuration::updateValue($this->name.'coupondesc_'.$i, $coupondesc.' '.$iso);
    }

    Configuration::updateValue($this->name.'vouchercode', "FCC");
    Configuration::updateValue($this->name.'discount_type', 2);
    Configuration::updateValue($this->name.'percentage_val', 1);
    if($this->_is16)
        $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
      else
        $cur = Currency::getCurrencies();

    foreach ($cur AS $_cur){
        if(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency']){
          Configuration::updateValue('sdamount_'.(int)$_cur['id_currency'], 1);
        }
    }

    Configuration::updateValue($this->name.'taxc', 1);
    Configuration::updateValue($this->name.'taxpercc', 1);


    Configuration::updateValue($this->name.'sdvvalid', 365);

    // facebook connect settings

    Configuration::updateValue($this->name.'blockauthis_on', 1);
    if($this->_is16==1){
      Configuration::updateValue($this->name.'signinfacebook_pos', "left");
    } else {
    Configuration::updateValue($this->name.'signinfacebook_pos', "right");
    }

    Configuration::updateValue($this->name.'adv_type', 1);
    $languages = Language::getLanguages(false);
      foreach ($languages as $language){
        $i = $language['id_lang'];
        $iso = Tools::strtoupper(Language::getIsoById($i));

        $advtext = $this->l('Custom Advertising Text in block Sign in with Facebook');
      Configuration::updateValue($this->name.'advtext_'.$i, $advtext.' '.$iso);
    }

    $languages = Language::getLanguages(false);
      foreach ($languages as $language){
        $i = $language['id_lang'];
        $iso = Tools::strtoupper(Language::getIsoById($i));

        $advtext = $this->l('Sign in with Facebook');
      Configuration::updateValue($this->name.'blocktitletxt_'.$i, $advtext.' '.$iso);
    }



    /// auth page settings

    Configuration::updateValue($this->name.'advauthis_on', 1);
    Configuration::updateValue($this->name.'fauthis_on', 1);

    Configuration::updateValue($this->name.'adv_typeauth', 1);
    $languages = Language::getLanguages(false);
      foreach ($languages as $language){
        $i = $language['id_lang'];
        $iso = Tools::strtoupper(Language::getIsoById($i));

        $advtext = $this->l('Custom Advertising Text on Authentication page');
      Configuration::updateValue($this->name.'advtextauth_'.$i, $advtext.' '.$iso);
    }

    // log in settings
    Configuration::updateValue($this->name.'floginauthis_on', 1);


    if (!parent::install())
      return false;
    if($this->_is15){
      if (!$this->_createFolderAndSetPermissions()
      OR !$this->installTable()
      OR !$this->registerHook('leftColumn')
      OR !$this->registerHook('rightColumn')
      OR !$this->registerHook('header')

      OR !$this->registerHook('extraLeft')
      OR !$this->registerHook('extraRight')
      OR !$this->registerHook('productFooter')
      OR !$this->registerHook('productActions')
      OR !$this->registerHook('home')

      OR !$this->registerHook('displayShoppingCartFooter')

      )
      return false;
    } else {

    if (!$this->_createFolderAndSetPermissions()
      OR !$this->installTable()
      OR !$this->registerHook('leftColumn')
      OR !$this->registerHook('rightColumn')
      OR !$this->registerHook('header')

      OR !$this->registerHook('extraLeft')
      OR !$this->registerHook('extraRight')
      OR !$this->registerHook('productFooter')
      OR !$this->registerHook('productActions')
      OR !$this->registerHook('home')

      OR !$this->registerHook('shoppingCart')
      )
      return false;
    }

    return true;
  }



  public function uninstall()
  {

    if (!$this->uninstallTable() OR !parent::uninstall()
      )
      return false;
    return true;
  }

  private function installTable(){
    $db = Db::getInstance();

    $query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'facebook_customer_fb (
          `customer_id` int(10) NOT NULL,
          `fb_id` bigint(20) NOT NULL,
          `id_shop` int(11) NOT NULL default \'0\',
          UNIQUE KEY `FBCANDV_CUSTOMER` (`customer_id`,`fb_id`,`id_shop`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
    $db->Execute($query);

    $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'facebook_img` (
            `id` int(11) NOT NULL auto_increment,
            `img` text,
            `id_shop` int(11) NOT NULL default \'0\',
            `type` int(11) NOT NULL default \'1\' COMMENT \'1 - block Sing in with Facebook 2 - Authentication Page 3 -In the block with a link Log In\',
            PRIMARY KEY  (`id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
    $db->Execute($sql);

    $sql_coupon = '
        CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'facebookfancoupon` (
          `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
          `id_discount` INT( 11 ) NOT NULL ,
          `ip_adress` VARCHAR(255) NOT NULL,
          `id_guest` INT( 11 ) NOT NULL ,
          `id_customer` INT( 11 ) NOT NULL ,
            INDEX (`id_discount`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ';
    $db->Execute($sql_coupon);

    return true;
  }


  public function uninstallTable() {
    Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'facebook_img');
    Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'facebook_customer_fb');
    Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'facebookfancoupon');
    return true;
  }

  private function _createFolderAndSetPermissions(){

    $prev_cwd = getcwd();

    $module_dir = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;
    @chdir($module_dir);

    //folder logo

    $module_dir_img = $module_dir.$this->name.DIRECTORY_SEPARATOR;
    @mkdir($module_dir_img, 0777);

    @chdir($prev_cwd);

    return true;
  }



  public function hookHeader($params){
      $smarty = $this->context->smarty;
    $cookie = $this->context->cookie;

      if(version_compare(_PS_VERSION_, '1.5', '>')){
        $this->context->controller->addCSS(($this->_path).'css/facebookcandvc.css', 'all');
      } else {
        //Tools::addCSS($this->_path . 'css/facebookcandvc.css', 'all');
        //Tools::addCSS(_PS_CSS_DIR_.'css/facebookcandvc.css', 'all');
      }

      $smarty->assign($this->name.'is15', $this->_is15);

      $smarty->assign($this->name.'appid', Configuration::get($this->name.'appid'));

      if(Tools::strlen(Configuration::get($this->name.'appid')) == 0
      || Tools::strlen(Configuration::get($this->name.'secretkey')) == 0)
        $is_ok_fill_data = 0;
      else
        $is_ok_fill_data = 1;

      $smarty->assign($this->name.'is_ok_fill_data', $is_ok_fill_data);

      $data_fb = $this->getfacebooklib((int)$params['cookie']->id_lang);
    $smarty->assign($this->name.'fbliburl', $data_fb['url']);
    $smarty->assign($this->name.'lng_iso', $data_fb['lng_iso']);


      $is_logged = isset($params['cookie']->id_customer)?$params['cookie']->id_customer:0;
      $smarty->assign($this->name.'islogged', $is_logged);

      $smarty->assign($this->name.'vis_on', Configuration::get($this->name.'vis_on'));

      $smarty->assign($this->name.'fauthis_on', Configuration::get($this->name.'fauthis_on'));

      $smarty->assign($this->name.'advauthis_on', Configuration::get($this->name.'advauthis_on'));

      $adv_type = Configuration::get($this->name.'adv_typeauth');
      if($adv_type == 1){
        // set discount
        switch (Configuration::get($this->name.'discount_type'))
      {
        case 1:
          // percent
          $id_discount_type = 1;
          $value = Configuration::get($this->name.'percentage_val');
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
      $valuta = "%";

      if($id_discount_type == 2){
        if($this->_is16)
            $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
          else
            $cur = Currency::getCurrencies();

        foreach ($cur AS $_cur){
          if(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency']){
              $valuta = $_cur['sign'];
            }
          }
      }
        $smarty->assign($this->name.'advtextauth', $this->standart_text.' '.$value.$valuta);
      } else {
        $current_language = (int)$cookie->id_lang;
        $smarty->assign($this->name.'advtextauth', Configuration::get($this->name.'advtextauth_'.$current_language));
      }
      $smarty->assign($this->name.'adv_typeauth', $adv_type);


      //get image
      include_once(dirname(__FILE__).'/classes/facebookcandvchelp.class.php');
    $obj = new facebookcandvchelp();
      $data_img = $obj->getImages();

      $smarty->assign($this->name.'authimg', $data_img['block_auth']);

      // log in settings
      $smarty->assign($this->name.'floginauthis_on', Configuration::get($this->name.'floginauthis_on'));
      $smarty->assign($this->name.'loginimg', $data_img['block_blocklogin']);


      // facebook fan coupon
      $this->_setSettings();


      $id_lang = (int)$cookie->id_lang;

      $iso_lang = Language::getIsoById((int)($id_lang))."/";

    if(!$this->_multiple_lang)
      $iso_lang = "";


    // if order page
        if(version_compare(_PS_VERSION_, '1.5', '>')){
          $data = explode("?",$this->_http_referer);
        $data  = end($data);
        $data_url_rewrite_on = explode("/",$this->_http_referer);
        $data_url_rewrite_on = end($data_url_rewrite_on);

        if(Configuration::get('PS_REWRITING_SETTINGS'))
          $uri = $iso_lang.'my-account';
        else
          $uri = 'index.php?controller=my-account&id_lang='.$id_lang;
        $order_page = 0;
          if($data == 'controller=order' || $data_url_rewrite_on == 'order'){
            $order_page = 1;
          if($data == 'controller=order')
            $uri = 'index.php?controller=order&step=1&id_lang='.$id_lang;
          elseif($data_url_rewrite_on == 'order')
            $uri = $iso_lang.'order?step=1';

           $smarty->assign($this->name.'uri', $uri);
        }
        $smarty->assign($this->name.'order_page', $order_page);
      } else {
        $data = explode("/",$this->_http_referer);
        $data  = end($data);

        if(Configuration::get('PS_REWRITING_SETTINGS') && version_compare(_PS_VERSION_, '1.4', '>'))
          $uri = $iso_lang.'my-account';
        else
          $uri = 'my-account.php?id_lang='.$id_lang;
        $order_page = 0;
        if($data == 'order.php'
        || $data == 'order'
        ){
          $order_page = 1;
          if($data == 'order.php')
            $uri = 'order.php?step=1&id_lang='.$id_lang;
          elseif($data == 'order')
            $uri = $iso_lang.'order?step=1';

          $smarty->assign($this->name.'uri', $uri);
        }
        $smarty->assign($this->name.'order_page', $order_page);
      }
      // if order page


      return $this->display(dirname(__FILE__).'/facebookcandvc.php', 'views/templates/hooks/head.tpl');
    }

    public function getfacebooklib($id_lang){

      $lang = new Language((int)$id_lang);

      $lng_code = isset($lang->language_code)?$lang->language_code:$lang->iso_code;
      if(strstr($lng_code, '-')){
      $res = explode('-', $lng_code);
      $language_iso = Tools::strtolower($res[0]).'_'.Tools::strtoupper($res[1]);
    } else {
      $language_iso = Tools::strtolower($lng_code).'_'.Tools::strtoupper($lng_code);
    }


    if (!in_array($language_iso, $this->getfacebooklocale()))
      $language_iso = "en_US";

    if (Configuration::get('PS_SSL_ENABLED') == 1)
      $url = "https://";
    else
      $url = "http://";

    return array(
          'url'=>$url . 'connect.facebook.net/'.$language_iso.'/all.js#xfbml=1&appId='.Configuration::get($this->name.'appid'),
            //'url'=>$url . 'connect.facebook.net/'.$language_iso.'/all.js#xfbml=1',
            'lng_iso' => $language_iso);
    }

  public function getfacebooklocale()
  {
    $locales = array();

    if (($xml=simplexml_load_file(_PS_MODULE_DIR_ . $this->name."/lib/facebook_locales.xml")) === false)
      return $locales;

    $result = $xml->xpath('/locales/locale/codes/code/standard/representation');

    foreach ($result as $locale)
    {
      list($k, $node) = each($locale);
      $locales[] = $node;
    }

    return $locales;
  }


  public function hookLeftColumn($params)
  {
    $smarty = $this->context->smarty;
    $cookie = $this->context->cookie;

    $smarty->assign($this->name.'likelayout', Configuration::get($this->name.'likelayout'));

    $smarty->assign($this->name.'fheight', Configuration::get($this->name.'fheight'));
    $smarty->assign($this->name.'fwidth', Configuration::get($this->name.'fwidth'));
    $smarty->assign($this->name.'show_face', Configuration::get($this->name.'show_face'));

    $smarty->assign($this->name.'vis_on', Configuration::get($this->name.'vis_on'));
    $smarty->assign($this->name.'signinfacebook_pos', Configuration::get($this->name.'signinfacebook_pos'));
    $is_logged = isset($params['cookie']->id_customer)?$params['cookie']->id_customer:0;
      $smarty->assign($this->name.'islogged', $is_logged);

      $smarty->assign($this->name.'blockauthis_on', Configuration::get($this->name.'blockauthis_on'));

      $adv_type = Configuration::get($this->name.'adv_type');
      $current_language = (int)$cookie->id_lang;

      if($adv_type == 1){
        // set discount
        switch (Configuration::get('facebookcandvcdiscount_type'))
      {
        case 1:
          // percent
          $id_discount_type = 1;
          $value = Configuration::get('facebookcandvcpercentage_val');
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
      $valuta = "%";

      if($id_discount_type == 2){
        if($this->_is16)
            $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
          else
            $cur = Currency::getCurrencies();

        foreach ($cur AS $_cur){
          if(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency']){
              $valuta = $_cur['sign'];
            }
          }
      }
        $smarty->assign($this->name.'advtext', $this->standart_text.' '.$value.$valuta);
        $smarty->assign($this->name.'blocktitletxt', $this->title_standart_text);
      } else {
        $smarty->assign($this->name.'advtext', Configuration::get($this->name.'advtext_'.$current_language));
        $smarty->assign($this->name.'blocktitletxt', Configuration::get($this->name.'blocktitletxt_'.$current_language));
      }
      $smarty->assign($this->name.'adv_type', $adv_type);


      //get image
      include_once(dirname(__FILE__).'/classes/facebookcandvchelp.class.php');
    $obj = new facebookcandvchelp();
      $data_img = $obj->getImages();

      $smarty->assign($this->name.'blockimg', $data_img['block_sign_in_with_facebook']);

      $smarty->assign($this->name.'is15', $this->_is15);


      // facebook fan coupon
      $this->_setSettings();


      $smarty->assign($this->name.'_psleftColumn', Configuration::get($this->name.'_psleftColumn'));



        // facebook fan coupon
      return $this->display(dirname(__FILE__).'/facebookcandvc.php', 'views/templates/hooks/left.tpl');

  }


  public function hookRightColumn($params)
  {
    $smarty = $this->context->smarty;
    $cookie = $this->context->cookie;

    $smarty->assign($this->name.'likelayout', Configuration::get($this->name.'likelayout'));

    $smarty->assign($this->name.'show_face', Configuration::get($this->name.'show_face'));
    $smarty->assign($this->name.'fheight', Configuration::get($this->name.'fheight'));
    $smarty->assign($this->name.'fwidth', Configuration::get($this->name.'fwidth'));


    $smarty->assign($this->name.'vis_on', Configuration::get($this->name.'vis_on'));
    $smarty->assign($this->name.'signinfacebook_pos', Configuration::get($this->name.'signinfacebook_pos'));
    $is_logged = isset($params['cookie']->id_customer)?$params['cookie']->id_customer:0;
      $smarty->assign($this->name.'islogged', $is_logged);

      $smarty->assign($this->name.'blockauthis_on', Configuration::get($this->name.'blockauthis_on'));

      $adv_type = Configuration::get($this->name.'adv_type');
      $current_language = (int)$cookie->id_lang;

      if($adv_type == 1){
        // set discount
        switch (Configuration::get($this->name.'discount_type'))
      {
        case 1:
          // percent
          $id_discount_type = 1;
          $value = Configuration::get($this->name.'percentage_val');
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
      $valuta = "%";

      if($id_discount_type == 2){
        if($this->_is16)
            $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
          else
            $cur = Currency::getCurrencies();

        foreach ($cur AS $_cur){
          if(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency']){
              $valuta = $_cur['sign'];
            }
          }
      }
        $smarty->assign($this->name.'advtext', $this->standart_text.' '.$value.$valuta);
        $smarty->assign($this->name.'blocktitletxt', $this->title_standart_text);
      } else {
        $smarty->assign($this->name.'advtext', Configuration::get($this->name.'advtext_'.$current_language));
        $smarty->assign($this->name.'blocktitletxt', Configuration::get($this->name.'blocktitletxt_'.$current_language));
      }
      $smarty->assign($this->name.'adv_type', $adv_type);


      //get image
      include_once(dirname(__FILE__).'/classes/facebookcandvchelp.class.php');
    $obj = new facebookcandvchelp();
      $data_img = $obj->getImages();

      $smarty->assign($this->name.'blockimg', $data_img['block_sign_in_with_facebook']);

      $smarty->assign($this->name.'is15', $this->_is15);

      $this->_setSettings();

      $smarty->assign($this->name.'_psrightColumn', Configuration::get($this->name.'_psrightColumn'));

      return $this->display(dirname(__FILE__).'/facebookcandvc.php', 'views/templates/hooks/right.tpl');


  }

  public function hookShoppingCart($params)
  {
    return $this->hookDisplayShoppingCartFooter($params);
  }

  public function hookDisplayShoppingCartFooter($params){
    $smarty = $this->context->smarty;

    $smarty->assign($this->name.'likelayout', Configuration::get($this->name.'likelayout'));

    $smarty->assign($this->name.'show_face', Configuration::get($this->name.'show_face'));
    $smarty->assign($this->name.'fheight', Configuration::get($this->name.'fheight'));
    $smarty->assign($this->name.'fwidth', Configuration::get($this->name.'fwidth'));

    $this->_setSettings();

      // facebook fan coupon

        $smarty->assign($this->name.'_pscheckoutPage', Configuration::get($this->name.'_pscheckoutPage'));

        return $this->display(dirname(__FILE__).'/facebookcandvc.php', 'views/templates/hooks/cart.tpl');

  }

  public function hookextraRight($params){
    $smarty = $this->context->smarty;

    $smarty->assign($this->name.'likelayout', Configuration::get($this->name.'likelayout'));

    $smarty->assign($this->name.'show_face', Configuration::get($this->name.'show_face'));
    $smarty->assign($this->name.'fheight', Configuration::get($this->name.'fheight'));
    $smarty->assign($this->name.'fwidth', Configuration::get($this->name.'fwidth'));


    $this->_setSettings();


        // facebook fan coupon

        $smarty->assign($this->name.'_psextraRight', Configuration::get($this->name.'_psextraRight'));

        return $this->display(dirname(__FILE__).'/facebookcandvc.php', 'views/templates/hooks/extraright.tpl');

  }

  public function hookhome($params){
    $smarty = $this->context->smarty;

    $smarty->assign($this->name.'likelayout', Configuration::get($this->name.'likelayout'));


    $smarty->assign($this->name.'show_face', Configuration::get($this->name.'show_face'));
    $smarty->assign($this->name.'fheight', Configuration::get($this->name.'fheight'));
    $smarty->assign($this->name.'fwidth', Configuration::get($this->name.'fwidth'));

    $this->_setSettings();


        // facebook fan coupon

        $smarty->assign($this->name.'_pshome', Configuration::get($this->name.'_pshome'));

        return $this->display(dirname(__FILE__).'/facebookcandvc.php', 'views/templates/hooks/home.tpl');


  }

  public function hookExtraLeft($params)
  {
    $smarty = $this->context->smarty;

    $smarty->assign($this->name.'likelayout', Configuration::get($this->name.'likelayout'));
    $smarty->assign($this->name.'show_face', Configuration::get($this->name.'show_face'));
    $smarty->assign($this->name.'fheight', Configuration::get($this->name.'fheight'));
    $smarty->assign($this->name.'fwidth', Configuration::get($this->name.'fwidth'));


    $this->_setSettings();


        // facebook fan coupon

        $smarty->assign($this->name.'_psextraLeft', Configuration::get($this->name.'_psextraLeft'));

        return $this->display(dirname(__FILE__).'/facebookcandvc.php', 'views/templates/hooks/extraleft.tpl');
  }

  public function hookproductFooter($params){
    $smarty = $this->context->smarty;

    $smarty->assign($this->name.'likelayout', Configuration::get($this->name.'likelayout'));

    $smarty->assign($this->name.'show_face', Configuration::get($this->name.'show_face'));
    $smarty->assign($this->name.'fheight', Configuration::get($this->name.'fheight'));
    $smarty->assign($this->name.'fwidth', Configuration::get($this->name.'fwidth'));


    $this->_setSettings();


        // facebook fan coupon

    $smarty->assign($this->name.'_psproductFooter', Configuration::get($this->name.'_psproductFooter'));

      return $this->display(dirname(__FILE__).'/facebookcandvc.php', 'views/templates/hooks/productfooter.tpl');

  }

  public function hookproductActions($params){
    $smarty = $this->context->smarty;

    $smarty->assign($this->name.'likelayout', Configuration::get($this->name.'likelayout'));

    $smarty->assign($this->name.'show_face', Configuration::get($this->name.'show_face'));
    $smarty->assign($this->name.'fheight', Configuration::get($this->name.'fheight'));
    $smarty->assign($this->name.'fwidth', Configuration::get($this->name.'fwidth'));


    $this->_setSettings();
         // facebook fan coupon

    $smarty->assign($this->name.'_psproductActions', Configuration::get($this->name.'_psproductActions'));

      return $this->display(dirname(__FILE__).'/facebookcandvc.php', 'views/templates/hooks/productactions.tpl');

  }


  private function _setSettings(){
    $smarty = $this->context->smarty;
    $cookie = $this->context->cookie;
    $current_language = (int)$cookie->id_lang;

    // facebook fan coupon
      $smarty->assign($this->name.'viscoupon_on', Configuration::get($this->name.'viscoupon_on'));
      $smarty->assign($this->name.'fanpageurl', Configuration::get($this->name.'fanpageurl'));

      $smarty->assign($this->name.'blockfantitletxt', Configuration::get($this->name.'blockfantitletxt_'.$current_language));
      $smarty->assign($this->name.'fcoupondesc', Configuration::get($this->name.'fcoupondesc_'.$current_language));

        $smarty->assign($this->name.'fvouchercode', Configuration::get($this->name.'fvouchercode'));
      $smarty->assign($this->name.'fdiscount_type', Configuration::get($this->name.'fdiscount_type'));
      $smarty->assign($this->name.'fpercentage_val', Configuration::get($this->name.'fpercentage_val'));


        // set discount
        switch (Configuration::get($this->name.'fdiscount_type'))
      {
        case 1:
          // percent
          $fid_discount_type = 1;
          $fvalue = Configuration::get($this->name.'fpercentage_val');
          break;
        case 2:
          // currency
          $fid_discount_type = 2;
          $id_currency = (int)$cookie->id_currency;
          $fvalue = Configuration::get('fsdamount_'.(int)$id_currency);
        break;
        default:
          $fid_discount_type = 2;
          $id_currency = (int)$cookie->id_currency;
          $fvalue = Configuration::get('fsdamount_'.(int)$id_currency);
      }
      $fvaluta = "%";

      if($fid_discount_type == 2){
        if($this->_is16)
            $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
          else
            $cur = Currency::getCurrencies();

        foreach ($cur AS $_cur){
          if(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency']){
              $fvaluta = $_cur['sign'];
            }
          }
      }
      $smarty->assign($this->name.'blockfanadvtxt', Configuration::get($this->name.'blockfanadvtxt_'.$current_language).' '.$fvalue.$fvaluta);



        $smarty->assign($this->name.'fsdvvalid', Configuration::get($this->name.'fsdvvalid'));


        // facebook fan coupon
  }

  public function getContent()
    {
      global $currentIndex;
      $cookie = $this->context->cookie;

      $this->_html = '';

      $this->_html .= $this->_headercssfiles();

      $fancouponset = Tools::getValue("fancouponset");
        if (Tools::strlen($fancouponset)>0) {
          $this->_html .= '<script>init_tabs(6);</script>';
        }

        if (Tools::isSubmit('fancouponsettings'))
        {


          Configuration::updateValue($this->name.'fwidth', Tools::getValue('fwidth'));
        Configuration::updateValue($this->name.'fheight', Tools::getValue('fheight'));
        Configuration::updateValue($this->name.'likelayout', Tools::getValue('likelayout'));

          Configuration::updateValue($this->name.'viscoupon_on', Tools::getValue('viscoupon_on'));

          Configuration::updateValue($this->name.'_psleftColumn', Tools::getValue('psleftColumn'));
      Configuration::updateValue($this->name.'_psextraLeft', Tools::getValue('psextraLeft'));
      Configuration::updateValue($this->name.'_psrightColumn', Tools::getValue('psrightColumn'));
      Configuration::updateValue($this->name.'_psextraRight', Tools::getValue('psextraRight'));
      Configuration::updateValue($this->name.'_psproductFooter', Tools::getValue('psproductFooter'));
      Configuration::updateValue($this->name.'_psproductActions', Tools::getValue('psproductActions'));
      Configuration::updateValue($this->name.'_pscheckoutPage', Tools::getValue('pscheckoutPage'));
      Configuration::updateValue($this->name.'_pshome', Tools::getValue('pshome'));


      Configuration::updateValue($this->name.'show_face', Tools::getValue('show_face'));

          Configuration::updateValue($this->name.'fanpageurl', Tools::getValue('fanpageurl'));

          $languages = Language::getLanguages(false);
          foreach ($languages as $language){
          $i = $language['id_lang'];
            Configuration::updateValue($this->name.'blockfantitletxt_'.$i, Tools::getValue('blockfantitletxt_'.$i));
          }

          $languages = Language::getLanguages(false);
          foreach ($languages as $language){
          $i = $language['id_lang'];
            Configuration::updateValue($this->name.'blockfanadvtxt_'.$i, Tools::getValue('blockfanadvtxt_'.$i));
          }

        $languages = Language::getLanguages(false);
          foreach ($languages as $language){
          $i = $language['id_lang'];
            Configuration::updateValue($this->name.'fcoupondesc_'.$i, Tools::getValue('fcoupondesc_'.$i));
          }
            Configuration::updateValue($this->name.'fvouchercode', Tools::getValue('fvouchercode'));

          Configuration::updateValue($this->name.'fdiscount_type', Tools::getValue('fdiscount_type'));
      Configuration::updateValue($this->name.'fpercentage_val', Tools::getValue('fpercentage_val'));

          if(Tools::getValue('fdiscount_type') == 2){
            Configuration::updateValue($this->name.'taxf', Tools::getValue('taxf'));
          } else{
            Configuration::updateValue($this->name.'taxpercf', Tools::getValue('taxpercf'));

          }

          foreach (Tools::getValue('fsdamount') AS $id => $value){
        Configuration::updateValue('fsdamount_'.(int)($id), (float)($value));
          }

            Configuration::updateValue($this->name.'fsdvvalid', Tools::getValue('fsdvvalid'));


             //
            if(Tools::getValue($this->name.'fanisminamount') == true){
            foreach (Tools::getValue('fanfsdminamount') AS $id => $value){
          Configuration::updateValue('fanfsdminamount_'.(int)($id), (float)($value));
            }
          }

            Configuration::updateValue($this->name.'fanisminamount', Tools::getValue($this->name.'fanisminamount'));

            // category
            $categoryBox = Tools::getValue('fancategoryBox');
            //var_Dump($categoryBox); exit;
            $categoryBox = implode(",",$categoryBox);
            Configuration::updateValue($this->name.'fancatbox', $categoryBox);


            // cumulable
            Configuration::updateValue($this->name.'fancumulativeother', Tools::getValue('fancumulativeother'));
      Configuration::updateValue($this->name.'fancumulativereduc', Tools::getValue('fancumulativereduc'));


          $url = $currentIndex.'&tab=AdminModules&fancouponset=1&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'';
          Tools::redirectAdmin($url);
        }

      $voucherset = Tools::getValue("voucherset");
        if (Tools::strlen($voucherset)>0) {
          $this->_html .= '<script>init_tabs(3);</script>';
        }

        if (Tools::isSubmit('vouchersettings'))
        {

          Configuration::updateValue($this->name.'vis_on', Tools::getValue('vis_on'));


        $languages = Language::getLanguages(false);
          foreach ($languages as $language){
          $i = $language['id_lang'];
            Configuration::updateValue($this->name.'coupondesc_'.$i, Tools::getValue('coupondesc_'.$i));
          }
            Configuration::updateValue($this->name.'vouchercode', Tools::getValue('vouchercode'));

          Configuration::updateValue($this->name.'discount_type', Tools::getValue('discount_type'));
      Configuration::updateValue($this->name.'percentage_val', Tools::getValue('percentage_val'));


          if(Tools::getValue('discount_type') == 2){
            Configuration::updateValue($this->name.'taxc', Tools::getValue('taxc'));
          } else{
            Configuration::updateValue($this->name.'taxpercc', Tools::getValue('taxpercc'));

          }
          foreach (Tools::getValue('sdamount') AS $id => $value){
        Configuration::updateValue('sdamount_'.(int)($id), (float)($value));
          }

            Configuration::updateValue($this->name.'sdvvalid', Tools::getValue('sdvvalid'));

            //
            if(Tools::getValue($this->name.'isminamount') == true){
            foreach (Tools::getValue('fsdminamount') AS $id => $value){
          Configuration::updateValue('fsdminamount_'.(int)($id), (float)($value));
            }
          }

            Configuration::updateValue($this->name.'isminamount', Tools::getValue($this->name.'isminamount'));

            // category
            $categoryBox = Tools::getValue('categoryBox');
            $categoryBox = implode(",",$categoryBox);
            Configuration::updateValue($this->name.'catbox', $categoryBox);


            // cumulable
            Configuration::updateValue($this->name.'cumulativeother', Tools::getValue('cumulativeother'));
      Configuration::updateValue($this->name.'cumulativereduc', Tools::getValue('cumulativereduc'));



          $url = $currentIndex.'&tab=AdminModules&voucherset=1&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'';
          Tools::redirectAdmin($url);
        }

      $facebookset = Tools::getValue("facebookset");
        if (Tools::strlen($facebookset)>0) {
          $this->_html .= '<script>init_tabs(2);</script>';
        }

        if (Tools::isSubmit('facebooksettings'))
        {

          Configuration::updateValue($this->name.'appid', Tools::getValue('appid'));
          Configuration::updateValue($this->name.'secretkey', Tools::getValue('secretkey'));


          /// sign in with facebook

          Configuration::updateValue($this->name.'signinfacebook_pos', Tools::getValue('signinfacebook_pos'));

          Configuration::updateValue($this->name.'blockauthis_on', Tools::getValue('blockauthis_on'));

          $languages = Language::getLanguages(false);
          foreach ($languages as $language){
          $i = $language['id_lang'];
            Configuration::updateValue($this->name.'advtext_'.$i, Tools::getValue('advtext_'.$i));
          }

          $languages = Language::getLanguages(false);
          foreach ($languages as $language){
          $i = $language['id_lang'];
            Configuration::updateValue($this->name.'blocktitletxt_'.$i, Tools::getValue('blocktitletxt_'.$i));
          }


          include_once(dirname(__FILE__).'/classes/facebookcandvchelp.class.php');
      $obj = new facebookcandvchelp();
        $obj->saveImage(array('type'=>'block'));


          /// auth page
          Configuration::updateValue($this->name.'fauthis_on', Tools::getValue('fauthis_on'));
          Configuration::updateValue($this->name.'advauthis_on', Tools::getValue('advauthis_on'));

          Configuration::updateValue($this->name.'adv_type', Tools::getValue('adv_type'));

          $languages = Language::getLanguages(false);
          foreach ($languages as $language){
          $i = $language['id_lang'];
            Configuration::updateValue($this->name.'advtextauth_'.$i, Tools::getValue('advtextauth_'.$i));
          }

          Configuration::updateValue($this->name.'adv_typeauth', Tools::getValue('adv_typeauth'));

          $obj->saveImage(array('type'=>'auth'));

          /// log in settings
          Configuration::updateValue($this->name.'floginauthis_on', Tools::getValue('floginauthis_on'));
          $obj->saveImage(array('type'=>'login'));

          $url = $currentIndex.'&tab=AdminModules&facebookset=1&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'';
          Tools::redirectAdmin($url);
        }

      if(Tools::isSubmit('cancel_search')){
          $url = $currentIndex.'&tab=AdminModules&pageitems&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'';
          Tools::redirectAdmin($url);

        }
        if (Tools::isSubmit('pageitems') || Tools::isSubmit('find') || Tools::isSubmit('search_query')) {
        $this->_html .= '<script>init_tabs(4);</script>';
        }

        $this->_html .= $this->_displayForm();
        return $this->_html;

    }

     public function _displayForm(){
      $_html = '';

      $_html .= '
    <fieldset '.(($this->_is15 == 1)?"class=\"ps15-width\"":"").'>
          <legend><img src="../modules/'.$this->name.'/i/logo-16x16.gif"  />
          '.$this->displayName.'</legend>

    <ul class="leftMenu">
      <li><a href="javascript:void(0)" onclick="tabs_custom(1)" id="tab-menu-1" class="selected"><img src="../modules/'.$this->name.'/i/logo-16x16.gif" />'.$this->l('Welcome').'</a></li>
      <li><a href="javascript:void(0)" onclick="tabs_custom(2)" id="tab-menu-2"><img src="../modules/'.$this->name.'/i/btn/ico-facebook.gif" />'.$this->l('Facebook Connect Settings').'</a></li>
      <li><a href="javascript:void(0)" onclick="tabs_custom(3)" id="tab-menu-3"><img src="../modules/'.$this->name.'/i/btn/ico-voucher.gif" />'.$this->l('Voucher Settings').'</a></li>
      <li><a href="javascript:void(0)" onclick="tabs_custom(6)" id="tab-menu-6"><img src="../modules/'.$this->name.'/i/btn/ico-facebook.gif" />'.$this->l('Fan Coupon Settings').'</a></li>
      <li><a href="javascript:void(0)" onclick="tabs_custom(4)" id="tab-menu-4"><img src="../modules/'.$this->name.'/i/btn/statistics.png" style="height:15px" />'.$this->l('Statistics').'</a></li>
      <li><a href="javascript:void(0)" onclick="tabs_custom(5)" id="tab-menu-5"><img src="../modules/'.$this->name.'/i/btn/ico-help.gif" />'.$this->l('Help / Documentation').'</a></li>
    </ul>
    ';
      $_html .= '<div style="clear:both"></div>';
    $_html .= '<div class="facebookcandvc-content">
            <div class="menu-content" id="tabs-1">'.$this->_welcome().'</div>';
    $_html .= '<div class="menu-content" id="tabs-2">'.$this->_FBSettings().'</div>';
    $_html .= '<div class="menu-content" id="tabs-3">'.$this->_voucherSettings().'</div>';
    $_html .= '<div class="menu-content" id="tabs-6">'.$this->_fancouponSettings().'</div>';
    $_html .= '<div class="menu-content" id="tabs-4">'.$this->_statistics().'</div>';
    $_html .= '<div class="menu-content" id="tabs-5">'.$this->_help_documentation().'</div>';
    $_html .= '<div style="clear:both"></div>';
    $_html .= '</div>';


    $_html .= '</fieldset>';

      return $_html;
    }

    private function _FBSettings(){
      $_html = '';

      $_html .= '<h3 class="title-block-content">'.$this->l('Facebook Connect Settings').'</h3>';


      $_html .= '<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">';

      // Facebook Application Id
      $_html .= '<label>'.$this->l('Facebook Application Id:').'</label>

            <div class="margin-form">
          <input type="text" name="appid" style="width:300px"
                          value="'.Tools::getValue('appid', Configuration::get($this->name.'appid')).'">
          <p class="clear">
          '.$this->l('To configure the Facebook Application Id read ').'<a target="_blank" href="../modules/'.$this->name.'/readme.pdf" style="text-decoration:underline;font-weight:bold">readme.pdf</a>'.$this->l(' , which is located in the folder  with the module.').'
          </p>
        </div>';

      // Facebook Secret Key
      $_html .= '<label>'.$this->l('Facebook Secret Key').':</label>

            <div class="margin-form">
          <input type="text" name="secretkey" style="width:300px"
                          value="'.Tools::getValue('secretkey', Configuration::get($this->name.'secretkey')).'">
          <p class="clear">
          '.$this->l('To configure the Facebook Secret Key read ').'<a target="_blank" href="../modules/'.$this->name.'/readme.pdf" style="text-decoration:underline;font-weight:bold">readme.pdf</a>'.$this->l(' , which is located in the folder  with the module.').'
          </p>
        </div>';

      $_html .= '<h3 class="title-block-content">'.$this->l('Block "Sign in with Facebook" Settings').'</h3>';


      // Position block "Sign in with Facebook"
      $_html .= '<label>'.$this->l('Position Block "Sign in with Facebook"').':</label>

            <div class="margin-form">
            <select class="select" name="signinfacebook_pos"
              id="signinfacebook_pos">
            <option '.(Tools::getValue('signinfacebook_pos', Configuration::get($this->name.'signinfacebook_pos'))  == "left" ? 'selected="selected" ' : '').' value="left">'.$this->l('Left').'</option>
            <option '.(Tools::getValue('signinfacebook_pos', Configuration::get($this->name.'signinfacebook_pos')) == "right" ? 'selected="selected" ' : '').' value="right">'.$this->l('Right').'</option>
            <option '.(Tools::getValue('signinfacebook_pos', Configuration::get($this->name.'signinfacebook_pos')) == "none" ? 'selected="selected" ' : '').' value="none">'.$this->l('None').'</option>

          </select>
          <p class="clear">'.$this->l('Position Block "Sign in with Facebook"').'.</p>
        </div>';

      $_html .= '<label>'.$this->l('Facebook Connect Image in Block "Sign in with Facebook"').'</label>

            <div class="margin-form">
          <input type="file" name="post_image_block" id="post_image_block" />';

      include_once(dirname(__FILE__).'/classes/facebookcandvchelp.class.php');
    $obj = new facebookcandvchelp();
      $data_img = $obj->getImages(array('admin'=>1));


      $_html .= '&nbsp;&nbsp;&nbsp;<img id="imagesignin" src="'.$data_img['block_sign_in_with_facebook'].'">';

      if(Tools::strlen($data_img['img_block'])>0)
        $_html .= '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="imagesignin-click" style="text-decoration:underline" onclick="return_default_img(\'sign\',\''.$this->l('Are you sure you want to remove this item?').'\')">'.$this->l('Click here to return the default image').'</a>';

    $_html .= '<p>Allow formats *.jpg; *.jpeg; *.png; *.gif.</p>';
      $_html .= '</div>';

      // enable or disable Advertising Text in block "Sign in with Facebook"
      $_html .= '<label>'.$this->l('Enable or Disable Advertising Text in block "Sign in with Facebook"').':</label>
        <div class="margin-form">

          <input type="radio" value="1" id="text_list_on" name="blockauthis_on" onclick="enableOrDisableBlock(1)"
              '.(Tools::getValue('blockauthis_on', Configuration::get($this->name.'blockauthis_on')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_on" class="t">
            <img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
          </label>

          <input type="radio" value="0" id="text_list_off" name="blockauthis_on"  onclick="enableOrDisableBlock(0)"
               '.(!Tools::getValue('blockauthis_on', Configuration::get($this->name.'blockauthis_on')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_off" class="t">
            <img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
          </label>

          <p class="clear">'.$this->l('Enable or Disable Advertising Text in block "Sign in with Facebook"').'.</p>
        </div>';


      $_html .= '<script type="text/javascript">
            function enableOrDisableBlock(id)
            {
            if(id==0){
              $("#block-fb-settings").hide(200);
            } else {
              $("#block-fb-settings").show(200);
            }

            }
          </script>';

      $_html .= '<div id="block-fb-settings" '.(Configuration::get($this->name.'blockauthis_on')==1?'style="display:block"':'style="display:none"').'>';

      //  type adv text in block "Sign in with Facebook"
      $_html .= '<label>'.$this->l('Advertising text in block "Sign in with Facebook"').':</label>

            <div class="margin-form">
            <select class="select" name="adv_type" onChange="selectItemsAdv(this.selectedIndex)"
              id="adv_type">
            <option '.(Tools::getValue('adv_type', Configuration::get($this->name.'adv_type'))  == 1 ? 'selected="selected" ' : '').' value="1">'.$this->l('Standart Advertising Text in block "Sign in with Facebook"').'</option>
            <option '.(Tools::getValue('adv_type', Configuration::get($this->name.'adv_type')) == 2 ? 'selected="selected" ' : '').' value="2">'.$this->l('Custom Advertising Text in block "Sign in with Facebook"').'</option>
          </select>

          <p class="clear">'.$this->l('You can choose one of two types: 1. Standart Advertising Text in block "Sign in with Facebook" 2. Custom Advertising Text in block "Sign in with Facebook"').'</p>

        </div>

    <script type="text/javascript">
      function selectItemsAdv(id)
      {

      if(id==1){
        $("#sd-standart").hide();
        $("#sd-custom").show(200);
      } else {
        $("#sd-custom").hide();
        $("#sd-standart").show(200);
      }

      }
    </script>
        ';
      $_html .= '<div id="sd-standart"
            '.(Tools::getValue('adv_type', Configuration::get($this->name.'adv_type')) == 1 ? '' : 'style="display:none" ').'>';

    $_html .= '<label>'.$this->l('Standart Advertising Text in block "Sign in with Facebook"').':</label>

            <div class="margin-form">
            <p style="font-size:14px;padding-top:3px;font-weight:bold">
          '.$this->standart_text.'
          </p>
          <img src="../modules/'.$this->name.'/i/standart_text.png" />
          <p class="clear">'.$this->l('Standart Advertising Text in block "Sign in with Facebook"').'</p>
        </div>';
      $_html .= '</div>';


      $_html .= '<div id="sd-custom" '.(Tools::getValue('adv_type', Configuration::get($this->name.'adv_type'))  == 2 ? '' : 'style="display:none"').'>';


      $divLangName = "advtext¤advtextauth¤blocktitletxt";


      // advertising text
      $_html .= '<label>'.$this->l('Custom Title Text in block "Sign in with Facebook"').':</label>

            <div class="margin-form">';

        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);

        foreach ($languages as $language){
      $id_lng = (int)$language['id_lang'];
        $coupondesc = Configuration::get($this->name.'blocktitletxt'.'_'.$id_lng);


      $_html .= ' <div id="blocktitletxt_'.$language['id_lang'].'"
               style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;"
               >

            <input type="text" style="width:400px"
                  id="blocktitletxt_'.$language['id_lang'].'"
                  name="blocktitletxt_'.$language['id_lang'].'"
                  value="'.htmlentities(Tools::stripslashes($coupondesc), ENT_COMPAT, 'UTF-8').'"/>
            </div>';
        }
      $_html .= '';
      ob_start();
      $this->displayFlags($languages, $defaultLanguage, $divLangName, 'blocktitletxt');
      $displayflags = ob_get_clean();
      $_html .= $displayflags;
      $_html .= '<div style="clear:both"></div>';
      $_html .= '<p class="clear">'.$this->l('Custom Title Text in block "Sign in with Facebook"').'</p>';
      $_html .= '</div>';
      // advertising text


      // advertising text
      $_html .= '<label>'.$this->l('Custom Advertising Text in block "Sign in with Facebook"').':</label>

            <div class="margin-form">';

        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);

        foreach ($languages as $language){
      $id_lng = (int)$language['id_lang'];
        $coupondesc = Configuration::get($this->name.'advtext'.'_'.$id_lng);


      $_html .= ' <div id="advtext_'.$language['id_lang'].'"
               style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;"
               >

            <input type="text" style="width:400px"
                  id="advtext_'.$language['id_lang'].'"
                  name="advtext_'.$language['id_lang'].'"
                  value="'.htmlentities(Tools::stripslashes($coupondesc), ENT_COMPAT, 'UTF-8').'"/>
            </div>';
        }
      $_html .= '';
      ob_start();
      $this->displayFlags($languages, $defaultLanguage, $divLangName, 'advtext');
      $displayflags = ob_get_clean();
      $_html .= $displayflags;
      $_html .= '<div style="clear:both"></div>';
      $_html .= '<img src="../modules/'.$this->name.'/i/custom_text.png" />';
      $_html .= '<p class="clear">'.$this->l('Custom Advertising Text in block "Sign in with Facebook"').'</p>';
      $_html .= '</div>';
      // advertising text
      $_html .= '</div>';




      $_html .= '</div>';

      $_html .= '<h3 class="title-block-content">'.$this->l('Facebook Connect on Authentication page Settings').'</h3>';


      // enable or disable Facebook Connect on Authentication page
      $_html .= '<label>'.$this->l('Enable or Disable Facebook Connect on Authentication page').':</label>
        <div class="margin-form">

          <input type="radio" value="1" id="text_list_on" name="fauthis_on"
              '.(Tools::getValue('fauthis_on', Configuration::get($this->name.'fauthis_on')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_on" class="t">
            <img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
          </label>

          <input type="radio" value="0" id="text_list_off" name="fauthis_on"
               '.(!Tools::getValue('fauthis_on', Configuration::get($this->name.'fauthis_on')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_off" class="t">
            <img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
          </label>

          <p class="clear">'.$this->l('Enable or Disable Facebook Connect on Authentication page').'.</p>
        </div>';

      $_html .= '<label>'.$this->l('Facebook Connect Image on Authentication page').'</label>

            <div class="margin-form">
          <input type="file" name="post_image_auth" id="post_image_auth" />';


      $_html .= '&nbsp;&nbsp;&nbsp;<img id="imageauthpage" src="'.$data_img['block_auth'].'">';
      if(Tools::strlen($data_img['img_blockauth']))
        $_html .= '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="imageauthpage-click" style="text-decoration:underline" onclick="return_default_img(\'auth\',\''.$this->l('Are you sure you want to remove this item?').'\')">'.$this->l('Click here to return the default image').'</a>';
    $_html .= '<p>Allow formats *.jpg; *.jpeg; *.png; *.gif.</p>';
      $_html .= '</div>';


      // enable or disable Advertising Text on Authentication page
      $_html .= '<label>'.$this->l('Enable or Disable Advertising Text on Authentication page').':</label>
        <div class="margin-form">

          <input type="radio" value="1" id="text_list_on" name="advauthis_on" onclick="enableOrDisableAuth(1)"
              '.(Tools::getValue('advauthis_on', Configuration::get($this->name.'advauthis_on')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_on" class="t">
            <img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
          </label>

          <input type="radio" value="0" id="text_list_off" name="advauthis_on" onclick="enableOrDisableAuth(0)"
               '.(!Tools::getValue('advauthis_on', Configuration::get($this->name.'advauthis_on')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_off" class="t">
            <img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
          </label>

          <p class="clear">'.$this->l('Enable or Disable Advertising Text on Authentication page').'.</p>
        </div>';

      $_html .= '<script type="text/javascript">
            function enableOrDisableAuth(id)
            {
            if(id==0){
              $("#auth-fb-settings").hide(200);
            } else {
              $("#auth-fb-settings").show(200);
            }

            }
          </script>';

      $_html .= '<div id="auth-fb-settings" '.(Configuration::get($this->name.'advauthis_on')==1?'style="display:block"':'style="display:none"').'>';



      //  type adv text on Authentication page
      $_html .= '<label>'.$this->l('Advertising text on Authentication page').':</label>

            <div class="margin-form">
            <select class="select" name="adv_typeauth" onChange="selectItemsAdvAuth(this.selectedIndex)"
              id="adv_typeauth">
            <option '.(Tools::getValue('adv_typeauth', Configuration::get($this->name.'adv_typeauth'))  == 1 ? 'selected="selected" ' : '').' value="1">'.$this->l('Standart Advertising Text on Authentication page').'</option>
            <option '.(Tools::getValue('adv_typeauth', Configuration::get($this->name.'adv_typeauth')) == 2 ? 'selected="selected" ' : '').' value="2">'.$this->l('Custom Advertising Text on Authentication page').'</option>
          </select>

          <p class="clear">'.$this->l('You can choose one of two types: 1. Standart Advertising Text on Authentication page 2. Custom Advertising Text on Authentication page').'</p>

        </div>

    <script type="text/javascript">
      function selectItemsAdvAuth(id)
      {

      if(id==1){
        $("#sd-standartauth").hide();
        $("#sd-customauth").show(200);
      } else {
        $("#sd-customauth").hide();
        $("#sd-standartauth").show(200);
      }

      }
    </script>
        ';
      $_html .= '<div id="sd-standartauth"
            '.(Tools::getValue('adv_typeauth', Configuration::get($this->name.'adv_typeauth')) == 1 ? '' : 'style="display:none" ').'>';

    $_html .= '<label>'.$this->l('Standart Advertising Text on Authentication page').':</label>

            <div class="margin-form">
            <p style="font-size:14px;padding-top:3px;font-weight:bold">
          '.$this->standart_text.'
          </p>
          <img src="../modules/'.$this->name.'/i/standart_text_auth.png" />
          <p class="clear">'.$this->l('Standart Advertising Text on Authentication page').'</p>
        </div>';
      $_html .= '</div>';


      $_html .= '<div id="sd-customauth" '.(Tools::getValue('adv_typeauth', Configuration::get($this->name.'adv_typeauth'))  == 2 ? '' : 'style="display:none"').'>';


      // advertising text
      $_html .= '<label>'.$this->l('Custom Advertising Text on Authentication page').':</label>

            <div class="margin-form">';

        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);

        foreach ($languages as $language){
      $id_lng = (int)$language['id_lang'];
        $coupondesc = Configuration::get($this->name.'advtextauth'.'_'.$id_lng);


      $_html .= ' <div id="advtextauth_'.$language['id_lang'].'"
               style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;"
               >

            <input type="text" style="width:400px"
                  id="advtextauth_'.$language['id_lang'].'"
                  name="advtextauth_'.$language['id_lang'].'"
                  value="'.htmlentities(Tools::stripslashes($coupondesc), ENT_COMPAT, 'UTF-8').'"/>
            </div>';
        }
      $_html .= '';
      ob_start();
      $this->displayFlags($languages, $defaultLanguage, $divLangName, 'advtextauth');
      $displayflags = ob_get_clean();
      $_html .= $displayflags;
      $_html .= '<div style="clear:both"></div>';
      $_html .= '<img src="../modules/'.$this->name.'/i/custom_text_auth.png" />';
      $_html .= '<p class="clear">'.$this->l('Custom Advertising Text on Authentication page').'</p>';
      $_html .= '</div>';
      // advertising text
      $_html .= '</div>';

      $_html .= '</div>';


      $_html .= '<h3 class="title-block-content">'.$this->l('Facebook Connect in the block with a link Log In Settings').'</h3>';


      // enable or disable Facebook Connect on Authentication page
      $_html .= '<label>'.$this->l('Enable or Disable Facebook Connect in the block with a link Log In').':</label>
        <div class="margin-form">

          <input type="radio" value="1" id="text_list_on" name="floginauthis_on"  onclick="enableOrDisableLogin(1)"
              '.(Tools::getValue('floginauthis_on', Configuration::get($this->name.'floginauthis_on')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_on" class="t">
            <img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
          </label>

          <input type="radio" value="0" id="text_list_off" name="floginauthis_on"  onclick="enableOrDisableLogin(0)"
               '.(!Tools::getValue('floginauthis_on', Configuration::get($this->name.'floginauthis_on')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_off" class="t">
            <img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
          </label>

          <p class="clear">'.$this->l('Enable or Disable Facebook Connect in the block with a link Log In').'.</p>
        </div>';

        $_html .= '<script type="text/javascript">
            function enableOrDisableLogin(id)
            {
            if(id==0){
              $("#login-fb-settings").hide(200);
            } else {
              $("#login-fb-settings").show(200);
            }

            }
          </script>';

      $_html .= '<div id="login-fb-settings" '.(Configuration::get($this->name.'floginauthis_on')==1?'style="display:block"':'style="display:none"').'>';


      $_html .= '<label>'.$this->l('Facebook Connect Image in the block with a link Log In').'</label>

            <div class="margin-form">
          <input type="file" name="post_image_login" id="post_image_login" />';


      $_html .= '&nbsp;&nbsp;&nbsp;<img id="imagelogin" src="'.$data_img['block_blocklogin'].'">';
      if(Tools::strlen($data_img['img_blocklogin']))
        $_html .= '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="imagelogin-click" style="text-decoration:underline" onclick="return_default_img(\'login\',\''.$this->l('Are you sure you want to remove this item?').'\')">'.$this->l('Click here to return the default image').'</a>';
    $_html .= '<p>Allow formats *.jpg; *.jpeg; *.png; *.gif.</p>';
      $_html .= '</div>';


      $_html .= '</div>';


      $_html .= '<p class="center" style="padding: 10px; margin-top: 10px;">
          <input type="submit" name="facebooksettings" value="'.$this->l('Update settings').'"
                       class="button"  />
                  </p>';

      $_html .= '</form>';
      return $_html;
    }

    private function _fancouponSettings(){
      $cookie = $this->context->cookie;

    $_html = '';

      $_html .= '<h3 class="title-block-content">'.$this->l('Fan Coupon Settings').'</h3>';


      $_html .= '<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'">';


      // enable or disable vouchers
      $_html .= '<label>'.$this->l('Enable or Disable Fan Coupon').':</label>
        <div class="margin-form">

          <input type="radio" value="1" id="text_list_on" name="viscoupon_on" onclick="enableOrDisableCoupon(1)"
              '.(Tools::getValue('viscoupon_on', Configuration::get($this->name.'viscoupon_on')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_on" class="t">
            <img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
          </label>

          <input type="radio" value="0" id="text_list_off" name="viscoupon_on" onclick="enableOrDisableCoupon(0)"
               '.(!Tools::getValue('viscoupon_on', Configuration::get($this->name.'viscoupon_on')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_off" class="t">
            <img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
          </label>

          <p class="clear">'.$this->l('Enable or Disable Fan Coupon').'.</p>
        </div>';


      $_html .= '<script type="text/javascript">
            function enableOrDisableCoupon(id)
            {
            if(id==0){
              $("#block-fan-coupon-settings").hide(200);
            } else {
              $("#block-fan-coupon-settings").show(200);
            }

            }
          </script>';

    $_html .= '<div id="block-fan-coupon-settings" '.(Configuration::get($this->name.'viscoupon_on')==1?'style="display:block"':'style="display:none"').'>';


      // Position block Fan Coupon


    $psrightColumn  = Configuration::get($this->name.'_psrightColumn');
    $psleftColumn = Configuration::get($this->name.'_psleftColumn');
    $psextraLeft = Configuration::get($this->name.'_psextraLeft');

    $psproductFooter = Configuration::get($this->name.'_psproductFooter');
    $psproductActions  = Configuration::get($this->name.'_psproductActions');
    $psextraRight = Configuration::get($this->name.'_psextraRight');

    $pscheckoutPage = Configuration::get($this->name.'_pscheckoutPage');
    $pshome = Configuration::get($this->name.'_pshome');

    $_html .= '<style type="text/css">
      .choose_hooks input{margin-bottom: 10px}
    </style>

            <label>'.$this->l('Position Fan Block').':</label>
        <div class="margin-form choose_hooks">
            <table style="width:80%;">
              <tr>
                <td style="width: 33%">'.$this->l('Right column').'</td>
                <td style="width: 33%">'.$this->l('Left column').'</td>
                <td style="width: 33%">'.$this->l('Extra left').'</td>
              </tr>
              <tr>
                <td>
                  <input type="checkbox" name="psrightColumn" '.($psrightColumn == 'psrightColumn' ? 'checked="checked"' : '').' value="psrightColumn"/>
                </td>
                <td>
                  <input type="checkbox" name="psleftColumn" '.($psleftColumn == 'psleftColumn' ? 'checked="checked"' : '').' value="psleftColumn"/>
                </td>
                <td>
                  <input type="checkbox" name="psextraLeft" '.($psextraLeft == 'psextraLeft' ? 'checked="checked"' : '').' value="psextraLeft"/>
                </td>
              </tr>
              <tr>
                <td>'.$this->l('Product footer').'</td>
                <td>'.$this->l('Product actions').'</td>
                <td>'.$this->l('Extra right').'</td>
              </tr>
              <tr>
                <td>
                  <input type="checkbox" name="psproductFooter" '.($psproductFooter == 'psproductFooter' ? 'checked="checked"' : '').' value="psproductFooter"/>
                </td>
                <td>
                  <input type="checkbox" name="psproductActions" '.($psproductActions == 'psproductActions' ? 'checked="checked"' : '').' value="psproductActions"/>
                </td>
                <td>
                  <input type="checkbox" name="psextraRight" '.($psextraRight == 'psextraRight' ? 'checked="checked"' : '').' value="psextraRight"/>
                </td>
              </tr>
              <tr>
                <td>'.$this->l('Order / Checkout Page').'</td>
                <td>'.$this->l('Home').'</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>
                  <input type="checkbox" name="pscheckoutPage" '.($pscheckoutPage == 'pscheckoutPage' ? 'checked="checked"' : '').' value="pscheckoutPage"/>
                </td>
                <td>
                  <input type="checkbox" name="pshome" '.($pshome == 'pshome' ? 'checked="checked"' : '').' value="pshome"/>

                </td>
                <td>&nbsp;</td>
              </tr>

            </table>

          </div>';



    $_html .= '<br/><br/><br/>';

    if($this->_like_button_settings==1){

    $_html .= '<label>'.$this->l('Facebook Button Layout Style').':</label>
        <div class="margin-form">
          <select class=" select" name="likelayout"
              id="likelayout">
            <option '.((Tools::getValue('likelayout', Configuration::get($this->name.'likelayout')) == 'standard'
                  || Tools::getValue('likelayout', Configuration::get($this->name.'likelayout'))
                  ) ? 'selected="selected" ' : '').' value="standard">Standard</option>
            <option '.((Tools::getValue('likelayout', Configuration::get($this->name.'likelayout')) == 'button_count') ? 'selected="selected" ' : '').' value="button_count">'.$this->l('Button Count').'</option>
            <option '.((Tools::getValue('likelayout', Configuration::get($this->name.'likelayout')) == 'box_count') ? 'selected="selected" ' : '').' value="box_count">'.$this->l('Box Count').'</option>
            <option '.((Tools::getValue('likelayout', Configuration::get($this->name.'likelayout')) == 'button') ? 'selected="selected" ' : '').' value="button">'.$this->l('Button').'</option>
          </select>
        </div>';

    //Show faces
      $_html .= '<label>'.$this->l('Show friends faces').':</label>
        <div class="margin-form">

          <input type="radio" value="1" id="text_list_on" name="show_face"
              '.(Tools::getValue('show_face', Configuration::get($this->name.'show_face')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_on" class="t">
            <img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
          </label>

          <input type="radio" value="0" id="text_list_off" name="show_face"
               '.(!Tools::getValue('show_face', Configuration::get($this->name.'show_face')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_off" class="t">
            <img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
          </label>
          <p class="clear"><b>'.$this->l('WARNING:').'</b> '.$this->l('Only when Facebook Button Layout Style = Standard').'. '.$this->l('More info').' <a href=https://developers.facebook.com/docs/plugins/like-button/ target=_blank>https://developers.facebook.com/docs/plugins/like-button/</a></p>
        </div>';

      // Width Facebook Like
    $_html .= '<label>'.$this->l('Width Facebook Like').':</label>

        <div class="margin-form">
        <input type="text" name="fwidth" style="width:400px"
                 value="'.Tools::getValue('fwidth', Configuration::get($this->name.'fwidth')).'">
        <p class="clear">'.$this->l('Width Facebook Like').'</p>

      </div>';
      // Width Facebook Like


    // height Facebook Like
    $_html .= '<label>'.$this->l('Height Facebook Like').':</label>

        <div class="margin-form">
        <input type="text" name="fheight" style="width:400px"
                 value="'.Tools::getValue('fheight', Configuration::get($this->name.'fheight')).'">
        <p class="clear">'.$this->l('Height Facebook Like').'</p>

      </div>';
      // height Facebook Like
    }

    // Fan Page URL
    $_html .= '<label>'.$this->l('Fan Page URL').':</label>

        <div class="margin-form">
        <input type="text" name="fanpageurl" style="width:400px"
                 value="'.Tools::getValue('fanpageurl', Configuration::get($this->name.'fanpageurl')).'">
        <p class="clear">'.$this->l('Fan Page URL').'</p>

      </div>';
      // Fan Page URL



      $divLangName = "fcoupondesc¤blockfantitletxt¤blockfanadvtxt";

      // Title Facebook Fan block
      $_html .= '<label>'.$this->l('Title Facebook Fan block').':</label>

            <div class="margin-form">';

        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);

        foreach ($languages as $language){
      $id_lng = (int)$language['id_lang'];
        $coupondesc = Configuration::get($this->name.'blockfantitletxt'.'_'.$id_lng);


      $_html .= ' <div id="blockfantitletxt_'.$language['id_lang'].'"
               style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;"
               >

            <input type="text" style="width:400px"
                  id="blockfantitletxt_'.$language['id_lang'].'"
                  name="blockfantitletxt_'.$language['id_lang'].'"
                  value="'.htmlentities(Tools::stripslashes($coupondesc), ENT_COMPAT, 'UTF-8').'"/>
            </div>';
        }
      $_html .= '';
      ob_start();
      $this->displayFlags($languages, $defaultLanguage, $divLangName, 'blockfantitletxt');
      $displayflags = ob_get_clean();
      $_html .= $displayflags;
      $_html .= '<div style="clear:both"></div>';
      $_html .= '<p class="clear">'.$this->l('Title Facebook Fan block').'</p>';
      $_html .= '</div>';
      // Title Facebook Fan block



      $_html .= '<label>'.$this->l('Advertising text in Facebook Fan block').':</label>

            <div class="margin-form">';

        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);

        foreach ($languages as $language){
      $id_lng = (int)$language['id_lang'];
        $coupondesc = Configuration::get($this->name.'blockfanadvtxt'.'_'.$id_lng);


      $_html .= ' <div id="blockfanadvtxt_'.$language['id_lang'].'"
               style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;"
               >

            <input type="text" style="width:400px"
                  id="blockfanadvtxt_'.$language['id_lang'].'"
                  name="blockfanadvtxt_'.$language['id_lang'].'"
                  value="'.htmlentities(Tools::stripslashes($coupondesc), ENT_COMPAT, 'UTF-8').'"/>
            </div>';
        }
      $_html .= '';
      ob_start();
      $this->displayFlags($languages, $defaultLanguage, $divLangName, 'blockfanadvtxt');
      $displayflags = ob_get_clean();
      $_html .= $displayflags;
      $_html .= '<div style="clear:both"></div>';
      $_html .= '<p class="clear">'.$this->l('Advertising text in Facebook Fan block').'</p>';
      $_html .= '</div>';
      // advertising text

      // Fan Coupon Description
      $_html .= '<label>'.$this->l('Fan Coupon Description:').'</label>

            <div class="margin-form">';

        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);

        foreach ($languages as $language){
      $id_lng = (int)$language['id_lang'];
        $coupondesc = Configuration::get($this->name.'fcoupondesc'.'_'.$id_lng);


      $_html .= ' <div id="fcoupondesc_'.$language['id_lang'].'"
               style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;"
               >

            <input type="text" style="width:400px"
                  id="fcoupondesc_'.$language['id_lang'].'"
                  name="fcoupondesc_'.$language['id_lang'].'"
                  value="'.htmlentities(Tools::stripslashes($coupondesc), ENT_COMPAT, 'UTF-8').'"/>
            </div>';
        }
      $_html .= '';
      ob_start();
      $this->displayFlags($languages, $defaultLanguage, $divLangName, 'fcoupondesc');
      $displayflags = ob_get_clean();
      $_html .= $displayflags;
      $_html .= '<div style="clear:both"></div>';
      $_html .= '<p class="clear">'.$this->l('Brief description of a fan coupon code').'</p>';
      $_html .= '</div>';
      // Voucher Description

    // Voucher code
    $_html .= '<label>'.$this->l('Voucher code').':</label>

        <div class="margin-form">
        <input type="text" name="fvouchercode" size="5" maxlength="5"
                 value="'.Tools::getValue('fvouchercode', Configuration::get($this->name.'fvouchercode')).'">
        <p class="clear">'.$this->l('Voucher code prefix. It must be at least 3 letters long. Prefix voucher code will be used in the first part of the coupon code, which the user will use to get a discount.').'</p>

      </div>';
      // Voucher code


    // discount type
      $_html .= '<label>'.$this->l('Discount Type:').'</label>

            <div class="margin-form">
            <select class="select" name="fdiscount_type" onChange="selectItemsFan(this.selectedIndex)"
              id="fdiscount_type">
            <option '.(Tools::getValue('fdiscount_type', Configuration::get($this->name.'fdiscount_type'))  == 1 ? 'selected="selected" ' : '').' value="1">'.$this->l('Percentages').'</option>
            <option '.(Tools::getValue('fdiscount_type', Configuration::get($this->name.'fdiscount_type')) == 2 ? 'selected="selected" ' : '').' value="2">'.$this->l('Currency').'</option>
          </select>

        </div>

    <script type="text/javascript">
      function selectItemsFan(id)
      {
      if(id==0){
        $("#fan-sd-currency").hide();
        $("#fan-sd-percentage").show(200);
      } else {
        $("#fan-sd-percentage").hide();
        $("#fan-sd-currency").show(200);
      }

      }
    </script>
        ';

      if($this->_is16)
        $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
      else
        $cur = Currency::getCurrencies();


      // Discount Amount

      $_html .= '<div id="fan-sd-currency"
            '.(Tools::getValue('fdiscount_type', Configuration::get($this->name.'fdiscount_type')) == 2 ? '' : 'style="display:none" ').'>

        <label style="font-size: 13px; font-weight: bold; color: rgb(0, 0, 0);">'.$this->l('Discount Amount:').
                  '</label>';

      foreach ($cur AS $_cur){
        if(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency']){
      $_html .= '<div class="margin-form">'.(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency'] ? '<span style="font-weight: bold;font-size:12px">' : '').htmlentities($_cur['name'], ENT_NOQUOTES, 'utf-8').(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency'] ? '<span style="font-weight: bold;">' : '').'
                <input type="text" name="fsdamount['.(int)($_cur['id_currency']).']" id="fsdamount['.(int)($_cur['id_currency']).']" value="'.Tools::getValue('fsdamount['.(int)($_cur['id_currency']).']', Configuration::get('fsdamount_'.(int)($_cur['id_currency']))).'"
                    style="width: 50px; text-align: right;" /> '.$_cur['sign'].'
            </div>';
        }
      }

      $_html .= '<label style="font-size: 13px; font-weight: bold; color: rgb(0, 0, 0);">'.$this->l('Tax').':</label>';

      $_html .= '<div class="margin-form">';
      $_html .= '<select style="display: block;" name="taxf" id="taxf">
                        <option '.(Tools::getValue('taxf', Configuration::get($this->name.'taxf'))  == 0 ? 'selected="selected" ' : '').' value="0">'.$this->l('Tax Excluded').'</option>
                        <option '.(Tools::getValue('taxf', Configuration::get($this->name.'taxf'))  == 1 ? 'selected="selected" ' : '').' value="1">'.$this->l('Tax Included').'</option>
                    </select>';
      $_html .= '</div>';

      $_html .= '<div style="clear:both"></div>';

      //$_html .= '</table>
      $_html .= '</div>

      <div id="fan-sd-percentage" '.(Tools::getValue('fdiscount_type', Configuration::get($this->name.'fdiscount_type'))  == 1 ? '' : 'style="display:none"').'>
      <label style="font-size: 13px; font-weight: bold; color: rgb(0, 0, 0);">'.$this->l('Voucher percentage:').'</label>
      <div class="margin-form">
      <input type="text" name="fpercentage_val"

         value="'.Tools::getValue('fpercentage_val', Configuration::get($this->name.'fpercentage_val')).'">&nbsp;%
    </div>


      ';

      $_html .= '<label style="font-size: 13px; font-weight: bold; color: rgb(0, 0, 0);">'.$this->l('Tax').':</label>';

      $_html .= '<div class="margin-form">';
      $_html .= '<select style="display: block;" name="taxpercf" id="taxpercf">
                        <option '.(Tools::getValue('taxpercf', Configuration::get($this->name.'taxpercf'))  == 0 ? 'selected="selected" ' : '').' value="0">'.$this->l('Tax Excluded').'</option>
                        <option '.(Tools::getValue('taxpercf', Configuration::get($this->name.'taxpercf'))  == 1 ? 'selected="selected" ' : '').' value="1">'.$this->l('Tax Included').'</option>
                    </select>';
      $_html .= '</div>';


    $_html .= '<div style="clear:both"></div>';

    $_html .= '</div>

      ';


      $_html .= '<label>'.$this->l('Minimum checkout').':</label>

            <div class="margin-form">
            <input type="checkbox" value="'.(Configuration::get($this->name.'fanisminamount') == true ? 1 : 0).'"
            name="'.$this->name.'fanisminamount" id="'.$this->name.'fanisminamount"
            '.(Configuration::get($this->name.'fanisminamount') == true ? 'checked="checked" ' : '').'>


        </div>

    <script type="text/javascript">

    $("#'.$this->name.'fanisminamount").change(function() {
        if($(this).is(":checked")) {
            $("#'.$this->name.'fanisminamount").val($(this).is(":checked"));

            $("#fan-fanisminamount").show(200);
        } else {
          $("#fan-fanisminamount").hide(200);
        }
        });

      </script>
        ';

      $_html .= '<div id="fan-fanisminamount"
            '.(Configuration::get($this->name.'fanisminamount') == true? '' : 'style="display:none" ').'>';

      $_html .= ' <div class="margin-form">
            <table cellpadding="5" style="border: 1px solid #BBB;" border="0">
                      <tr>
                        <th style="width: 80px;">'.$this->l('Currency').'</th>
                        <th>'.$this->l('Minimum checkout').'</th>
                      </tr>';
      if($this->_is16)
        $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
      else
        $cur = Currency::getCurrencies();

    foreach ($cur AS $_cur)
          $_html .= '<tr>
                  <td>
                    '.(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency'] ? '<span style="font-weight: bold;">' : '').htmlentities($_cur['name'], ENT_NOQUOTES, 'utf-8').(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency'] ? '</span>' : '').'
                  </td>
                  <td>
                      <input type="text" name="fanfsdminamount['.(int)($_cur['id_currency']).']" id="fanfsdminamount['.(int)($_cur['id_currency']).']" value="'.(int)Tools::getValue('fanfsdminamount['.(int)($_cur['id_currency']).']', Configuration::get('fanfsdminamount_'.(int)($_cur['id_currency']))).'"
                      style="width: 50px; text-align: right;" /> '.$_cur['sign'].'
                  </td>
                </tr>
                  ';
    $_html .= '</table></div>';

      $_html .= '</div>';

      $_html .= '<br/>';

      // select categories
      $_html .= '
            <label>'.$this->l('Select categories').':</label>
              <div class="margin-form" style="margin-bottom:20px">';

      $cat = new Category();
      $list_cat = $cat->getCategories($cookie->id_lang);

      $_html .= '<table class="table">';
      $_html .= '<tr>
            <th><input type="checkbox" onclick="checkDelBoxes(this.form, \'fancategoryBox[]\', this.checked)" class="noborder" name="checkme"></th>
            <th>ID</th>
            <th style="width: 400px">'.$this->l('Name').'</th>
            </tr>';
      $current_cat = Category::getRootCategory()->id;
      ob_start();
      $this->recurseCategoryForInclude($list_cat, $list_cat, $current_cat, 1, null, "fan");
      $cat_option = ob_get_clean();

      $_html .= $cat_option;

      $_html .= '</table>';

      $_html .= '</div>';

      // select categories

    $_html .= '<br/>';

      // Number of Refferals
      $_html .= '<label>'.$this->l('Term of validity').':</label>

            <div class="margin-form">
          <input type="text" name="fsdvvalid"  style="width: 50px"
                          value="'.Tools::getValue('fsdvvalid', Configuration::get($this->name.'fsdvvalid')).'">&nbsp; '.$this->l('Days').'
               <p class="clear">'.$this->l('Voucher term of validity in days.').'</p>
        </div>';


      // Cumulative with others vouchers
      $_html .= '<label>'.$this->l('Cumulative with others vouchers').':</label>
        <div class="margin-form">

          <input type="radio" value="1" id="text_list_on" name="fancumulativeother"
              '.(Tools::getValue('fancumulativeother', Configuration::get($this->name.'fancumulativeother')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_on" class="t">
            <img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
          </label>

          <input type="radio" value="0" id="text_list_off" name="fancumulativeother"
               '.(!Tools::getValue('fancumulativeother', Configuration::get($this->name.'fancumulativeother')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_off" class="t">
            <img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
          </label>

        </div>';
      $_html .='<br/>';

      // Cumulative with price reductions
      $_html .= '<label>'.$this->l('Cumulative with price reductions').':</label>
        <div class="margin-form">

          <input type="radio" value="1" id="text_list_on" name="fancumulativereduc"
              '.(Tools::getValue('fancumulativereduc', Configuration::get($this->name.'fancumulativereduc')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_on" class="t">
            <img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
          </label>

          <input type="radio" value="0" id="text_list_off" name="fancumulativereduc"
               '.(!Tools::getValue('fancumulativereduc', Configuration::get($this->name.'fancumulativereduc')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_off" class="t">
            <img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
          </label>

        </div>';

      $_html .= '</div>';

      $_html .= '<p class="center" style="padding: 10px; margin-top: 10px;">
          <input type="submit" name="fancouponsettings" value="'.$this->l('Update settings').'"
                       class="button"  />
                  </p>';

      $_html .= '</form>';

      return $_html;
    }
  private function _voucherSettings(){
      $cookie = $this->context->cookie;

    $_html = '';

      $_html .= '<h3 class="title-block-content">'.$this->l('Voucher Settings').'</h3>';


      $_html .= '<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'">';


      // enable or disable vouchers
      $_html .= '<label>'.$this->l('Enable or Disable Voucher').':</label>
        <div class="margin-form">

          <input type="radio" value="1" id="text_list_on" name="vis_on" onclick="enableOrDisable(1)"
              '.(Tools::getValue('vis_on', Configuration::get($this->name.'vis_on')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_on" class="t">
            <img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
          </label>

          <input type="radio" value="0" id="text_list_off" name="vis_on" onclick="enableOrDisable(0)"
               '.(!Tools::getValue('vis_on', Configuration::get($this->name.'vis_on')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_off" class="t">
            <img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
          </label>

          <p class="clear">'.$this->l('Enable or Disable Voucher').'.</p>
        </div>';

      $_html .= '<script type="text/javascript">
            function enableOrDisable(id)
            {
            if(id==0){
              $("#block-voucher-settings").hide(200);
            } else {
              $("#block-voucher-settings").show(200);
            }

            }
          </script>';

    $_html .= '<div id="block-voucher-settings" '.(Configuration::get($this->name.'vis_on')==1?'style="display:block"':'style="display:none"').'>';
      $divLangName = "coupondesc";

      // Voucher Description
      $_html .= '<label>'.$this->l('Voucher Description:').'</label>

            <div class="margin-form">';

        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(false);

        foreach ($languages as $language){
      $id_lng = (int)$language['id_lang'];
        $coupondesc = Configuration::get($this->name.'coupondesc'.'_'.$id_lng);


      $_html .= ' <div id="coupondesc_'.$language['id_lang'].'"
               style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;"
               >

            <input type="text" style="width:400px"
                  id="coupondesc_'.$language['id_lang'].'"
                  name="coupondesc_'.$language['id_lang'].'"
                  value="'.htmlentities(Tools::stripslashes($coupondesc), ENT_COMPAT, 'UTF-8').'"/>
            </div>';
        }
      $_html .= '';
      ob_start();
      $this->displayFlags($languages, $defaultLanguage, $divLangName, 'coupondesc');
      $displayflags = ob_get_clean();
      $_html .= $displayflags;
      $_html .= '<div style="clear:both"></div>';
      $_html .= '<p class="clear">'.$this->l('Brief description of a voucher code').'</p>';
      $_html .= '</div>';
      // Voucher Description

    // Voucher code
    $_html .= '<label>'.$this->l('Voucher code').':</label>

        <div class="margin-form">
        <input type="text" name="vouchercode" size="5" maxlength="5"
                 value="'.Tools::getValue('vouchercode', Configuration::get($this->name.'vouchercode')).'">
        <p class="clear">'.$this->l('Voucher code prefix. It must be at least 3 letters long. Prefix voucher code will be used in the first part of the coupon code, which the user will use to get a discount.').'</p>

      </div>';
      // Voucher code


    // discount type
      $_html .= '<label>'.$this->l('Discount Type:').'</label>

            <div class="margin-form">
            <select class="select" name="discount_type" onChange="selectItemsFb(this.selectedIndex)"
              id="discount_type">
            <option '.(Tools::getValue('discount_type', Configuration::get($this->name.'discount_type'))  == 1 ? 'selected="selected" ' : '').' value="1">'.$this->l('Percentages').'</option>
            <option '.(Tools::getValue('discount_type', Configuration::get($this->name.'discount_type')) == 2 ? 'selected="selected" ' : '').' value="2">'.$this->l('Currency').'</option>
          </select>

        </div>

    <script type="text/javascript">
      function selectItemsFb(id)
      {
      if(id==0){
        $("#sd-currency").hide();
        $("#sd-percentage").show(200);
      } else {
        $("#sd-percentage").hide();
        $("#sd-currency").show(200);
      }

      }
    </script>
        ';

      if($this->_is16)
        $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
      else
        $cur = Currency::getCurrencies();


      // Discount Amount

      $_html .= '<div id="sd-currency"
            '.(Tools::getValue('discount_type', Configuration::get($this->name.'discount_type')) == 2 ? '' : 'style="display:none" ').'>

        <label style="font-size: 13px; font-weight: bold; color: rgb(0, 0, 0);">'.$this->l('Discount Amount:').
                  '</label>';

      foreach ($cur AS $_cur){
        if(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency']){
      $_html .= '<div class="margin-form">'.(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency'] ? '<span style="font-weight: bold;font-size:12px">' : '').htmlentities($_cur['name'], ENT_NOQUOTES, 'utf-8').(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency'] ? '<span style="font-weight: bold;">' : '').'
                <input type="text" name="sdamount['.(int)($_cur['id_currency']).']" id="sdamount['.(int)($_cur['id_currency']).']" value="'.Tools::getValue('sdamount['.(int)($_cur['id_currency']).']', Configuration::get('sdamount_'.(int)($_cur['id_currency']))).'"
                    style="width: 50px; text-align: right;" /> '.$_cur['sign'].'
            </div>';
        }
      }
      $_html .= '<label style="font-size: 13px; font-weight: bold; color: rgb(0, 0, 0);">'.$this->l('Tax').':</label>';

      $_html .= '<div class="margin-form">';
      $_html .= '<select style="display: block;" name="taxc" id="taxc">
                        <option '.(Tools::getValue('taxc', Configuration::get($this->name.'taxc'))  == 0 ? 'selected="selected" ' : '').' value="0">'.$this->l('Tax Excluded').'</option>
                        <option '.(Tools::getValue('taxc', Configuration::get($this->name.'taxc'))  == 1 ? 'selected="selected" ' : '').' value="1">'.$this->l('Tax Included').'</option>
                    </select>';
      $_html .= '</div>';

      $_html .= '<div style="clear:both"></div>';

      //$_html .= '</table>
      $_html .= '</div>

      <div id="sd-percentage" '.(Tools::getValue('discount_type', Configuration::get($this->name.'discount_type'))  == 1 ? '' : 'style="display:none"').'>
      <label style="font-size: 13px; font-weight: bold; color: rgb(0, 0, 0);">'.$this->l('Voucher percentage:').'</label>
      <div class="margin-form">
      <input type="text" name="percentage_val"

         value="'.Tools::getValue('percentage_val', Configuration::get($this->name.'percentage_val')).'">&nbsp;%
    </div>


      ';

      $_html .= '<label style="font-size: 13px; font-weight: bold; color: rgb(0, 0, 0);">'.$this->l('Tax').':</label>';

      $_html .= '<div class="margin-form">';
      $_html .= '<select style="display: block;" name="taxpercc" id="taxpercc">
                        <option '.(Tools::getValue('taxpercc', Configuration::get($this->name.'taxpercc'))  == 0 ? 'selected="selected" ' : '').' value="0">'.$this->l('Tax Excluded').'</option>
                        <option '.(Tools::getValue('taxpercc', Configuration::get($this->name.'taxpercc'))  == 1 ? 'selected="selected" ' : '').' value="1">'.$this->l('Tax Included').'</option>
                    </select>';
      $_html .= '</div>';


    $_html .= '<div style="clear:both"></div>';

    $_html .= '</div>

      ';

      $_html .= '<br/>';

      $_html .= '<label>'.$this->l('Minimum checkout').':</label>

            <div class="margin-form">
            <input type="checkbox" value="'.(Configuration::get($this->name.'isminamount') == true ? 1 : 0).'"
            name="'.$this->name.'isminamount" id="'.$this->name.'isminamount"
            '.(Configuration::get($this->name.'isminamount') == true ? 'checked="checked" ' : '').'>


        </div>

    <script type="text/javascript">

    $("#'.$this->name.'isminamount").change(function() {
        if($(this).is(":checked")) {
            //alert("check");
            $("#'.$this->name.'isminamount").val($(this).is(":checked"));

            $("#fan-isminamount").show(200);
        } else {
          //alert("no check");
            $("#fan-isminamount").hide(200);
        }
        });

      </script>
        ';

      $_html .= '<div id="fan-isminamount"
            '.(Configuration::get($this->name.'isminamount') == true? '' : 'style="display:none" ').'>';

      $_html .= ' <div class="margin-form">
            <table cellpadding="5" style="border: 1px solid #BBB;" border="0">
                      <tr>
                        <th style="width: 80px;">'.$this->l('Currency').'</th>
                        <th>'.$this->l('Minimum checkout').'</th>
                      </tr>';
      if($this->_is16)
        $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
      else
        $cur = Currency::getCurrencies();

    foreach ($cur AS $_cur)
          $_html .= '<tr>
                  <td>
                    '.(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency'] ? '<span style="font-weight: bold;">' : '').htmlentities($_cur['name'], ENT_NOQUOTES, 'utf-8').(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency'] ? '</span>' : '').'
                  </td>
                  <td>
                      <input type="text" name="fsdminamount['.(int)($_cur['id_currency']).']" id="fsdminamount['.(int)($_cur['id_currency']).']" value="'.(int)Tools::getValue('fsdminamount['.(int)($_cur['id_currency']).']', Configuration::get('fsdminamount_'.(int)($_cur['id_currency']))).'"
                      style="width: 50px; text-align: right;" /> '.$_cur['sign'].'
                  </td>
                </tr>
                  ';
    $_html .= '</table></div>';

      $_html .= '</div>';

      $_html .= '<br/>';

      // select categories
      $_html .= '
            <label>'.$this->l('Select categories').':</label>
              <div class="margin-form" style="margin-bottom:20px">';

      $cat = new Category();
      $list_cat = $cat->getCategories($cookie->id_lang);

      $_html .= '<table class="table">';
      $_html .= '<tr>
            <th><input type="checkbox" onclick="checkDelBoxes(this.form, \'categoryBox[]\', this.checked)" class="noborder" name="checkme"></th>
            <th>ID</th>
            <th style="width: 400px">'.$this->l('Name').'</th>
            </tr>';
      $current_cat = Category::getRootCategory()->id;
      ob_start();
      $this->recurseCategoryForInclude($list_cat, $list_cat, $current_cat);
      $cat_option = ob_get_clean();

      $_html .= $cat_option;

      $_html .= '</table>';

      $_html .= '</div>';

      // select categories

    $_html .= '<br/>';

      // Term of validity
      $_html .= '<label>'.$this->l('Term of validity').':</label>

            <div class="margin-form">
          <input type="text" name="sdvvalid"  style="width: 50px"
                          value="'.Tools::getValue('sdvvalid', Configuration::get($this->name.'sdvvalid')).'">&nbsp; '.$this->l('Days').'
               <p class="clear">'.$this->l('Voucher term of validity in days.').'</p>
        </div>';


      // Cumulative with others vouchers
      $_html .= '<label>'.$this->l('Cumulative with others vouchers').':</label>
        <div class="margin-form">

          <input type="radio" value="1" id="text_list_on" name="cumulativeother"
              '.(Tools::getValue('cumulativeother', Configuration::get($this->name.'cumulativeother')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_on" class="t">
            <img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
          </label>

          <input type="radio" value="0" id="text_list_off" name="cumulativeother"
               '.(!Tools::getValue('cumulativeother', Configuration::get($this->name.'cumulativeother')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_off" class="t">
            <img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
          </label>

        </div>';
      $_html .='<br/>';

      // Cumulative with price reductions
      $_html .= '<label>'.$this->l('Cumulative with price reductions').':</label>
        <div class="margin-form">

          <input type="radio" value="1" id="text_list_on" name="cumulativereduc"
              '.(Tools::getValue('cumulativereduc', Configuration::get($this->name.'cumulativereduc')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_on" class="t">
            <img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
          </label>

          <input type="radio" value="0" id="text_list_off" name="cumulativereduc"
               '.(!Tools::getValue('cumulativereduc', Configuration::get($this->name.'cumulativereduc')) ? 'checked="checked" ' : '').'>
          <label for="dhtml_off" class="t">
            <img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
          </label>

        </div>';

      $_html .= '</div>';

      $_html .= '<p class="center" style="padding: 10px; margin-top: 10px;">
          <input type="submit" name="vouchersettings" value="'.$this->l('Update settings').'"
                       class="button"  />
                  </p>';

      $_html .= '</form>';

      return $_html;
    }

    private function _statistics(){

      global $currentIndex;
      $cookie = $this->context->cookie;

      $_html = '';

      $_html .= '<h3 class="title-block-content">'.$this->l('Customers registered through Facebook').'</h3>';

      if(Tools::getValue('pageitems')){
          $start = Tools::getValue('pageitems');
        } else {
          $start = 0;
        }

      include_once(dirname(__FILE__).'/classes/facebookcandvchelp.class.php');
      $obj_help = new facebookcandvchelp();

      if(Tools::getValue('search_query')){
      $data = $obj_help->getCustomersSearch(array('search_query'=>Tools::getValue('search_query')));
    } else {
      $step = $this->_step;
      $data = $obj_help->getCustomers(array('start'=>$start,'step'=>$step));
    }

    $count_all = $data['count_all'];
      $data_info = $data['data'];

      if($count_all>0){

      if(Tools::getValue('search_query')){
      $_html .= '<div style="margin:10px;float:left">';
      $_html .= '<b style="font-size:16px">'.$this->l('Search'). '&nbsp;&nbsp;"'.Tools::getValue('search_query').'"</b>';
        $_html .= '<br/><br/><b>'.$count_all.'&nbsp;'.$this->l('results have been found.').'</b>';
      $_html .= '</div>';
      }

      $_html .= '<div style="margin:10px;float:right">';
      $_html .= '<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" name="find">';
      if(Tools::getValue('search_query')){
      $_html .= '<a onclick="window.location.href = \''.$currentIndex.'&tab=AdminModules&cancel_search=1&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'\';"
                   style="text-decoration: underline; font-size: 11px; cursor: pointer; margin-right: 5px;">
                   '.$this->l('Clear search').'</a>';
      }
      $_html .= '<input type="text" value="'.$this->l('Find Customer').'"
                     onfocus="if(this.value == \''.$this->l('Find Customer').'\') {this.value=\'\';}"
                     onblur="if(this.value == \'\') {this.value=\''.$this->l('Find Customer').'\';}"
                     id="search_query" size="25" name="search_query">
              <input type="image" src="../modules/'.$this->name.'/i/btn/adv_search.png"
                   >
                ';
      $_html .= '</form>';
      $_html .= '</div>';
      $_html .= '<div style="clear:both"></div>';

      $_html .= '<table class="table  customer" style="width: 100%; margin-bottom:10px;">';

      $_html .= '<tr>';
        $_html .= '<th style="padding:5px 1px">'.$this->l('ID').'</th>';
        $_html .= '<th style="padding:5px 1px">'.$this->l('User Name').'</th>';
        if(version_compare(_PS_VERSION_, '1.5', '>')){
          $_html .= '<th style="padding:5px 1px">'.$this->l('Shop').'</th>';
        }
      $_html .= '</tr>';

      foreach($data_info as $_items){
        $uid = $_items['id'];
        $name_user = $_items['firstname']. ' '.$_items['lastname'];
        $name_shop = $_items['name_shop'];

        $_html .= '<tr>';
          $_html .= '<td>'.$uid.'</td>';
          $_html .= '<td><img src="../modules/'.$this->name.'/i/btn/ico-facebook.gif" />'.$name_user.'</td>';
          if(Tools::strlen($name_shop)>0){
            $_html .= '<td>'.$name_shop.'</td>';
          }
       $_html .= '</tr>';
      }
      $_html .= '</table>';

      if(Tools::getValue('search_query')){
        // empty
      } else {
        $paging = $obj_help->PageNav($start,$count_all,$this->_step,
                    array('admin' => 1,'currentIndex'=>$currentIndex,
                        'token' => '&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)),
                        'item' => 'items',
                        'text_page' => $this->l('Page')
                    ));

      $_html .= '<div style="margin:5px">';
      $_html .= $paging;
      $_html .= '</div>';
      }

      } else {

        $_html .= '<div style="text-align:center;border:1px solid #CCCCCC;padding:10px">
          '.$this->l('There are not items yet').'';
        if(Tools::getValue('search_query')){
        $_html .= '<a onclick="window.location.href = \''.$currentIndex.'&tab=AdminModules&cancel_search=1&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'\';"
                   style="text-decoration: underline; font-size: 11px; cursor: pointer; margin-left: 10px;"
                   >'.$this->l('Go to Statistics').'</a>';
        }
        $_html .= '</div>';
      }
      return $_html;
    }


     private function _help_documentation(){
      return '<h3 class="title-block-content">'.$this->l('Help / Documentation').'</h3>'.
          '<b style="text-transform:uppercase">'.$this->l('MODULE DOCUMENTATION ').':</b>&nbsp;<a target="_blank" href="../modules/'.$this->name.'/readme.pdf" style="text-decoration:underline;font-weight:bold">readme.pdf</a>
          <br/><br/>
          <b style="text-transform:uppercase">'.$this->l('CONTACT ').':</b>&nbsp;<a target="_blank" href="mailto:developersaddons@gmail.com" style="text-decoration:underline;font-weight:bold">developersaddons@gmail.com</a>';
    }



 private function _welcome(){
    $cookie = $this->context->cookie;
    $current_language = (int)$cookie->id_lang;
    $iso_lng = Language::getIsoById((int)($current_language));
    $time = time();

      return  '<h3 class="title-block-content">'.$this->l('Welcome').'</h3>'.
          ''
          .$this->l('Welcome and thank you for purchasing the module.').
          '<br/><br/>'
          .$this->l('Facebook Connect, Fan Coupon, Coupon for registration include many settings and statistics of customers registered through Facebook').
          '<br/><br/>'
          .$this->l('To configure module please read').'&nbsp;<b><a style="text-decoration:underline" id="tab-menu-5" onclick="tabs_custom(5)" href="javascript:void(0)">'.$this->l('Help / Documentation').'</a></b>
          <br/><br/><br/><br/>
          <iframe src="http://www.mitrocops.com/promo.php?ts='.$time.'&amp;version='.$this->version.'&amp;name='.$this->name.'&amp;lang='.$iso_lng.'" class="mitrocopspromoiframe"
          style="border: 1px solid #CCCCCC !important;height: 330px !important;overflow: auto !important;width: 670px !important;"></iframe>
          ';
    }

  private function _headercssfiles(){
    $_html = '';
    if(version_compare(_PS_VERSION_, '1.6', '>')){
      $_html .=  '<link rel="stylesheet" media="screen" type="text/css" href="../modules/'.$this->name.'/css/prestashop16.css" />';

      }
    // menu
      $_html .= '<link rel="stylesheet" href="../modules/'.$this->name.'/css/menu.css" type="text/css" />';
      $_html .= '<script type="text/javascript" src="../modules/'.$this->name.'/js/menu.js"></script>';

      return $_html;
  }

  public function translateFB($data=null){
    $cookie = $this->context->cookie;

    $fan = isset($data['fan'])?$data['fan']:0;

    if($fan){
        switch (Configuration::get($this->name.'fdiscount_type'))
        {
          case 1:
            // percent
            $id_discount_type = 1;
            $value = Configuration::get($this->name.'fpercentage_val');
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
    } else{
      // set discount
        switch (Configuration::get($this->name.'discount_type'))
      {
        case 1:
          // percent
          $id_discount_type = 1;
          $value = Configuration::get($this->name.'percentage_val');
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
    }
      $valuta = "%";

      if($id_discount_type == 2){
        if($this->_is16)
            $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
          else
            $cur = Currency::getCurrencies();

        foreach ($cur AS $_cur){
          if(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency']){
              $valuta = $_cur['sign'];
            }
          }
      }



    return array('firsttext' => $this->l('You get voucher for discount'),
           'secondtext' => $this->l('Here is you voucher code'),
           'threetext' => $this->l('It is valid until'),
           'discountvalue' => $value.$valuta,
          'review_voucher'=>$this->l('You get voucher for discount')

           );
  }

  public function translateFanCoupon(){
    $cookie = $this->context->cookie;

    // fan coupon
        // set discount
          switch (Configuration::get($this->name.'fdiscount_type'))
        {
          case 1:
            // percent
            $fid_discount_type = 1;
            $fvalue = Configuration::get($this->name.'fpercentage_val');
            break;
          case 2:
            // currency
            $fid_discount_type = 2;
            $id_currency = (int)$cookie->id_currency;
            $fvalue = Configuration::get('fsdamount_'.(int)$id_currency);
          break;
          default:
            $fid_discount_type = 2;
            $id_currency = (int)$cookie->id_currency;
            $fvalue = Configuration::get('fsdamount_'.(int)$id_currency);
        }
        $fvaluta = "%";

        if($fid_discount_type == 2){
          if($this->_is16)
              $cur = Currency::getCurrenciesByIdShop(Context::getContext()->shop->id);
            else
              $cur = Currency::getCurrencies();

          foreach ($cur AS $_cur){
            if(Configuration::get('PS_CURRENCY_DEFAULT') == $_cur['id_currency']){
                $fvaluta = $_cur['sign'];
              }
            }
        }


      // fan coupon

    return array('firsttext' => $this->l('You get voucher for discount'),
           'secondtext' => $this->l('Here is you voucher code'),
           'threetext' => $this->l('It is valid until'),
           'discountvalue' => $fvalue.$fvaluta,
           'delete_coupon' => $this->l('You lost the right to a discount in our store.'),
           'use_coupon' => $this->l('You have already used a discount coupon in our store.'),
             'already_get_coupon' => $this->l('You have already received a coupon for a discount.'),
           'expiried_voucher' => $this->l('The validity of your coupon for discount has expired.')
           );

  }

public function recurseCategoryForInclude($indexedCategories, $categories, $current, $id_category = 1, $id_category_default = NULL, $prefix = '')
  {
    global $done;
    static $irow;
    $id_obj = (int)(Tools::getValue($this->identifier));

    if (!isset($done[$current['infos']['id_parent']]))
      $done[$current['infos']['id_parent']] = 0;
    $done[$current['infos']['id_parent']] += 1;

    $todo = @sizeof($categories[$current['infos']['id_parent']]);
    $doneC = $done[$current['infos']['id_parent']];

    $level = $current['infos']['level_depth'] + 1;
    $img = $level == 1 ? 'lv1.gif' : 'lv'.$level.'_'.($todo == $doneC ? 'f' : 'b').'.gif';
    echo '
    <tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
      <td>
        <input type="checkbox" name="'.$prefix.'categoryBox[]" class="'.$prefix.'categoryBox'.($id_category_default == $id_category ? ' id_category_default' : '').'" id="categoryBox_'.$id_category.'" value="'.$id_category.'" '.((in_array($id_category,explode(",",Configuration::get($this->name.$prefix.'catbox'))) OR in_array($id_category, $indexedCategories) OR ((int)(Tools::getValue('id_category')) == $id_category AND !(int)($id_obj))) ? ' checked="checked"' : '').' />
      </td>
      <td>
        '.$id_category.'
      </td>
      <td>
        <img src="../modules/'.$this->name.'/i/'.$img.'" alt="" /> &nbsp;<label for="categoryBox_'.$id_category.'" class="t">'.Tools::stripslashes($this->hideCategoryPosition($current['infos']['name'])).'</label>
      </td>
    </tr>';

    if (isset($categories[$id_category]))
      foreach ($categories[$id_category] AS $key => $row)
        if ($key != 'infos')
          $this->recurseCategoryForInclude($indexedCategories, $categories, $categories[$id_category][$key], $key, $id_category_default, $prefix);
  }

  public function recurseCategoryIds($indexedCategories, $categories, $current, $id_category = 1, $id_category_default = NULL)
  {
    global $done;

    // set variables
    static $_idsCat;

    if ($id_category == 1) {
      $_idsCat = null;
    }


    if (!isset($done[$current['infos']['id_parent']]))
      $done[$current['infos']['id_parent']] = 0;
    $done[$current['infos']['id_parent']] += 1;


    $_idsCat[] = (string)$id_category;

    if (isset($categories[$id_category]))
      foreach ($categories[$id_category] AS $key => $row)
        if ($key != 'infos')
          $this->recurseCategoryIds($indexedCategories, $categories, $categories[$id_category][$key], $key, $id_category_default);
    return $_idsCat;
  }

  public function getIdsCategories(){
    /// get all category ids ///
    $cookie = $this->context->cookie;
    $cat = new Category();
    $list_cat = $cat->getCategories($cookie->id_lang);
    $current_cat = Category::getRootCategory()->id;
    $cat_ids = $this->recurseCategoryIds($list_cat, $list_cat, $current_cat);
    $cat_ids = implode(",",$cat_ids);
    return $cat_ids;
    /// get all category ids ///
  }

  public function hideCategoryPosition($name)
  {
    return preg_replace('/^[0-9]+\./', '', $name);
  }

}
