<?php
/**
* @package    TXTImpact Texting SMS Notification plugin
* @author     TXTImpact <support@txtimpact.com>
* @copyright  2012 TXTImpact Texting https://www.txtimpact.com
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    1.0
* @since      1.0
*/
/*  Copyright 2012 TXTImpactTexting
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
/**
* Send SMS messages.
*
* @package TXTIMPACT SMS Notification plugin
**/
/**
* send sms to all subscribers.
* if success return stdOject with attribute:
*  recipient_counts - Number of intended recipients. Please note: This includes globally opted out numbers.
*  credits - Number of credits charged for the message.
* if Failed return stdOject with attribute:
*  failed - true
*  response - object TXTIMPACT_Response
*
* @param string $message
* @return stdClass
*/
function txtimpact_send_sms_to_subscribers($message)
{
$return_value = new stdClass();
$patrial_count = 40;
$page = 1;
$txtimpact_options = get_option( 'txtimpact' );
if( !isset($txtimpact_options['txtimpact_user']) || !isset($txtimpact_options['txtimpact_password']) )
return;
$txtimpact_user     = $txtimpact_options['txtimpact_user'];
$txtimpact_password = $txtimpact_options['txtimpact_password'];
$txtimpact_vasid     = $txtimpact_options['txtimpact_vasid'];
$txtimpact_sms_api = new TXTIMPACT_Sending( $txtimpact_user, $txtimpact_password, $txtimpact_vasid);
$recipient_counts   = 0;
$responses = array();
$subscribers = TXTIMPACT_Subscribers::fetch_all( $page, $patrial_count, false);
while( $subscribers = TXTIMPACT_Subscribers::fetch_all( $page, $patrial_count, false) ) {
++$page;
$phone_number_array = array();
$subscribers_assoc  = array();
foreach( $subscribers as $subscriber ) {
$phone_number_array[] = $subscriber->phone_number;
$subscribers_assoc[$subscriber->ID] = $subscriber->phone_number;
}
}
$response_message = $txtimpact_sms_api->send_sms($phone_number_array, $message);
if(!count($response_message["success"])) {
$return_value->failed = true;
}
$return_value->response = $response_message;
return $return_value;
}