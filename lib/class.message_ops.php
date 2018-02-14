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
namespace Orders;

class message_ops
{
    private function __construct() {}

  static public function &load_by_id($id)
  {
    $tmp = null;
    $gCms = cmsms(); $db = $gCms->GetDb();
    $query = 'SELECT id,order_id,sender_name,subject,is_html,body,sent
                FROM '.cms_db_prefix().'module_orders_messages
               WHERE id = ?';
    $row = $db->GetRow($query,array($id));
    if( !$row )	return $tmp;

    $message = new order_message();
    $message->from_array($row);
    return $message;
  }


  static public function insert(order_message &$message)
  {
    $gCms = cmsms(); $db = $gCms->GetDb();
    $now = $db->DbTimeStamp(time());
    if( $message->get_sent() == '' ) $message->set_sent(trim($now,"'"));
    $query = 'INSERT INTO '.cms_db_prefix().'module_orders_messages
              (order_id,sender_name,subject,is_html,body,sent)
              VALUES (?,?,?,?,?,?)';
    $dbr = $db->Execute($query,
			array($message->get_order_id(),
			      $message->get_sender_name(),
			      $message->get_subject(),
			      $message->get_is_html(),
			      $message->get_body(),
			      trim($message->get_sent(),'"')));
    if( !$dbr ) throw new \cg_sql_error($db->sql."\n".$db->ErrorMsg());
    $message->set_id($db->Insert_Id());
    return TRUE;
  }


  static public function update(order_message& $message)
  {
    $gCms = cmsms(); $db = $gCms->GetDb();
    $query = 'UPDATE '.cms_db_prefix().'module_orders_messages
                 SET order_id = ?, sender_name = ?, subject = ?,
                     is_html = ?, body = ?, sent = ?
               WHERE id = ?';
    $dbr = $db->Execute($query,
			array($message->get_order_id(),
			      $message->get_sender_name(),
			      $message->get_subject(),
			      $message->get_is_html(),
			      $message->get_body(),
			      trim($message->get_sent(),'"')));
    if( !$dbr ) throw new \cg_sql_error($db->sql."\n".$db->ErrorMsg());
    return TRUE;
  }


  static public function delete_by_id($id)
  {
    $gCms = cmsms(); $db = $gCms->GetDb();
    $query = 'DELETE FROM '.cms_db_prefix().'module_orders_messages
               WHERE id = ?';
    $dbr = $db->Execute($query,array($id));
    if( !$dbr ) return FALSE;
    return TRUE;
  }
} // end of class

#
# EOF
#
