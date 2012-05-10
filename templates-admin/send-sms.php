<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<div class="wrap">
<h2><?php _e('Send SMS Message', 'txtimpact')?></h2>
<form method="post" action="">
<?php wp_nonce_field( 'txtimpact-send-sms', '_txtimpact_nonce' ); ?>
<table class="form-table">
<tr valign="top">
<th scope="row">
<?php _e('Your SMS Message:', 'txtimpact') ?>
<div id="txtimpact_counter">
<?php _e('160 Remaining Characters', 'txtimpact')?>
</div>
</th>
<td>
<textarea id="txtimpact_message" name="txtimpact_message"></textarea>
<div id="txtimpact_message_template">
<p id="txtimpact_blog_length"><span>{blog_name}</span>&nbsp;(<span class="value"><?php echo esc_html($length_blog_name)?></span>) - <?php _e('The blog name', 'txtimpact') ?></p>
<p id="txtimpact_blog_url_length"><span>{blog_url}</span>&nbsp;(<span class="value"><?php echo esc_html($length_blog_url)?></span>) - <?php _e('The homepage of your blog', 'txtimpact') ?></p>
<div class="txtimpact_note">
<?php _e('<strong>Send a text message to all of your subscribers.</strong><br />
Message can be up to a max of 160 characters.
<br/>We do not recommend using non-standard characters such as but not limited to ~ or { or }.', 'txtimpact')?>
</div>
</div>
</td>
</tr>
</table>
<input type="hidden" name="action" value="send-sms" />
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Send Message', 'txtimpact') ?>" />
</p>
</form>
</div>