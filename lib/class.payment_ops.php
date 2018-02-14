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

class payment_ops
{
  static public function &load_by_txn($txn_id)
  {
    $tmp = null;
    $gCms = cmsms(); $db = $gCms->GetDb();
    $query = 'SELECT id,order_id,amount,payment_date,method,status,gateway,
                     AES_DECRYPT(cc_number,unhex(?)) AS cc_number,
                     cc_expiry,
                     AES_DECRYPT(cc_verifycode, unhex(?)) AS cc_verifycode,
                     confirmation_num, txn_id, notes, assocdata
                FROM '.cms_db_prefix().'module_orders_payments WHERE txn_id = ?';
    $str = orders_helper::get_encryption_key();
    $row = $db->GetRow($query,array($str,$str,$id));
    if( !$row )
      {
	return $tmp;
      }

    // get assoc data
    if( $row['assocdata'] )
      {
	$row['assocdata'] = unserialize($row['assocdata']);
      }

    $payment = new Payment();
    $payment->from_array($row);

    return $payment;
  }


  static public function &load_by_id($id)
  {
    $tmp = null;
    $gCms = cmsms(); $db = $gCms->GetDb();
    $query = 'SELECT id,order_id,amount,payment_date,method,status,gateway,
                     AES_DECRYPT(cc_number,unhex(?)) AS cc_number,
                     cc_expiry,
                     AES_DECRYPT(cc_verifycode, unhex(?)) AS cc_verifycode,
                     confirmation_num, txn_id, notes, assocdata
                FROM '.cms_db_prefix().'module_orders_payments WHERE id = ?';
    $str = orders_helper::get_encryption_key();
    $row = $db->GetRow($query,array($str,$str,$id));
    if( !$row )
      {
	return $tmp;
      }

    // get assoc data
    if( isset($row['assocdata']) )
      {
	$row['assocdata'] = unserialize($row['assocdata']);
      }

    $payment = new Payment();
    $payment->from_array($row);
    return $payment;
  }


  static public function insert(Payment& $payment)
  {
    $gCms = cmsms(); $db = $gCms->GetDb();
    $now = $db->DbTimeStamp(time());
    $query = 'INSERT INTO '.cms_db_prefix()."module_orders_payments
                 (order_id,amount,payment_date,method,status,gateway,
                  cc_number,cc_expiry,cc_verifycode,confirmation_num,txn_id,
                  notes,assocdata)
              VALUES (?,?,?,?,?,?,AES_ENCRYPT(?,unhex(?)),?,AES_ENCRYPT(?,unhex(?)),?,?,?,?)";
    $key = orders_helper::get_encryption_key();
    $assocdata = $payment->get_assocdata();
    if( $assocdata )
      {
	$assocdata = serialize($assocdata);
      }
    $dbr = $db->Execute($query,
			array($payment->get_order_id(),
			      $payment->get_amount(),
			      $payment->get_payment_date(),
			      $payment->get_method(),
			      $payment->get_status(),
			      $payment->get_gateway(),
			      $payment->get_cc_number(),
			      $key,
			      $payment->get_cc_expiry(),
			      $payment->get_cc_verifycode(),
			      $key,
			      $payment->get_confirmation_num(),
			      $payment->get_txn_id(),
			      $payment->get_notes(),
			      $assocdata));

    if( !$dbr ) throw new \cg_sql_error($db->sql."\n".$db->ErrorMsg());
    $payment->set_id($db->Insert_Id());
    return TRUE;
  }


  static public function update(Payment& $payment)
  {
    $gCms = cmsms(); $db = $gCms->GetDb();
    $query = 'UPDATE '.cms_db_prefix().'module_orders_payments
                 SET order_id = ?,
                     amount = ?,
                     payment_date = ?,
                     method = ?,
                     status = ?,
                     gateway = ?,
                     cc_number = AES_ENCRYPT(?,unhex(?)),
                     cc_expiry = ?,
                     cc_verifycode = AES_ENCRYPT(?,unhex(?)),
                     confirmation_num = ?,
                     txn_id = ?,
                     notes = ?,
                     assocdata = ?
               WHERE id = ?';

    $assocdata = $payment->get_assocdata();
    if( $assocdata )
      {
	$assocdata = serialize($assocdata);
      }

    $key = orders_helper::get_encryption_key();
    $dbr = $db->Execute($query,
			array($payment->get_order_id(),
			      $payment->get_amount(),
			      $payment->get_payment_date(),
			      $payment->get_method(),
			      $payment->get_status(),
			      $payment->get_gateway(),
			      $payment->get_cc_number(),
			      $key,
			      $payment->get_cc_expiry(),
			      $payment->get_cc_verifycode(),
			      $key,
			      $payment->get_confirmation_num(),
			      $payment->get_txn_id(),
			      $payment->get_notes(),
			      $assocdata,
			      $payment->get_id()));
    if( !$dbr ) throw new \cg_sql_error($db->sql."\n".$db->ErrorMsg());
    return TRUE;
  }


  static public function delete_by_id($id)
  {
    $gCms = cmsms();
    $db = $gCms->GetDb();
    $query = 'DELETE FROM '.cms_db_prefix().'module_orders_payments
               WHERE id = ?';
    $dbr = $db->Execute($query,array($id));
    if( !$dbr ) return FALSE;

    return TRUE;
  }
} // end of class

// EOF
?>