<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<p><?php _e( 'Please enter your mobile number below and select "Unsubscribe" to unsubscribe from all SMS notifications from this site.', 'txtimpact' ); ?></p>
<form action="" method="post">
<input type="hidden" name="txtimpact-unsubscribe" value="1" />
<?php if ( $error ) : ?>
<p style="color: #c00; background-color: #ffc; border-radius: 4px; -moz-border-radius: 4px; -webkit-border-radius: 4px; padding: 5px"><?php echo esc_html( $error ); ?></p>
<?php endif; ?>
<p>
<label for="txtimpact-phone-number">
<?php _e( 'Your phone number:', 'txtimpact' ); ?><br />
<input type="text" name="txtimpact-phone-number" value="<?php echo esc_attr( $txtimpact_phone_number ); ?>" id="txtimpact-phone-number"  />
</label>
</p>
<p><input type="submit" name="submit" value="<?php esc_attr_e( 'Unsubscribe', 'txtimpact' ); ?>" /></p>

</form>