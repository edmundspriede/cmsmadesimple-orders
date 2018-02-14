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

abstract class report_base
{
    private $_db;
    private $_data;
    private $_startdate;
    private $_enddate;

    private $_query;
    private $_pagelimit = 100000;
    private $_pageoffset = 0;

    public function __construct($db)
    {
        $this->_db = $db;
    }

    protected function fixname($in)
    {
        return basename(str_replace('\\','/',$in));
    }

    protected function get_db()
    {
        return $this->_db;
    }

    abstract public function get_name();

    abstract public function get_description();

    abstract protected function get_query();

    public function set_startdate($startdate)
    {
        $this->_startdate = (int)$startdate;
    }

    protected function get_startdate()
    {
        return $this->_startdate;
    }

    public function set_enddate($enddate)
    {
        $this->_enddate = (int)$enddate;
    }

    protected function get_enddate()
    {
        return $this->_enddate;
    }

    public function set_limit($limit)
    {
        $this->_limit = (int)$limit;
    }

    protected function on_query_row($rowdata)
    {
        return $rowdata;
    }

    protected function postprocess($data)
    {
        return $data;
    }

    protected function get_template_name()
    {
        return $this->get_name();
    }

    protected function get_data()
    {
        $query = $this->get_query();
        if( empty($query) ) return FALSE;

        // generate the output.
        $dbr = $this->_db->SelectLimit($query,$this->_pagelimit,$this->_pageoffset);
        if( !$dbr ) {
            // error occurred.
            echo $this->_db->sql.'<br/>'.$this->_db->ErrorMsg().'<br/>';
            return FALSE;
        }

        // add the data to the report object.

        $data = array();
        while( $dbr && ($row = $dbr->FetchRow()) ) {
            $row = $this->on_query_row($row);
            if( $row ) $data[] = $row;
        }

        // postprocess the data
        $data = $this->postprocess($data);
        if( !$data ) return FALSE;
        $this->_data = $data;

        return TRUE;
    }

    public function generate()
    {
        $this->get_data();
        $uid = get_userid(false);

        $gCms = \CmsApp::get_instance();
        $smarty = $gCms->GetSmarty();
        $smarty->assign('report_data',$this->_data);
        $smarty->assign('start_date',$this->_startdate);
        $smarty->assign('end_date',$this->_enddate);
        $smarty->assign('report_name',$this->get_name());
        $smarty->assign('report_description',$this->get_description());
        $smarty->assign('currency_symbol',\cg_ecomm::get_currency_symbol());
        $smarty->assign('weight_units',\cg_ecomm::get_weight_units());
        if( $uid ) {
            $userops = $gCms->GetUserOperations();
            $user = $userops->LoadUserById($uid);
            if( is_object($user) ) $smarty->assign('user',$user);
        }

        $mod = \cge_utils::get_module(MOD_ORDERS);
        $tmp = $mod->ProcessTemplateFromDatabase($this->get_template_name());
        return $tmp;
    }
} // end of class.
