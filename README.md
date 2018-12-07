# Ejo Cookie Consent

## How it works
### Customize
To edit the text (and button text) go to WordPress > Settings > Cookie Consent. 

Here you can delete the cookie as well if you want. 

### Disable plugin stylesheet
`add_filter( 'ejo_cookie_consent_custom_style', '__return_true' )`

## Issues

### PHP Cookie vs Javascript Cookie?
Most of the plugin works in javascript for managing the cookie.

But I use PHP Cookie code to detect if stylesheet needs to be added (based on given consent). 

Does this lead to problems?

### Debug mode should be an option in the settings
Right now it's hooked to SCRIPT_DEBUG constant. When developing with that turned on it is a bugger...