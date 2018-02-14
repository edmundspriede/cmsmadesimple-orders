<h3>{$Orders->Lang('order')} #: {$order.id}</h3>

<fieldset style="float: left; width: 300px; margin-right: 25px;">
	<legend>&nbsp;{$Orders->Lang('billing_info')}:&nbsp;</legend>
	{$Orders->Lang('first_name')}: {$order.billing_first_name}<br />
	{$Orders->Lang('last_name')}: {$order.billing_last_name}<br />
	{$Orders->Lang('address1')}: {$order.billing_address1}<br />
	{$Orders->Lang('address2')}: {$order.billing_address2}<br />
	{$Orders->Lang('city')}: {$order.billing_city}<br />
	{$Orders->Lang('state/province')}: {$order.billing_state}<br />
	{$Orders->Lang('postal')}: {$order.billing_postal}<br />
	{$Orders->Lang('country')}: {$order.billing_country}<br />
	{$Orders->Lang('phone')}: {$order.billing_phone}<br />
</fieldset>

<fieldset style="width: 300px;">
	<legend>&nbsp;{$Orders->Lang('shipping_info')}:&nbsp;</legend>
	{$Orders->Lang('first_name')}: {$order.shipping_first_name}<br />
	{$Orders->Lang('last_name')}: {$order.shipping_last_name}<br />
	{$Orders->Lang('address1')}: {$order.shipping_address1}<br />
	{$Orders->Lang('address2')}: {$order.shipping_address2}<br />
	{$Orders->Lang('city')}: {$order.shipping_city}<br />
	{$Orders->Lang('state/province')}: {$order.shipping_state}<br />
	{$Orders->Lang('postal')}: {$order.shipping_postal}<br />
	{$Orders->Lang('country')}: {$order.shipping_country}<br />
	{$Orders->Lang('phone')}: {$order.shipping_phone}<br />
</fieldset>

<br/>

{$order_status_form_start}
	Order Status: {$order_status_dropdown} {$order_status_form_submit}<br />
	<br />
	{$back_to_orders}
{$order_status_form_end}
