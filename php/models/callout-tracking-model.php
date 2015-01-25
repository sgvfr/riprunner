<?php 
// ==============================================================
//	Copyright (C) 2014 Mark Vejvoda
//	Under GNU GPL v3.0
// ==============================================================
namespace riprunner;

require_once __RIPRUNNER_ROOT__ . '/config.php';
require_once __RIPRUNNER_ROOT__ . '/functions.php';
require_once __RIPRUNNER_ROOT__ . '/models/base-model.php';
require_once __RIPRUNNER_ROOT__ . '/firehall_parsing.php';
require_once __RIPRUNNER_ROOT__ . '/functions.php';

// The model class handling variable requests dynamically
class CalloutTrackingViewModel extends BaseViewModel {

	private $responding_people;
	private $responding_people_icons;
	private $callout_status;
	private $user_authenticated;
	private $useracctid;
	private $callout_tracking_id;
	private $responding_people_geo_list;
	
	protected function getVarContainerName() { 
		return "callout_tracking_vm";
	}
	
	public function __get($name) {
		if('firehall_id' == $name) {
			return $this->getFirehallId();
		}
		if('callout_id' == $name) {
			return $this->getCalloutId();
		}
		if('user_id' == $name) {
			return $this->getUserId();
		}
		if('has_user_password' == $name) {
			return ($this->getUserPassword() != null);
		}
		if('user_lat' == $name) {
			return $this->getUserLat();
		}
		if('user_long' == $name) {
			return $this->getUserLong();
		}
		if('calloutkey_id' == $name) {
			return $this->getCalloutKeyId();
		}
		if('firehall' == $name) {
			return $this->getFirehall();
		}
		if('tracking_action' == $name) {
			return $this->getTrackingAction();
		}
		if('tracking_delay' == $name) {
			return $this->getTrackingDelay();
		}
		if('callout_status' == $name) {
			return $this->getCalloutStatus();
		}
		if('callout_status_desc' == $name) {
			return getCallStatusDisplayText($this->getCalloutStatus());
		}
		if('callout_in_progress' == $name) {
			return isCalloutInProgress($this->callout_status);
		}
		if('responding_people' == $name) {
			$this->getRespondingPeople();
			return $this->responding_people;
		}
		if('responding_people_icons' == $name) {
			$this->getRespondingPeople();
			return $this->responding_people_icons;
		}
		if('user_authenticated' == $name) {
			$this->checkAuth();
			return $this->user_authenticated;
		}
		if('useracctid' == $name) {
			$this->checkAuth();
			return $this->useracctid;
		}
		if('track_geo' == $name) {
			$this->trackGeo();
			return '';
		}
		if('callout_tracking_id') {
			return $this->callout_tracking_id;
		}
		if('responding_people_geo_list') {
			$this->getRespondingPeopleGeoList();
			return $this->responding_people_geo_list;
		}
		
		return parent::__get($name);
	}

	public function __isset($name) {
		if(in_array($name,
			array('firehall_id','callout_id','user_id','has_user_password','user_lat',
				  'user_long', 'calloutkey_id', 'firehall', 'tracking_action',
				  'tracking_delay', 'responding_people', 'responding_people_icons',
				  'callout_status', 'callout_status_desc','callout_in_progress',
  				  'user_authenticated', 'useracctid', 'track_geo', 'callout_tracking_id',
				  'responding_people_geo_list'
			 ))) {
			return true;
		}
		return parent::__isset($name);
	}
	
	private function getFirehallId() {
		$firehall_id = get_query_param('fhid');
		return $firehall_id;
	}
	private function getCalloutId() {
		$callout_id = get_query_param('cid');
		return $callout_id;
	}
	private function getUserId() {
		$user_id = get_query_param('uid');
		return $user_id;
	}
	private function getUserPassword() {
		$user_pwd = get_query_param('upwd');
		return $user_pwd;
	}
	private function getUserLat() {
		$user_lat = get_query_param('lat');
		return $user_lat;
	}
	private function getUserLong() {
		$user_long = get_query_param('long');
		return $user_long;
	}
	private function getCalloutKeyId() {
		$callkey_id = get_query_param('ckid');
		return $callkey_id;
	}
	private function getFirehall() {
		$firehall = null;
		if($this->getFirehallId() != null) {
			$firehall = findFireHallConfigById($this->getFirehallId(), $this->getGvm()->firehall_list);
		}
		return $firehall;
	}
	private function getTrackingAction() {
		$tracking_action = get_query_param('ta');
		return $tracking_action;
	}
	private function getTrackingDelay() {
		$tracking_delay = get_query_param('delay');
		return $tracking_delay;
	}
	private function getCalloutStatus() {
		return $this->callout_status;
	}
	
