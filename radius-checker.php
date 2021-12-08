<?php
/**
 * Plugin Name: Radius Checker
 * Plugin URI: https://irnanto.com
 * Description: Radius Checker
 * Author: Irnanto Dwi Saputra
 * Author URI: http://irnanto.com
 * Version: 1.0.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: radius_checker
 *
 * @package Radius Checker
 */

/*
'Radius Checker' is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

'Radius Checker' is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with 'Radius Checker'. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/



/**
 * Class IrnantoRadiusChecker for first setup
 */
class IrnantoRadiusChecker {
	/**
	 * A reference to an instance of this class.
	 *
	 * @var obj
	 */
	private static $instance;

	/**
	 * Options
	 *
	 * @var data option
	 */
	public $opt = array();

	/**
	 * Returns an instance of this class.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Construct metabox
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'init_settings' ) );
		add_shortcode( 'radius-checker', array($this, 'form_radius_checker' ) );

		$this->opt = get_option( 'irnanto_settings_radius' );
	}

	/**
	 * Register menu
	 */
	public function menu() {
		add_menu_page( 'Radius Checker', 'Radius Checker', 'manage_options', 'irnanto-radius', array( $this, 'page_settings' ), 'dashicons-admin-generic' );
	}

	/**
	 * init setting for display in admin/backend
	 */
	public function init_settings() {
		
		register_setting(
			'irnanto_settings_radius_group',
			'irnanto_settings_radius', // nama option
			array( $this, 'validate' )
		);

		add_settings_section(
			'irnanto_section_radius',
			__( 'Settings', 'irnanto' ),
			false,
			'irnanto-settings-radius'
		);

		add_settings_field(
			'api',
			__( 'Google Map API', 'irnanto' ),
			array( $this, 'text' ),
			'irnanto-settings-radius',
			'irnanto_section_radius',
			array(
				'label_for' => 'api',
				'required'  => true,
				'name' => 'api',
			)
		);
		add_settings_field(
			'url',
			__( 'Google Map Zone URL', 'irnanto' ),
			array( $this, 'url' ),
			'irnanto-settings-radius',
			'irnanto_section_radius',
			array(
				'label_for' => 'url',
				'required'  => true,
				'name' => 'url',
			)
		);

	}

	/**
	 * Displays a text field for a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function text( $args ) {
		$disabled  = '';
		$required  = '';
		$value = '';
		if ( 'api' === $args['label_for'] ) {
			$value = isset( $this->opt['api'] ) ? $this->opt['api'] : '';
		}

		if ( isset( $args['disabled'] ) ) {
			$disabled = ' disabled="disabled"';
		}

		if ( isset( $args['required'] ) ) {
			$required = ' required';
		}

		$html  = sprintf( '<input type="text" class="regular-text" id="%1$s-%2$s" name="%1$s[%2$s]" value="%3$s"%4$s/>', 'irnanto_settings_radius', $args['name'], esc_attr( $value ), $disabled . $required );
		// $html .= $this->get_field_description( $args );
		echo $html;
	}

	/**
	 * Displays a text field for a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function url( $args ) {
		$disabled  = '';
		$required  = '';
		$value = '';
		if ( 'api' === $args['label_for'] ) {
			$value = isset( $this->opt['api'] ) ? $this->opt['api'] : '';
		}

		if ( isset( $args['disabled'] ) ) {
			$disabled = ' disabled="disabled"';
		}

		if ( isset( $args['required'] ) ) {
			$required = ' required';
		}

		$html  = sprintf( '<input type="url" class="regular-text" id="%1$s-%2$s" name="%1$s[%2$s]" value="%3$s"%4$s/>', 'irnanto_settings_radius', $args['name'], esc_attr( $value ), $disabled . $required );
		// $html .= $this->get_field_description( $args );
		echo $html;
	}

	/**
	 * page option to show form
	 */
	public function page_settings() {
		// Check required user capability
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'irnanto' ) );
		}

		// Admin Page Layout
		echo '<div class="wrap">' . "\n";
		echo '  <h1>' . get_admin_page_title() . '</h1>' . "\n";

		echo '  <form action="options.php" method="post" id="form-irnanto-settings" data-nonce="' . wp_create_nonce( 'irnanto-settings-nonce' ) . '">' . "\n";

		settings_fields( 'irnanto_settings_radius_group' );
		do_settings_sections( 'irnanto-settings-radius' );
		submit_button();

		echo '  </form>' . "\n";
		echo '</div>' . "\n";
	}

	/**
	 * Form radius checker
	 */
	public function form_radius_checker($atts) {
		?>
		<form method="post">
			<h2>Check whether an address is inside or outside of the service area ?</h2>
			<label for="address">Address :</label><input type="text" name="address" id="address">
			<input type="submit" name="check" value="Check">
		</form>
		<?php
	}

}

/**
 * Load class
 *
 * @return object object class IrnantoRadiusChecker
 */
function irnanto_radius_checker() {
	return IrnantoRadiusChecker::get_instance();
}

/**
 * Call function irnanto_radius_checker
 */
irnanto_radius_checker();
