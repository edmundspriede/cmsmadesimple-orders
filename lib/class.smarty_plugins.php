<?php
namespace Orders;

final class smarty_plugins
{
    protected function __construct() {}

    public static function init($smarty)
    {
        $smarty->register_function('orders_country_options','\\Orders\smarty_plugins::country_options');
        $smarty->register_function('orders_state_options','\\Orders\smarty_plugins::state_options');
    }

    public static function country_options($params,&$tpl)
    {
        $mod = \cms_utils::get_module(MOD_ORDERS);
        $valid_countries = $mod->get_country_list_options();
        $dflt_country = $mod->GetPreference('dflt_country');
        $selected = \cge_param::get_string($params,'selected',$dflt_country);

        $options = \CmsFormUtils::create_options( $valid_countries, $selected );
        if( ($assign = \cge_param::get_string($params,'assign') ) ) {
            $tpl->assign($assign,$options);
        } else {
            return $options;
        }
    }

    public static function state_options($params,&$tpl)
    {
        $mod = \cms_utils::get_module(MOD_ORDERS);
        $valid = $mod->get_state_list_options();
        $dflt_state = $mod->GetPreference('dflt_state');
        $require_state = $mod->GetPreference('require_state');
        $selected = \cge_param::get_string($params,'selected',$dflt_state);

        if( !$require_state ) {
            $valid = array_merge( [''=>$mod->Lang('nostate')], $valid );
        }
        $options = \CmsFormUtils::create_options( $valid , $selected );

        if( ($assign = \cge_param::get_string($params,'assign') ) ) {
            $tpl->assign($assign,$options);
        } else {
            return $options;
        }
    }
}