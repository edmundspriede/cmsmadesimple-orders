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

$this->DeleteTemplate();
$this->RemovePreference();
$this->RemovePermission(ORDERS_PERM_VIEWORDERS);
$this->RemovePermission(ORDERS_PERM_MANAGEORDERS);
$this->RemovePermission(ORDERS_PERM_CONTACT_CUSTOMERS);
$this->RemovePermission(ORDERS_PERM_DELETE_ORDERS);

$db =& $this->GetDb();
$dict = NewDataDictionary( $db );

$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_orders_message_templates" );
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_orders_messages" );
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_orders_shipping" );
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_orders_items" );
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_orders_payments" );
$dict->ExecuteSQLArray($sqlarray);

$sqlarray = $dict->DropTableSQL( cms_db_prefix()."module_orders" );
$dict->ExecuteSQLArray($sqlarray);

// events
$this->RemoveEvent('PostGatewayComplete');
$this->RemoveEvent('CalculateDiscounts');
$this->RemoveEvent('OrderDeletedPre');
$this->RemoveEvent('OrderDeletedPost');
$this->RemoveEventHandler('CGPaymentGatewayBase','on_incoming_event');

// EOF
