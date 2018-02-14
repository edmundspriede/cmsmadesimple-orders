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
if( !$this->CheckPermission(ORDERS_PERM_VIEWORDERS) && !$this->CheckPermission(ORDERS_PERM_MANAGEORDERS) ) return;

$this->SetCurrentTab('orders');
$statuses = array();
$statuses[$this->Lang(ITEMSTATUS_PENDING)] = ITEMSTATUS_PENDING;
$statuses[$this->Lang(ITEMSTATUS_DELIVERED)] = ITEMSTATUS_DELIVERED;
$statuses[$this->Lang(ITEMSTATUS_SHIPPED)] = ITEMSTATUS_SHIPPED;
$statuses[$this->Lang(ITEMSTATUS_NOTSHIPPED)] = ITEMSTATUS_NOTSHIPPED;
$statuses[$this->Lang(ITEMSTATUS_HOLD)] = ITEMSTATUS_HOLD;
$statuses[$this->Lang(ITEMSTATUS_BACKORDER)] = ITEMSTATUS_BACKORDER;

// display a report and form that provides information about this order
// people with authorized permission can edit certain details
// provide links to send users an email
if( !isset($params['orderid']) )  {
    echo $this->DisplayErrorMessage($this->Lang('error_insufficientparams'));
    return;
}
$order_id = (int)$params['orderid'];

if( isset($params['cancel']) ) $this->RedirectToTab($id);

$order = \Orders\orders_ops::load_by_id($order_id);
if( !$this->CheckPermission(ORDERS_PERM_MANAGEORDERS) ) $order->hide_ccinfo();

//
// Handle submit
//
if( isset($params['submit']) ) {
    if( isset($params['input_status']) ) $order->set_status(trim($params['input_status']));
    if( isset($params['input_confirmnum']) ) $order->set_confirmation_num(trim($params['input_confirmnum']));

    // Update the items
    foreach( $params as $key => $value ) {
        if( !startswith($key,'input_itemstatus_') ) continue;

        $itemid = (int)substr($key,strlen('input_itemstatus_'));
        for( $j = 0; $j < $order->count_destinations(); $j++ ) {
            $shipping =& $order->get_shipping($j);
            for( $i = 0; $i < $shipping->count_all_items(); $i++ ) {
                $item =& $shipping->get_item($i);
                if( $item->get_id() != $itemid ) continue;
                $item->set_status($value);
            }
        }
    }

    $order->save();

    // and redirect
    $this->RedirectToTab($id);
}

