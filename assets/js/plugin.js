"use strict";

// EJO Cookie Consent
// Inspired by the Ilmenite Cookie Banner of Bernskiold Media

var ejoCookieConsent = (function ($) {

    /**
     * Module pattern
     *
     * Keep stuff out of the global namespace
     * By using private and public object there is no need to 
     * map properties and methods. 
     */
    var priv = {}; // Store private properties and methods
    var publ = {}; // Store public properties and methods

    /**
     * Note: erasing cookie with javascript has no use because this script is not
     *       added to the site when the cookie is present
     */

    // Variables
    priv.debugMode            = true;                     // Debug Mode: true will disable the cookie, allowing you to debug the banner.
    priv.consentDuration      = 30;                       // Duration in Days: The number of days before the cookie should expire.
    priv.containerID          = 'cookie-consent-block';   // The ID of the notice container div
    priv.containerButtonClass = 'close-cookie-consent-block';
    priv.cookieName           = 'EUCookieConsent';        // The name of the cookie
    priv.cookieActiveValue    = '1';                      // The active value of the cookie.

    priv.setComplianceCookie = function() {

        // If no debug mode, set the cookie
        if ( ! priv.debugMode ) {

            // Set the consent duration into a cookie date string
            var date = new Date();
            date.setTime(date.getTime()+(priv.consentDuration*24*60*60*1000));

            // Set the actual cookie
            document.cookie = priv.cookieName + '=' + priv.cookieActiveValue + '; expires=' + date.toGMTString() + '; path=/';
        }
    };

    priv.createSpaceForCookieConsentBlock = function() {
        // Get the height of the consent block
        var consentBlockHeight = $('#' + priv.containerID).innerHeight();

        // Add class to body
        $('body').addClass('has-cookie-banner');
        $('body').css('padding-top', consentBlockHeight + 'px');
    };

    priv.getCookie = function(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    };

    priv.hideCookieConsentBlock = function(){

        // Hide the cookie banner
        $( '#' + priv.containerID).slideToggle(function() {
            // Remove cookie banner class
            $('body').removeClass('has-cookie-banner');
            $('body').css('padding-top', '0px');

        });

        // Set the cookie
        priv.setComplianceCookie();
    };

    publ.init = function() {

        if(priv.getCookie(priv.cookieName) != priv.cookieActiveValue ){
            priv.createSpaceForCookieConsentBlock();
        }

        $( '.' + priv.containerButtonClass).click(priv.hideCookieConsentBlock);
    };

    /**
     * Share public properties and methods with the global namespace
     */
    return publ;

})(jQuery);


jQuery(document).ready(function($) {
    ejoCookieConsent.init();
});