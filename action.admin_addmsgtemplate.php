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
if( !$this->CheckPermission('Modify Templates') )
  {
    echo $this->ShowErrors($this->Lang('error_permissiondenied'));
    return;
  }
$this->SetCurrentTab('messagetemplates');

if( isset($params['cancel']) )
  {
    $this->RedirectToTab($id,'','','admin_templates');
  }

$name = '';
$subject = '';
$ishtml = 0;
$template = '';

if( isset($params['templateid']) )
  {
    // Load it from the database
    $query = 'SELECT * FROM '.cms_db_prefix().'module_orders_message_templates
               WHERE id = ?';
    $row = $db->GetRow($query,array((int)$params['templateid']));
    if( $row ) {
      $name = $row['name'];
      $subject = $row['subject'];
      $ishtml = $row['is_html'];
      $template = $row['template'];
    }
  }

// get the data from the parameters
if( isset($params['input_name']) ) $name = trim($params['input_name']);
if( isset($params['input_subject']) ) $subject = trim($params['input_subject']);
if( isset($params['input_html']) ) $ishtml = trim($params['input_html']);
if( isset($params['input_template'] ) ) $template = cms_html_entity_decode(trim($params['input_template']));

if( isset($params['submit']) )
  {
    // data validation
    if( empty($name) || empty($subject) || empty($template) )
      {
	echo $this->ShowErrors($this->Lang('error_insufficientparams'));
      }
    else
      {
	if( isset($params['templateid']) )
	  {
	    // it's an update
	    // check for a template that already has this name
	    $query = 'SELECT id FROM '.cms_db_prefix().'module_orders_message_templates
                       WHERE name = ? AND id != ? LIMIT 1';
	    $res = $db->GetOne($query,array($name,(int)$params['templateid']));
	    if( $res )
	      {
		echo $this->ShowErrors($this->Lang('error_nameexists'));
	      }
	    else
	      {
		// good to update
		$now = $db->DbTimeStamp(time());
		$query = 'UPDATE '.cms_db_prefix()."module_orders_message_templates
                             SET name = ?, is_html = ?, subject = ?, template = ?, 
                                 modified_date = $now
                           WHERE id = ?";
		$db->Execute($query,array($name,$ishtml,$subject,$template,
					  (int)$params['templateid']));

		echo $this->RedirectToTab($id,'','','admin_templates');
	      }
	  }
	else
	  {
	    // it's an insert
	    // check for a template that already has this name
	    $query = 'SELECT id FROM '.cms_db_prefix().'module_orders_message_templates
                   WHERE name = ? LIMIT 1';
	    $res = $db->GetOne($query,array($name));
	    if( $res )
	      {
		echo $this->ShowErrors($this->Lang('error_nameexists'));
	      }
	    else
	      {
		// good to commit
		$now = $db->DbTimeStamp(time());
		$query = 'INSERT INTO '.cms_db_prefix()."module_orders_message_templates
                        (name,is_html,subject,template,create_date,modified_date)
                      VALUES (?,?,?,?,$now,$now)";
		$db->Execute($query,array($name,$ishtml,$subject,$template));
		
		echo $this->RedirectToTab($id,'','','admin_templates');
	      }
	  }
      }
  }

$parms = array();
if( isset($params['templateid']) )
  {
    $parms['templateid'] = (int)$params['templateid'];
  }
$smarty->assign('formstart',$this->CGCreateFormStart($id,'admin_addmsgtemplate','',$parms));
$smarty->assign('formend',$this->CreateFormEnd());
$smarty->assign('input_name',$this->CreateInputText($id,'input_name',$name,80,255));
$smarty->assign('input_html',$this->CreateInputYesNoDropdown($id,'input_html',$ishtml,'onchange=\'this.form.submit()\''));
$smarty->assign('input_subject',$this->CreateInputText($id,'input_subject',$subject,80,255));
$smarty->assign('input_template',
		$this->CreateTextArea($ishtml,$id,$template,'input_template'));
$smarty->assign('submit',
		$this->CreateInputSubmit($id,'submit',$this->Lang('submit')));
$smarty->assign('cancel',
		$this->CreateInputSubmit($id,'cancel',$this->Lang('cancel')));

echo $this->ProcessTemplate('admin_addmsgtemplate.tpl');
#
# EOF
#
?>