<?php
/**
 * Plugin Name:  EJO Cookie Consent
 * Plugin URI:   https://github.com/erikjoling/ejo-cookie-consent
 * Description:  Inform your visitors about Cookies & Privacy
 * Version:      1.1
 * Author:       Erik Joling <erik@ejoweb.nl>
 * Author URI:   https://www.ejoweb.nl
 * Text Domain:  ejo/cookie-consent
 * Domain Path:  /resources/languages
 * Requires PHP: 7
 * License:      GPLv3
 *
 * GitHub Plugin URI:  https://github.com/erikjoling/ejo-cookie-consent
 * GitHub Branch:      master
 */

namespace Ejo\Cookie_Consent;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main class that bootstraps the plugin.
 */
final class Plugin {

    /**
     * Version
     *
     * @var string
     */
    private static $version;

    /**
     * File
     *
     * @var string
     */
    private static $file;

    /**
     * Directory path with trailing slash.
     *
     * @var string
     */
    private static $dir;

    /**
     * Directory URI with trailing slash.
     *
     * @var string
     */
    private static $uri;

    /**
     * Plugin identifier
     *
     * @var string
     */
    private static $id;

    /**
     * Constructor method.
     *
     * @access private
     * @return void
     */
    private function __construct() {}

    /**
     * Returns the instance.
     *
     * @access public
     * @return object
     */
    public static function get_instance() {

        static $instance = null;

        if ( is_null( $instance ) ) {
            $instance = new self;
            $instance::setup();
            $instance::load();
        }

        return $instance;
    }

    /**
     * Sets up.
     *
     * @access private
     * @return void
     */
    private static function setup() {

        // Setting file has priority because other setters are dependant on it
        static::set_file();
        static::set_dir();
        static::set_uri();
        static::set_version();
        static::set_id();

        // Inform WordPress of custom language directory
        load_plugin_textdomain( 'ejo/cookie-consent', false, __DIR__ . '/assets/languages' );

        // Debug
        // log(static::debug_data());
    }

    /**
     * Loads
     *
     * @access private
     * @return void
     */
    private static function load() {

        // Load Class
        require_once( static::get_dir() . 'lib/Cookie.php' );
        require_once( static::get_dir() . 'lib/Core.php' );
        require_once( static::get_dir() . 'lib/Admin.php' );

        // Start Core
        \Ejo\Cookie_Consent\Core::get_instance();

        // Start Admin
        \Ejo\Cookie_Consent\Admin::get_instance();


        // Give off the loaded hook for this plugin
        do_action( static::get_id() . '_loaded' );
    }

    /*=============================================================*/
    /**                     Getters & Setters                      */
    /*=============================================================*/

    /**
     * Sets the plugin file
     *
     * @return void
     */
    private static function set_file() {
        static::$file = __FILE__;
    }

    /**
     * Gets the plugin file
     *
     * @return string
     */
    public static function get_file() {
        return static::$file;
    }

    /**
     * Sets the plugin directory
     *
     * @return void
     */
    private static function set_dir() {
        static::$dir = plugin_dir_path( static::get_file() );
    }

    /**
     * Gets the plugin directory path
     *
     * @return string
     */
    public static function get_dir() {
        return static::$dir;
    }

    /**
     * Sets the plugin URI
     *
     * @return void
     */
    private static function set_uri() {
        static::$uri = plugin_dir_url( static::get_file() );
    }

    /**
     * Gets the plugin uri path
     *
     * @return string
     */
    public static function get_uri() {
        return static::$uri;
    }

    /**
     * Sets the plugin ID
     *
     * @return void
     */
    private static function set_id() {
        static::$id = basename(__DIR__);
    }

    /**
     * Gets the plugin id
     *
     * @return string
     */
    public static function get_id() {
        return static::$id;
    }

    /**
     * Sets the plugin version
     *
     * @return void
     */
    private static function set_version() {

        // Note: Can't use `get_file_data()` because it doesn't work on the frontend
        $plugin_data = get_file_data( static::get_file(), array('Version' => 'Version') );

        // Set the version property
        static::$version = $plugin_data['Version'];
    }

    /**
     * Gets the plugin version
     *
     * @return string
     */
    public static function get_version() {
        return static::$version;
    }


    /*=============================================================*/
    /**                    Plugin de/activation                    */
    /*=============================================================*/

    public static function on_activation() {}
    public static function on_deactivation() {}


    /*=============================================================*/
    /**                           Debug                            */
    /*=============================================================*/

    /**
     * Debug plugin data
     *
     * @return array
     */
    public static function debug_data() {

        return [
            'file'    => static::get_file(),
            'dir'     => static::get_dir(),
            'uri'     => static::get_uri(),
            'id'      => static::get_id(),
            'version' => static::get_version()
        ];
    }
}

/**
 * Load the plugin, when WP is loaded
 */
add_action( 'plugins_loaded', [ '\Ejo\Cookie_Consent\Plugin', 'get_instance' ] );

/**
 * Registration & deactivation:
 */
register_activation_hook( __FILE__, [ '\Ejo\Cookie_Consent\Plugin', 'on_activation' ] );
register_deactivation_hook( __FILE__, [ '\Ejo\Cookie_Consent\Plugin', 'on_deactivation' ] );
