<?php
/**
* @package    TXTImpact Texting SMS Notification plugin
* @author     TXTImpact <support@txtimpact.com>
* @copyright  2012 TXTImpact Texting https://www.txtimpact.com
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    1.0
* @since      1.0
* @see        http://www.txtimpact.com/developer_apis.as
*/
/*  Copyright 2011 TXTImpactTexting
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
require_once 'class-txtimpact-request.php';
/**
* Send SMS messages.
*
* @package TXTIMPACT SMS plugin
* */
class TXTIMPACT_Sending {
const API_MESSAGE_URL   = 'http://smsapi.Wire2Air.com/smsadmin/submitsm.aspx';
private $_user;
private $_password;
private $_last_response;
private $_stamp_to_send;
protected static $_http_request = null;
public function __construct($user, $password, $vasid) {
$this->_user = $user;
$this->_password = $password;
$this->_vasid = $vasid;
}
/**
* Send SMS via TXTIMPACT Gateway
*
* @param TXTIMPACT_Message $message
* @param TXTIMPACT_phone_numbers $phone_numbers
* @return TXTIMPACT_Message
*/
public function send_sms($phone_numbers, $message) {
$global_response = array();
$count_numbers = count($phone_numbers);
$phone_numbers = implode(",",$phone_numbers);
$options = get_option("txtimpact");
$data = array(
'VERSION'	=>	"2.0",
'userid'          => $this->_user,
'password'      => $this->_password,
'VASId'      => $this->_vasid,
'FROM'   => "27126",
"to" => $phone_numbers,
"ReplyPath" => dirname(plugin_dir_url ( __FILE__ ))."/txtimpact-message-handler.php?wpkey=".$options['txtimpact_security_key'],
'Text'   => $message
);
if( !empty($this->_stamp_to_send) )
$data['StampToSend'] = $this->_stamp_to_send;
$http_request = $this->get_http_request();
$response  = $http_request->send(self::API_MESSAGE_URL, $data);
$this->_last_response = $response;
if ( $response->is_error() ){
$global_response["errors"] = $response->get_errors();	
}
$success = array();
if ($response->is_successful()){
if(!TXTIMPACT_Sent_messages::save_message($message,$phone_numbers))
$global_response["errors"]["999"] = array("message" =>  "error occurred when saving the message");
else 
$global_response["success"] = array("message" =>  "success", "smsId" => $response->get_smsId(),"numbers" => $count_numbers);
}
return $global_response;
}
/**
* get time to send a scheduled message
*
* @return timestamp
*/
public function get_stamp_to_send() {
return $this->_stamp_to_send;
}
/**
* Set time to send a scheduled message (should be a Unix timestamp)
*
* @param unix(timestamp) $time_stamp
* @return TXTIMPACT_Message
*/
public function set_stamp_to_send($time_stamp) {
$this->_stamp_to_send = $time_stamp;
return $this;
}
/**
* get last response data
*
* @return TXTIMPACT_Response
*/
public function get_last_response() {
return $this->_last_response;
}
public static function set_http_request(TXTIMPACT_Request $request) {
self::$_http_request = $request;
}
public static function get_http_request() {
if(empty(self::$_http_request) )
self::$_http_request = new TXTIMPACT_Request();
return self::$_http_request;

    }

}