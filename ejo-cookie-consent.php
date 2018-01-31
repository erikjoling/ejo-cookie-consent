<?php
/*
Plugin Name:  EJO Cookie Consent
Plugin URI:   https://github.com/erikjoling/ejo-cookie-consent
Description:  WordPress Cookie Consent (EU law) plugin
Author:       Erik Joling <erik@ejoweb.nl>
Version:      0.2
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
    public $cookie_name = 'EJOCookieConsent';

    /**
     * Debug mode
     *
     * @access public
     * @var    boolean
     */
    public $debug_mode = false;

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

        // Debugging
        if ($this->debug_mode) {
            // error_log( 'Get Cookie: ' . $this->get_cookie() );
            $this->delete_cookie();
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
     * Get the consent cookie
     * 
     * @access private
     * @return boolean
     */
    private function get_cookie() {
        if ( isset($_COOKIE[$this->cookie_name]) )
            return $_COOKIE[$this->cookie_name];
        else
            return false;
    }

    /**
     * Delete the consent cookie
     * 
     * @access private
     * @return void
     */
    private function delete_cookie() {
        if ($this->cookie_consent_is_given()) {
            unset($_COOKIE[$this->cookie_name]);
            setcookie($this->cookie_name, '', time() - 3600, '/'); // empty value and old timestamp
        }
    }

    /**
     * Check whether cookie consent is given
     *
     * @access public
     * @return boolean
     */
    public function cookie_consent_is_given() {
        if ($this->get_cookie())
            return true;
        else
            return false;
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
        wp_register_script( 'ejo-cookie-consent-plugin', $this->uri . "assets/js/plugin{$suffix}.js", array( 'jquery' ), $this->version, true );


        // Load script if the consent cookie isn't set
        if ( ! $this->cookie_consent_is_given() ) {

            // Localize the script
            wp_localize_script( 'ejo-cookie-consent-plugin', 'ejoccLocalization', array(
                'cookieName' => $this->cookie_name,
            ) );

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
        if ( ! $this->cookie_consent_is_given() && ! apply_filters('ejocc_custom_style', false) ) {
            wp_enqueue_style( 'ejo-cookie-consent-style' );
        }
    }

    /**
     * Get Privacy Policy URL
     *
     * @access public
     * @return string
     */
    public function get_policy_url() {
        return get_option( 'ejocc_policy_url', '' );
    }

    /**
     * Get Cookie Consent Text
     *
     * @access public
     * @return string
     */
    public function get_cookie_consent_text() {

        $cookie_consent_text = sprintf( '<p>' . __('This website uses cookies to enhance the browsing experience. By continuing you give us permission to deploy cookies as per our <a href="%s" rel="nofollow">privacy and cookies policy</a>.', 'ejocc') . '</p>', $this->get_policy_url());
        $cookie_consent_text = apply_filters( 'ejocc_cookie_consent_text', $cookie_consent_text );

        if ( apply_filters('ejocc_custom_cookie_consent_content', false) ) {
            $cookie_consent_text = apply_filters( 'the_content', get_option( 'ejocc_cookie_consent_text', $cookie_consent_text) );
        }

        return $cookie_consent_text;
    }

    /**
     * Get Cookie Consent Button Text
     *
     * @access public
     * @return string
     */
    public function get_cookie_consent_button_text() {

        $cookie_consent_button_text = __('I understand', 'ejocc');
        $cookie_consent_button_text = apply_filters( 'ejocc_cookie_consent_button_text', $cookie_consent_button_text );

        if ( apply_filters('ejocc_custom_cookie_consent_content', false) ) {
            $cookie_consent_button_text = get_option( 'ejocc_cookie_consent_button_text', $cookie_consent_button_text);
        }

        return $cookie_consent_button_text;
    }

    /**
     * Cookie Bar output
     *
     * @access public
     * @return void
     */
    public function cookie_consent_block() {

        // Don't enqueue if consent cookie is set
        if ( ! $this->cookie_consent_is_given() ) : ?>
            
            <div class="" id="cookie-consent-block"><?= $this->get_cookie_consent_text(); ?><button class="close-cookie-consent-block"><?= $this->get_cookie_consent_button_text(); ?></button></div>
            
        <?php endif;
    }

    /**
     * Admin Settings Fields
     */
    public function settings() {
        if ( apply_filters('ejocc_custom_cookie_consent_content', false) ) {
            $this->cookie_consent_text_setting();
            $this->cookie_consent_button_setting();
        }
        else {
            $this->policy_url_setting();
        }
    }

    /**
     * Settings - Cookie Consent Text
     */
    public function cookie_consent_text_setting() {
        $option_group = 'reading';
        $option_id  = 'ejocc_cookie_consent_text';

        // Register Setting - Privacy Policy URL
        register_setting( $option_group, $option_id, array( 'sanitize_callback' => 'wp_kses_post' ) );

        // Add Settings Field - Privacy Policy URL
        add_settings_field(
            $option_id, 
            __( 'Cookie Consent Text' , 'ejocc' ),
            function() {
                $option_id = 'ejocc_cookie_consent_text';
                $content     = $this->get_cookie_consent_text();

                wp_editor( $content, $option_id, array(
                    'media_buttons'   => false,
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
        $option_id    = 'ejocc_cookie_consent_button_text';

        // Register Setting - Privacy Policy URL
        register_setting( $option_group, $option_id, array( 'sanitize_callback' => 'sanitize_text_field' ) );

        // Add Settings Field - Privacy Policy URL
        add_settings_field(
            $option_id, 
            __( 'Cookie Consent Button' , 'ejocc' ),
            function() {
                $option_id   = 'ejocc_cookie_consent_button_text';
                $value       = $this->get_cookie_consent_button_text();
                ?>

                <input type="text" class="regular-text" id="<?= $option_id ?>" name="<?= $option_id ?>" value="<?= $value ?>" />
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
    public function policy_url_setting() {
        $option_group = 'reading';
        $option_id    = 'ejocc_policy_url';

        // Register Setting - Privacy Policy URL
        register_setting( $option_group, $option_id, array( 'sanitize_callback' => 'esc_attr' ) );

        // Add Settings Field - Privacy Policy URL
        add_settings_field(
            $option_id, 
            __( 'Privacy and Cookie Policy URL' , 'ejocc' ),
            function() {
                $option_id   = 'ejocc_policy_url';
                $value       = get_option( $option_id, '' );
                ?>

                <input type="text" class="regular-text code" id="<?= $option_id ?>" name="<?= $option_id ?>" value="<?= $value ?>" />
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