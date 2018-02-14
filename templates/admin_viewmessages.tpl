<h3>{$order.invoice} - {$Orders->Lang('view_messages')}</h3>
<br/>

<table class="pagetable" cellspacing="0">
  <thead>
    <tr>
      <th>{$Orders->Lang('subject')}</th>
      <th>{$Orders->Lang('from')}</th>
      <th>{$Orders->Lang('sent')}</th>
      <th class="pageicon">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    {foreach from=$messages item='one'}
      {cycle values="row1,row2" assign="rowclass"}
      <tr class="{$rowclass}" onmouseover="this.className='{$rowclass}hover';" onmouseout="this.className='{$rowclass}';">
        <td>{$one.subject|truncate:80|trim}</td>
        <td>{$one.sender_name}</td>
        <td>{$one.sent|cms_date_format}</td>
        <td>{$one.view_link}</td>
      </tr>
    {/foreach}
  </tbody>
</table>

{$return_link}
