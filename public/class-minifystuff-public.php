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
class minifyStuff_Public
{

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

    public    $_linefeed     = "\n";
    public    $_c_return    = "\r"; 
    public  $_tab_key    = "\t";
    public    $begin         = '@minify-begin@';
    public    $stop          = '@minify-stop@';
    public  $utf8_mod;
    public  $max_replace = -1;



    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

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

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/minifyStuff-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

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

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/minifyStuff-public.js', array( 'jquery' ), $this->version, false);

    }

    public function init_minify_stuff()
    {    
        if ( !is_admin() && get_option('minify_stuff_active') != 'no' ) {
            ob_start(array($this, 'minify_stuff_output'));
        }
    }

    private function str_begin($str, $strbegin)
    {
        $strbegin_len = strlen($strbegin);
        return ($strbegin == substr($str, 0, $strbegin_len));
    }

    public function css_handle($str)
    {
        $linefeed = $this->_linefeed;
        $enc_mod = $this->utf8_mod;

        $str = preg_replace(
            array ('/\>[^\S ]+' . $enc_mod, '/[^\S ]+\<' . $enc_mod,
                   '/(\s)+' . $enc_mod), 
            array('>', '<', '\\1'), 
            $str, $this->max_replace
        );        
            
        //minify html comment
        $str = preg_replace(
            '!/\*[^*]*\*+([^/][^*]*\*+)*/!', 
            '', 
            $str, $this->max_replace
        );

        $str = str_replace(
            array ($linefeed, 
                   ' {','{ ',' }','} ','( ',' )', 
                   ' :',': ',' ;','; ',' ,',', ', ';}'), 
            array (''       , 
                   '{' , '{', '}', '}', '(', ')',
                   ':' , ':', ';', ';', ',', ',', '}' ), 
            $str
        );
        return $str;
    }

    public function js_handle($str)
    {
        $linefeed = $this->_linefeed;
        $str_array = explode($linefeed, $str);
        $str = '';
        
        foreach ($str_array as $str_element) {
            if ( $str_element ) {
                $str .= trim($str_element) . $linefeed;
            }
            $end_of_str = substr(trim($str_element), -1);
            //remove js comment
            if (   ( strpos($str_element, '//') !== false) 
                && (   $end_of_str == ';' || $end_of_str == '>' 
                    || $end_of_str == '{' || $end_of_str == '}' 
                    || $end_of_str == ',') 
            ) {
                $str .= $linefeed;
            }
        }

        if ( $str ) {
            $str = substr($str, 0, -1);
            //remove html comment
            $str = preg_replace(
                '!/\*[^*]*\*+([^/][^*]*\*+)*/!', 
                '', $str, $this->max_replace
            );
            //minify js
            $str = str_replace(
                array (';' . $linefeed, '>' . $linefeed, 
                       '{' . $linefeed, '}' . $linefeed, 
                       ',' . $linefeed), 
                array (';'            , '>', 
                    '{'               , '}', 
                    ','), 
                $str
            );
        }
        
        return $str;
    }

    public function minify_stuff_output($buffer)
    {
        $linefeed = $this->_linefeed;
        $c_return = $this->_c_return;
        $tab_key  = $this->_tab_key;
        $l_begin  = $this->begin;
        $l_stop   = $this->stop;

        //step 1: skip xml files
        if ( $this->str_begin(ltrim($buffer), '<?xml') ) {
            return ( $buffer );
        }

// Explicitly creating a new session.
// Note that severity is optional and defaults to 0.
do_action( 'wp_logger_create_session', 'minify-stuff', 'message', 'new session', 0 );

	do_action( 'wp_logger_add', 'minify-stuff', 'message', 'session test 1', 1 );
	do_action( 'wp_logger_add', 'minify-stuff', 'message', 'session test 2', 2 );

// Implicitly ends current session by creating a new session.
do_action( 'wp_logger_create_session', 'minify-stuff', 'message', 'new session', 0 );

	do_action( 'wp_logger_add', 'minify-stuff', 'message', 'session test 3', 3 );
	do_action( 'wp_logger_add', 'minify-stuff', 'message', 'session test 4', 4 );

// Explicitly end the current session.
do_action( 'wp_logger_end_session' );


        do_action( 'wp_logger_add', 'minify-stuff', 'message', 'step 1', 1 );
        do_action( 'wp_logger_add', 'wp-logger-test', 'message', 'Hello World! 1', 1 );
        do_action( 'wp_logger_add', 'wp-logger-test', 'message', 'Hello World! 2', 2 );
        do_action( 'wp_logger_add', 'wp-logger-test', 'message', 'Hello World! 3', 3 );
        do_action( 'wp_logger_add', 'wp-logger-test', 'message', 'Hello World! 4', 4 );
        do_action( 'wp_logger_add', 'wp-logger-test', 'message', 'Hello World! 5', 5 );
        //do_action( 'wp_logger_add', 'wp-logger-test', 'message', 'Hello World!', 6 );

        //step 2: utf-8 encode detection
        $enc_mod = ( mb_detect_encoding($buffer, 'UTF-8', true) ) ? '/u' : '/s';
        $this->utf8_mod = $enc_mod;
        do_action( 'wp_logger_add', 'minify-stuff', 'message', 'step 2', 2 );

        //step 3: compress \r\n\t to \n
        $buffer = str_replace(
            array ( $c_return . $linefeed, $tab_key), 
            array (             $linefeed,       ''), 
            $buffer
        );
        do_action( 'wp_logger_add', 'minify-stuff', 'message', 'step 3', 3 );

        //step 4-1: add begin-stop token for js, css
        $buffer = str_ireplace(
            array (          '<script' , '/script>', 
                             '<style'  ,  '/style>'), 
            array ( $l_begin.'<script' , '/script>'.$l_stop, 
                    $l_begin.'<style'  ,  '/style>'.$l_stop), 
            $buffer
        );

        do_action( 'wp_logger_add', 'minify-stuff', 'message', 'step 4', 4 );

        //step 4-2: split entire buffer into array by stop token
        $split_array = explode($l_stop, $buffer);
        $buffer = '';
        foreach ($split_array as $split_element) {
            $begin_pos = strpos($split_element, $l_begin);
            if ( $begin_pos === false ) {
                $pre_begin = $split_element;
                $post_begin = '';
            }else{
                $pre_begin = substr($split_element, 0, $begin_pos);
                $post_begin = substr($split_element, $begin_pos + strlen($l_begin));
                if ( $this->str_begin($post_begin, '<script') ) {
                    $post_begin = $this->js_handle($post_begin);

                } else if ( $this->str_begin($post_begin, '<style') ) {
                    $post_begin = $this->css_handle($post_begin);
                }
            } 
            $pre_begin = preg_replace(
                array ('/\>[^\S ]+' . $enc_mod, 
                       '/[^\S ]+\<' . $enc_mod, 
                       '/(\s)+'     . $enc_mod, 
                       '/"\n>'      . $enc_mod, 
                       '/"\n'       . $enc_mod), 
                array ('>', '<', '\\1', '">', '" '), 
                $pre_begin, $this->max_replace
            );
            $pre_begin = preg_replace(
                '/(?=<!--)([\s\S]*?)-->' . $enc_mod, 
                '', $pre_begin, $this->max_replace
            );
            $buffer .= $pre_begin.$post_begin;
        }
        $buffer = str_replace(
            array ($linefeed . '<script', $linefeed . '<style', 
                        '*/' . $linefeed, $l_begin), 
            array ('<script'            ,  '<style', 
                         '*/'           , ''), 
            $buffer
        );

        return ( $buffer );
    }
}
