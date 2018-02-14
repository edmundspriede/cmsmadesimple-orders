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
if( !$this->CheckPermission(ORDERS_PERM_MANAGEORDERS) ) exit;
$this->SetCurrentTab('orders');
  
if( !isset($params['query']) )
  {
    $this->SetError($this->Lang('error_insufficientparams'));
    $this->RedirectToTab($id);
  }

$query = base64_decode($params['query']);
$tmp = $db->GetArray($query);
if( !is_array($tmp) )
  {
    $this->SetError($this->Lang('error_nomatches'));
    $this->RedirectToTab($id);
  }

$order_ids = cge_array::extract_field($tmp,'id');
if( !is_array($order_ids) )
  {
    $this->SetError($this->Lang('error_nomatches'));
    $this->RedirectToTab($id);
  }

$smarty->assign('currency_symbol',cg_ecomm::get_currency_symbol());
$smarty->assign('weight_units',cg_ecomm::get_weight_units());
$text = '';
set_time_limit(9999);
$fn = tempnam(TMP_CACHE_LOCATION,'oe').'.csv';
$fh = fopen($fn,"w");
foreach( $order_ids as $one_id )
{
  $tmp = orders_ops::load_by_id($one_id);
  $smarty->assign('order',$tmp);
  $text = $this->ProcessTemplate('admin_export_format.tpl');
  if( !endswith($text,"\n") ) $text .= "\n";
  fwrite($fh,$text);
}
fclose($fh);

cge_utils::send_file_and_exit($fn);
//cge_utils::send_data_and_exit($text,'text/csv','orders_export.csv');

@unlink($fn);
#
# EOF
#
?>
