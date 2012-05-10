<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<div class="wrap">
<h2><?php _e('SMS Notification Configuration', 'txtimpact') ?></h2>
<form method="post" action="">
<?php wp_nonce_field( 'txtimpact-save-settings', '_txtimpact_nonce' ); ?>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Username:', 'txtimpact') ?></th>
<td><input type="text" name="txtimpact_user" value="<?php echo  esc_attr( $txtimpact_user ) ?>" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Password:', 'txtimpact') ?></th>
<td><input type="password" name="txtimpact_password" value="<?php echo  esc_attr( $txtimpact_password ) ?>" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('VASId:', 'txtimpact') ?></th>
<td><input type="text" name="txtimpact_vasid" value="<?php echo  esc_attr( $txtimpact_vasid ) ?>" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Callback URL:', 'txtimpact') ?></th>
<td><input type="text" readonly="readonly" name="txtimpact_receive_url" value="<?php print dirname(plugin_dir_url ( __FILE__ ))."/txtimpact-message-handler.php?wpkey=".$txtimpact_security_key;?>"  /> <a href="<?php echo get_admin_url()."admin.php?page=txtimpact_options&newkey=1";?>">Make new key</a></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Notify SMS subscribers:', 'txtimpact') ?></th>
<td>
<fieldset>
<legend class="screen-reader-text"><span><?php _e( 'Notify SMS subscribers', 'txtimpact' ); ?></span></legend>
<label for="txtimpact_new_post">
<input name="txtimpact_new_post" type="checkbox" id="txtimpact_new_post" value="1" <?php echo ( $txtimpact_new_post ) ? 'checked' : ''?>/>
<?php _e( 'When a new post is published', 'txtimpact' ); ?></label><br>
</fieldset>
</td>
</tr>
<tr valign="top" id="txtimpact_new_post_message_row">
<th scope="row">
<?php _e('New Post Message:', 'txtimpact') ?>
<div id="txtimpact_counter">
<?php _e('160 Remaining Characters', 'txtimpact')?>
</div>
</th>
<td>
<textarea id="txtimpact_new_post_message" name="txtimpact_new_post_message"><?php echo esc_textarea($txtimpact_new_post_message)?></textarea>
<div id="txtimpact_message_template">
<p id="txtimpact_blog_length"><span>{blog_name}</span>&nbsp;(<span class="value"><?php echo esc_html($length_blog_name)?></span>) - <?php _e('The blog name', 'txtimpact') ?></p>
<p id="txtimpact_blog_url_length"><span>{blog_url}</span>&nbsp;(<span class="value"><?php echo esc_html($length_blog_url)?></span>) - <?php _e('The homepage of your blog', 'txtimpact') ?></p>
<p id="txtimpact_post_author"><span>{post_author}</span>&nbsp;(<span class="value"><?php echo esc_html($length_post_author)?></span>) - <?php _e('The display name the author of the post', 'txtimpact') ?></p>
<p id="txtimpact_post_title"><span>{post_title}</span>&nbsp;(<span class="value"><?php echo esc_html($length_post_title)?></span>) - <?php _e('The title of the post', 'txtimpact') ?></p>
<p id="txtimpact_post_url"><span>{post_url}</span>&nbsp;(<span class="value"><?php echo esc_html($length_post_url)?></span>) - <?php _e('The web address of the post', 'txtimpact') ?></p>
<div class="txtimpact_note">
<?php _e('Message can be up to a max of 160 characters.
<br/>We do not recommend using non-standard characters such as but not limited to ~ or { or }.', 'txtimpact')?>
</div>
</div>
</td>
</tr>
</table>
<input type="hidden" name="action" value="save-settings" />
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'txtimpact') ?>" />
</p>
</form>
</div>