	private function getRespondingPeople() {
		if(isset($this->responding_people) == false) {
			global $log;
			$log->trace("Call Tracking firehall_id [". $this->getFirehallId() ."] cid [". $this->getCalloutId() ."] user_id [". $this->getUserId() ."] ckid [" .$this->getCalloutKeyId(). "]");
			
			// Get the callout info
			$sql = 'SELECT status, latitude, longitude, address ' .
					' FROM callouts ' .
					' WHERE id = ' . $this->getGvm()->RR_DB_CONN->real_escape_string( $this->getCalloutId() ) . ';';
			$sql_result = $this->getGvm()->RR_DB_CONN->query( $sql );
			if($sql_result == false) {
				$log->error("Call Tracking callouts SQL error for sql [$sql] error: " . mysqli_error($this->getGvm()->RR_DB_CONN));
				throw new \Exception(mysqli_error( $this->getGvm()->RR_DB_CONN ) . "[ " . $sql . "]");
			}
			
			$this->responding_people = '';
			$this->responding_people_icons = '';
			
			$this->responding_people .= "['FireHall: ". $this->getFirehall()->WEBSITE->FIREHALL_HOME_ADDRESS ."', ". $this->getFirehall()->WEBSITE->FIREHALL_GEO_COORD_LATITUDE .", ". $this->getFirehall()->WEBSITE->FIREHALL_GEO_COORD_LONGITUDE ."]";
			$this->responding_people_icons .= "iconURLPrefix + 'blue-dot.png'";
			
			$this->callout_status = null;
			$callout_address = null;
			$callout_lat = null;
			$callout_long = null;
			
			if($row = $sql_result->fetch_object()) {
				$this->callout_status = $row->status;
				$callout_address = $row->address;
				$callout_lat = $row->latitude;
				$callout_long = $row->longitude;
			
				if(isset($callout_lat) == false || $callout_lat == '' || $callout_lat == 0 ||
						isset($callout_long) == false || $callout_long == '' || $callout_long == 0) {
							$geo_lookup = getGEOCoordinatesFromAddress($this->getFirehall(),$callout_address);
							if(isset($geo_lookup)) {
								$callout_lat = $geo_lookup[0];
								$callout_long = $geo_lookup[1];
							}
						}
			
						if($this->responding_people != '') {
							$this->responding_people .= ',' . PHP_EOL;
						}
			
						$this->responding_people .= "['Destination: ". $callout_address ."', ". $callout_lat .", ". $callout_long ."]";
			
						if($this->responding_people_icons != '') {
							$this->responding_people_icons .= ',' . PHP_EOL;
						}
			
						$this->responding_people_icons .= "iconURLPrefix + 'red-dot.png'";
			}
			$sql_result->close();
			
			// Get the latest GEO coordinates for each responding member
			$sql = 'SELECT a.useracctid, a.calloutid, a.latitude,a.longitude, b.user_id ' .
					' FROM callouts_geo_tracking a ' .
					' LEFT JOIN user_accounts b ON a.useracctid = b.id ' .
					' WHERE firehall_id = \'' .
					$this->getGvm()->RR_DB_CONN->real_escape_string( $this->getFirehallId() ) . '\'' .
					' AND a.calloutid = ' . $this->getGvm()->RR_DB_CONN->real_escape_string( $this->getCalloutId() ) .
					' AND a.trackingtime = (SELECT MAX(a1.trackingtime) FROM callouts_geo_tracking a1 WHERE a.calloutid = a1.calloutid AND a.useracctid = a1.useracctid)' .
					' ORDER BY a.useracctid,a.trackingtime DESC;';
			$sql_result = $this->getGvm()->RR_DB_CONN->query( $sql );
			if($sql_result == false) {
				$log->error("Call Tracking callouts geo tracking SQL error for sql [$sql] error: " . mysqli_error($this->getGvm()->RR_DB_CONN));
				throw new \Exception(mysqli_error( $this->getGvm()->RR_DB_CONN ) . "[ " . $sql . "]");
			}
			
			while($row = $sql_result->fetch_object()) {
				if($this->responding_people != '') {
					$this->responding_people .= ',' . PHP_EOL;
				}
				$this->responding_people .= "['". $row->user_id ."', ". $row->latitude .", ". $row->longitude ."]";
			
				if($this->responding_people_icons != '') {
					$this->responding_people_icons .= ',' . PHP_EOL;
				}
				$this->responding_people_icons .= "iconURLPrefix + 'green-dot.png'";
			}
			$sql_result->close();
		}

		return $this->responding_people;
	}

