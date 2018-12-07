<?php

namespace Ejo\Cookie_Consent;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class that manages the cookie
 */
final class Cookie {

    /**
     * Cookie name
     *
     * @var    string
     */
    private static $name = 'EJOCookieConsent';

    /**
     * Consent duration (in days)
     *
     * @var    string
     */
    private static $duration = 365;

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

    }


    /*=============================================================*/
    /**                     Getters & Setters                      */
    /*=============================================================*/

    /**
     * Gets the cookie name
     *
     * @return string
     */
    public static function get_name() {
        return static::$name;
    }

    /**
     * Gets the cookie duration
     *
     * @return string
     */
    public static function get_duration() {
        return static::$duration;
    }

    /**
     * Get the consent cookie
     * 
     * @return boolean
     */
    public static function get() {
        if ( isset( $_COOKIE[static::get_name()] ) )
            return $_COOKIE[static::get_name()];
        else
            return false;
    }

    /**
     * Delete the consent cookie
     * 
     * @access private
     * @return void
     */
    private static function delete() {
        if ( static::consent_is_given() ) {

            // Unset
            unset( $_COOKIE[static::get_name()] );

            // Overwrite (?) (empty value and old timestamp?)
            setcookie( static::get_name(), '', time() - 3600, '/' ); 
        }
    }

    /**
     * Check whether cookie consent is given
     *
     * @access public
     * @return boolean
     */
    public static function consent_is_given() {
        if ( static::get() )
            return true;
        else
            return false;
    }


    /*=============================================================*/
    /**                           Debug                            */
    /*=============================================================*/
    
    /**
     * Debug plugin data
     *
     * @return array
     */
    public static function get_debug_data() {

        return [
            'get_name()'         => static::get_name(),
            'get()'              => static::get(),
            'consent_is_given()' => static::consent_is_given(),
        ];
    }
}
