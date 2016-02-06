<?php
// ==============================================================
//	Copyright (C) 2015 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================
ini_set('display_errors', 'On');
error_reporting(E_ALL);

//
// This file manages routing of requests
//
if(defined('INCLUSION_PERMITTED') === false) {
    define( 'INCLUSION_PERMITTED', true);
}

require_once dirname(dirname(__FILE__)).'/baseDBFixture.php';
require_once __RIPRUNNER_ROOT__ . '/plugins/sms-provider-hook/sms_cmd_handler.php';

class SmsCommandsTest extends BaseDBFixture {
	
    protected function setUp() {
        // Add special fixture setup here
        parent::setUp();
    }
    
    protected function tearDown() {
        // Add special fixture teardown here
        parent::tearDown();
    }
    
	public function testSMSCommand_InvalidAuth()  {
		$sms_cmd_handler = new \riprunner\SMSCommandHandler();
		// Check if Twilio is calling us, if not 401
		$result = $sms_cmd_handler->validateTwilioHost($this->FIREHALLS);
		$this->assertEquals(false, $result);
	}
	public function testSMSCommand_ValidAuth()  {
	    $FIREHALL = findFireHallConfigById(0, $this->FIREHALLS);
	    
	    $authToken = explode(":", $FIREHALL->SMS->SMS_PROVIDER_TWILIO_AUTH_TOKEN);
	    $validator = new \Services_Twilio_RequestValidator($authToken[1]);
	    $site_root = $FIREHALL->WEBSITE->WEBSITE_ROOT_URL;
	    $url = $site_root.\riprunner\SMSCommandHandler::getTwilioWebhookUrl();
	    $post_vars = array();
	    $validate_result = $validator->computeSignature($url, $post_vars);
	    $server_variables = array('HTTP_X_TWILIO_SIGNATURE' => $validate_result);
	    
	    $sms_cmd_handler = new \riprunner\SMSCommandHandler($server_variables, $post_vars);
	    $result = $sms_cmd_handler->validateTwilioHost($this->FIREHALLS);
	    $this->assertEquals(true, $result);
	}

	public function testSMSCommand_handle_CMD_TEST_Valid()  {
	    $FIREHALL = findFireHallConfigById(0, $this->FIREHALLS);
	     
	    $authToken = explode(":", $FIREHALL->SMS->SMS_PROVIDER_TWILIO_AUTH_TOKEN);
	    $validator = new \Services_Twilio_RequestValidator($authToken[1]);
	    $site_root = $FIREHALL->WEBSITE->WEBSITE_ROOT_URL;
	    $url = $site_root.\riprunner\SMSCommandHandler::getTwilioWebhookUrl();
	    $post_vars = array();
	    $validate_result = $validator->computeSignature($url, $post_vars);
	    $server_variables = array('HTTP_X_TWILIO_SIGNATURE' => $validate_result);
	    
	    $request_vars = array('From' => '2505551212',
	                          'Body' => \riprunner\SMSCommandHandler::$SMS_AUTO_CMD_TEST[0]
	    );

	    // Create a stub for the HTTPCli class.
	    $mock_http_client = $this->getMockBuilder('\riprunner\HTTPCli')
	    ->getMock(array('setURL','execute'));
	    
	    // Ensure execute is called
	    $mock_http_client->expects($this->once())
	    ->method('execute')
	    ->with($this->anything());
	     
	    $mock_http_client->expects($this->once())
	    ->method('setURL')
	    ->with($this->stringContains('test/fhid='));

	    // Stub in dummy db connection for this test
	    $this->getDBConnection($FIREHALL);
	     
	    $sms_cmd_handler = new \riprunner\SMSCommandHandler($server_variables, $post_vars, $request_vars, $mock_http_client);
	    $result = $sms_cmd_handler->handle_sms_command($this->FIREHALLS,SMS_GATEWAY_TWILIO);
	    $this->assertEquals(true, $result->getIsProcessed());
	    $this->assertEquals('2505551212', $result->getSmsCaller());
	    $this->assertEquals('mark.vejvoda', $result->getUserId());
	}

