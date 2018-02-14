<?php
if( !isset($gCms) ) exit;
if( !$this->CheckPermission(ORDERS_PERM_VIEWORDERS) && !$this->CheckPermission(ORDERS_PERM_MANAGEORDERS) ) exit;

$order_id = \cge_param::get_int($params,'orderid');
$shipping_id = \cge_param::get_int($params,'shipping_id');
if( $order_id < 1 || $shipping_id < 1 ) throw new \LogicException('Invalid parameters passed to admin_print_packinglist action');

$order = \Orders\orders_ops::load_by_id( $order_id );
if( !$order ) throw new \LogicException('Could not find order with the specified id');

$dest = $order->get_destination_by_id( $shipping_id );
if( !$dest ) throw new \LogicException('Could not find the destination object specified by id '.$shipping_id);

// NOTE: packing list dimensions and weight are in metric (grams and mm)
$packing_list = $dest->get_packing_list();
$total_weight = 0;
$total_value = 0;
$boxes = $packing_list->get_boxes();
foreach( $boxes as $box ) {
    $total_value += $box->total_value;
    $total_weight += $box->total_weight;
}
$tpl = $this->CreateSmartyTemplate('admin_print_packinglist.tpl');
$tpl->assign('order',$order);
$tpl->assign('packing_list',$packing_list);
$tpl->assign('total_value',$total_value);
$tpl->assign('total_weight',$total_weight);
$tpl->assign('shipping_id',$shipping_id);
$tpl->display();
