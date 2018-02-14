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

if (isset($params['back_to_orders_submit']))
{
	$this->Redirect($id, 'defaultadmin', $returnid, array('order_status' => $params['order_status_filter']));
}

require_once(dirname(__FILE__).'/lib/class.orders_ops.php');
$order_obj = order_ops::load_by_id($params['order_id']);

if (isset($params['order_status_submit']))
{
  $order_obj->set_status($params['order_status']);
  $order_obj->save();
}

$smarty->assign_by_ref('order', $order->to_array());
$smarty->assign_by_ref('mod', $this);

$smarty->assign('order_status_form_start', $this->CreateFormStart($id, 'vieworder', $returnid));
$smarty->assign('order_status_dropdown', $this->CreateInputDropdown($id, 'order_status', array($this->Lang('proposed') => 'proposed', $this->Lang('submitted') => 'submitted', $this->Lang('paid') => 'paid', $this->Lang('shipped') => 'shipped', $this->Lang('cancelled') => 'cancelled'), -1, $order->get_status()));
$smarty->assign('order_status_form_submit', $this->CreateInputSubmit($id, 'order_status_submit', $this->Lang('update_status')) . $this->CreateInputHidden($id, 'order_id', $params['order_id']) . $this->CreateInputHidden($id, 'order_status_filter', $params['order_status_filter']));
$smarty->assign('back_to_orders', $this->CreateInputSubmit($id, 'back_to_orders_submit', $this->Lang('back_to_orders')));
$smarty->assign('order_status_form_end', $this->CreateFormEnd());

echo $this->ProcessTemplate('vieworder.tpl');

// EOF
?>