<?php
// ==============================================================
//	Copyright (C) 2014 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================
namespace riprunner;

if ( defined('INCLUSION_PERMITTED') === false ||
    (defined('INCLUSION_PERMITTED') === true && INCLUSION_PERMITTED === false ) ) {
	die( 'This file must not be invoked directly.' );
}

require_once 'plugin_interfaces.php';

class SMSPlivoPlugin implements ISMSPlugin {

	public function getPluginType() {
		return 'PLIVO';
	}
	public function getMaxSMSTextLength() {
		return 0;
	}
	public function signalRecipients($SMSConfig, $recipient_list, $recipient_list_type, $smsText) {

		$resultSMS = 'START Send SMS using Plivo.' . PHP_EOL;

		if($recipient_list_type === RecipientListType::GroupList) {
			throw new \Exception("Plivo SMS Plugin does not support groups!");
		}
		else {
			// Remove empty and null entries
			$recipient_list_numbers = array_filter($recipient_list, 'strlen' );
		}
	
		$resultSMS .= 'About to send SMS to: [' . implode(",", $recipient_list_numbers) . ']' . PHP_EOL;
	
		$url = $SMSConfig->SMS_PROVIDER_PLIVO_BASE_URL.'Account/'.$SMSConfig->SMS_PROVIDER_PLIVO_AUTH_ID.'/Message/';
	
		$dst_sms = '';
		foreach ($recipient_list_numbers as $recipient) {
		    if(trim($recipient) == '') {
		        continue;
		    }
		    if($dst_sms !== '') {
		        $dst_sms .= '<';
		    }
		    $dst_sms .= '1' . $recipient;
		}
		
		//foreach($recipient_list_numbers as $recipient) {
			$data = array("dst" => $dst_sms, 
						  "src" => $SMSConfig->SMS_PROVIDER_PLIVO_FROM,
						  "text" => $smsText);
		
			$data_string = json_encode($data);
			
			$s = curl_init();
			curl_setopt($s, CURLOPT_URL, $url);
			curl_setopt($s, CURLOPT_CUSTOMREQUEST, "POST");
			//curl_setopt($s, CURLOPT_POSTFIELDS, http_build_query($data));
			curl_setopt($s, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($s, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($s, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($s, CURLOPT_USERPWD, $SMSConfig->SMS_PROVIDER_PLIVO_AUTH_ID.':'.$SMSConfig->SMS_PROVIDER_PLIVO_AUTH_TOKEN);
		
			curl_setopt($s, CURLOPT_HTTPHEADER, array(
			        'Content-Type: application/json',
			        'Content-Length: ' . strlen($data_string))
			        );
				
			//echo '<br>PLIVO SMS URL to call: ' . $url . '<br>using JSON: ' . $data_string . PHP_EOL;
			$result = curl_exec($s);

			$this->logTrace('Plivo curl_exec for id: '.$dst_sms.' returned: '.$result);

			$resultSMS_current = '<br>RESPONSE: ' . $result .PHP_EOL;
		
			if(curl_errno($s) === 0) {
				$info = curl_getinfo($s);
				$resultSMS_current .= 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'] . PHP_EOL;
			}
			else {
				$resultSMS_current .= 'Curl error: ' . curl_error($s) . PHP_EOL;
			}
		
			$httpCode = curl_getinfo($s, CURLINFO_HTTP_CODE);
			if ($httpCode != 202) {
			    $resultSMS_current .= 'Plivo error code: ' . $httpCode . PHP_EOL;
			}
			
			curl_close($s);
			//echo 'PLIVO SMS result: ' . $resultSMS_current . PHP_EOL;
			
			$resultSMS .= $resultSMS_current;
		//}
				
		return $resultSMS;
	}
	protected function logError($text) {
		global $log;
		if($log != null) $log->error($text);
	}

	protected function logWarning($text) {
		global $log;
		if($log != null) $log->warn($text);
	}

	protected function logTrace($text) {
		global $log;
		if($log != null) $log->trace($text);
	}

}
