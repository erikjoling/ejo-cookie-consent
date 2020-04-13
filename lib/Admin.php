<?php

namespace Ejo\Cookie_Consent;

return;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class that manages the cookie
 */
final class Admin {

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
            $instance::setup();
            $instance::load();
        }

        return $instance;
    }

    /**
     * Sets up.
     *
     * @return void
     */
    private static function setup() {

    }

    /**
     * Loads
     *
     * @return void
     */
    private static function load() {

        // Register Settings Fields
        // add_action( 'admin_init', [ static::class, 'manage_settings' ] );

        // Add
        add_action( 'admin_menu', [static::class, 'add_admin_page' ] );
    }

    /**
     * Add Cookie Consent Admin Page
     *
     * @return void
     */
    public static function add_admin_page() {

        // Allow to themes to disable database option
        if ( apply_filters( 'ejo_cookie_consent_custom_content', true ) ) {

            // Add admin page to options
            add_options_page(
                'Cookie Consent',
                'Cookie Consent',
                'manage_privacy_options',
                'ejo-cookie-consent',
                [ static::class, 'admin_page' ]
            );

        }
    }

    /**
     * Cookie Consent Admin Page
     *
     * @return void
     */
    public static function admin_page() {
        $action = isset( $_POST['action'] ) ? $_POST['action'] : '';

        // Print javascript cookie helpers
        \Ejo\Cookie_Consent\Admin::display_js_cookie_helpers();

        if ( ! empty( $action ) ) {
            check_admin_referer( $action );

            if ( 'ejo-cookie-consent-update-settings' === $action ) {

                // Get
                $cookie_consent_text        = $_POST['ejo_cookie_consent_text'];
                $cookie_consent_button_text = $_POST['ejo_cookie_consent_button_text'];

                // Sanitize
                $cookie_consent_text        = wp_kses_post($cookie_consent_text);
                $cookie_consent_button_text = sanitize_text_field($cookie_consent_button_text);

                // Save
                update_option( 'ejo_cookie_consent_text', $cookie_consent_text );
                update_option( 'ejo_cookie_consent_button_text', $cookie_consent_button_text );

                // Notify
                \Ejo\Cookie_Consent\Admin::admin_page_notification( __( 'Cookie Consent settings updated successfully.', 'ejo-cookie-consent' ) );
            }

            if ( 'ejo-cookie-consent-revoke-consent' === $action ) {

                ?>
                <script>
                    EjoCookieConsentEraseCookie( '<?= \Ejo\Cookie_Consent\Cookie::get_name() ?>' );
                </script>
                <?php

                // Notify
                \Ejo\Cookie_Consent\Admin::admin_page_notification( __( 'Cookie Consent revoked successfully.', 'ejo-cookie-consent' ) );
            }
        }
        ?>

        <div class="wrap">
            <h1><?= __( 'Privacy Settings' ) . ' | ' . __( 'Cookie Consent', 'ejo-cookie-consent' ) ?></h1>
            <p>
                <?php

                $privacy_settings_permalink = '<a href="' . get_admin_url() . 'options-privacy.php">' . __( 'Privacy Settings' ) . '</a>';
                printf(
                    __( 'You should link to your Privacy Page in the Cookie Consent Text. If you don\'t have a Privacy Policy Page you can set it at %s.', 'ejo-cookie-consent' ),
                    $privacy_settings_permalink
                );

                ?>
            </p>
            <form method="post" action="">
                <input type="hidden" name="action" value="ejo-cookie-consent-update-settings" />
                <table class="form-table ejo-cookie-consent-page">
                    <tr>
                        <th scope="row">
                            <?= __( 'Cookie Consent Text', 'ejo-cookie-consent' ); ?>
                        </th>
                        <td>
                            <div style="max-width:600px">
                                <?php

                                $option_id = 'ejo_cookie_consent_text';
                                $content   = \Ejo\Cookie_Consent\Core::get_text();

                                wp_editor( $content, $option_id, [
                                    'media_buttons'   => false,
                                    'teeny'           => true,
                                    'editor_height'   => 100,
                                ]);

                                ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?= __( 'Cookie Consent Button Text', 'ejo-cookie-consent' ); ?>
                        </th>
                        <td>
                            <div style="max-width:600px">
                                <?php

                                $option_id = 'ejo_cookie_consent_button_text';
                                $value     = \Ejo\Cookie_Consent\Core::get_button_text();
                                ?>

                                <input type="text" class="regular-text" id="<?= $option_id ?>" name="<?= $option_id ?>" value="<?= $value ?>" />

                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">

                        </th>
                        <td>
                            <?php
                            wp_nonce_field( 'ejo-cookie-consent-update-settings' );

                            submit_button( __( 'Save' ), 'primary', 'submit' );
                            ?>
                        </td>
                    </tr>
                </table>
            </form>

            <hr/>
            <h2><?= __( 'Personal Cookie Consent', 'ejo-cookie-consent' ); ?></h2>

            <table class="form-table ejo-cookie-consent-page">
                <tr>
                    <th scope="row">
                        <?= __( 'Cookie Consent?', 'ejo-cookie-consent' ); ?>
                    </th>
                    <td>
                        <div style="max-width:600px">

                            <script>
                                var EjoCookieConsentCookie = EjoCookieConsentGetCookie( '<?= \Ejo\Cookie_Consent\Cookie::get_name() ?>' );

                                if (EjoCookieConsentCookie) {
                                    document.write( '<p>Cookie Consent is given by you on this browser.<br/>Value: <i>' + EjoCookieConsentCookie + '</i></p>' );
                                }
                                else {
                                    document.write( '<p>No Cookie Consent is given by you on this browser.</p>' );
                                }
                            </script>

                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">

                    </th>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="action" value="ejo-cookie-consent-revoke-consent" />

                            <?php wp_nonce_field( 'ejo-cookie-consent-revoke-consent' ); ?>

                            <input type="submit" name="submit" id="submit" class="button" value="<?= __( 'Revoke your Cookie Consent', 'ejo-cookie-consent' ) ?>">
                        </form>
                    </td>
                </tr>
            </table>
        </div>

        <?php
    }

    /**
     * Javascript Cookie Helpers
     *
     * @return void
     */
    public static function display_js_cookie_helpers() {

        ?>
        <script>
            /**
             * Javascript Cookie helpers
             */

            // Get Cookie
            var EjoCookieConsentGetCookie = function( name ) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');
                for(var i=0;i < ca.length;i++) {
                    var c = ca[i];
                    while (c.charAt(0)==' ') c = c.substring(1,c.length);
                    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
                }
                return null;
            };

            // Erase Cookie
            var EjoCookieConsentEraseCookie = function( name ) {
                document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
            }
        </script>
        <?php
    }


    /**
     * Cookie Consent Admin Page
     *
     * @return void
     */
    public static function admin_page_notification( $message = null ) {

        $message = ( $message ) ? $message : __( 'Instellingen Opgeslagen' );

        ?>
        <div id="message" class="updated notice is-dismissible">
            <p><?= $message ?></p>
            <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?= __( 'Dit bericht verbergen.' ) ?></span></button>
        </div>
        <?php

    }


    /**
     * Manage settings
     *
     * Deprecated. We don't use the settings API
     *
     * @return void
     */
    public static function manage_settings() {

        if ( apply_filters( 'ejo_cookie_consent_custom_content', true ) ) {
            static::register_text_setting();
            static::register_button_setting();
        }
        else {
            static::register_privacy_policy_url_setting();
        }
    }

    /**
     * Settings - Cookie Consent Text
     *
     * Deprecated. We don't use the settings API
     *
     * @return void
     */
    public static function register_text_setting() {

        $option_group = 'reading';
        $option_id    = 'ejo_cookie_consent_text';

        // Register Setting - Privacy Policy URL
        register_setting( $option_group, $option_id, [ 'sanitize_callback' => 'wp_kses_post' ] );

        // Add Settings Field - Privacy Policy URL
        add_settings_field(
            $option_id,
            __( 'Cookie Consent Text' , 'ejo-cookie-consent' ),
            function() {

                $option_id = 'ejo_cookie_consent_text';
                $content   = \Ejo\Cookie_Consent\Core::get_text();

                echo '<div style="max-width:600px">';
                wp_editor( $content, $option_id, [
                    'media_buttons'   => false,
                    'teeny'           => true,
                    'editor_height'   => 100,
                ]);
                echo '</div>';

                // echo (!empty($desc))?'<br/><span class="description">'.$desc.'</span>':'';
            },
            $option_group,
            'default',
            [ 'label_for' => $option_id ]
        );
    }


    /**
     * Settings - Cookie Consent Button
     *
     * Deprecated. We don't use the settings API
     *
     * @return void
     */
    public static function register_button_setting() {

        $option_group = 'reading';
        $option_id    = 'ejo_cookie_consent_button_text';

        // Register Setting - Privacy Policy URL
        register_setting( $option_group, $option_id, array( 'sanitize_callback' => 'sanitize_text_field' ) );

        // Add Settings Field - Privacy Policy URL
        add_settings_field(
            $option_id,
            __( 'Cookie Consent Button' , 'ejo-cookie-consent' ),
            function() {

                $option_id = 'ejo_cookie_consent_button_text';
                $value     = \Ejo\Cookie_Consent\Core::get_button_text();
                ?>

                <input type="text" class="regular-text" id="<?= $option_id ?>" name="<?= $option_id ?>" value="<?= $value ?>" />
                <p class="description"><?php _e( 'Enter the text to show on the button.', 'ejo-cookie-consent' ); ?></p>

                <?php
            },
            $option_group,
            'default',
            array( 'label_for' => $option_id )
        );
    }

    /**
     * Settings - Privacy Policy URL
     *
     * Deprecated. We don't use the settings API
     *
     * @return void
     */
    public static function register_privacy_policy_url_setting() {
        $option_group = 'reading';
        $option_id    = 'ejo_cookie_consent_privacy_policy_url';

        // Register Setting - Privacy Policy URL
        register_setting( $option_group, $option_id, array( 'sanitize_callback' => 'esc_attr' ) );

        // Add Settings Field - Privacy Policy URL
        add_settings_field(
            $option_id,
            __( 'Privacy and Cookie Policy URL' , 'ejo-cookie-consent' ),
            function() {
                $option_id   = 'ejo_cookie_consent_privacy_policy_url';
                $value       = \Ejo\Cookie_Consent\Core::get_privacy_policy_url();
                ?>

                <input type="text" class="regular-text code" id="<?= $option_id ?>" name="<?= $option_id ?>" value="<?= $value ?>" />
                <p class="description"><?php _e( 'Enter a link to your privacy and cookie policy where you outline the use of cookies. This link will be used in the cookie consent banner.', 'ejo-cookie-consent' ); ?></p>

                <?php
            },
            $option_group,
            'default',
            array( 'label_for' => $option_id )
        );
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

        return [];
    }
}
