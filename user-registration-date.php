<?php
/*
Plugin Name: User Registration Date
Plugin URI:  http://www.genexim.net
Description: Adds registration date on edit user profile screen and All users page show user registration date and time. 
Version:     1.0
Author:      Morshed Alam
Author URI:           https://github.com/wabcode
License:              GNU General Public License v2 or later
License URI:          http://www.gnu.org/licenses/gpl-2.0.html

Copyright 2010-2011  Morshed Alam  morshedalam@yandex.com
*/

class UserRegistrationDate {

	/**
	 * Start User Registration Date code
	 *
	 * @since 1.0
	 * @access public
	 */

    public function __construct() {
        add_action( 'init', array( &$this, 'init' ) );
    }


	/**
	 * All init functions
	 *
	 * @since 3.4
	 * @access public
	 */

    public function init() {
		add_filter( 'manage_users_columns', array( $this,'registered_users_columns') );
		add_action( 'manage_users_custom_column',  array( $this ,'users_join_date_column'), 10, 3);
		add_action( 'show_user_profile', array( $this ,'add_join_date_user_profile_fields'), 10, 1 );
		add_action( 'edit_user_profile', array( $this ,'add_join_date_user_profile_fields'), 10, 1 );
		add_filter( 'manage_users_sortable_columns', array( $this ,'registered_users_sortable_columns') );
		add_filter( 'request', array( $this ,'registered_users_orderby_column') );
		add_action( 'plugins_loaded', array( $this ,'load_this_textdomain') );

	}
        
        /**
	 * Registers column for display
	 *
	 * @since 1.0
	 * @access public
	 */ 
        
        function add_join_date_user_profile_fields( $user ){
            $table =
            '<h3>%1$s</h3>
            <table class="form-table">
                <tr>
                    <th>
                        %1$s
                    </th>
                    <td>
                        <p>Member since: %2$s</p>
                    </td>
                </tr>
            </table>';
            $udata = get_userdata( $user->ID );
            $registered = $udata->user_registered;
            printf(
                $table,
                'Registered',
                date( "d M Y", strtotime( $registered ) )
            );
        }

	/**
	 * Registers column for display
	 *
	 * @since 1.0
	 * @access public
	 */

	public static function registered_users_columns($columns) {
		$columns['registerdate'] = _x('Registered', 'user', 'recently-registered');
		return $columns;
	}

	/**
	 * Users Page registered join date and show.
	 * 
	 * This uses the same code as column_registered, which is why
	 *
	 * @since 1.0
	 * @access public
	 *
	 * @global string $mode
	 */

	public static function users_join_date_column( $value, $column_name, $user_id ) {

		global $mode;
		$mode = empty( $_REQUEST['mode'] ) ? 'list' : $_REQUEST['mode'];

        if ( 'registerdate' != $column_name ) {
           return $value;
        } else {
	        $user = get_userdata( $user_id );

	        if ( is_multisite() && ( 'list' == $mode ) ) {
	        	$formated_date = __( 'd M Y' );
	        } else {
		        $formated_date = __( 'd M Y g:i:s a' );
	        }

	        $registered   = strtotime(get_date_from_gmt($user->user_registered));
	        $registerdate = '<span>'. date_i18n( $formated_date, $registered ) .'</span>' ;

			return $registerdate;
		}
	}
        

	/**
	 * Makes Users page column sortable
	 *
	 * @since 1.0
	 * @access public
	 */

	public static function registered_users_sortable_columns($columns) {
          $custom = array(
		  // meta column id => sortby value used in query
          'registerdate'    => 'registered',
          );
      return wp_parse_args($custom, $columns);
	}

	/**
	 * User Page Calculate the order if we sort by date.
	 *
	 * @since 1.0
	 * @access public
	 */
	public static function registered_users_orderby_column( $vars ) {
        if ( isset( $vars['orderby'] ) && 'registerdate' == $vars['orderby'] ) {
                $vars = array_merge( $vars, array(
                        'meta_key' => 'registerdate',
                        'orderby' => 'meta_value'
                ) );
        }
        return $vars;
	}

	/**
	 * Internationalization - We're just going to use the language packs for this.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function load_this_textdomain() {
	    load_plugin_textdomain( 'user-registration-date' );
	}


}

new UserRegistrationDate();