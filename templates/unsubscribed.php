<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<p>
<?php printf( __( 'The phone number %s has been successfully unsubscribed.', 'txtimpact'), $txtimpact_phone_number ); ?>&nbsp;
<a href="<?php echo home_url()?>"><?php _e( 'Go home!', 'txtimpact')?></a>
</p>