<?PHP

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Billing Plan Base Resources
 *
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Andrew Yatskovets
 * @copyright 2011 / OnApp
 * @link      http://www.onapp.com/
 * @see       ONAPP
 */

/**
 * require Base class
 */
require_once dirname( __FILE__ ) . '/../ONAPP.php';
require_once dirname( __FILE__ ) . '/BaseResource.php';

/**
 * The ONAPP_BillingPlan_ResourceIpAddress class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 */
class ONAPP_BillingPlan_ResourceIpAddress extends ONAPP_BillingPlan_BaseResource {

    function _init_fields( $version = NULL ) {
        parent::_init_fields();

        $this->_fields[ 'resource_class'] = array(
            ONAPP_FIELD_MAP           => '_resource_class',
            ONAPP_FIELD_TYPE          => 'string',
            ONAPP_FIELD_REQUIRED      => true,
            ONAPP_FIELD_READ_ONLY     => true,
            ONAPP_FIELD_DEFAULT_VALUE => 'Resource::IpAddress'
        );

        return $this->_fields;
    }
}