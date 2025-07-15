<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/liaisontw/
 * @since      1.0.0
 *
 * @package    minifyStuff
 * @subpackage minifyStuff/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    minifyStuff
 * @subpackage minifyStuff/public
 * @author     Liaison Chang <liaison.tw@gmail.com>
 */
class minifyStuff_Public {

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
	protected $action_start;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/minifyStuff-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/minifyStuff-public.js', array( 'jquery' ), $this->version, false );

	}

	public function init_minify_stuff() {	
		if ( !is_admin() && get_option( 'minify_stuff_active' ) != 'no' ) {
			ob_start( array($this, 'minify_stuff_output') );
		}
	}

	protected function minify_stuff_output($buffer) {
		$minify_begin_token = '@minify-begin';
		$minify_stop_token  = '@minify-stop';
		$line_feed 			= chr(10);
		$carriage_return	= chr(13);

		//chr(13) is a 'carriage return'
		if ( substr( ltrim( $buffer ), 0, 5) == '<?xml' ) {
			return ( $buffer );
		}
		$mod = '/u';
		//Chr(9)為Tab
		//Chr(13)為歸位字元(回車符號)
		$buffer = str_replace(array (chr(13) . $line_feed, chr(9)), array ($line_feed, ''), $buffer);
		$buffer = str_ireplace(
			array ( '<script', '/script>', '<pre', '/pre>', 
					'<textarea', '/textarea>', '<style', '/style>'), 
			array ( $minify_begin_token.'<script', '/script>'.$minify_stop_token, 
			        $minify_begin_token.'<pre', '/pre>'.$minify_stop_token, 
					$minify_begin_token.'<textarea', '/textarea>'.$minify_stop_token, 
					$minify_begin_token.'<style', '/style>'.$minify_stop_token), $buffer);
		$split_array = explode($minify_stop_token, $buffer);
		$buffer = ''; 

		foreach ($split_array as $split_element) {
			$ii = strpos($split_element, $minify_begin_token);
			if ( $ii !== false ) {
				$process = substr($split_element, 0, $ii);
				$asis = substr($split_element, $ii + strlen($minify_begin_token));
				if ( substr($asis, 0, 7) == '<script' ) {
					$asis = '';
					$split_element_array = explode($line_feed, $asis);
					
					foreach ($split_element_array as $split_element_element) {
						if ( $split_element_element ) {
							$asis .= trim($split_element_element) . $line_feed;
						}

						$last = substr(trim($split_element_element), -1);
						if ( strpos($split_element_element, '//') !== false && ($last == ';' || $last == '>' || $last == '{' || $last == '}' || $last == ',') ) {
							$asis .= $line_feed;
						}
					}

					if ( $asis ) {
						$asis = substr($asis, 0, -1);
					}
					$asis = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $asis, 100000);
					$asis = str_replace(array (';' . $line_feed, '>' . $line_feed, '{' . $line_feed, '}' . $line_feed, ',' . $line_feed), array(';', '>', '{', '}', ','), $asis);
					
				} else if ( substr($asis, 0, 6) == '<style' ) {
					$asis = preg_replace(array ('/\>[^\S ]+' . $mod, '/[^\S ]+\<' . $mod, '/(\s)+' . $mod), array('>', '<', '\\1'), $asis, 100000);				
					$asis = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $asis, 100000);
					$asis = str_replace(array ($line_feed, ' {', '{ ', ' }', '} ', '( ', ' )', ' :', ': ', ' ;', '; ', ' ,', ', ', ';}'), array('', '{', '{', '}', '}', '(', ')', ':', ':', ';', ';', ',', ',', '}'), $asis);
				}
			} else {
				$process = $split_element;
				$asis = '';
			}
			$process = preg_replace(array ('/\>[^\S ]+' . $mod, '/[^\S ]+\<' . $mod, '/(\s)+' . $mod, '/"\n>' . $mod, '/"\n' . $mod), array('>', '<', '\\1', '">', '" '), $process, 100000);
			$process = preg_replace('/(?=<!--)([\s\S]*?)-->' . $mod, '', $process, 100000);
			$buffer .= $process.$asis;
		}
		$buffer = str_replace(array ($line_feed . '<script', $line_feed . '<style', '*/' . $line_feed, $minify_begin_token), 
							  array ('<script', '<style', '*/', ''), $buffer);
		return ($buffer);
	}
}
