<?php
/**
* @package    TXTImpact Texting SMS Notification plugin
* @author     TXTImpact <support@txtimpact.com>
* @copyright  2012 TXTImpact Texting https://www.txtimpact.com
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    1.0
* @since      1.0
* @see        http://www.txtimpact.com/developer_apis.asp
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
* Parse TXTIMPACT API Response.
*
* @package TXTIMPACT SMS plugin
* */
ignore_user_abort(true); 
$phone_number = $_REQUEST['mobilenumber'];
$content = $_REQUEST ['message'];
$smsinboxid = $_REQUEST ['smsinboxid']; 
$rcvd = $_REQUEST ['Rcvd'];
$shortcode = $_REQUEST ['SHORTCODE'];
$key = isset($_REQUEST["wpkey"])?$_REQUEST["wpkey"]:"";
require_once('../../../wp-load.php');
$options = get_option("txtimpact");
if(!empty($key) && ($options['txtimpact_security_key'] == $key)){
/**
*
*	retrieve messages from clients and saved
*
**/
if(!empty($phone_number) && !empty($content) && !empty($smsinboxid) && !empty($rcvd)){
//filter phoen number
$phone_number = trim( $phone_number );
$phone_number = preg_replace('/[^\d]/', '', $phone_number);
$exist_phone_number = TXTIMPACT_Subscribers::fetch_row_by_phone_number($phone_number);
$match = strpos(strtolower($content),"stop");
if(!empty($match) && !empty($exist_phone_number)){
TXTIMPACT_Subscribers::delete($exist_phone_number->ID);
}else{
if( empty($exist_phone_number) )
TXTIMPACT_Subscribers::save_number($phone_number);
TXTIMPACT_Received_messages::save_number($content,$phone_number,$rcvd,$smsinboxid);
}
}else{
die();
}
}else{
print "You do not have permission to access this file";
}
?>