	public function testSMSCommand_handle_CMD_RESPONDING_Valid()  {
	    $FIREHALL = findFireHallConfigById(0, $this->FIREHALLS);
	
	    $authToken = explode(":", $FIREHALL->SMS->SMS_PROVIDER_TWILIO_AUTH_TOKEN);
	    $validator = new \Services_Twilio_RequestValidator($authToken[1]);
	    $site_root = $FIREHALL->WEBSITE->WEBSITE_ROOT_URL;
	    $url = $site_root.\riprunner\SMSCommandHandler::getTwilioWebhookUrl();
	    $post_vars = array();
	    $validate_result = $validator->computeSignature($url, $post_vars);
	    $server_variables = array('HTTP_X_TWILIO_SIGNATURE' => $validate_result);
	     
	    $request_vars = array('From' => '2505551212',
	            'Body' => \riprunner\SMSCommandHandler::$SMS_AUTO_CMD_RESPONDING[0]
	    );
	
	    // Create a stub for the HTTPCli class.
	    $mock_http_client = $this->getMockBuilder('\riprunner\HTTPCli')
	    ->getMock(array('setURL','execute'));
	     
	    // Ensure execute is called
	    $mock_http_client->expects($this->once())
	    ->method('execute')
	    ->with($this->anything());

	    // Ensure setURL is called
	    $mock_http_client->expects($this->once())
	    ->method('setURL')
	    ->with($this->stringContains('cr/fhid='));

	    // Esnure responding DOES NOT contain setting the status explicitly
	    $mock_http_client->expects($this->once())
	    ->method('setURL')
	    ->with($this->matchesRegularExpression('/^((?!\&status=).)*$/s'));
	     
	    // Stub in dummy db connection for this test
	    $this->getDBConnection($FIREHALL);
	    
	    $sms_cmd_handler = new \riprunner\SMSCommandHandler($server_variables, $post_vars, $request_vars, $mock_http_client);
	    $result = $sms_cmd_handler->handle_sms_command($this->FIREHALLS,SMS_GATEWAY_TWILIO);
	    $this->assertEquals(true, $result->getIsProcessed());
	    $this->assertEquals('2505551212', $result->getSmsCaller());
	    $this->assertEquals('mark.vejvoda', $result->getUserId());
	}

	public function testSMSCommand_handle_CMD_COMPLETED_Valid()  {
	    $FIREHALL = findFireHallConfigById(0, $this->FIREHALLS);
	
	    $authToken = explode(":", $FIREHALL->SMS->SMS_PROVIDER_TWILIO_AUTH_TOKEN);
	    $validator = new \Services_Twilio_RequestValidator($authToken[1]);
	    $site_root = $FIREHALL->WEBSITE->WEBSITE_ROOT_URL;
	    $url = $site_root.\riprunner\SMSCommandHandler::getTwilioWebhookUrl();
	    $post_vars = array();
	    $validate_result = $validator->computeSignature($url, $post_vars);
	    $server_variables = array('HTTP_X_TWILIO_SIGNATURE' => $validate_result);
	
	    $request_vars = array('From' => '2505551212',
	            'Body' => \riprunner\SMSCommandHandler::$SMS_AUTO_CMD_COMPLETED[0]
	    );
	
	    // Create a stub for the HTTPCli class.
	    $mock_http_client = $this->getMockBuilder('\riprunner\HTTPCli')
	    ->getMock(array('setURL','execute'));
	
	    // Ensure execute is called
	    $mock_http_client->expects($this->once())
	    ->method('execute')
	    ->with($this->anything());
	
	    // Ensure setURL is called
	    $mock_http_client->expects($this->once())
	    ->method('setURL')
	    ->with($this->stringContains('cr/fhid='));

	    $mock_http_client->expects($this->once())
	    ->method('setURL')
	    ->with($this->stringContains('&status='.urlencode(\CalloutStatusType::Complete)));
	     
	    // Stub in dummy db connection for this test
	    $this->getDBConnection($FIREHALL);
	     
	    $sms_cmd_handler = new \riprunner\SMSCommandHandler($server_variables, $post_vars, $request_vars, $mock_http_client);
	    $result = $sms_cmd_handler->handle_sms_command($this->FIREHALLS,SMS_GATEWAY_TWILIO);
	    $this->assertEquals(true, $result->getIsProcessed());
	    $this->assertEquals('2505551212', $result->getSmsCaller());
	    $this->assertEquals('mark.vejvoda', $result->getUserId());
	}

