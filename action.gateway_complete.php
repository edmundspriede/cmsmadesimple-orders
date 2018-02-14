<?php
#-------------------------------------------------------------------------
# Module: Orders - A simple order processing module.
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
if( !isset($gCms) ) exit;

//
// This method handles synchronous gateway transactions
// usually from gateways that perform synchronously, and then return back to
// the destination URL (this action) after processing is complete.
//
//

// get the gateway module, and restore its state.
$gateway_module_name = cge_utils::get_param($params,'gateway');
if( !$gateway_module_name ) {
    audit('',$this->GetName(),'gateway_complate action called, but no gateway parameter');
    echo $this->DisplayErrorMessage($this->Lang('error_insufficientprams'));
    return;
}
$gateway_module = \cms_utils::get_module($gateway_module_name);
if( !$gateway_module ) {
    echo $this->DisplayErrorMessage($this->Lang('error_gateway_notfound'));
    return;
}
// get the encryption key from the gateway.
$enc_key = cge_utils::get_param($params,'datakey');
debug_to_log('gateway complete got key '.$enc_key);
$res = $gateway_module->RestoreState($enc_key);
if( !$res ) {
    audit('',$this->GetName(),'gateway_complate could not restore gateway information');
    echo $this->DisplayErrorMessage($this->Lang('error_retrieve_data'));
    return;
}
$cart_module = cg_ecomm::get_cart_module();
if( !is_object($cart_module) ) {
  // can't find the cart module.
    audit('',$this->GetName(),'gateway_complate action called, but no cart module found');
    echo $this->DisplayErrorMessage($this->Lang('error_nocartmodule'));
    return;
}

$order_id = $gateway_module->GetOrderId();
$keyname = \Orders\orders_helper::get_security_key();
$cname = 'c'.$keyname;
if( encrypted_store::get($keyname) != $order_id ) {
    audit('',$this->GetName(),'problem restoring data from encrypted store.');
    echo $this->DisplayErrorMessage($this->Lang('error_encryption_problem'));
    return;
}

// get gateway module

$transaction_id = $message = $error_message = $payment_id = $amount = null;
$status = 'SUCCESS';
if( $gateway_module->CheckInfo() ) {
    // build a transaction from the info in the gateway module.
    $status = $gateway_module->GetTransactionStatus();
    $transaction_id = $gateway_module->GetTransactionID();
    $amount = $gateway_module->GetTransactionAmount();
    $message = $gateway_module->GetMessage();
    $error_message = $gateway_module->GetErrorMessage();
    $payment_id = $gateway_module->GetPaymentId();

    $trans = new \CGEcommerceBase\async_transaction($order_id,$amount,$gateway_module->GetName());
    $trans->set_id($transaction_id);
    $trans->set_message($error_message);
    $trans->set_status($status);
    $trans->set_payment_id($payment_id);
    $keys = $gateway_module->GetTransactionKeys();
    if( is_array($keys) ) {
        foreach( $keys as $one ) {
            $trans->set_other_val($one,$gateway_module->GetTransactionData($one));
        }
    }

    // we're done with the gateway now
    // so reset it (just in case)
    $gateway_module->Reset();

    $res = \Orders\gateway_helper::process_gateway_transaction($trans);
    if( $res ) $smarty->assign('error',$res);
}

// get the order.

$order_obj = $this->GetOrder($order_id);
$status = $order_obj->get_status();
cms_utils::set_app_data('orders_gateway_complete',1);

// set a variable that indicates the current order status
// clear out cruft.
$cart_module->EraseCart('gateway-complete');

encrypted_store::erase($cname);
encrypted_store::erase($keyname);

// Display the post processing template
// variables have already been set to smarty by ProcessGateWayResult
// process the information into the order.
$smarty->assign('gateway_module',$gateway_module->GetName());
$smarty->assign('order_obj',$order_obj);
$smarty->assign('ordernumber',$order_obj->get_invoice()); // deprecated.
$smarty->assign('transaction_id',$transaction_id);
if( $status ) $smarty->assign('status',$status);
$billing_addr = $order_obj->get_billing();
$smarty->assign('email_address',$billing_addr->get_email());
if( $message ) $smarty->assign('message',$message);
if( $error_message ) $smarty->assign('error_message',$message);
$smarty->assign('amount',$amount);
$smarty->assign('payment_id',$payment_id);
switch( $status ) {
case ORDERSTATUS_CANCELLED:
    $status = 'CANCELLED';
    break;

case ORDERSTATUS_HOLD:
case ORDERSTATUS_INCOMPLETE:
    // should never get here with these statuses.
    $status = '';
    break;

case ORDERSTATUS_COMPLETED:
    $status = 'COMPLETED';
    break;

//case ORDERSTATUS_PROPOSED: // proposed order here means something has gone wrong.
case ORDERSTATUS_PENDING:
    $status = 'PENDING';
    break;

case ORDERSTATUS_CONFIRMED:
case ORDERSTATUS_INVOICED:
case ORDERSTATUS_PAID:
case ORDERSTATUS_BALANCEDUE:
case ORDERSTATUS_SUBSCRIBED:
    $status = 'SUCCESS';
    break;
}
$smarty->assign('status',$status);

$thetemplate = $this->GetPreference('dflt_gateway_complete_template');
echo $this->CGProcessTemplate($thetemplate,'gateway_complete_');

// Send Event w/ Order information
\CMSMS\HookManager::do_hook('Orders::PostGatewayComplete', array('order_id' => $order_id, 'order' => $order_obj->to_array()));

// EOF
