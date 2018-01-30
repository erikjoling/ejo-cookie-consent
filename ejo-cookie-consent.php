<?php
/*
Plugin Name:  EJO Cookie Consent
Plugin URI:   https://github.com/erikjoling/ejo-cookie-consent
Description:  WordPress Cookie Consent (EU law) plugin
Author:       Erik Joling <erik@ejoweb.nl>
Version:      0.1
Author URI:   https://github.com/erikjoling/
Text Domain:  ejo-cc

GitHub Theme URI:  https://github.com/erikjoling/ejo-cookie-consent
GitHub Branch:     master
*/

final class EJO_Cookie_Consent {

    /**
     * Version
     *
     * @access public
     * @var    string
     */
    public $version = '';
    
    /**
     * Directory path with trailing slash.
     *
     * @access public
     * @var    string
     */
    public $dir = '';

    /**
     * Directory URI with trailing slash.
     *
     * @access public
     * @var    string
     */
    public $uri = '';

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
            $instance->setup();
            $instance->core();
            $instance->setup_actions();
        }

        return $instance;
    }

    /**
     * Constructor method.
     *
     * @access private
     * @return void
     */
    private function __construct() {}

    /**
     * Sets up.
     *
     * @access private
     * @return void
     */
    private function setup() {

        // Set the directory properties.
        $this->dir = plugin_dir_path( __FILE__ );
        $this->uri = plugin_dir_url( __FILE__ );

        // Set the version property
        $this->version = get_plugin_data( __FILE__ )['Version'];
    }

    /**
     * Loads the core files.
     *
     * @access private
     * @return void
     */
    private function core() {

    }

    /**
     * Adds the necessary setup actions for the theme.
     *
     * @access private
     * @return void
     */
    private function setup_actions() {
        add_action( 'wp_enqueue_scripts', array($this, 'scripts') );
        add_action( 'wp_enqueue_scripts', array($this, 'styles') );

        add_action( 'wp_footer', array($this, 'cookie_bar') );
    }

    /**
     * Hook scripts
     *
     * @access public
     * @return void
     */
    public function scripts() {

        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        // Register Scripts
        wp_register_script( 'ejo-cookie-consent-plugin', $this->uri . "/assets/js/plugin{$suffix}.js", array( 'jquery' ), $this->version, true );

        // Load script if the consent cookie isn't set
        if ( ! isset( $_COOKIE['EUCookieConsent'] ) ) {
            wp_enqueue_script( 'ejo-cookie-consent-plugin' );
        }
    }

    /**
     * Hook styles
     *
     * @access public
     * @return void
     */
    public function styles() {

        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        // Styles
        wp_register_style( 'ejo-cookie-consent-style', $this->uri ."assets/css/plugin{$suffix}.css", array(), $this->version );

        // Don't enqueue if consent cookie is set
        if ( ! isset( $_COOKIE['EUCookieConsent'] ) ) {
            wp_enqueue_style( 'ejo-cookie-consent-style' );
        }
    }

    /**
     * Cookie Bar output
     *
     * @access public
     * @return void
     */
    public function cookie_bar() {

        // Don't enqueue if consent cookie is set
        if ( ! isset( $_COOKIE['EUCookieConsent'] ) ) : ?>
            
            <div class="" id="cookie-consent-block">Om Henneken.nl optimaal te laten functioneren gebruiken wij cookies. Voor meer informatie zie ons Privacy Beleid. - <button class="close-cookie-consent-block">Akkoord &amp; Sluiten</button></div>
            
        <?php endif;
    }

}

/**
 * Gets the instance of the `EJO_Core` class.  This function is useful for quickly grabbing data
 * used throughout the framework.
 *
 * @access public
 * @return object
 */
function ejo_cookie_consent() {
    return EJO_Cookie_Consent::get_instance();
}

// Startup!
ejo_cookie_consent();