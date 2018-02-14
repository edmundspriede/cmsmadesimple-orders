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
namespace Orders\reports;

class report_subscr_picklist extends \Orders\report_base
{
  public function get_name()
  {
      return $this->fixname(get_class($this));
  }

  public function get_description()
  {
    $mod = \cge_utils::get_module('Orders');
    return $mod->Lang('report_'.$this->get_name());
  }

  protected function get_query()
  {
    $query = 'SELECT oi.*,os.* FROM '.cms_db_prefix().'module_orders_items oi
                LEFT JOIN '.cms_db_prefix().'module_orders oo
                  ON oi.order_id = oo.id
                LEFT JOIN '.cms_db_prefix().'module_orders_shipping os
                  ON oi.shipping_id = os.id
               WHERE oo.status != \'proposed\'
                 AND oo.status != \'cancelled\'
                 AND oo.status != \'hold\'
                 AND oi.item_type = \'product\'
                 AND oi.subscr_payperiod != \'none\'
                 AND oi.subscr_delperiod != \'none\'
                 AND (oo.modified_date BETWEEN __START_DATE__ AND __END_DATE__)';

    $db = $this->get_db();
    $query = str_replace('__START_DATE__',trim($db->DbTimeStamp($this->get_startdate(),"'")),$query);
    $query = str_replace('__END_DATE__',trim($db->DbTimeStamp($this->get_enddate(),"'")),$query);
    return $query;
  }

}

// EOF
?>