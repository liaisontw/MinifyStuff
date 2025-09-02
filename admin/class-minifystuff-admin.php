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
		
		$minify_stuff_active = get_option( 'minify_stuff_active' );
		$minify_javascript = get_option( 'minify_javascript' );
		$minify_comments = get_option( 'minify_comments' );
		if ( !$minify_stuff_active ) $minify_stuff_active = 'yes';
		if ( !$minify_javascript ) $minify_javascript = 'yes';
		if ( !$minify_comments ) $minify_comments = 'yes';
		
		if ( current_user_can( 'manage_options' ) ) 
		{
			if (   isset($_POST[ 'minify_stuff_submit_hidden' ]) 
				&& $_POST[ 'minify_stuff_submit_hidden' ] == 'Y' ) 
			{ 
				if (   isset( $_POST['minify_stuff_nonce'] )
				    && wp_verify_nonce( 
					   sanitize_text_field( wp_unslash( $_POST['minify_stuff_nonce'] ) ),
					   'minify-stuff-nonce' ) 
				   ) 
				{
					if ( isset( $_POST[ 'minify_stuff_active' ] ) ) {
						$minify_stuff_active = filter_var ( 
							wp_unslash( $_POST[ 'minify_stuff_active' ] ), 
							FILTER_SANITIZE_FULL_SPECIAL_CHARS 
						); 
					} else {
						$minify_stuff_active = 'yes';
					}
					if ( isset( $_POST[ 'minify_javascript' ] ) ) {
						$minify_javascript = filter_var ( 
							wp_unslash( $_POST[ 'minify_javascript' ] ), 
							FILTER_SANITIZE_FULL_SPECIAL_CHARS 
						); 
					} else {
						$minify_javascript = 'yes';
					}
					if ( isset( $_POST[ 'minify_comments' ] ) ) {
						$minify_comments = filter_var ( 
							wp_unslash( $_POST[ 'minify_comments' ] ), 
							FILTER_SANITIZE_FULL_SPECIAL_CHARS ); 
					} else {
						$minify_comments = 'yes';
					}
					update_option( 'minify_stuff_active', $minify_stuff_active );
					update_option( 'minify_javascript', $minify_javascript );
					update_option( 'minify_comments', $minify_comments );
					echo '<div class="updated"><p><strong>' . esc_html( 'Settings saved.' ) . '</strong></p></div>';
				} else { 
					wp_die( esc_html( 'Form failed nonce verification.' ) );   
				}				 
			}
			
		}
		


	?>
	
	<div class="wrap" id="minify_stuff">
		<h2>Minify HTML Settings</h2>
		<p>
				Notice: 
				This plugin may breaks server based caching such as nginx and Varnish.
				If your WordPress hosts apply server based caching, 
				please do not apply this plugin or the website visitors will 
				have a non-cached view of the website
		</p>
		<form name="form1" method="post" action="">
			<input type="hidden" name="minify_stuff_nonce" value="<?php echo esc_html( wp_create_nonce('minify-stuff-nonce') ); ?>">
			<input type="hidden" name="minify_stuff_submit_hidden" value="Y">
			<table border="1" class="form-table" >
				<tbody>
					<tr>
						<td>
							<p class="description"><?php esc_html_e( 'Option', 'minify-stuff' ); ?></p>
						</td>
						<td>
							<p class="description"><?php esc_html_e( 'Setting', 'minify-stuff' ); ?></p>
						</td>
						<td>
							<p class="description"><?php esc_html_e( 'Description', 'minify-stuff' ); ?></p>
						</td>
					</tr>
					<tr class="minify_stuff_active">
						<th><label><?php esc_html_e( "Minify Stuff (Remove comments of HTML, JavaScript and CSS)", 'minify-stuff' ); ?></label></th>
						<td>
							<input type="radio" name="minify_stuff_active" value="yes"<?php echo ($minify_stuff_active=='yes' ? ' checked' : ''); ?>><span class="value"><strong><?php esc_html_e( 'Enable', 'minify-stuff' ); ?></strong></span>
							<input type="radio" name="minify_stuff_active" value="no"<?php echo ($minify_stuff_active!='yes' ? ' checked' : ''); ?>><span class="value"><?php esc_html_e( 'Disable', 'minify-stuff' ); ?></span>
						</td>
						<td>
							<p class="description"><?php esc_html_e( 'Enable or disable Minify Stuff', 'minify-stuff' ); ?></p>
						</td>
					</tr>
					
			<p class="submit">
				<input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Changes', 'minify-stuff' ) ?>" />
			</p>
		</form>
	</div>
	<?php

	}

}
?>