// and do the smarty stuff
if( $this->CheckPermission(ORDERS_PERM_MANAGEORDERS) ) {
    $smarty->assign('canmanage',1);
    $smarty->assign('formstart',$this->CGCreateFormStart($id,'admin_manageorder',$returnid,$params));
    $smarty->assign('formend',$this->CreateFormEnd());

    $statuses = array();
    $statuses[$this->Lang(ORDERSTATUS_PROPOSED)] = ORDERSTATUS_PROPOSED;
    $statuses[$this->Lang(ORDERSTATUS_SUBMITTED)] = ORDERSTATUS_SUBMITTED;
    $statuses[$this->Lang(ORDERSTATUS_PAID)] = ORDERSTATUS_PAID;
    $statuses[$this->Lang(ORDERSTATUS_BALANCEDUE)] = ORDERSTATUS_BALANCEDUE;
    $statuses[$this->Lang(ORDERSTATUS_INVOICED)] = ORDERSTATUS_INVOICED;
    $statuses[$this->Lang(ORDERSTATUS_CANCELLED)] = ORDERSTATUS_CANCELLED;
    $statuses[$this->Lang(ORDERSTATUS_HOLD)] = ORDERSTATUS_HOLD;
    $statuses[$this->Lang(ORDERSTATUS_INCOMPLETE)] = ORDERSTATUS_INCOMPLETE;
    $statuses[$this->Lang(ORDERSTATUS_CONFIRMED)] = ORDERSTATUS_CONFIRMED;
    $statuses[$this->Lang(ORDERSTATUS_COMPLETED)] = ORDERSTATUS_COMPLETED;
    $statuses[$this->Lang(ORDERSTATUS_SUBSCRIBED)] = ORDERSTATUS_SUBSCRIBED;
    $statuses = array_flip($statuses);
    $smarty->assign('order_statuses',$statuses);
    $smarty->assign('submit', $this->CreateInputSubmit($id,'submit',$this->Lang('submit')));
    $smarty->assign('cancel', $this->CreateInputSubmit($id,'cancel',$this->Lang('cancel')));
}
if( $this->CheckPermission(ORDERS_PERM_CONTACT_CUSTOMERS) ) {
    $smarty->assign('sendmail_link',
                    $this->CreateImageLink($id,'admin_sendmail',$returnid,
                                           $this->Lang('send_message'),'email_add.png',
                                           array('orderid'=>$order_id),'','',false));
}
if( $this->CheckPermission(ORDERS_PERM_VIEWORDERS) ) {
    $query = 'SELECT id FROM '.cms_db_prefix().'module_orders_messages
               WHERE order_id = ? LIMIT 1';
    $res = $db->GetOne($query,array($order_id));
    if( $res ) {
        $smarty->assign('viewmail_link',
                        $this->CreateImageLink($id,'admin_viewmessages',$returnid,
                                               $this->Lang('view_messages'),
                                               'email.png',
                                               array('orderid'=>$order_id),
                                               '','',false));
    }
}

$print_url = $this->CreateURL($id,'admin_printinvoice',$returnid, array('orderid'=>$order_id));
$print_url .= '&suppress_output=1';
$smarty->assign('print_url',$print_url);
$statuses = array();
$statuses[ITEMSTATUS_PENDING] = $this->Lang(ITEMSTATUS_PENDING);
$statuses[ITEMSTATUS_DELIVERED] = $this->Lang(ITEMSTATUS_DELIVERED);
$statuses[ITEMSTATUS_SHIPPED] = $this->Lang(ITEMSTATUS_SHIPPED);
$statuses[ITEMSTATUS_NOTSHIPPED] = $this->Lang(ITEMSTATUS_NOTSHIPPED);
$statuses[ITEMSTATUS_HOLD] = $this->Lang(ITEMSTATUS_HOLD);
$statuses[ITEMSTATUS_BACKORDER] = $this->Lang(ITEMSTATUS_BACKORDER);
$smarty->assign('statuses',$statuses);
$config = $gCms->GetConfig();
$smarty->assign('expand',$config['root_url'].'/modules/'.$this->GetName().'/icons/bullet_toggle_plus.png');
$smarty->assign('contract',$config['root_url'].'/modules/'.$this->GetName().'/icons/bullet_toggle_minus.png');
$smarty->assign('currencysymbol',cg_ecomm::get_currency_symbol());
$smarty->assign('weightunits',cg_ecomm::get_weight_units());
$smarty->assign('order',$order);
$smarty->assign('print_img',$this->DisplayImage('printer.png',$this->Lang('print_invoice')));
$smarty->assign('invoice_img',$this->DisplayImage('invoice_lg.gif',$this->Lang('create_invoice')));
$can_manage_orders = $this->CheckPermission(ORDERS_PERM_MANAGEORDERS);
$can_manual_process = 0;
$ccprocessing_gateway = $this->GetPreference('ccprocessing_module',-1);
if( $ccprocessing_gateway && $ccprocessing_gateway != -1 ) {
    $module = cms_utils::get_module($ccprocessing_gateway);
    if( $module && $module->RequiresCreditCardInfo() ) $can_manual_process = 1;
}
$smarty->assign('can_manage_orders',$can_manage_orders);
$smarty->assign('can_manual_process',$can_manual_process);
echo $this->ProcessTemplate('admin_manageorder.tpl');

#
# EOF
#
?>