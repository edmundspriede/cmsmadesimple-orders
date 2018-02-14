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

if( version_compare(phpversion(),'5.4.11') < 0 ) {
    return "Minimum PHP version of 5.4.11 required";
}

$current_version = $oldversion;
$dict = NewDataDictionary($db);
$taboptarray = array('mysql' => 'TYPE=MyISAM');

if( version_compare($oldversion, '1.16.99') < 0 ) {
    return 'Sorry, but you cannot upgrade from such an old version.  Minimum upgrade version is 1.17';
}
if( version_compare($oldversion, '1.17') < 0 ) {
    $sqlarray = $dict->AddColumnSQL(cms_db_prefix().'module_orders','extra X2');
    $dict->ExecuteSQLArray($sqlarray);
}
if( version_compare($oldversion, '1.19') < 0 ) {
    $sqlarray = $dict->AddColumnSQL(cms_db_prefix().'module_orders_shipping','packing_list X2');
    $dict->ExecuteSQLArray($sqlarray);
}
if( version_compare($oldversion, '1.20') < 0 ) {
    Events::CreateEvent('Orders', 'OrderDeletedPre');
    Events::CreateEvent('Orders', 'OrderDeletedPost');
    $sqlarray = $dict->AddColumnSQL(cms_db_prefix().'module_orders_shipping',
                                    'source_company C(255),
                                     source_first_name C(50),
                                     source_last_name C(50),
                                     source_address1 C(100),
                                     source_address2 C(100),
                                     source_city C(50),
                                     source_state C(25),
                                     source_postal C(25),
                                     source_country C(50),
                                     source_phone C(25),
                                     source_fax C(25),
                                     source_email C(255),
                                     vendor_id I,
                                     optional C(255)');
    $dict->ExecuteSQLArray($sqlarray);
}
if( version_compare($oldversion, '1.21') < 0 ) {
    $sqlarray = $dict->AddColumnSQL(cms_db_prefix().'module_orders_shipping','pickup I1 DEFAULT 0');
    $dict->ExecuteSQLArray($sqlarray);
}
if( version_compare($oldversion, '1.21.1') < 0 ) {
    $sqlarray = $dict->AddColumnSQL(cms_db_prefix().'module_orders_shipping','status C(50)');
    $dict->ExecuteSQLArray($sqlarray);
}
if( version_compare($oldversion, '1.21.2') < 0 ) {
    $db = \cge_utils::get_db();
    try {
        // get a list of all payments that exist without a valid order
        $sql = 'SELECT P.order_id FROM '.cms_db_prefix().'module_orders_payments P
                LEFT JOIN '.cms_db_prefix().'module_orders O ON P.order_id = O.id
                WHERE O.id IS NULL';
        $bad_payments = $db->GetCol($sql);

        // get a list of all messages that exist without a valid order
        $sql = 'SELECT M.order_id FROM '.cms_db_prefix().'module_orders_messages M
                LEFT JOIN '.cms_db_prefix().'module_orders O ON M.order_id = O.id
                WHERE O.id IS NULL';
        $bad_messages = $db->GetCol($sql);

        // get a list of all items that exist without a valid order
        $sql = 'SELECT I.order_id FROM '.cms_db_prefix().'module_orders_items I
                LEFT JOIN '.cms_db_prefix().'module_orders O ON I.order_id = O.id
                WHERE O.id IS NULL';
        $bad_lineitems = $db->GetCol($sql);

        // get a list of all items that exist without a valid destination
        $sql = 'SELECT I.shipping_id FROM '.cms_db_prefix().'module_orders_items I
                LEFT JOIN '.cms_db_prefix().'module_orders_shipping S ON I.shipping_id = S.id
                WHERE S.id IS NULL';
        $bad_lineitems2 = $db->GetCol($sql);

        // get a list of all items that exist without a valid order
        $sql = 'SELECT S.order_id FROM '.cms_db_prefix().'module_orders_shipping S
                LEFT JOIN '.cms_db_prefix().'module_orders O ON S.order_id = O.id
                WHERE O.id IS NULL';
        $bad_destinations = $db->GetCol($sql);

        if( count($bad_payments) ) throw new \RuntimeException('Cannot upgrade, there are payments for non-existing orders');
        if( count($bad_messages) ) throw new \RuntimeException('Cannot upgrade, there are messages for non-existing orders');
        if( count($bad_lineitems) ) throw new \RuntimeException('Cannot upgrade, there are line items for non-existing orders');
        if( count($bad_lineitems2) ) throw new \RuntimeException('Cannot upgrade, there are line items for non-existing destinations');
        if( count($bad_destinations) ) throw new \RuntimeException('Cannot upgrade, there are shipping objects for non-existing orders');

        // get a list of all destinations that exist without a valid order
        // get a list of all orders that exist without a valid feu_user_id (set them to null)
        $tables = [];
        $tables[] = cms_db_prefix().'module_orders';
        $tables[] = cms_db_prefix().'module_orders_shipping';
        $tables[] = cms_db_prefix().'module_orders_items';
        $tables[] = cms_db_prefix().'module_orders_messages';
        $tables[] = cms_db_prefix().'module_orders_message_templates';
        $tables[] = cms_db_prefix().'module_orders_payments';
        $sql_i = 'ALTER TABLE %s ENGINE=InnoDB';
        foreach( $tables as $one ) {
            $db->Execute( sprintf($sql_i, $one));
        }

        // setup foreign key relationships
        $sql = [];
        $sql[] = 'ALTER TABLE '.cms_db_prefix().'module_orders_payments ADD FOREIGN KEY (order_id) REFERENCES '.cms_db_prefix().'module_orders (id)';
        $sql[] = 'ALTER TABLE '.cms_db_prefix().'module_orders_messages ADD FOREIGN KEY (order_id) REFERENCES '.cms_db_prefix().'module_orders (id)';
        $sql[] = 'ALTER TABLE '.cms_db_prefix().'module_orders_items ADD FOREIGN KEY (order_id) REFERENCES '.cms_db_prefix().'module_orders (id)';
        $sql[] = 'ALTER TABLE '.cms_db_prefix().'module_orders_items ADD FOREIGN KEY (shipping_id) REFERENCES '.cms_db_prefix().'module_orders_shipping (id)';
        $sql[] = 'ALTER TABLE '.cms_db_prefix().'module_orders_shipping ADD FOREIGN KEY (order_id) REFERENCES '.cms_db_prefix().'module_orders (id)';
        $sql[] = 'ALTER TABLE '.cms_db_prefix().'module_orders_shipping ADD FOREIGN KEY (vendor_id) REFERENCES '.cms_db_prefix().'module_feusers_users (id)';
        foreach( $sql as $one ) {
            $db->Execute( $one );
        }
    }
    catch( \Exception $e ) {
        die($db->sql.' '.$db->ErrorMsg());
        \cge_utils::log_exception($e);
        audit('',$this->GetName(),'Upgrade Failed: '.$e->GetMessage());
        return $e->GetMessage();
    }
}
// EOF