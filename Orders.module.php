<?php
#-------------------------------------------------------------------------
# Module: Orders - A simple example frontend form module
# Version: 1.0, calguy1000 <calguy1000@cmsmadesimple.org>
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/skeleton/
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# However, as a special exception to the GPL, this software is distributed
# as an addon module to CMS Made Simple.  You may not use this software
# in any Non GPL version of CMS Made simple, or in any version of CMS
# Made simple that does not indicate clearly and obviously in every page of
# its admin section that the site was built with CMS Made simple, and
# provide a link to the CMS Made Simple website.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

// this should go into the orders_order class.
define('ORDERSTATUS_PROPOSED','proposed');      // customer has placed order, but not confirmed it yet
define('ORDERSTATUS_PENDING','pending');        // this order has been submitted for payment, but not yet approved.
define('ORDERSTATUS_CONFIRMED','confirmed');    // customer has confirmed order, but not processed payment

define('ORDERSTATUS_SUBMITTED','submitted');    // delete me?
define('ORDERSTATUS_INVOICED','invoiced');      // customer has completed manual invoice process
define('ORDERSTATUS_CANCELLED','cancelled');    // this order has been cancelled (by an admin, or by the user)
define('ORDERSTATUS_PAID','paid');              // this order is paid in full
define('ORDERSTATUS_HOLD','hold');              // this order is on hold
define('ORDERSTATUS_BALANCEDUE','balancedue');  // this order has not been paid in full
define('ORDERSTATUS_INCOMPLETE','incomplete');  // not all items for this order have been shipped
define('ORDERSTATUS_COMPLETED','completed');    // this order has been paid in full, and shipped in full.
define('ORDERSTATUS_SUBSCRIBED','subscribed');  // this order is a subscription only order, and is active.

define('ITEMSTATUS_PENDING','pending');         // this service item has not been delivered
define('ITEMSTATUS_DELIVERED','delivered');     // this service item has been delivered
define('ITEMSTATUS_BACKORDER','backorder');     // this order item is on backorder
define('ITEMSTATUS_SHIPPED','shipped');         // this order item has been shipped
define('ITEMSTATUS_NOTSHIPPED','notshipped');   // this order item has not been shppped
define('ITEMSTATUS_HOLD','hold');               // this order item is on hold

define('ORDERS_PERM_VIEWORDERS','View Orders');
define('ORDERS_PERM_MANAGEORDERS','Manage Orders');
define('ORDERS_PERM_CONTACT_CUSTOMERS','Contact Customers');
define('ORDERS_PERM_DELETE_ORDERS','Delete Orders');

if( !class_exists('CGEcommerceBase') ) cms_utils::get_module('CGEcommerceBase');

final class Orders extends CGEcommerceBase
{
    public function __construct()
    {
        parent::__construct();
        $this->AddImageDir('icons');

        $smarty = CmsApp::get_instance()->GetSmarty();
        if( !$smarty ) return;

        \Orders\smarty_plugins::init( $smarty );
    }

    function GetName() { return 'Orders'; }
    function GetFriendlyName() { return $this->Lang('friendlyname'); }
    function GetVersion() { return '1.23.2'; }
    function AllowAutoInstall() { return FALSE; }
    function AllowAutoUpgrade() { return FALSE; }
    function GetAuthor() { return 'calguy1000'; }
    function GetAuthorEmail() { return 'calguy1000@cmsmadesimple.org'; }
    function IsPluginModule() { return true; }
    function HasAdmin() { return true; }
    function GetAdminSection() { return 'ecommerce'; }
    function GetAdminDescription() { return $this->Lang('moddescription'); }
    function MinimumCMSVersion() { return '2.2.2'; }
    function LazyLoadAdmin() { return TRUE; }
    function LazyLoadFrontend() { return TRUE; }
    function InstallPostMessage() { return $this->Lang('postinstall'); }
    function UninstallPostMessage() { return $this->Lang('postuninstall'); }
    function UninstallPreMessage() { return $this->Lang('really_uninstall'); }
    function HandlesEvents() { return TRUE; }
    function GetEventHelp($event_name) { return $this->Lang('event_help_'.$event_name); }
    function GetEventDescription($event_name) { return $this->Lang('event_desc_'.$event_name); }

    protected function CanViewAdmin()
    {
        return ( $this->CheckPermission(ORDERS_PERM_VIEWORDERS) ||
                 $this->CheckPermission(ORDERS_PERM_MANAGEORDERS) ||
                 $this->CheckPermission(ORDERS_PERM_CONTACT_CUSTOMERS) );
    }

    function VisibleToAdminUser()
    {
        return ( $this->CheckPermission('Modify Templates') ||
                 $this->CheckPermission('Modify Site Preferences') ||
                 $this->CanViewAdmin() );
    }

