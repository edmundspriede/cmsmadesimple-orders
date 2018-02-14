<?php
namespace Orders;

class billing_address_retriever
{
    const SESSION_KEY = '__mybillingaddr__';
    const ADDR_POLICY_COOKIE = 'addr::cookie';
    const ADDR_POLICY_FEU = 'addr::from_feu';
    const ADDR_POLICY_LAST = 'addr::from_last';
    const ADDR_POLICY_NONE = 'addr::none';

    private $_mod;
    private $_policy;
    private $_uid;

    public function __construct(\Orders $mod, $policy, $uid)
    {
        $this->_mod = $mod;
        $this->_policy = $policy;
        $this->_uid = (int) $uid;
    }

    /**
     * Returns Address object, or null
     */
    public function get_address()
    {
        // prefer session address... if it exists.
        if( isset($_SESSION[self::SESSION_KEY]) ) {
            $data = $_SESSION[self::SESSION_KEY];
            if( $data ) $data = unserialize( $data );
            if( is_object($data) ) return $data;
        }

        $out = null;
        switch( $this->_policy ) {
        case self::ADDR_POLICY_FEU:
            if( $this->_uid > 0 ) {
                $out = orders_helper::get_feu_address();
            }
            break;

        case self::ADDR_POLICY_LAST:
            if( $this->_uid > 0 ) {
                $last_order_id = orders_ops::find_last_feu_order($this->_uid);
                if( $last_order_id ) {
                    $tmp_order = orders_ops::load_by_id($last_order_id);
                    $out = $tmp_order->get_billing();
                }
            }
            break;

        case self::ADDR_POLICY_COOKIE:
            $tmp = \cms_cookies::get('my_address');
            if( $tmp ) {
                $tmp = unserialize(base64_decode($tmp));
                if( $tmp instanceof Address ) $out = $tmp;
            }
            break;

        case self::ADDR_POLICY_NONE:
            break;
        }

        if( !$out ) {
            $out = new Address;
            $out->state = $this->_mod->GetPreference('dflt_state');
            $out->country = $this->_mod->GetPreference('dflt_country');
        }
        return $out;
    }

    public function save_address(Address $address)
    {
        // save the address in the session.
        $_SESSION[self::SESSION_KEY] = serialize($address);

        if( $this->_policy == self::ADDR_POLICY_COOKIE ) {
            $data = base64_encode(serialize($address));
            @setcookie('my_address',$data,time()+(365*24*60*60));
        }
    }

} // end of class