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

	/**
	 * The variables of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $begin, $stop, $linefeed("\n"), $c_return("\r")
	 */

	private	$linefeed 	= '\n';
	private	$c_return	= '\r'; 
	private $tab_key	= '\t';
	private	$begin 		= '@minify-begin';
	private	$stop  		= '@minify-stop';
	


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

	private function string_begin_with($str, $strbegin) {
		//$strbegin_len = strlen($strbegin)+1;
		return ($strbegin == substr($str, 0));
	}

	private function css_handle($str) {
		$str = preg_replace(
			array ('/\>[^\S ]+' . $mod, '/[^\S ]+\<' . $mod, '/(\s)+' . $mod), 
			array('>', '<', '\\1'), 
			$str, 100000);		
			
		//minify html comment
		$str = preg_replace(
			'!/\*[^*]*\*+([^/][^*]*\*+)*/!', 
			'', 
			$str, 100000);
		$str = str_replace(array ($this->linefeed, ' {', '{ ', ' }', '} ', '( ', ' )', ' :', ': ', ' ;', '; ', ' ,', ', ', ';}'), array('', '{', '{', '}', '}', '(', ')', ':', ':', ';', ';', ',', ',', '}'), $str);
		return $str;
	}

	private function js_handle($str) {
		$split_element_array = explode($this->linefeed, $str);
		$str = '';
		foreach ($split_element_array as $split_element_element) {
			if ( $split_element_element ) {
				$str .= trim($split_element_element) . $this->linefeed;
			}

			$last = substr(trim($split_element_element), -1);

			//minify js
			if (   ( strpos($split_element_element, '//') !== false) 
				&& (   $last == ';' || $last == '>' 
					|| $last == '{' || $last == '}' || $last == ',') 
			) {
				$str .= $this->linefeed;
			}
		}

		if ( $str ) {
			//minify html comment
			$str = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $str, 100000);

			//minify js
			$str = str_replace(array (';' . $this->linefeed, '>' . $this->linefeed, 
									'{' . $this->linefeed, '}' . $this->linefeed, 
									',' . $this->linefeed), 
								array(';', '>', 
									'{', '}', 
									','), 
								$str);
		}
		return $str;
	}

	protected function minify_stuff_output($buffer) {

		if ( $this->string_begin_with(ltrim($buffer), '<?xml') ) {
			return ( $buffer );
		}
		
		$buffer = str_replace(
			array ( $this->c_return . $this->linefeed, $this->tab_key), 
		    array ( $this->linefeed, ''), 
			$buffer);
		$buffer = str_ireplace(
			array ( '<script', '/script>', '<pre', '/pre>', 
					'<textarea', '/textarea>', '<style', '/style>'), 
			array ( $this->begin.'<script', '/script>'.$this->stop, 
			        $this->begin.'<pre', '/pre>'.$this->stop, 
					$this->begin.'<textarea', '/textarea>'.$this->stop, 
					$this->begin.'<style', '/style>'.$this->stop), 
			$buffer);
		$split_array = explode($this->stop, $buffer);
		$buffer = ''; 

		$mod = '/u';
		foreach ($split_array as $split_element) {
			$begin_pos = strpos($split_element, $this->begin);

			if ( $begin_pos === false ) {
				$pre_begin = $split_element;
				$post_begin = '';
			} else {
				$pre_begin = substr($split_element, 0, $begin_pos);
				$post_begin = substr($split_element, $begin_pos + strlen($this->begin));

				if ( $this->string_begin_with($post_begin, '<style') ) {
					$post_begin = $this->css_handle($post_begin);
				} else if ( $this->string_begin_with($post_begin, '<script') ) {	
					$post_begin = $this->js_handle($post_begin);
				}
			} 
			$pre_begin = preg_replace(array ('/\>[^\S ]+' . $mod, '/[^\S ]+\<' . $mod, '/(\s)+' . $mod, '/"\n>' . $mod, '/"\n' . $mod), array('>', '<', '\\1', '">', '" '), $pre_begin, 100000);
			$pre_begin = preg_replace('/(?=<!--)([\s\S]*?)-->' . $mod, '', $pre_begin, 100000);
			$buffer .= $pre_begin.$post_begin;
		}
		$buffer = str_replace(array ($this->linefeed . '<script', $this->linefeed . '<style', 
		                             '*/' . $this->linefeed     , $this->begin), 
							  array ('<script'             ,  '<style', 
							         '*/'                  , ''), 
							  $buffer);
		return ($buffer);
	}
}
