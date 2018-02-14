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
if( !$this->CheckPermission(ORDERS_PERM_MANAGEORDERS) )
  {
    echo $this->DisplayErrorMessage('error_permissiondenied');
    return;
  }
if( !isset($params['orderid']) )
  {
    echo $this->DisplayErrorMessage('error_insufficientparams');
    return;
  }

////////////////////////////////////////////
// An action to allow an authorized admin
// to display an invoice for an order in a
// new window, for printing or emailing.
// allow the admin to select the invoice
// template, and optionally send it to
// the customer, or display in a new
// window for printing.
////////////////////////////////////////////

//
// Initialization
//
$orderid = (int)$params['orderid'];

//
// Setup
//
$templates = $this->ListTemplatesWithPrefix('invoice_');

//
// Handle form submit
//
if( isset($params['cancel']) )
  {
    $this->Redirect($id,'admin_manageorder',$returnid,array('orderid'=>$orderid));
    return;
  }

//
// Give everything to smarty
//
$tmp = array();
foreach( $templates as $one )
{
  $xx = substr($one,strlen('invoice_'));
  $tmp[$one] = $xx;
}

$order_obj = \Orders\orders_ops::load_by_id($orderid);
$opts = array();
$opts['popup'] = $this->Lang('popup_new_window');
$opts['email'] = $this->Lang('email_invoice');
$smarty->assign('options',$opts);
$smarty->assign('ordernum',$order_obj->get_invoice());
$smarty->assign('orderid',$orderid);
$smarty->assign('templates',$tmp);
$smarty->assign('dflttemplate','invoice_'.$this->GetPreference('dflt_invoice_template'));
$smarty->assign('formstart',$this->CGCreateFormStart($id,'admin_createinvoice',$returnid,$params));
$smarty->assign('formend',$this->CreateFormEnd());
$smarty->assign('submit',$this->CreateInputSubmit($id,'submit',$this->Lang('submit')));
$smarty->assign('cancel',$this->CreateInputSubmit($id,'cancel',$this->Lang('cancel')));
$smarty->assign('popupurl',
		$this->CreateURL($id,'admin_printinvoice',$returnid,
				 array('orderid'=>$orderid)));
$smarty->assign('emailurl',
		$this->CreateURL($id,'admin_emailinvoice',$returnid,
				 array('orderid'=>$orderid)));
$smarty->assign('returnurl',
		$this->CreateURL($id,'admin_manageorder',$returnid,
				 array('orderid'=>$orderid)));

//
// Process template
//
echo $this->ProcessTemplate('admin_createinvoice.tpl');

// EOF
?>