	private function checkAuth() {
		if(isset($this->user_authenticated) == false) {
			global $log;
			
			// Authenticate the user
			$sql = 'SELECT id,user_pwd FROM user_accounts WHERE firehall_id = \'' .
					$this->getGvm()->RR_DB_CONN->real_escape_string( $this->getFirehallId() ) . '\'' .
					' AND user_id = \'' . $this->getGvm()->RR_DB_CONN->real_escape_string( $this->getUserId() ) . '\';';
			$sql_result = $this->getGvm()->RR_DB_CONN->query( $sql );
			if($sql_result == false) {
				$log->error("Call Tracking callouts user_id check tracking SQL error for sql [$sql] error: " . mysqli_error($this->getGvm()->RR_DB_CONN));
				throw new \Exception(mysqli_error( $this->getGvm()->RR_DB_CONN ) . "[ " . $sql . "]");
			}
			
			$this->useracctid = null;
			$this->user_authenticated = false;
			$this->callout_status = null;
			
			if($row = $sql_result->fetch_object()) {
				// Validate the the callkey is legit
				$sql_callkey = 'SELECT status FROM callouts WHERE id = ' .
						$this->getGvm()->RR_DB_CONN->real_escape_string( $this->getCalloutId() ) .
						' AND call_key = \'' . $this->getGvm()->RR_DB_CONN->real_escape_string( $this->getCalloutKeyId() ) . '\';';
				$sql_callkey_result = $this->getGvm()->RR_DB_CONN->query( $sql_callkey );
				if($sql_callkey_result == false) {
					$log->error("Call Tracking callouts status tracking SQL error for sql [$sql_callkey] error: " . mysqli_error($this->getGvm()->RR_DB_CONN));
					throw new \Exception(mysqli_error( $this->getGvm()->RR_DB_CONN ) . "[ " . $sql_callkey . "]");
				}
			
				if( $sql_callkey_result->num_rows > 0) {
			
					if($this->getUserPassword() == null && $this->getCalloutKeyId() != null) {
			
						$this->user_authenticated = true;
						$this->useracctid = $row->id;
					}
					if($row_callout = $sql_callkey_result->fetch_object()) {
						$this->callout_status = $row_callout->status;
					}
				}
				$sql_callkey_result->close();
			
				if($this->getUserPassword() == null && $this->getCalloutKeyId() != null) {
			
				}
				else {
					// Validate the users password
					if (crypt($this->getGvm()->RR_DB_CONN->real_escape_string( $this->getUserPassword() ), $row->user_pwd) === $row->user_pwd ) {
			
						$this->user_authenticated = true;
						$this->useracctid = $row->id;
					}
				}
			}
			$sql_result->close();
		}
		return $this->user_authenticated;
	}
	
	private function trackGeo() {
		global $log;
		
		// INSERT tracking information
		$sql = 'INSERT INTO callouts_geo_tracking (calloutid,useracctid,latitude,longitude) ' .
				' values(' .
				'' . $this->getGvm()->RR_DB_CONN->real_escape_string( $this->getCalloutId() )  . ', ' .
				'' . $this->getGvm()->RR_DB_CONN->real_escape_string( $this->useracctid )  . ', ' .
				'' . $this->getGvm()->RR_DB_CONN->real_escape_string( $this->getUserLat() )    . ', ' .
				'' . $this->getGvm()->RR_DB_CONN->real_escape_string( $this->getUserLong() )   . ');';
		
		$sql_result = $this->getGvm()->RR_DB_CONN->query( $sql );
		
		if($sql_result == false) {
			$log->error("Call Tracking callouts insert tracking SQL error for sql [$sql] error: " . mysqli_error($this->getGvm()->RR_DB_CONN));
			throw new \Exception(mysqli_error( $this->getGvm()->RR_DB_CONN ) . "[ " . $sql . "]");
		}
		
		$this->callout_tracking_id = $this->getGvm()->RR_DB_CONN->insert_id;
	}
	
	private function getRespondingPeopleGeoList() {
		if(isset($this->responding_people_geo_list) == false) {
			global $log;
			
			// Get the latest GEO coordinates for each responding member
			$sql = 'SELECT a.useracctid, a.calloutid, a.latitude,a.longitude, b.user_id ' .
					' FROM callouts_geo_tracking a ' .
					' LEFT JOIN user_accounts b ON a.useracctid = b.id ' .
					' WHERE firehall_id = \'' .
					$this->getGvm()->RR_DB_CONN->real_escape_string( $this->getFirehallId() ) . '\'' .
					' AND a.calloutid = ' . $this->getGvm()->RR_DB_CONN->real_escape_string( $this->getCalloutId() ) .
					' AND a.trackingtime = (SELECT MAX(a1.trackingtime) FROM callouts_geo_tracking a1 WHERE a.calloutid = a1.calloutid AND a.useracctid = a1.useracctid)' .
					' ORDER BY a.useracctid,a.trackingtime DESC;';
			$sql_result = $this->getGvm()->RR_DB_CONN->query( $sql );
			if($sql_result == false) {
				$log->error("Call Tracking callouts get geo members tracking SQL error for sql [$sql] error: " . mysqli_error($this->getGvm()->RR_DB_CONN));
				throw new \Exception(mysqli_error( $this->getGvm()->RR_DB_CONN ) . "[ " . $sql . "]");
			}
			
			$this->responding_people_geo_list = '';
			while($row = $sql_result->fetch_object()) {
				if($this->responding_people_geo_list != '') {
					$this->responding_people_geo_list .= '^' . PHP_EOL;
				}
				$this->responding_people_geo_list .=  $row->user_id ."', ". $row->latitude .", ". $row->longitude;
			}
			$sql_result->close();
			
			$response_result = "OK=" . $this->callout_tracking_id . "|" . $this->responding_people_geo_list . "|";

			$log->trace("Call Tracking end result [$response_result]");
		}
		return $this->responding_people_geo_list;
	}
	
}