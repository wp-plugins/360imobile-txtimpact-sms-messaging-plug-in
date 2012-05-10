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
require_once( 'class-txtimpact-widget.php' );
/**
* new WordPress Widget format
* Wordpress 2.8 and above
* @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
*/
class TXTIMPACT_Widget_Subscribe extends TXTIMPACT_Widget {
function  __construct($id_base = false, $name = 'TXTImpact Widget', $widget_options = array(), $control_options = array()) {
$widget_options = array(
'description' => __( 'All your readers to subscribe to SMS updates when you add a post. Requires an TXTImpact account.', 'txtimpact' )
);
parent::__construct('txtimpact-subscribe', __( 'TXTIMPACT: SMS Updates', 'txtimpact' ), $widget_options, $control_options);
$this->load_widget_files();
}
function form($instance) {
extract( $instance, EXTR_SKIP );
if ( empty( $instance ) )
$show_link = true;
if ( empty( $title ) )
$title = __( 'Subscribe To SMS Updates', 'txtimpact' );
if( empty( $info ) )
$info = __( 'We will send you a text message when we post to the blog.', 'txtimpact' );
if ( empty( $success_info ) )
$success_info = __( 'Thank you for joining our text messaging list.', 'txtimpactsubscribe' );
$this->input_text( __( 'Title', 'txtimpact' ), 'title', $title );
$this->textarea( __( 'Description', 'txtimpact' ), 'info', $info, __( 'Add a sentence or two to explain to your customers or members what your text messaging list is all about.', 'txtimpact' ));
$this->textarea( __( 'Successful Signup Message', 'txtimpact' ), 'success_info', $success_info,
__( 'This message will appear after a customer or member has successfully been added to your text messaging list', 'txtimpact' ));
}
function update($new_instance, $old_instance) {
$updated_instance = $new_instance;
return $updated_instance;
}
function widget($args, $instance) {
// outputs the content of the widget
extract( $args );
extract( $instance, EXTR_SKIP );
$title = apply_filters( 'widget_title', $title );
$info  = apply_filters( 'widget_info', $info );
?>
<?php echo $before_widget; ?>
<?php if ( $title ) : ?>
<?php echo $before_title . $title . $after_title; ?>
<?php endif; ?>
<?php if ( $info ) : ?>
<p><?php echo esc_html( $info ); ?></p>
<?php endif; ?>   
<form action="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>" method="post" class="txtimpactsubscribe_form">
<input type="hidden" name="action" value="txtimpact_subscribe" id="action">
<input type="hidden" name="successInfo" value="<?php echo esc_attr( $success_info ); ?>"/>
<p class="txtimpactsubscribe-phoneNumber">
<span><?php _e( 'Enter your mobile number (Country Code Included):', 'txtimpact' ); ?></span>
<input type="text" name="phone_number" value="" id="<?php echo esc_attr( $widget_id ); ?>">
<span class="phoneNumber-error"></span><br/>
<span class="infos">Msg&Data rates may apply</span>
</p>
<p class="txtimpact-subscribe">
<input type="submit" name="subscribe_button" value="<?php echo esc_attr( __( 'Subscribe', 'txtimpact' ) ); ?>" />
|
<a href="<?php echo esc_attr( add_query_arg( array( 'txtimpact-unsubscribe' => 1 ), home_url() ) ); ?>"><?php _e( 'Unsubscribe', 'txtimpact' ); ?></a>
</p>
</form>
<?php echo $after_widget; ?>
<?php
}
public function load_widget_files()
{
if( is_admin() )
return;
$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.dev' : '';
wp_enqueue_script( 'txtimpactsubscribe-admin', txtimpact_url( "/js/widget{$suffix}.js" ), array( 'jquery' ), $this->version );
$localized = array(
);
wp_localize_script( 'txtimpactsubscribe-admin', 'txtimpactsubscribe_widget', $localized );
wp_enqueue_style( 'txtimpactsubscribe-admin', txtimpact_url( "/css/widget{$suffix}.css" ), array(), $this->version, 'all' );
}
}
add_action( 'widgets_init', create_function( '', "register_widget('TXTIMPACT_Widget_Subscribe');" ) );