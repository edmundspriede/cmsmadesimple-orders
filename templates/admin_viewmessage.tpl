<h3>{$Orders->Lang('view_message')}</h3>
<div class="pageoverflow">
  <p class="pagetext">{$Orders->Lang('sent')}:</p>
  <p class="pagetext">{$message.sent|cms_date_format}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$Orders->Lang('to')}:</p>
  <p class="pagetext">{$email}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$Orders->Lang('from')}:</p>
  <p class="pagetext">{$message.sender_name}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$Orders->Lang('subject')}:</p>
  <p class="pagetext">{$message.subject}</p>
</div>
<div class="pageoverflow">
  <p class="pagetext">{$Orders->Lang('body')}:</p>
  <p class="pagetext">{$message.body}</p>
</div>
<br/>
{$return_link}