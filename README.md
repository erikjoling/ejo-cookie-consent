# Ejo Cookie Consent

## How it works
### Customize
To edit the text (and button text) go to WordPress > Settings > Cookie Consent. 

Here you can delete the cookie as well if you want. 

### Disable plugin stylesheet
`add_filter( 'ejo_cookie_consent_disable_style', '__return_true' )`

## Issues

### PHP Cookie vs Javascript Cookie?
Most of the plugin works in javascript for managing the cookie. Gotta remove the PHP cookie stuff.

### Debug mode should be an option in the settings
Right now it's hooked to SCRIPT_DEBUG constant. When developing with that turned on it is a bugger...