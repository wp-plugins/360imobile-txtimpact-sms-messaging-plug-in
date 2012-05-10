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
class TXTIMPACT_Sent_messages {
/**
* The current version of the class
*/
const VERSION = 1;
/**
* Check for updating db structure
*
* @global $wpdb WordPress Database Object
* @return void
*/
public static function check_update() {
global $wpdb;
$version = get_option( 'txtimpact-sent-messages-version', 0 );
$done_upgrade = false;
if( self::VERSION > $version ) {
error_log( "TXTIMPACT: Upgrading sent messages DB table" );
$charset_collate = txtimpact_db_charset_collate();
$table = $wpdb->prefix . 'txtimpact_sent_messages';
$wpdb->query( "DROP TABLE $table" );
$sql  = " CREATE TABLE $table ( ";
$sql .= "    `ID` int(10) unsigned NOT NULL auto_increment, ";
$sql .= "    `message` text, ";
$sql .= "    `phone_numbers` text, ";
$sql .= "    `created` datetime NOT NULL, ";
$sql .= "    PRIMARY KEY  (`ID`)";
$sql .= ") $charset_collate ";
$wpdb->query( $sql );
$done_upgrade = true;
}
if ( $done_upgrade ) {
error_log( "TXTIMPACT: Done upgrade" );
update_option( 'txtimpact-sent-messages-version', self::VERSION );
}
}
/**
* Save a sent messages .Returns false if errors, or the number of rows
* affected if successful.
*
* @global $wpdb WordPress Database Object
* @param string $message
* @return count inserted row
*/
public static function save_message($message,$phone_numbers) {
global $wpdb;
$table = $wpdb->prefix . 'txtimpact_sent_messages';
$data = array(
'message' => $message,
'phone_numbers' => $phone_numbers,
'created'      => date('Y-m-d H:i:s')
);
return $wpdb->insert( $table, $data );
}
/**
* Get all messages
*
* @global $wpdb WordPress Database Object
* @param int $page - current page
* @param int $pageSize - messages count to get
* @param boolean $opt_out - opt_out status
* @return messages objects
*/
public static function fetch_all($page = 1, $pageSize = 10, $opt_out = null) {
global $wpdb;
$start = ( $page - 1 ) * $pageSize;
$table = $wpdb->prefix . 'txtimpact_sent_messages';
$where = '';
$query = $wpdb->prepare( " SELECT * FROM $table WHERE 1=1 $where ORDER BY `created` DESC LIMIT %d, %d ", $start, $pageSize );
return $wpdb->get_results( $query );
}
/**
* Get tottal count site messages
*
* @global $wpdb WordPress Database Object
* @return int
*/
public static function get_count() {
global $wpdb;
$table = $wpdb->prefix . 'txtimpact_sent_messages';
$query = $wpdb->prepare( " SELECT COUNT(*) as Count FROM $table");
return $wpdb->get_var( $query );
}
/**
* Delete messages from messages table.Returns false if errors, or the number of rows
* affected if successful.
*
* @global $wpdb WordPress Database Object
* @param int|array $message_ids
* @return int
*/
public static function delete($message_ids) {
global $wpdb;
if ( ! is_array($message_ids) )
$message_ids = (array) $message_ids;
$table          = $wpdb->prefix . 'txtimpact_sent_messages';
$message_ids = join( ', ', $message_ids );
$sql = "DELETE FROM $table WHERE `ID` IN ( $message_ids ) ";
return (int) $deleted = $wpdb->query( $sql );
}
/**
* Delete messages data. Drop the $wpdb->prefix . 'txtimpact_messages' table,
* clear delete txtimpact-sent-messages-version option
*
* @global $wpdb WordPress Database Object
* @return void;
*/
public static function uninstall() {
global $wpdb;
$table = $wpdb->prefix . 'txtimpact_sent_messages';
$wpdb->query( "DROP TABLE $table" );
delete_option('txtimpact-sent-messages-version');
}
}