<h3>{$order.invoice} - {$Orders->Lang('send_message')}</h3>
<h4>{$Orders->Lang('to')}: {$order.email}</h4>
<br/>

{$formstart}
<div class="pageoverflow">
  <p class="pagetext">{$Orders->Lang('template')}:</p>
  <p class="pageinput">{$input_template}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$Orders->Lang('subject')}:</p>
  <p class="pageinput">{$input_subject}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$Orders->Lang('message')}:</p>
  <p class="pageinput">{$input_body}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">&nbsp;</p>
  <p class="pageinput">{$input_send}{$input_cancel}</p>
</div>
{$formend}