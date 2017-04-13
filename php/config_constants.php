<?php
// ==============================================================
//	Copyright (C) 2014 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================
ini_set('display_errors', 'On');
error_reporting(E_ALL);

if ( defined('INCLUSION_PERMITTED') === false ||
     ( defined('INCLUSION_PERMITTED') === true && INCLUSION_PERMITTED === false ) ) { 
	die( 'This file must not be invoked directly.' ); 
}

	if(defined('__RIPRUNNER_ROOT__') === false) {
	    define('__RIPRUNNER_ROOT__', dirname(__FILE__));
	}

// ==============================================================

	define( 'PRODUCT_NAME', 		'RipRunner' );
	define( 'PRODUCT_URL',  		'http://soft-haus.com' );
	define( 'DEBUG_MODE', 			false);
	define( 'ENABLE_AUDITING',		true);
	define( 'SECURE', 				false);    // FOR DEVELOPMENT ONLY!!!!
	define( 'ENABLE_ASYNCH_MODE', 	true);

	define('USER_PASSWORD_HASH_ALGORITHM', 	 'sha512');
	        
	define('USER_ACCESS_ADMIN', 	             0x1);
	define('USER_ACCESS_SIGNAL_SMS',             0x2);
	define('USER_ACCESS_CALLOUT_RESPOND_SELF',   0x4);
	define('USER_ACCESS_CALLOUT_RESPOND_OTHERS', 0x8);
	//define('USER_ACCESS_X', 0x4);
	//define('USER_ACCESS_X', 0x8);

	# Actual application version
	define('CURRENT_VERSION', '1.0.0');
		
	// ----------------------------------------------------------------------
	// SMS Provider Settings
	define( 'SMS_GATEWAY_TEXTBELT', 					'TEXTBELT');
	define( 'SMS_GATEWAY_TEXTBELT_LOCAL', 				'TEXTBELT-LOCAL');
	define( 'SMS_GATEWAY_SENDHUB', 						'SENDHUB');
	define( 'SMS_GATEWAY_EZTEXTING', 					'EZTEXTING');
	define( 'SMS_GATEWAY_TWILIO', 						'TWILIO');
	define( 'SMS_GATEWAY_PLIVO', 						'PLIVO');
	
	define( 'SMS_CALLOUT_PROVIDER_DEFAULT', 			'DEFAULT');

	// ----------------------------------------------------------------------
	// Mobile App Settings
	define( 'DEFAULT_GCM_SEND_URL',	'https://android.googleapis.com/gcm/send');

	// ----------------------------------------------------------------------
	// Website Settings
	define( 'DEFAULT_GOOGLE_MAPS_API_URL',	'http://maps.googleapis.com/maps/api/geocode/');
	
	// The current versions of the Rip Runner Android app
	define( 'CURRENT_ANDROID_VERSIONCODE',	9);
	define( 'CURRENT_ANDROID_VERSIONNAME',	'1.8');

	function handle_config_error($root_path, \Exception $e) {
	    echo '<!DOCTYPE html>'.PHP_EOL.
	    '<html>'.PHP_EOL.
	    '<head>'.PHP_EOL.
	    '<meta charset="UTF-8">'.PHP_EOL.
	    '<title>Installation Page for: '.PRODUCT_NAME.'</title>'.PHP_EOL.
	    '<link rel="stylesheet" href="'.$root_path.'styles/main.css" />'.PHP_EOL.
	    '</head>'.PHP_EOL.
	    '<body>'.PHP_EOL.
	    '<p style="font-size:40px; color: white">'.
	    PRODUCT_NAME.' v'.CURRENT_VERSION.' - '.PRODUCT_URL.'<br>'.
	    '<hr></p>'.PHP_EOL.
	    '<p style="font-size:35px; color: red">'.PHP_EOL.
	    'Error detected, message : ' . $e->getMessage().', '.'Code : ' . $e->getCode().
	    '<br><span style="font-size:35px; color: yellow">Please create a config.php script.</span>'.PHP_EOL.
	    '<br><b>Step #1: <a target="_blank" href="'.$root_path.'config-builder.php">Click here</a> to generate a config.php file.
        <br>Step #2: <a target="_blank" href="'.$root_path.'install.php">Click here</a> once the config.php has been saved
        <br>in the riprunner php folder on the server.</b>'.PHP_EOL.
        '</p><hr>'.PHP_EOL.
        '</body>'.PHP_EOL.
        '</html>';
	}
	