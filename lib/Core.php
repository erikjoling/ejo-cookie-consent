<?php

namespace Ejo\Cookie_Consent;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class that manages the cookie
 */
final class Core {

    /**
     * Constructor method.
     *
     * @return void
     */
    private function __construct() {}

    /**
     * Returns the instance.
     *
     * @return object
     */
    public static function get_instance() {

        static $instance = null;

        if ( is_null( $instance ) ) {
            $instance = new self;
            $instance::load();
        }

        return $instance;
    }

    /**
     * Loads
     *
     * @return void
     */
    private static function load() {

        // Add scripts and styles
        add_action( 'wp_enqueue_scripts', [static::class, 'manage_scripts_and_styles'] );
    }

    /**
     * Scripts & Styles
     *
     * @return void
     */
    public static function manage_scripts_and_styles() {

        $suffix     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
        $debug_mode = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );

        // Register
        wp_enqueue_script(
            \Ejo\Cookie_Consent\Plugin::get_id() . "-script",
            \Ejo\Cookie_Consent\Plugin::get_uri() . "assets/js/plugin{$suffix}.js",
            [],
            \Ejo\Cookie_Consent\Plugin::get_version(),
            true
        );

        // Localize the script
        wp_localize_script( \Ejo\Cookie_Consent\Plugin::get_id() . "-script", 'ejoCookieConsentPlugin', [
            'debugMode'         => $debug_mode,
            'consentDuration'   => \Ejo\Cookie_Consent\Cookie::get_duration(),
            'cookieName'        => \Ejo\Cookie_Consent\Cookie::get_name(),
            'consentText'       => \Ejo\Cookie_Consent\Core::get_text(),
            'consentButtonText' => \Ejo\Cookie_Consent\Core::get_button_text()
        ] );

        // Don't enqueue if custom style
        if ( ! apply_filters( 'ejo_cookie_consent_disable_style', false ) ) {
            wp_enqueue_style(
                \Ejo\Cookie_Consent\Plugin::get_id() . '-style',
                \Ejo\Cookie_Consent\Plugin::get_uri() . "assets/css/plugin{$suffix}.css",
                [],
                \Ejo\Cookie_Consent\Plugin::get_version()
            );
        }
    }


    /*=============================================================*/
    /**                     Getters & Setters                      */
    /*=============================================================*/

    /**
     * Get Privacy Policy URL
     *
     * Deprecated. Just use WordPress Core `get_privacy_policy_url` and `get_the_privacy_policy_link`
     *
     * @return string or false
     */
    public static function get_privacy_policy_url() {

        return get_privacy_policy_url();
    }

    /**
     * Get Cookie Consent Text
     *
     * @return string
     */
    public static function get_text() {

        // Allow to themes to disable database option
        if ( apply_filters( 'ejo_cookie_consent_custom_content', true ) ) {

            // Load consent text from database
            $text = get_option( 'ejo_cookie_consent_text', false);
        }

        // Setup hardcoded text if nothing is loaded from database
        if ( empty( $text ) ) {

            // Get link to privacy policy
            $privacy_policy_link = get_the_privacy_policy_link();

            if ( $privacy_policy_link ) {
                $text = sprintf (
                    __('This website uses cookies to enhance the browsing experience. Check our %s to learn more about this.', 'ejo-cookie-consent'),
                    $privacy_policy_link
                );
            }
            else {
                $text = __('This website uses cookies to enhance the browsing experience.', 'ejo-cookie-consent');
            }

            // Allow text to be filtered
            $text = apply_filters( 'ejo_cookie_consent_text', $text );
        }

        return apply_filters( 'the_content', $text );
    }

    /**
     * Get Cookie Consent Button Text
     *
     * @return string
     */
    public static function get_button_text() {

        // Allow to themes to disable database option
        if ( apply_filters( 'ejo_cookie_consent_custom_content', true ) ) {

            // Load button text from database
            $button_text = get_option( 'ejo_cookie_consent_button_text', false );
        }

        // Setup hardcoded button text if nothing is loaded from database
        if ( empty( $button_text ) ) {

            $button_text = __('I understand', 'ejo-cookie-consent');
            $button_text = apply_filters( 'ejo_cookie_consent_button_text', $button_text );
        }

        return $button_text;
    }


    /*=============================================================*/
    /**                          Other                             */
    /*=============================================================*/

    /**
     * Cookie Bar output
     *
     * Deprecated. We use javascript to generate the cookie-consent
     *
     * @return void
     */
    public static function display() {

        // Don't enqueue if consent cookie is set
        if ( ! \Ejo\Cookie_Consent\Cookie::consent_is_given() ) : ?>

            <div class="ejo-cookie-consent">
                <div class="ejo-cookie-consent__inner">
                    <?= static::get_text(); ?><button class="ejo-cookie-consent__button"><?= static::get_button_text(); ?></button>
                </div>
            </div>

        <?php endif;
    }


    /*=============================================================*/
    /**                           Debug                            */
    /*=============================================================*/

    /**
     * Debug plugin data
     *
     * @return array
     */
    private static function debug_data() {

        return [

        ];
    }
}