    function GetDependencies()
    {
        return [ 'CGEcommerceBase'=>'1.8.1', 'FrontEndUsers'=>'2.5', 'CGExtensions'=>'1.56.2',
                 'CGSimpleSmarty'=>'1.9', 'CGPaymentGatewayBase'=>'1.6.1' ];
    }

    function SetParameters()
    {
        encrypted_store::set_timeout($this->GetPreference('datastore_timeout',10)*60);

        // these aliases are only for compatibility... remove after some time.
        @class_alias('\Orders\Order','orders_order');
        @class_alias('\Orders\Address','orders_address');
        @class_alias('\Orders\Destination','orders_shipping');
        @class_alias('\Orders\orders_ops','orders_ops');
        @class_alias('\Orders\LineItem','line_item');

        $this->AddImageDir('icons');
        $this->RegisterModulePlugin();
        $this->RestrictUnknownParams();

        $this->SetParameterType('order_notes',CLEAN_STRING);
        $this->SetParameterType(CLEAN_REGEXP.'/orders_.*/',CLEAN_STRING);
        $this->SetParameterType(CLEAN_REGEXP.'/billing_.*/',CLEAN_STRING);
        $this->SetParameterType(CLEAN_REGEXP.'/shipping_.*/',CLEAN_STRING);
        $this->SetParameterType('submit',CLEAN_STRING);
        $this->SetParameterType('submit_x',CLEAN_INT);
        $this->SetParameterType('submit_y',CLEAN_INT);
        $this->SetParameterType('gateway',CLEAN_STRING);
        $this->SetParameterType('confirmation_code',CLEAN_STRING);
        $this->SetParameterType('order_id',CLEAN_INT);
        $this->SetParameterType('datakey',CLEAN_STRING);
        $this->SetParameterType('invoice_prefix',CLEAN_STRING);

        $this->CreateParameter('invoice_prefix','',$this->Lang('help_param_invoice_prefix'));
        $this->CreateParameter('template','',$this->Lang('help_param_template'));
        $this->SetParameterType('template',CLEAN_STRING);
    }

    function GetHeaderHTML()
    {
        $mod = cms_utils::get_module(MOD_CGEXTENSIONS);
        $output = $mod->GetHeaderHTML();
        $gCms = cmsms();

        $url = $this->GetModuleURLPath().'/admin.css';
        $url2 = $this->GetModuleURLPath().'/admin_print.css';
        $str = '';
        $str .= '<link rel="stylesheet" type="text/css" href="'.$url.'" />';
        $str .= '<link rel="stylesheet" type="text/css" href="'.$url2.'" media="print" />';
        $output .= $str;

        $obj = cms_utils::get_module('JQueryTools','1.2');
        if( is_object($obj) ) {
$tmpl = <<<EOT
{JQueryTools action='require' lib='tablesorter,JQueryTools'}
{JQueryTools action='placemarker'}
EOT;
            $smarty = cmsms()->GetSmarty();
            $output .= "\n".$smarty->fetch('string:'.$tmpl);
        }

        return $output;
    }

    function SuppressAdminOutput(&$request)
    {
        if( isset($request['suppress_output']) ) return TRUE;

        $ret = FALSE;
        foreach( $request as $key => $value ) {
            if( endswith($key,'showtemplate') && ($value == FALSE || $value == 'false') ) {
                $ret = TRUE;
                break;
            }
        }
        return $ret;
    }

    public function SetOrderFactory(\Orders\order_factory $factory)
    {
        $this->_order_factory = $factory;
    }

    public function GetOrderFactory()
    {
        if( is_object($this->_order_factory) ) return $this->_order_factory;

        $factory = new \Orders\order_maker;
        return $factory;
    }

    protected function GetPreparedOrderFactory()
    {
        $factory = $this->GetOrderFactory();
        $factory->set_cart_module(\cg_ecomm::get_cart_module());
        $factory->set_shipping_module(\cg_ecomm::get_shipping_module());
        $factory->set_packaging_module(\cg_ecomm::get_packaging_module());
        $factory->set_tax_module(\cg_ecomm::get_tax_module());
        $factory->set_handling_module(\cg_ecomm::get_handling_module());
        $factory->set_user_id(\Orders\Orders_helper::is_valid_user());

        $policy = $this->GetPreference('address_retrieval',\Orders\billing_address_retriever::ADDR_POLICY_FEU);
        $uid = \Orders\orders_helper::is_valid_user();
        $billing_address_retriever = new \Orders\billing_address_retriever( $this, $policy, $uid );
        $addr = $billing_address_retriever->get_address();
        if( $addr ) $factory->set_billing_address($addr);
        return $factory;
    }

