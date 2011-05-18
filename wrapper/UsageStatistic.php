<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Usage Statistics
 *
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Vitaliy Kondratyuk
 * @copyright 2010 / OnApp
 * @link      http://www.onapp.com/
 * @see       ONAPP
 */

/**
 * require Base class
 */
require_once dirname( __FILE__ ) . '/ONAPP.php';

/**
 * Usage Statistics
 *
 * The Usage Statistics class uses the following basic methods:
 * {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of Usage Statistics
 *
 *     - <i>GET onapp.com/usage_statistics.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of CPU Usage Statistics
 *
 *     - <i>GET onapp.com/usage_statistics.json</i>
 */
class ONAPP_UsageStatistic extends ONAPP {

    /**
     * ID
     *
     * @var integer
     */
    var $_id;

    /**
     * the date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var datetime
     */
    var $_created_at;

    /**
     * the date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var datetime
     */
    var $_updated_at;

    /**
     * CPU used
     *
     * @var integer
     */
    var $_cpu_used;

    /**
     * CPU shares
     *
     * @var integer
     */
    var $_cpu_shares;

    /**
     * disk size
     *
     * @var integer
     */
    var $_disk_size;

    /**
     * Amount
     *
     * @var decimal
     */
    var $_amount;

    /**
     * CPU count
     *
     * @var integer
     */
    var $_cpu_count;

    /**
     * bandwidth used
     *
     * @var integer
     */
    var $_bandwidth_used;

    /**
     * User ID
     *
     * @var integer
     */
    var $_user_id;

    /**
     * number of hours
     *
     * @var integer
     */
    var $_number_of_hours;

    /**
     * @todo: Add description
     *
     * @var boolean
     */
    var $_booted;

    /**
     * RAM used in MB
     *
     * @var integer
     */
    var $_ram;

    /**
     * VM ID
     *
     * @var integer
     */
    var $_virtual_machine_id;

    /**
     * count of used IP addresses
     *
     * @var integer
     */
    var $_ip_addresses_count;

    /**
     *
     *
     */
    var $_cost;
    var $_stat_time;
    var $_vm_billing_stat_id;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'vm-hourly-stat';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'usage_statistics';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_UsageStatistic';

    /**
     * API Fields description
     *
     * @access private
     * @var    array
     */
    function _init_fields( $version = NULL ) {
        if( !isset( $this->options[ ONAPP_OPTION_API_TYPE ] ) || ( $this->options[ ONAPP_OPTION_API_TYPE ] == 'json' ) ) {
            $this->_tagRoot = 'vm_hourly_stat';
        }

        if( is_null( $version ) ) {
            $version = $this->_version;
        }

        switch( $version ) {
            case '2.0':
                $this->_fields = array(
                    'id' => array(
                        ONAPP_FIELD_MAP => '_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'created_at' => array(
                        ONAPP_FIELD_MAP => '_created_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'updated_at' => array(
                        ONAPP_FIELD_MAP => '_updated_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'cpu_used' => array(
                        ONAPP_FIELD_MAP => '_cpu_used',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'cpu_shares' => array(
                        ONAPP_FIELD_MAP => '_cpu_shares',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'disk_size' => array(
                        ONAPP_FIELD_MAP => '_disk_size',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'amount' => array(
                        ONAPP_FIELD_MAP => '_amount',
                        ONAPP_FIELD_TYPE => 'decimal',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'cpu_count' => array(
                        ONAPP_FIELD_MAP => '_cpu_count',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'bandwidth_used' => array(
                        ONAPP_FIELD_MAP => '_bandwidth_used',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'user_id' => array(
                        ONAPP_FIELD_MAP => '_user_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'number_of_hours' => array(
                        ONAPP_FIELD_MAP => '_number_of_hours',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'booted' => array(
                        ONAPP_FIELD_MAP => '_booted',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'ram' => array(
                        ONAPP_FIELD_MAP => '_ram',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'virtual_machine_id' => array(
                        ONAPP_FIELD_MAP => '_virtual_machine_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'ip_addresses_count' => array(
                        ONAPP_FIELD_MAP => '_ip_addresses_count',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                );

                break;

            case '2.1':
                $this->_fields = $this->_init_fields('2.0');


                unset( $this->_fields[ 'amount' ] );
                unset( $this->_fields[ 'bandwidth_used' ] );
                unset( $this->_fields[ 'booted' ] );
                unset( $this->_fields[ 'cpu_count' ] );
                unset( $this->_fields[ 'cpu_shares' ] );
                unset( $this->_fields[ 'cpu_used' ] );
                unset( $this->_fields[ 'disk_size' ] );
                unset( $this->_fields[ 'ip_addresses_count' ] );
                unset( $this->_fields[ 'number_of_hours' ] );
                unset( $this->_fields[ 'ram' ] );

                $this->_fields[ 'cost' ] = array(
                    ONAPP_FIELD_MAP => '_cost',
                    ONAPP_FIELD_TYPE => 'string',
                    ONAPP_FIELD_READ_ONLY => true,
                );

                $this->_fields[ 'stat_time' ] = array(
                    ONAPP_FIELD_MAP => '_stat_time',
                    ONAPP_FIELD_TYPE => 'string',
                    ONAPP_FIELD_READ_ONLY => true,
                );

                $this->_fields[ 'vm_billing_stat_id' ] = array(
                    ONAPP_FIELD_MAP => '_vm_billing_stat_id',
                    ONAPP_FIELD_TYPE => 'integer',
                    ONAPP_FIELD_READ_ONLY => true,
                );

                break;
        };

        return $this->_fields;
    }

    /**
     * Activates action performed with object
     *
     * @param string $action_name the name of action
     *
     * @access public
     */
    function activate( $action_name ) {
        switch( $action_name ) {
            case ONAPP_ACTIVATE_LOAD:
            case ONAPP_ACTIVATE_SAVE:
            case ONAPP_ACTIVATE_DELETE:
                die( "Call to undefined method " . __CLASS__ . "::$action_name()" );
                break;
        }
    }
}