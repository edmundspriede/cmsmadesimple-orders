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
if( !$this->CheckPermission(ORDERS_PERM_MANAGEORDERS) ) return;
if( !isset($params['orderid']) ) return;

// initialization
require_once(dirname(__FILE__).'/lib/class.orders_ops.php');

$order_id = (int)$params['orderid'];
$email=0;
$thetemplate = $this->GetPreference('admin_invoice_template',-1);
if( $thetemplate == -1 ) $thetemplate = 'invoice_'.$this->GetPreference('dflt_invoice_template');
if( isset($params['template']) ) $thetemplate = trim($params['template']);

$tmp = $this->GetTemplate($thetemplate);
if( empty($tmp) ) $thetemplate = 'invoice_'.$this->GetPreference('dflt_invoice_template');
if( isset($params['email']) ) $email = (int)$params['email'];

$order = \Orders\orders_ops::load_by_id($order_id);
//
// Give everything to smarty
//
$smarty->assign('headerhtml',$this->GetHeaderHTML());
$smarty->assign('ordernumber',$order->get_invoice());
$smarty->assign('order_id',$order_id);
$smarty->assign('currencysymbol',cg_ecomm::get_currency_symbol());
$smarty->assign('weightunits',cg_ecomm::get_weight_units());
$smarty->assign('order',$order->to_array());
$smarty->assign('order_obj',$order);
$smarty->assign('invoice_message',$this->GetPreference('invoice_message'));
$invoice_txt = $this->ProcessTemplateFromDatabase($thetemplate);
$smarty->assign('invoice',$invoice_txt);

if( $email ) {
    $destaddresses = array();
    $billing_addr =& $order->get_billing();
    $destaddresses[] = $billing_addr->get_email();

    //
    // Send Emails
    //
    $subject = $this->ProcessTemplateFromData($this->GetPreference('useremail_subject'));
    $body = $this->ProcessTemplateFromDatabase('useremail_template');
    $cmsmailer = new \cms_mailer;
    $cmsmailer->IsHTML(true);
    $cmsmailer->AddAddress($billing_addr->get_email());
    $addresses = explode(',',$this->GetPreference('admin_email'));
    foreach( $addresses as $addr ) {
        $cmsmailer->AddCC($addr);
        $destaddresses[] = $addr;
    }
    $cmsmailer->SetSubject($subject);
    $cmsmailer->SetBody($invoice_txt);
    $cmsmailer->Send();
    $cmsmailer->reset();
    $this->SetMessage($this->Lang('email_sent_to').' '.implode(',',$destaddresses));
    $this->CGRedirect($id,'admin_manageorder',$returnid,array('orderid'=>$order_id));
}
else  {
    //
    // Process the template
    //
    echo $this->ProcessTemplate('admin_printinvoice.tpl');
}
// EOF
?>