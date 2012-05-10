<?php
/**
* @package    txtimpact Texting SMS Notification plugin
* @author     TXTImpact <support@txtimpact.com>
* @copyright  2012 txtimpact Texting https://www.txtimpact.com
* @license    http://opensource.org/licenses/gpl-license.php GNU Public License
* @version    1.0
* @since      1.0
*/
/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
require_once( 'class-txtimpact-plugin.php' );
/**
* The main class of the plugin.
*/
class TXTIMPACT extends TXTIMPACT_Plugin {
/**
* The version of this file, for the purposes of rewrite
* refreshes, JS/CSS cache bursting, etc.
*
* @var int
**/
protected $version;
/**
* for test purpose
* 
* @var W2A_Functions
*/
public static $_wp_functions = null;
/**
* Start point.
* init base action
*/
public function  __construct() {
$this->setup( 'txtimpact' );
if ( is_admin() ) {
$this->add_action( 'admin_init' );
$this->add_action( 'admin_menu' );
$this->add_action( 'wp_ajax_txtimpact_subscribe', 'ajax_subscribe' );
$this->add_action( 'wp_ajax_nopriv_txtimpact_subscribe', 'ajax_subscribe' );
}
$this->add_action( 'transition_post_status', null, null, 3 );
$this->add_action( 'init' );
$this->version = 1;
}
/**
* Hook admin init action
*/
public function admin_init() {
$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.dev' : '';
wp_enqueue_script( 'txtimpact-admin', $this->url( "/js/admin{$suffix}.js" ), array( 'jquery' ), $this->version );
$localized = array(
'top_menu_label' => __( 'TXTImpact SMS', 'txtimpact' ),
);
wp_localize_script( 'txtimpact-admin', 'txtimpact', $localized );
wp_enqueue_style( 'txtimpact-admin', $this->url( "/css/admin{$suffix}.css" ), array(), $this->version, 'all' );
TXTIMPACT_Subscribers::check_update();
TXTIMPACT_Received_messages::check_update();
TXTIMPACT_Sent_messages::check_update();
}
/**
* Hooks the init action to:
* * Process unsubscribe requests
*
* @return void
**/
public function init()
{
$this->maybe_unsubscribe_request();
}
/**
* Hooks the WP admin_menu function to add in our top
* level menu and various sub-pages.
*
* @return void
**/
public function admin_menu() {
// Create a top level SMS menu item, and some sub-items
$hook_name = add_object_page( __( 'Your SMS Subscribers', 'txtimpact' ), __( 'Subscribers', 'txtimpact' ), 'edit_users', 'txtimpact_main', array( $this, 'manage_subscribers' ), plugin_dir_url ( __FILE__ )."img/mobile.png");
$this->add_action( "load-$hook_name", 'load_subscribers_action' );
$hook_name = add_submenu_page( 'txtimpact_main', __( 'SMS Settings', 'txtimpact' ), __( 'Settings', 'txtimpact' ), 'publish_posts', 'txtimpact_options', array( $this, 'manage_options' ) );
$this->add_action( "load-$hook_name", 'load_settings' );
$hook_name = add_submenu_page( 'txtimpact_main', __( 'SMS Sent Messages', 'txtimpact' ), __( 'Sent Messages', 'txtimpact' ), 'edit_users', 'txtimpact_sent_messages', array( $this, 'manage_sent_messages' ) );
$this->add_action( "load-$hook_name", 'load_sent_messages_action' );
$hook_name = add_submenu_page( 'txtimpact_main', __( 'SMS Received Messages', 'txtimpact' ), __( 'Received Messages', 'txtimpact' ), 'edit_users', 'txtimpact_received_messages', array( $this, 'manage_received_messages' ) );
$this->add_action( "load-$hook_name", 'load_received_messages_action' );
$hook_name = add_submenu_page( 'txtimpact_main', __( 'Send SMS', 'txtimpact' ), __( 'Send SMS', 'txtimpact' ), 'publish_posts', 'txtimpact_sendsms', array( $this, 'send_sms' ) );
$this->add_action( "load-$hook_name", 'load_send_message' );
}
/**
* Callback function for rendering options page
*
* @global WordPress database object $wpdb
* @return void
*/
public function manage_options() {
global $wpdb;
$vars = array();
$vars['txtimpact_user']                 = $this->get_option( 'txtimpact_user' );
$vars['txtimpact_password']             = $this->get_option( 'txtimpact_password' );
$vars['txtimpact_vasid']             = $this->get_option( 'txtimpact_vasid' );
$new_var = isset($_GET["newkey"]) ? $_GET["newkey"] : 0;
$security_key = $this->get_option( 'txtimpact_security_key' );
if(empty($security_key) || !empty($new_var)){
$new_key = $this->createRandomKey(20);
$options = get_option("txtimpact");
$new_options = $options;
$new_options["txtimpact_security_key"] = $new_key;	
update_option('txtimpact',$new_options );
echo "<script type='text/javascript'>top.location.href = '".get_admin_url()."admin.php?page=txtimpact_options';</script>";
}
$options = get_option("txtimpact");
$vars['txtimpact_security_key']  = $options['txtimpact_security_key' ];
$vars['txtimpact_new_post']         = (bool) $this->get_option( 'txtimpact_new_post' );
$vars['txtimpact_new_post_message'] = $this->new_post_message();
$vars['length_post_title']   = (int) ceil( $wpdb->get_var( " SELECT AVG( CHAR_LENGTH( post_title ) ) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' LIMIT 500 " ) );
$vars['length_post_author']  = (int) ceil( $wpdb->get_var( " SELECT AVG( CHAR_LENGTH( display_name ) ) FROM $wpdb->users LIMIT 500 " ) );
$max_post_id = $wpdb->get_var( " SELECT MAX(ID) FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' " );
$vars['length_post_url']  = strlen( $this->get_post_short_link( $max_post_id ) );
$vars['length_blog_name'] = strlen( get_bloginfo( 'name' ) );
$vars['length_blog_url']  = strlen( home_url() );
$this->render_admin( 'options.php', $vars);
}
/**
* Hook the load TXTImpact SMS setting page
*
* @return void
*/
public function load_settings() {
if ( ! isset($_POST[ '_txtimpact_nonce' ]) )
return;
if( isset($_POST['action']) ) {
switch ( $_POST['action'] ) {
case 'save-settings':
$this->save_settings();
break;
case 'delete-uninstall':
$this->confirm_delete_uninstall();
break;
case 'confirm-delete-uninstall':
$this->delete_uninstall();
break;
}
}
$wp_function = self::get_wp_functions();
$wp_function->wp_redirect(admin_url( '/admin.php?page=txtimpact_options' ), true);
}
/**
* Callback function for rendering send sms page
*
* @return void
*/
public function send_sms() {
$vars = array();
$vars['length_blog_name'] = strlen( get_bloginfo( 'name' ) );
$vars['length_blog_url']  = strlen( home_url() );
$this->render_admin( 'send-sms.php', $vars);
}
/**
* Hook the load send message page
*
* @return void
*/
public function load_send_message() {
if ( ! isset($_POST[ '_txtimpact_nonce' ]) )
return;
$wp_function = self::get_wp_functions();
$wp_function->check_admin_referer( 'txtimpact-send-sms', '_txtimpact_nonce' );
$post = $_POST;
$message = ( isset($_POST['txtimpact_message']) ) ? trim($_POST['txtimpact_message']) : '';
if( empty($message) ) {
$this->set_admin_error( __("The Message field is required!", 'txtimpact') );
$wp_function->wp_redirect(txtimpact_current_page_url(), true );
}
$message = $this->get_original_message($message);
$response = txtimpact_send_sms_to_subscribers($message);
if($response->failed) {
if(isset($response->response["errors"]["301"])) {
$this->set_admin_error(
__($response->response["errors"]["301"]["message"].'! Your txtimpact username, password and/or vasid are missing or incorrect.', 'txtimpact')
.' <a href="'.get_admin_url().'admin.php?page=txtimpact_options">'
.__('Please visit the settings page', 'txtimpact')
.'</a>'
);
}else if(isset($response->response["errors"]["304"])) {
$this->set_admin_error(
__($response->response["errors"][304]["message"].'!', 'txtimpact')
);
}else{
foreach($response->response["errors"] as $key => $errors)
$message_error .= $response->response["errors"][$key]["message"].'
';
$this->set_admin_error(
__($message_error.'!', 'txtimpact')
);
}
} else {
$this->set_admin_notice( sprintf( __('Your Message has been sent to %d subscribers.', 'txtimpact'), $response->response["success"]["numbers"] ) );
}
$wp_function->wp_redirect( txtimpact_current_page_url(), true );
}
/**
* Callback function for rendering messages sent page
*
* @return void
*/
public function manage_sent_messages() {
$page = ( !isset($_GET['p']) || !is_numeric($_GET['p']) ) ? 1 : $_GET['p'];
$total_items = TXTIMPACT_Sent_messages::get_count();
$page_size  = 20;
$total_page = ceil( $total_items / $page_size );
$vars = array(
'messages'  => TXTIMPACT_Sent_Messages::fetch_all( $page, $page_size ),
'current_page' => $page,
'page_size'    => $page_size,
'total_items'  => $total_items,
'total_page'   => $total_page
);
$this->render_admin( 'sent-messages.php', $vars );
}
/**
* Callback function for rendering messages received page
*
* @return void
*/
public function manage_received_messages() {
$page = ( !isset($_GET['p']) || !is_numeric($_GET['p']) ) ? 1 : $_GET['p'];
$total_items = TXTIMPACT_Received_messages::get_count();
$page_size  = 20;
$total_page = ceil( $total_items / $page_size );
$vars = array(
'messages'  => TXTIMPACT_Received_messages::fetch_all( $page, $page_size ),
'current_page' => $page,
'page_size'    => $page_size,
'total_items'  => $total_items,
'total_page'   => $total_page
);
$this->render_admin( 'received-messages.php', $vars );
}
/**
* Callback function for rendering subscribers page
*
* @return void
*/
public function manage_subscribers() {
$page = ( !isset($_GET['p']) || !is_numeric($_GET['p']) ) ? 1 : $_GET['p'];
$total_items = TXTIMPACT_Subscribers::get_count();
$page_size  = 20;
$total_page = ceil( $total_items / $page_size );
$vars = array(
'subscribers'  => TXTIMPACT_Subscribers::fetch_all( $page, $page_size ),
'current_page' => $page,
'page_size'    => $page_size,
'total_items'  => $total_items,
'total_page'   => $total_page
);
$this->render_admin( 'subscribers.php', $vars );
}
/**
* Hook the load action for subscribe page
*
* @return void
*/
public function load_subscribers_action() {
if ( ! isset($_POST[ '_txtimpact_nonce' ]) )
return;
$wp_function = self::get_wp_functions();
$wp_function->check_admin_referer( 'txtimpact-subscribe-action', '_txtimpact_nonce' );
$subscribers_ids = isset ($_POST['subscriber_ids']) ? $_POST['subscriber_ids'] : array();
$action  = ( isset($_POST['action']) && $_POST['action'] == 'delete' );
$action2 = ( isset($_POST['action2']) && $_POST['action2'] == 'delete' );
if( $action || $action2 ) {
if( empty($subscribers_ids) ) {
$this->set_admin_notice( __('Please select subscribers to delete.', 'txtimpact') );
$wp_function->wp_redirect( txtimpact_current_page_url(), true );
}
$result = TXTIMPACT_Subscribers::delete( $subscribers_ids );
$this->set_admin_notice( sprintf( __( 'Deleted %d subscribers.', 'txtimpact' ), $result ) );
$wp_function->wp_redirect( txtimpact_current_page_url(), true );
}
}
/**
* Hook the load action for received message page
*
* @return void
*/
public function load_received_messages_action() {
if ( ! isset($_POST[ '_txtimpact_nonce' ]) )
return;
$wp_function = self::get_wp_functions();
$wp_function->check_admin_referer( 'txtimpact-received-message-action', '_txtimpact_nonce' );
$messages_ids = isset ($_POST['message_ids']) ? $_POST['message_ids'] : array();
$action  = ( isset($_POST['action']) && $_POST['action'] == 'delete' );
$action2 = ( isset($_POST['action2']) && $_POST['action2'] == 'delete' );
if( $action || $action2 ) {
if( empty($messages_ids) ) {
$this->set_admin_notice( __('Please select messages to delete.', 'txtimpact') );
$wp_function->wp_redirect( txtimpact_current_page_url(), true );
}
$result = TXTIMPACT_Received_messages::delete( $messages_ids );
$this->set_admin_notice( sprintf( __( 'Deleted %d messages.', 'txtimpact' ), $result ) );
$wp_function->wp_redirect( txtimpact_current_page_url(), true );
}
}
/**
* Hook the load action for sent message page
*
* @return void
*/
public function load_sent_messages_action() {
if ( ! isset($_POST[ '_txtimpact_nonce' ]) )
return;
$wp_function = self::get_wp_functions();
$wp_function->check_admin_referer( 'txtimpact-sent-message-action', '_txtimpact_nonce' );
$messages_ids = isset ($_POST['message_ids']) ? $_POST['message_ids'] : array();
$action  = ( isset($_POST['action']) && $_POST['action'] == 'delete' );
$action2 = ( isset($_POST['action2']) && $_POST['action2'] == 'delete' );
if( $action || $action2 ) {
if( empty($messages_ids) ) {
$this->set_admin_notice( __('Please select messages to delete.', 'txtimpact') );
$wp_function->wp_redirect( txtimpact_current_page_url(), true );
}
$result = TXTIMPACT_Sent_messages::delete( $messages_ids );
$this->set_admin_notice( sprintf( __( 'Deleted %d messages.', 'txtimpact' ), $result ) );
$wp_function->wp_redirect( txtimpact_current_page_url(), true );
}
}
/**
* Hook for wp_ajax_txtimpact_subscribe. prcess subscibe form
*
* @return void
*/
public function ajax_subscribe() {
$response = array( 'success' => true );
//filter phoen number
$phone_number = trim( $_POST['phone_number'] );
$phone_number = preg_replace('/[^\d]/', '', $phone_number);
$exist_phone_number = TXTIMPACT_Subscribers::fetch_row_by_phone_number($phone_number);
if( ! empty($exist_phone_number) && $exist_phone_number->opt_out == 1)
$response = array(
'success' => false,
'messages' => __('That phone number is opted out from txtimpact Texting services', 'txtimpact')
);
elseif( !empty($exist_phone_number) )
$response = array(
'success' => false,
'messages' => __('That phone number is already subscribed to this list', 'txtimpact')
);
else
TXTIMPACT_Subscribers::save_number($phone_number);
header( "Content-Type: application/json" );
echo json_encode( $response );
exit;
}
/**
* Hook the transition_post_status for sending notifications
* @param string $new_status Transition to this post status.
* @param string $old_status Previous post status.
* @param object $post Post data.
*/
public function transition_post_status($new_status, $old_status, $post) {
if( $new_status == 'publish' && $old_status != 'publish' && (bool)$this->get_option('txtimpact_new_post') ) {
$message  = $this->get_original_message( $this->new_post_message(), $post );
txtimpact_send_sms_to_subscribers( $message );
} 
}
/**
* setup the default notification message template if message not set
*
* @return string
*/
protected function new_post_message() {
$default = sprintf( __( 'New %1$s post: %2$s', 'mbe2s' ), '{blog_name}', '{post_url}' );
return stripslashes( $this->get_option( 'txtimpact_new_post_message', $default ) );
}
/**
* get  a Random Key 
*
* @return string
*/
function createRandomKey($number){
$keyset  = "abcdefghijklmABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
$randkey = "";
for ($i=0; $i<$number; $i++)
$randkey .= substr($keyset, rand(0, strlen($keyset)-1), 1);
return $randkey;	
}
/**
* prepare message to send via sms
*
* @param string $message
* @param object $post Post data.
* @return string original message to send
*/
protected function get_original_message($message, $post = null) {
$search = array(
'{blog_name}',
'{blog_url}'
);
$replace = array(
get_bloginfo('name'),
home_url()
);
if( $post ) {
$account = new WP_User( $post->post_author );
$search[] = '{post_author}';
$search[] = '{post_title}';
$search[] = '{post_url}';
$replace[] = $account->display_name;
$replace[] = get_the_title( $post->ID );
$replace[] = $this->get_post_short_link( $post->ID );
}
return str_replace( $search, $replace, $message );
}
/**
* Save settings from the Options page.
*
* @return void
**/
protected function save_settings() {
$wp_function = self::get_wp_functions();
$wp_function->check_admin_referer( 'txtimpact-save-settings', '_txtimpact_nonce' );
$api = new TXTIMPACT_Sending($_POST['txtimpact_user'], $_POST['txtimpact_password'], $_POST['txtimpact_vasid']);
if( (bool) @$_POST['txtimpact_new_post'] ) {
if( empty($_POST['txtimpact_new_post_message']) ) {
$this->update_option( 'txtimpact_new_post', false );
$this->update_option( 'txtimpact_new_post_message', '' );
$this->set_admin_error( __("The Message field is required", 'txtimpact') );
$wp_function->wp_redirect(txtimpact_current_page_url());
return;
}
}
$this->update_option( 'txtimpact_user', ( isset($_POST['txtimpact_user']) ? $_POST['txtimpact_user'] : '' ) );
$this->update_option( 'txtimpact_password', ( isset($_POST['txtimpact_password']) ? $_POST['txtimpact_password'] : '' ) );
$this->update_option( 'txtimpact_vasid', ( isset($_POST['txtimpact_vasid']) ? $_POST['txtimpact_vasid'] : '' ) );
$this->update_option( 'txtimpact_new_post', (bool) @$_POST['txtimpact_new_post'] );
$this->update_option( 'txtimpact_new_post_message', ( isset( $_POST['txtimpact_new_post_message'] ) ? $_POST['txtimpact_new_post_message'] : '') );
$this->set_admin_notice( __( 'Settings saved.', 'txtimpact' ) );
}
/**
* Rendering confirmation page
*/
protected function confirm_delete_uninstall() {
$wp_function = self::get_wp_functions();
$wp_function->check_admin_referer( 'txtimpact-delete-uninstall', '_txtimpact_nonce' );
if ( ! $wp_function->current_user_can( 'activate_plugins' ) )
$wp_function->wp_die( __( 'Sorry, you are not allowed to deactivate plugins.', 'txtimpact' ) );
$html = $this->capture_admin( 'confirm-delete-uninstall.php', array() );
$wp_function->wp_die( $html, __( 'Confirm uninstall and delete!', 'txtimpact' ), array( 'response' => 200 ) );
}
/**
* Rendering delete plugin page
*/
protected function delete_uninstall() {
$wp_function = self::get_wp_functions();
$wp_function->check_admin_referer( 'txtimpact-confirm-delete-uninstall', '_txtimpact_nonce' );
if ( ! $wp_function->current_user_can( 'activate_plugins' ) )
$wp_function->wp_die( __( 'Sorry, you are not allowed to deactivate plugins.', 'txtimpact' ) );
TXTIMPACT_Subscribers::uninstall();
delete_option( $this->name );
$wp_function->deactivate_plugins( $this->folder . '/txtimpact-texting-sms-notifications.php' );
$wp_function->wp_die( sprintf( __( "txtimpact Texting: The SMS notifications plugin has been deactivated and all its data has been deleted; return to the <a href='%s'>Dashboard</a>.", 'txtimpact' ), admin_url() ), __( 'Confirm uninstall and delete!', 'txtimpact' ), array( 'response' => 200 ) );
}
/**
*  Callback function for unsubscribe request
*
* @return void
*/
protected function maybe_unsubscribe_request()
{
if ( ! isset ($_REQUEST['txtimpact-unsubscribe']) )
return;
$vars = array();
$removed = false;
if( isset($_POST['txtimpact-phone-number']) ) {
$vars['txtimpact_phone_number'] = $_POST['txtimpact-phone-number'];
$phone_number = trim( $_POST['txtimpact-phone-number'] );
$phone_number = preg_replace( '/[^\d]/', '', $phone_number );
$subscriber = TXTIMPACT_Subscribers::fetch_row_by_phone_number($phone_number);
if( $subscriber )
$removed = TXTIMPACT_Subscribers::delete( $subscriber->ID );                    
else 
$vars['error'] = sprintf( __( "We could not find the phone number %s to unsubscribe.", 'txtimpact' ), $vars['txtimpact_phone_number'] );
}
if ( $removed )
$html = $this->capture( 'unsubscribed.php', $vars );
else 
$html = $this->capture( 'unsubscribe.php', $vars );
$wp_function = self::get_wp_functions();
$wp_function->wp_die( $html, __( 'Unsubscribe from SMS Notifications', 'txtimpact' ), array( 'response' => 200 ) );
}
protected function get_post_short_link($id) {
return ( ( '' != get_option('permalink_structure') ) ?  wp_get_shortlink($id) : get_permalink($id) );
}
public static function set_wp_functions(W2A_Functions $class) {
self::$_wp_functions = $class;
}
public static function get_wp_functions() {
if( empty(self::$_wp_functions) )
self::$_wp_functions = new W2A_Functions();
return self::$_wp_functions;
}
}
$txtimpact = new TXTIMPACT();

?>