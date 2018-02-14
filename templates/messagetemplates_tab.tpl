{if isset($templates)}
<table class="pagetable" cellspacing="0">
  <thead>
   <tr>
     <th>{$Orders->Lang('name')}</th>
     <th>{$Orders->Lang('html')}</th>
     <th>{$Orders->Lang('created')}</th>
     <th>{$Orders->Lang('modified')}</th>
     <th class="pageicon">&nbsp;</th>
     <th class="pageicon">&nbsp;</th>
   </tr>
  </thead>
  <tbody>
  {foreach from=$templates item='one'}
    {cycle values="row1,row2" assign='rowclass'}
    <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
      <td>{module_action_link module='Orders' action='admin_addmsgtemplate' templateid=$one.id text=$one.name}</td>
      <td>{if $one.is_html}{$Orders->Lang('yes')}{else}{$Orders->Lang('no')}{/if}</td>
      <td>{$one.create_date|cms_date_format}</td>
      <td>{$one.modified_date|cms_date_format}</td>
      <td>{module_action_link module='Orders' action='admin_addmsgtemplate' templateid=$one.id text=$Orders->Lang('edit') image='icons/system/edit.gif' imageonly=true}</td>
      <td>{module_action_link module='Orders' action='admin_delmsgtemplate' templateid=$one.id text=$Orders->Lang('delete') image='icons/system/delete.gif' imageonly=true confmessage=$Orders->Lang('ask_delete')}</td>
    </tr>
  {/foreach}
  </tbody>
</table>
{/if}

<div class="pageoverflow">
  {$link_newtemplate}
</div>