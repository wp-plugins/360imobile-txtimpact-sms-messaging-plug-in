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
class TXTIMPACT_Request
{
private $_code;
private $_success;
private $_errors;
private $_number;
private $_smsId;
/**
* @param json $response
*/
/**
* make api call to txtimpact gateway
*
* @param string $url
* @param array $data
* @return TXTIMPACT_Request
*/
public function response($responses)
{
$i=0;
foreach($responses as $response){
if($response[0] == "ERR") {
$this->_errors[$response[1]]["message"] = $response[2];
$i++;
$this->_errors[$response[1]]["numbers"] = $i;
}else{
$this->_success[] = $response[1];
}
}
return $this;
}
/**
* Check whether the response is an error
* @return boolean
*/
public function is_error()
{
if (!empty($this->_errors))
return true;
return false;
}
/**
* Check whether the response in successful
*
* @return boolean
*/
public function is_successful()
{
if (!empty($this->_success))
return true;
return false;
}
/**
* @return array
*/
/**
* Get the HTTP response status code
*
* @return int
*/
public function get_code()
{
return $this->_code;
}
/**
* Get the phone number response
*
* @return int
*/
public function get_number()
{
return $this->_number;
}
/**
* Get the HTTP response sms Id
*
* @return int
*/
public function get_smsId()
{
return $this->_success;
}
/**
* Return Error Message
* 
* @return array
*/
public function get_errors()
{
return $this->_errors;
}
public function send($url, $data)
{
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
$to = array_splice($data, 5, 1); 
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, "&")."&to=".$to["to"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);
$data_reponse[] = explode(":", $response);
return $this->response($data_reponse);

    }

}

?>