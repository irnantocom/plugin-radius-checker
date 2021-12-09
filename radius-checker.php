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

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
use StepanDalecky\KmlParser\Parser;

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
		add_shortcode( 'radius-checker', array( $this, 'form_radius_checker' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		// add_filter( 'script_loader_tag', 'add_async_defer_attribute', 10, 2 );
		add_action( 'template_redirect', array( $this, 'check_submit' ) );

		$this->opt = get_option( 'irnanto_settings_radius' );
	}

	/**
	 * Register menu
	 */
	public function menu() {
		add_menu_page( 'Radius Checker', 'Radius Checker', 'manage_options', 'irnanto-radius', array( $this, 'page_settings' ), 'dashicons-admin-generic' );
	}

	/**
	 * Init setting for display in admin/backend
	 */
	public function init_settings() {

		register_setting(
			'irnanto_settings_radius_group',
			'irnanto_settings_radius', // nama option.
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
				'name'      => 'api',
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
				'name'      => 'url',
			)
		);
		add_settings_field(
			'actionurl',
			__( 'Action URL (inside the radius)', 'irnanto' ),
			array( $this, 'url' ),
			'irnanto-settings-radius',
			'irnanto_section_radius',
			array(
				'label_for' => 'actionurl',
				'required'  => true,
				'name'      => 'actionurl',
			)
		);
		add_settings_field(
			'actionurloutside',
			__( 'Action URL (outside the radius)', 'irnanto' ),
			array( $this, 'url' ),
			'irnanto-settings-radius',
			'irnanto_section_radius',
			array(
				'label_for' => 'actionurloutside',
				'required'  => true,
				'name'      => 'actionurloutside',
			)
		);

	}

	/**
	 * Displays a text field for a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function text( $args ) {
		$disabled = '';
		$required = '';
		$value    = '';
		if ( 'api' === $args['label_for'] ) {
			$value = isset( $this->opt['api'] ) ? $this->opt['api'] : '';
		}

		if ( isset( $args['disabled'] ) ) {
			$disabled = ' disabled="disabled"';
		}

		if ( isset( $args['required'] ) ) {
			$required = ' required';
		}

		$html = sprintf( '<input type="text" class="regular-text" id="%1$s-%2$s" name="%1$s[%2$s]" value="%3$s"%4$s/>', 'irnanto_settings_radius', $args['name'], esc_attr( $value ), $disabled . $required );
		echo $html;
	}

	/**
	 * Displays a text field for a settings field
	 *
	 * @param array $args settings field args.
	 */
	public function url( $args ) {
		$disabled = '';
		$required = '';
		$value    = '';
		if ( 'url' === $args['label_for'] ) {
			$value = isset( $this->opt['url'] ) ? $this->opt['url'] : '';
		} elseif ( 'actionurl' === $args['label_for'] ) {
			$value = isset( $this->opt['actionurl'] ) ? $this->opt['actionurl'] : '';
		} elseif ( 'actionurloutside' === $args['label_for'] ) {
			$value = isset( $this->opt['actionurloutside'] ) ? $this->opt['actionurloutside'] : '';
		}

		if ( isset( $args['disabled'] ) ) {
			$disabled = ' disabled="disabled"';
		}

		if ( isset( $args['required'] ) ) {
			$required = ' required';
		}

		$html = sprintf( '<input type="url" class="regular-text" id="%1$s-%2$s" name="%1$s[%2$s]" value="%3$s"%4$s/>', 'irnanto_settings_radius', $args['name'], esc_attr( $value ), $disabled . $required );
		echo $html;
	}

	/**
	 * Page option to show form
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
	public function form_radius_checker( $atts ) {
		?>
		<form method="post">
			<h2>Check whether an address is inside or outside of the service area ?</h2>
			<label for="address">Address :</label><input type="text" name="address" id="address">
			<?php wp_nonce_field( 'irnanto_find_location', 'nonce' ); ?>
			<input type="submit" name="check" value="Check">
		</form>
		<div id="container">
		  <div id="map"></div>
		</div>
		<?php
	}

	/**
	 * Check submit find place
	 */
	public function check_submit() {
		if ( is_singular() ) {
			global $post;
			if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'radius-checker' ) ) {
				if ( isset( $_POST['nonce'] ) ) {
					$nonce = $_POST['nonce'];
					if ( wp_verify_nonce( $nonce, 'irnanto_find_location' ) ) {
						$address = sanitize_text_field( $_POST['address'] );

						if ( isset( $this->opt['url'] ) ) {
							$kml = $this->opt['url'];
							$url = get_home_url();

							$parser = Parser::fromFile( $kml );
							// $parser = Parser::fromString($xmlString);

							$kml      = $parser->getKml();
							$document = $kml->getDocument();

							$folders   = $document->getFolders();
							$placemark = $folders[0]->getPlacemarks();

							$detect = false;
							foreach ( $placemark as $key => $place ) {
								if ( $place->hasName() ) {
									$place_name = $place->getName();
									if ( $address == $place_name ) {
										$detect = true;
										break;
									}
								}
							}

							if ( $detect ) {
								if ( isset( $this->opt['actionurl'] ) ) {
									$url = $this->opt['actionurl'];
								}
							} else {
								if ( isset( $this->opt['actionurloutside'] ) ) {
									$url = $this->opt['actionurloutside'];
								}
							}

							wp_redirect( $url, 301 );
							exit();
						}
					}
				}
			}
		}
	}

	/**
	 * Enqueue script JS
	 */
	public function enqueue_scripts() {
		if ( is_singular() ) {
			global $post;
			if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'radius-checker' ) ) {
				if ( isset( $this->opt['api'] ) ) {
					wp_enqueue_script( 'irnantomap', plugin_dir_url( __FILE__ ) . 'assets/js/front.js', array(), '1.0.0', true );
					wp_enqueue_script( 'googleapis', esc_url( add_query_arg( 'key', $this->opt['api'] . '&callback=initMap', '//maps.googleapis.com/maps/api/js' ) ), array(), '1.0.0', true );

					$data = array();

					if ( isset( $this->opt['url'] ) ) {
						$data['kml'] = $this->opt['url'];
					}

					wp_localize_script( 'irnantomap', 'mapdata', $data );
				}
			}
		}
	}

	/**
	 * Add async & defer attribute
	 *
	 * @param string $tag    src
	 * @param string $handle Name of the script
	 */
	public function add_async_defer_attribute( $tag, $handle ) {
		if ( 'googleapis' !== $handle ) {
			return $tag;
		} else {
			return str_replace( ' src', ' async src', $tag );
		}
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