	public function testSMSCommand_handle_CMD_CANCELLED_Valid()  {
	    $FIREHALL = findFireHallConfigById(0, $this->FIREHALLS);
	
	    $authToken = explode(":", $FIREHALL->SMS->SMS_PROVIDER_TWILIO_AUTH_TOKEN);
	    $validator = new \Services_Twilio_RequestValidator($authToken[1]);
	    $site_root = $FIREHALL->WEBSITE->WEBSITE_ROOT_URL;
	    $url = $site_root.\riprunner\SMSCommandHandler::getTwilioWebhookUrl();
	    $post_vars = array();
	    $validate_result = $validator->computeSignature($url, $post_vars);
	    $server_variables = array('HTTP_X_TWILIO_SIGNATURE' => $validate_result);
	
	    $request_vars = array('From' => '2505551212',
	            'Body' => \riprunner\SMSCommandHandler::$SMS_AUTO_CMD_CANCELLED[0]
	    );
	
	    // Create a stub for the HTTPCli class.
	    $mock_http_client = $this->getMockBuilder('\riprunner\HTTPCli')
	    ->getMock(array('setURL','execute'));
	
	    // Ensure execute is called
	    $mock_http_client->expects($this->once())
	    ->method('execute')
	    ->with($this->anything());
	
	    // Ensure setURL is called
	    $mock_http_client->expects($this->once())
	    ->method('setURL')
	    ->with($this->stringContains('cr/fhid='));
	
	    $mock_http_client->expects($this->once())
	    ->method('setURL')
	    ->with($this->stringContains('&status='.urlencode(\CalloutStatusType::Cancelled)));
	
	    // Stub in dummy db connection for this test
	    $this->getDBConnection($FIREHALL);
	
	    $sms_cmd_handler = new \riprunner\SMSCommandHandler($server_variables, $post_vars, $request_vars, $mock_http_client);
	    $result = $sms_cmd_handler->handle_sms_command($this->FIREHALLS,SMS_GATEWAY_TWILIO);
	    $this->assertEquals(true, $result->getIsProcessed());
	    $this->assertEquals('2505551212', $result->getSmsCaller());
	    $this->assertEquals('mark.vejvoda', $result->getUserId());
	}

	public function testSMSCommand_handle_bulk_sms_command_Valid()  {
	    $FIREHALL = findFireHallConfigById(0, $this->FIREHALLS);
	
	    // Stub in dummy db connection for this test
	    $this->getDBConnection($FIREHALL);
	
	    $result = new \riprunner\SmSCommandResult();
	    $result->setCmd(\riprunner\SMSCommandHandler::$SMS_AUTO_CMD_BULK);
	    $result->setSmsRecipients(array('2505551212','2505551213'));
	    $result->setUserId('test.user');
	    
	    $sms_cmd_handler = new \riprunner\SMSCommandHandler();
	    $result = $sms_cmd_handler->process_bulk_sms_command($result,SMS_GATEWAY_TWILIO);
	    $this->assertEquals("<Message to='+12505551212'>Group SMS from test.user: </Message><Message to='+12505551213'>Group SMS from test.user: </Message>", $result);
	}
	
}
