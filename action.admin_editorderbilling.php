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
    echo $this->DisplayErrormessage($this->Lang('error_insufficientparams'));
    return;
  }

//
// Initialization
//
require_once(dirname(__FILE__).'/lib/class.orders_ops.php');
$order_id = (int)$params['orderid'];

//
// Get Data
//
$order_obj = \Orders\orders_ops::load_by_id($order_id);
$billing_addr =& $order_obj->get_billing();

//
// Handle Form Submission
//
if( isset($params['cancel']) )
  {
    $this->Redirect($id,'admin_manageorder',$returnid,array('orderid'=>$order_id));
    return;
  }
else if( isset($params['submit']) )
  {
    $status = '';
    foreach( $params as $key => $value )
      {
	if( startswith($key,'billing') )
	  {
	    $nkey = substr($key,8);
	    $fn = 'set_'.$nkey;
	    $billing_addr->$fn($value);
	  }
      }

      if( !$billing_addr->is_valid() )
	{
	  echo $this->ShowErrors($this->Lang('error_invalidfield'));
	  $status = 'error';
	}

      if( empty($status) )
	{
	  $order_obj->save();

	  // and get out of here
	  $this->Redirect($id,'admin_manageorder',$returnid,array('orderid'=>$order_id));
	}
  }

//
// Give Everything to smarty
//
$smarty->assign('formstart',$this->CGCreateFormStart($id,'admin_editorderbilling',$returnid,$params));
$smarty->assign('formend',$this->CreateFormEnd());
$smarty->assign('input_billing_country',
		$this->CreateInputCountryDropdown($id,'billing_country',$billing_addr->get_country()));
$smarty->assign('order',$order_obj->to_array());
$smarty->assign('ordernum',$order_obj->get_invoice());

//
// Process Template
//
echo $this->ProcessTemplate('admin_editorderbilling.tpl');
// EOF
?>