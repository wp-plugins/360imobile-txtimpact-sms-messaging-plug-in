<?php if( !defined( 'ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') )
exit();
delete_option( 'txtimpact' );
delete_option( 'widget_txtimpact-subscribe' );
delete_option( 'txtimpact-subscribers-version' );
delete_option( 'txtimpact-received-messages-version' );
delete_option( 'txtimpact-sent-messages-version' );