<?php
// ==============================================================
//	Copyright (C) 2016 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================
namespace riprunner;

if ( defined('INCLUSION_PERMITTED') === false ||
   (defined('INCLUSION_PERMITTED') === true && INCLUSION_PERMITTED === false ) ) {
	die( 'This file must not be invoked directly.' );
}

require_once 'plugin_interfaces.php';
require __RIPRUNNER_ROOT__ . '/vendor/autoload.php';

class SMSTextBeltLocalPlugin implements ISMSPlugin {

	public function getPluginType() {
		return 'TEXTBELT-LOCAL';
	}
	public function getMaxSMSTextLength() {
		return 0;
	}
	public function signalRecipients($SMSConfig, $recipient_list, $recipient_list_type, $smsText) {
		$resultSMS = 'START Send SMS using TextBelt-LOCAL.' . PHP_EOL;
		
		if($recipient_list_type === RecipientListType::GroupList) {
			throw new \Exception("TextBelt-LOCAL SMS Plugin does not support groups!");
		}
		else {
			$recipient_list_numbers = $recipient_list;
		}
				
		$resultSMS .= 'About to send SMS to: [' . implode(",", $recipient_list_numbers) . ']' . PHP_EOL;
		
	    $from = $SMSConfig->SMS_PROVIDER_TEXTBELT_LOCAL_FROM;
	    $from_region = $SMSConfig->SMS_PROVIDER_TEXTBELT_LOCAL_REGION;
        foreach($recipient_list_numbers as $recipient) {
	        $SMS_local = new \PHPSMS\PHPSMS($recipient, $smsText, $from, $from_region);
			//$resultSMS .= $SMS_local->getResult();
			$resultSMS .= 'After send.'. PHP_EOL;
		}
		return $resultSMS;
	}
}
