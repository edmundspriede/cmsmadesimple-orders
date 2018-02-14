<?php
namespace Orders;

interface order_factory
{
    // set the order into
    public function __construct(Order $order = null);
    public function set_billing_address(\cge_address $address);
    public function set_order_notes($notes);
    public function set_shipping_address($dest_idx,\cge_address $addr);
    public function set_shipping_pickup($dest_idx,$flag);
    public function set_shipping_message($dest_idx,$message);
    public function set_order_extra($key,$val);
    public function validate_addresses();

    public function set_order(Order $order);
    public function set_cart_module(\CGEcommerceBase\shopping_cart_mgr $module);
    public function set_shipping_module(\CGEcommerceBase\shipping_assistant $module = null);
    public function set_tax_module(\CGEcommerceBase\tax_calculator $module = null);
    public function set_packaging_module(\CGEcommerceBase\packaging_calculator $module = null);
    public function set_handling_module(\CGEcommerceBase\handling_calculator $module = null);
    public function set_user_id($uid);
    //public function set_address_policy($policy);

    // talks to the cart, and makes a basic order (one or more destinations)
    // but does not have shipping, handling, or taxes.
    // @return order
    public function get_basic_order();

    // takes the order and adds line items for packaging, shipping, handling, taxes, promotions
    // @return order
    public function adjust_for_shipping();

    // return boolean if a single order can come from multiple different vendors
    public function supports_multiple_vendors();

    // returns a boolean if different shipping locations can be specified for each shipping/sub-order/destination
    public function supports_different_shipping_locations();
}