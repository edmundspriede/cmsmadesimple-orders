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
if( ! $this->CheckPermission(ORDERS_PERM_DELETE_ORDERS)) return;

$order_id = \cge_param::get_int($params,'order_id');
if( $order_id < 1 ) {
    echo $this->DisplayErrorMessage($this->Lang('error_insufficientparams'));
    return;
}

$db = \cge_utils::get_db();
$in_trans = false;
try {
    \CMSMS\HookManager::do_hook('Orders::OrderDeletedPre', [ 'order_id'=>$order_id ] );

    $db->BeginTrans();
    $in_trans = true;
    $query = 'DELETE FROM '.cms_db_prefix().'module_orders_messages WHERE order_id = ?';
    $db->Execute($query,array($order_id));

    $query = 'DELETE FROM '.cms_db_prefix().'module_orders_items WHERE order_id = ?';
    $db->Execute($query,array($order_id));

    $query = 'DELETE FROM '.cms_db_prefix().'module_orders_payments WHERE order_id = ?';
    $db->Execute($query,array($order_id));

    $query = 'DELETE FROM '.cms_db_prefix().'module_orders_shipping WHERE order_id = ?';
    $db->Execute($query,array($order_id));

    $query = 'DELETE FROM '.cms_db_prefix().'module_orders WHERE id = ?';
    $db->Execute($query,array($order_id));
    $db->CommitTrans();
    $in_trans = false;

    \CMSMS\HookManager::do_hook('Orders::OrderDeletedPost', [ 'order_id'=>$order_id ] );
    audit($params['order_id'],$this->GetName(),'Deleted Order');
    $this->RedirectToTab();
}
catch( \Exception $e ) {
    if( $in_trans ) $db->RollbackTrans();
    audit($order_id,$this->GetName(),'Problem deleting order');
    $this->SetError($this->Lang('error_problemdeleting_order'));
    $this->RedirectToTab();
}
// EOF
