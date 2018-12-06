"use strict";

// EJO Cookie Consent
// Inspired by the Ilmenite Cookie Banner of Bernskiold Media

var ejoCookieConsent = (function () {

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
     *       added to the site when the cookie is present. 
     *       Remark: script is added when SCRIPT_DEBUG is set in wp-config.
     */

    // Variables
    
    // Debug. It does set the cookie, but the banner is always visible
    priv.debugMode            = false; 
    priv.debugMode            = ejoCookieConsentPlugin.debugMode;

    // Duration in Days: The number of days before the cookie should expire.
    priv.consentDuration      = ejoCookieConsentPlugin.consentDuration; 

    // Self-explanatory
    priv.cookieName           = ejoCookieConsentPlugin.cookieName; 
    priv.consentText          = ejoCookieConsentPlugin.consentText;
    priv.consentButtonText    = ejoCookieConsentPlugin.consentButtonText;
    priv.root                 = document.querySelector('body');
    priv.cookieConsentBlock   = undefined;

    // Get Cookie helper
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

    // Set the cookie
    priv.setCookie = function() {
        var dateToday, value, expiration;        

        // Set the consent duration into a cookie date string
        dateToday = new Date();
        
        // Create date "dd-mm-yyyy"
        var dd = dateToday.getDate();
        var mm = dateToday.getMonth()+1; //January is 0!
        var yyyy = dateToday.getFullYear();

        // Leading zeroes
        if(dd<10) { dd = '0'+dd } 
        if(mm<10) { mm = '0'+mm }

        // Set curent date as value
        value = dd + "-" + mm + "-" + yyyy;

        // Set expiration
        dateToday.setTime(dateToday.getTime()+(priv.consentDuration*24*60*60*1000));
        expiration = dateToday.toUTCString();

        // Set the actual cookie
        document.cookie = priv.cookieName + '=' + value + '; expires=' + expiration + '; path=/';
    };

    // Check if cookie consent
    priv.cookieConsentIsGiven = function(argument) {

        return ( priv.getCookie(priv.cookieName) );
    };

    // Show 
    priv.displayCookieConsentBlock = function() {
        
        priv.cookieConsentBlock = document.createElement( 'div' );
        priv.cookieConsentBlock.classList.add('ejo-cookie-consent');

        var cookieConsentBlockInner = document.createElement( 'div' );
        cookieConsentBlockInner.classList.add('ejo-cookie-consent__inner');

        var cookieConsentBlockText = document.createElement( 'div' );
        cookieConsentBlockText.classList.add('ejo-cookie-consent__text');
        cookieConsentBlockText.innerHTML = priv.consentText;

        var cookieConsentBlockButton = document.createElement( 'button' );
        cookieConsentBlockButton.classList.add('ejo-cookie-consent__button');
        cookieConsentBlockButton.innerHTML = priv.consentButtonText;

        // Add to DOM
        cookieConsentBlockInner.appendChild(cookieConsentBlockText);
        cookieConsentBlockInner.appendChild(cookieConsentBlockButton);
        priv.cookieConsentBlock.appendChild(cookieConsentBlockInner);
        priv.root.appendChild(priv.cookieConsentBlock);

        // Let body know it has a cookie banner
        priv.root.classList.add('has-cookie-banner');
        
    };

    priv.hideCookieConsentBlock = function(){

        // Let body know it doens't have a cookie banner anymore
        priv.root.classList.remove('has-cookie-banner');

        // Remove
        // priv.root.removeChild( priv.root.querySelector( '.ejo-cookie-consent' ) );

        // Move the block out of sight
        var height = priv.cookieConsentBlock.offsetHeight + 'px';
        priv.cookieConsentBlock.style.marginBottom = '-' + height;

        // Prevent shadow from being shown
        priv.cookieConsentBlock.style.boxShadow = 'none';

        // Set the cookie
        priv.setCookie();
    };

    priv.init = function() {

        // Do stuff if no consent for cookie yet
        if ( ! priv.cookieConsentIsGiven() || priv.debugMode ) {
            
            // Show consent block
            priv.displayCookieConsentBlock();

            // Listen to button
            priv.cookieConsentBlock.querySelector( '.ejo-cookie-consent__button' ).addEventListener( 'click', priv.hideCookieConsentBlock );
        }
    };

    /**
     * Share public properties and methods with the global namespace
     */
    return {
        'init': priv.init
    };

})();

document.addEventListener("DOMContentLoaded", function() {
    ejoCookieConsent.init();
});