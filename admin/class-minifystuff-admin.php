<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/liaisontw/
 * @since      1.0.0
 *
 * @package    minifyStuff
 * @subpackage minifyStuff/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    minifyStuff
 * @subpackage minifyStuff/admin
 * @author     Liaison Chang <liaison.tw@gmail.com>
 */
class minifyStuff_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action( 'admin_menu', array($this, 'admin_menu') );
		//add_action( 'admin_menu', 'teckel_minify_stuff_menu' );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in minifyStuff_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The minifyStuff_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/minifyStuff-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in minifyStuff_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The minifyStuff_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/minifyStuff-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function admin_menu() {
		add_options_page( 'Minify Stuff Options', 
						  'Minify Stuff', 
						  'manage_options', 
						  'minify_stuff_options', 
						  array(&$this, 'minify_stuff_menu_options')				  
		);
	}

	public function minify_stuff_menu_options() {
		//$minify_stuff_active = get_option( 'minify_stuff_active' );
		//$minify_javascript = get_option( 'minify_javascript' );
		//$minify_comments = get_option( 'minify_comments' );
		$minify_stuff_active = 'yes';
		$minify_javascript = 'yes';
		$minify_comments = 'yes';
	?>
	<style>
	#minify_stuff label {white-space:nowrap}
	#minify_stuff input[type="radio"] {margin-left:15px}
	#minify_stuff input[type="radio"]:first-child {margin-left:0}
	#minify_stuff .value {display:inline-block;min-width:50px}
	#minify_stuff p {font-size:1.1em;font-weight:600}
	@media screen and (max-width: 500px) {#minify_stuff label {white-space:normal}}
	</style>
	<div class="wrap" id="minify_stuff">
		<h2>Minify HTML Settings</h2>
		<form name="form1" method="post" action="">
			<input type="hidden" name="minify_stuff_nonce" value="<?php echo esc_html( wp_create_nonce('minify-html-nonce') ); ?>">
			<input type="hidden" name="minify_stuff_submit_hidden" value="Y">
			<table border="1" class="form-table" >
				<tbody>
					<tr>
						<td>
							<p class="description"><?php esc_html_e( 'Option', 'minify-stuff-markup' ); ?></p>
						</td>
						<td>
							<p class="description"><?php esc_html_e( 'Setting', 'minify-stuff-markup' ); ?></p>
						</td>
						<td>
							<p class="description"><?php esc_html_e( 'Description', 'minify-stuff-markup' ); ?></p>
						</td>
					</tr>
					<tr class="minify_stuff_active">
						<th><label><?php esc_html_e( 'Minify Stuff', 'minify-stuff-markup' ); ?></label></th>
						<td>
							<input type="radio" name="minify_stuff_active" value="yes"<?php echo ($minify_stuff_active=='yes' ? ' checked' : ''); ?>><span class="value"><strong><?php esc_html_e( 'Enable', 'minify-stuff-markup' ); ?></strong></span>
							<input type="radio" name="minify_stuff_active" value="no"<?php echo ($minify_stuff_active!='yes' ? ' checked' : ''); ?>><span class="value"><?php esc_html_e( 'Disable', 'minify-stuff-markup' ); ?></span>
						</td>
						<td>
							<p class="description"><?php esc_html_e( 'Enable or disable Minify Stuff', 'minify-stuff-markup' ); ?></p>
						</td>
					</tr>
					<tr class="minify_javascript minify_stuff_options">
						<th><label><?php esc_html_e( 'Minify inline JavaScript', 'minify-stuff-markup' ); ?></label></th>
						<td>
							<input type="radio" name="minify_javascript" value="yes"<?php echo ($minify_javascript=='yes' ? ' checked' : ''); ?>><span class="value"><strong><?php esc_html_e( 'Yes', 'minify-stuff-markup' ); ?></strong></span>
							<input type="radio" name="minify_javascript" value="no"<?php echo ($minify_javascript!='yes' ? ' checked' : ''); ?>><span class="value"><?php esc_html_e( 'No', 'minify-stuff-markup' ); ?></span>
						</td>
						<td>
							<p class="description"><?php esc_html_e( 'Default "Yes"', 'minify-stuff-markup' ); ?></p>
						</td>
					</tr>
					<tr class="minify_comments minify_stuff_options">
						<th><label><?php esc_html_e( 'Remove HTML, JavaScript and CSS comments', 'minify-stuff-markup' ); ?></label></th>
						<td>
							<input type="radio" name="minify_comments" value="yes"<?php echo ($minify_comments=='yes' ? ' checked' : ''); ?>><span class="value"><strong><?php esc_html_e( 'Yes', 'minify-stuff-markup' ); ?></strong></span>
							<input type="radio" name="minify_comments" value="no"<?php echo ($minify_comments!='yes' ? ' checked' : ''); ?>><span class="value"><?php esc_html_e( 'No', 'minify-stuff-markup' ); ?></span>
						</td>
						<td>
							<p class="description"><?php esc_html_e( 'Default "Yes"', 'minify-stuff-markup' ); ?></p>
						</td>
					</tr>
			<p class="submit">
				<input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'minify-stuff-markup' ) ?>" />
			</p>
		</form>
	</div>
	<?php

	}

}
?>