    function GetUsername($uid)
    {
        $feu = $this->GetModuleInstance('FrontEndUsers');
        return $feu->GetUsername($uid);
    }


    public function _getRealEncryptionKey()
    {
        $gCms = cmsms();
        $config = $gCms->GetConfig();
        $key = $this->GetPreference('encryption_key');
        if( empty( $key ) ) return '';
        $key = md5($config['root_path'].' '.$key);
        $key = substr($key,0,16);
        return $key;
    }


    static public function &GetOrder($order_id)
    {
        return \Orders\orders_ops::load_by_id($order_id);
    }


    static public function get_portable_order($order_id)
    {
        $tmp = \Orders\orders_ops::load_by_id($order_id);
        if( is_object($tmp) ) return $tmp->to_array();
    }


    public function get_country_list()
    {
        $tmp = $this->GetPreference('valid_countries','');
        if( !$tmp ) return parent::get_country_list();

        $tmp2 = explode("\n",$tmp);
        $tmp3 = array();
        foreach( $tmp2 as $one ) {
            if( strstr($one,'=') === FALSE ) continue;
            $one = trim($one);
            list($code,$name) = explode('=',$one,2);
            $code = trim($code);
            $name = trim($name);
            $tmp3[] = array('name' => $name, 'code' => strtoupper($code));
        }
        return $tmp3;
    }

    protected function get_state_list()
    {
        $tmp = $this->GetPreference('valid_states','');
        if( !$tmp ) return parent::get_state_list();

        $tmp2 = explode("\n",$tmp);
        $tmp3 = array();
        foreach( $tmp2 as $one ) {
            if( strstr($one,'=') === FALSE ) continue;
            $one = trim($one);
            list($code,$name) = explode('=',$one,2);
            $tmp3[] = array('name' => trim($name), 'code' => trim($code));
        }
        return $tmp3;
    }


    public function autoload($classname)
    {
        $ret = parent::autoload($classname);
        if( $ret ) return;

        $dirs = array();
        $dirs[] = $this->GetModulePath().'/lib/reports';

        foreach( $dirs as $onedir ) {
            $fn = $onedir."/class.{$classname}.php";
            if( file_exists($fn) ) {
                require_once($fn);
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Submit an order synchrnously without user interaction
     *
     * This method throws exceptions on errors
     *
     * @return void
     */
    public function OfflineSubmitOrder($order_id)
    {
        $order_id = (int)$order_id;
        if( $order_id < 1 ) throw new CmsException('Invalid order id specified');
        // get the order
        $order = \Orders\orders_ops::load_by_id($order_id);
        if( !$order ) throw new CmsException('Could not find an order with the id '.$order_id);
        $amt = max(0,$order->get_total() - $order->get_amount_paid());
        if( $amt <= 0.00 ) throw new CmsException('Cannot submit order with a 0 amount owing');

        // get the payment gateway
        $gateway_module = cg_ecomm::get_payment_module();
        if( !$gateway_module ) throw new CmsException('Could not get the gateway module');
        if( !$gateway_module->SupportsOfflineCharges() ) throw new CmsException('Selected gateway module does not support offline charges');

        // create the payment
        $payment = new \Orders\Payment();
        $payment->set_amount($amt);
        $payment->set_status(\Orders\Payment::STATUS_NOTPROCESSED);
        $payment->set_order_id($order_id);
        $payment->set_gateway($gateway_module->GetName());
        $payment->save();

        // process offline transaction.
        $gateway_module->SetCurrencyCode(cg_ecomm::get_currency_code());
        $gateway_module->SetWeightUnits(cg_ecomm::get_weight_units());
        $gateway_module->SetInvoice($order->get_invoice());
        $gateway_module->SetOrderID($order_id);
        $gateway_module->SetOrderObject($order);
        $gateway_module->SetPaymentId($payment->get_id());
        $gateway_module->SetOrderId($order_id);
        $gateway_module->SetTransactionAmount($amt);
        $gateway_module->SetOrderDescription($this->GetPreference('gateway_description'));

        foreach( $order->get_destinations() as $dest ) {
            foreach( $dest as $item ) {
                $gateway_module->AddItem($item->get_description(),
                                         $item->get_item_id(),
                                         $item->get_quantity(),
                                         $item->get_weight(),
                                         $item->get_net_total());
            }
        }

        $txn = $gateway_module->ProcessSyncTransaction();
        if( !is_object($txn) ) return; // no post processing, rely on asynchronous stuff

        // with the transaction object, do the post processing
        $res = \Orders\gateway_helper::process_gateway_transaction($txn);
        if( $res ) throw new OrdersException($res);
    }
} // class

#
# EOF
#
