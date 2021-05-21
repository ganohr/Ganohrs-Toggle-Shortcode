<?php

if ( ! class_exists('ganohrs_toggle_shortcode_Options') ) :


/**
 * WP OutputLogFile Admin Mange Class
 */
class ganohrs_toggle_shortcode_Options {

	const PAGE_ID = 'ganohrs-toggle-shortcode-options';

	private $options;			// setting tab option

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	public function add_plugin_page() {
		$load_hook = add_options_page( "Ganohr's Toggle Shortcode",
									   "Ganohr's Toggle Shortcode",
									   'manage_options',
									   self::PAGE_ID,
									   array( $this, 'admin_manage_page' ) );
	}

	public function admin_manage_page()  {
		// Set class property
		$this->options = get_option( 'gts_options' );

		?>
		<div class="wrap">
		<h1>Ganohr's Toggle Shortcode</h1>

		<form method="post" action="options.php">
		<?php
			settings_fields( 'gts_options_group' );
			do_settings_sections( 'gts_options' );

			submit_button();
		?>
		</form>
		</div>
<?php
	}

	public function page_init() {
		register_setting(
			'gts_options_group', // Option group
			'gts_options',
			array( $this, 'sanitize_and_check' )
		);

		add_settings_section(
			'gts_setting_section', // section ID
			'Settings', // Title
			null,
			'gts_options'
		);
		add_settings_field( 'style', "Choose Style",
							array( $this,'style_callback' ),
							'gts_options',
							'gts_setting_section' );
	}


	public function sanitize_and_check( $input ) {
		$new_input = array();

		$new_input[ 'style' ] = isset( $input[ 'style' ] ) ? $input[ 'style' ] : 'nornaml';
		return $new_input;
	}

	public function style_callback() {
	    $st = $this->options['style'];
	    if(strlen($st) == 0) {
	        $st = 'normal';
        }
?>
<select name="gts_options[style]">
    <option <?php echo ($st == "normal"   ? 'selected' : '') ?> value="normal"  >Normal  </option>
    <option <?php echo ($st == "black"    ? 'selected' : '') ?> value="black"   >Black   </option>
    <option <?php echo ($st == "blue"     ? 'selected' : '') ?> value="blue"    >Blue    </option>
    <option <?php echo ($st == "brown"    ? 'selected' : '') ?> value="brown"   >Brown   </option>
    <option <?php echo ($st == "gray"     ? 'selected' : '') ?> value="gray"    >Gray    </option>
    <option <?php echo ($st == "green"    ? 'selected' : '') ?> value="green"   >Green   </option>
    <option <?php echo ($st == "ice"      ? 'selected' : '') ?> value="ice"     >Ice     </option>
    <option <?php echo ($st == "navy"     ? 'selected' : '') ?> value="navy"    >Navy    </option>
    <option <?php echo ($st == "pink"     ? 'selected' : '') ?> value="pink"    >Pink    </option>
    <option <?php echo ($st == "purple"   ? 'selected' : '') ?> value="purple"  >Purple  </option>
    <option <?php echo ($st == "red"      ? 'selected' : '') ?> value="red"     >Red     </option>
    <option <?php echo ($st == "white"    ? 'selected' : '') ?> value="white"   >White   </option>
    <option <?php echo ($st == "yellow"   ? 'selected' : '') ?> value="yellow"  >Yellow  </option>
    <option <?php echo ($st == "gold"     ? 'selected' : '') ?> value="gold"    >Gold    </option>
    <option <?php echo ($st == "shock"    ? 'selected' : '') ?> value="shock"   >Shock   </option>
    <option <?php echo ($st == "vivid"    ? 'selected' : '') ?> value="vivid"   >Vivid   </option>
    <option <?php echo ($st == "facebook" ? 'selected' : '') ?> value="facebook">Facebook</option>
    <option <?php echo ($st == "twitter"  ? 'selected' : '') ?> value="twitter" >Twitter </option>
    <option <?php echo ($st == "line"     ? 'selected' : '') ?> value="line"    >Line    </option>
</select>
<?php
	}
}

if( is_admin() )
	new ganohrs_toggle_shortcode_Options();

endif;

