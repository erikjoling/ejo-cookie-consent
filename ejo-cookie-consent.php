<?php
/*
Plugin Name:  EJO Cookie Consent
Plugin URI:   https://github.com/erikjoling/ejo-cookie-consent
Description:  WordPress Cookie Consent (EU law) plugin
Author:       Erik Joling <erik@ejoweb.nl>
Version:      0.1
Author URI:   https://github.com/erikjoling/
Text Domain:  ejocc

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
     * Cookie name
     *
     * @access public
     * @var    string
     */
    public $cookie_name = 'EUCookieConsent';

    /**
     * Debug mode
     *
     * @access public
     * @var    boolean
     */
    public $debug_mode = true;

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

        if ($this->debug_mode) {
            $this->delete_cookie();
        }
    }

    private function delete_cookie() {
        if (isset($_COOKIE[$this->cookie_name])) {
            error_log(print_r($_COOKIE[$this->cookie_name]));
            
            unset($_COOKIE[$this->cookie_name]);
            setcookie($this->cookie_name, '', time() - 3600, '/'); // empty value and old timestamp
        }
    }

    /**
     * Adds the necessary setup actions for the theme.
     *
     * @access private
     * @return void
     */
    private function setup_actions() {

        // Add scripts and styles
        add_action( 'wp_enqueue_scripts', array($this, 'scripts') );
        add_action( 'wp_enqueue_scripts', array($this, 'styles') );

        // Add cookie consent block to HTML
        add_action( 'wp_footer', array($this, 'cookie_consent_block') );

        // Register Settings Fields
        add_filter( 'admin_init', array( $this, 'settings' ) );
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
        if ( ! isset( $_COOKIE[$this->cookie_name] ) ) {
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
        if ( ! isset( $_COOKIE[$this->cookie_name] ) ) {
            wp_enqueue_style( 'ejo-cookie-consent-style' );
        }
    }

    /**
     * Cookie Bar output
     *
     * @access public
     * @return void
     */
    public function cookie_consent_block() {

        // Don't enqueue if consent cookie is set
        if ( ! isset( $_COOKIE[$this->cookie_name] ) ) : ?>
            
            <div class="" id="cookie-consent-block">Om Henneken.nl optimaal te laten functioneren gebruiken wij cookies. Voor meer informatie zie ons Privacy Beleid. - <button class="close-cookie-consent-block">Akkoord &amp; Sluiten</button></div>
            
        <?php endif;
    }

    /**
     * Admin Settings Fields
     */
    public function settings() {
        $custom_cookie_consent_text   = apply_filters( 'custom_cookie_consent_text', false );
        $custom_cookie_consent_button = apply_filters( 'custom_cookie_consent_button', false );

        if ($custom_cookie_consent_text) 
            $this->cookie_consent_text_setting();
        else 
            $this->privacy_policy_url_setting();

        if ($custom_cookie_consent_button)
            $this->cookie_consent_button_setting();
    }

    /**
     * Settings - Cookie Consent Text
     */
    public function cookie_consent_text_setting() {
        $option_group = 'reading';
        $option_id    = 'ejocc-cookie-consent-text';
        $option_name  = 'ejocc_cookie_consent_text';

        // Register Setting - Privacy Policy URL
        register_setting( $option_group, $option_id, array( 'sanitize_callback' => 'wp_kses_post' ) );

        // Add Settings Field - Privacy Policy URL
        add_settings_field(
            $option_id, 
            __( 'Cookie Consent Text' , 'ejocc' ),
            function() {
                $option_id   = 'ejocc-cookie-consent-text';
                $option_name = 'ejocc_cookie_consent_text';
                $content     = get_option( 'ejocc_cookie_consent_text', '' );

                wp_editor( $content, $option_id, array(
                    'textarea_name'   => $option_name,
                    // 'editor_class' => 'regular-text',
                    'media_buttons'   => false,
                    'quicktags'       => false,
                    'teeny'           => true,
                    'editor_height'   => 100
                ));

                // echo (!empty($desc))?'<br/><span class="description">'.$desc.'</span>':'';
            }, 
            $option_group,
            'default',
            array( 'label_for' => $option_id )
        );
    }


    /**
     * Settings - Cookie Consent Button
     */
    public function cookie_consent_button_setting() {
        $option_group = 'reading';
        $option_id    = 'ejocc-cookie-consent-button';
        $option_name  = 'ejocc_cookie_consent_button';

        // Register Setting - Privacy Policy URL
        register_setting( $option_group, $option_id, array( 'sanitize_callback' => 'sanitize_text_field' ) );

        // Add Settings Field - Privacy Policy URL
        add_settings_field(
            $option_id, 
            __( 'Cookie Consent Button' , 'ejocc' ),
            function() {
                $option_id   = 'ejocc-cookie-consent-button';
                $option_name = 'ejocc_cookie_consent_button';
                $value       = get_option( 'ejocc_cookie_consent_button', '' );
                ?>

                <input type="text" class="regular-text" id="<?= $option_id ?>" name="<?= $option_name ?>" value="<?= $value ?>" />
                <p class="description"><?php _e( 'Enter the text to show on the button.', 'ejocc' ); ?></p>

                <?php
            }, 
            $option_group,
            'default',
            array( 'label_for' => $option_id )
        );
    }

    /**
     * Settings - Privacy Policy URL 
     */
    public function privacy_policy_url_setting() {
        $option_group = 'reading';
        $option_id    = 'ejocc-policy-url';
        $option_name  = 'ejocc_policy_url';

        // Register Setting - Privacy Policy URL
        register_setting( $option_group, $option_id, array( 'sanitize_callback' => 'esc_attr' ) );

        // Add Settings Field - Privacy Policy URL
        add_settings_field(
            $option_id, 
            __( 'Privacy and Cookie Policy URL' , 'ejocc' ),
            function() {
                $option_id   = 'ejocc-policy-url';
                $option_name = 'ejocc_policy_url';
                $value       = get_option( $option_name, '' );
                ?>

                <input type="url" class="regular-text code" id="<?= $option_id ?>" name="<?= $option_name ?>" value="<?= $value ?>" />
                <p class="description"><?php _e( 'Enter a link to your privacy and cookie policy where you outline the use of cookies. This link will be used in the cookie consent banner.', 'ejocc' ); ?></p>

                <?php
            }, 
            $option_group,
            'default',
            array( 'label_for' => $option_id )
        );
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