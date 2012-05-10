<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<div class="wrap">
<h2><?php _e( 'SMS Messages', 'txtimpact' )?></h2>
<form id="txtimpact_message_list_form" method="post" action="">
<?php wp_nonce_field( 'txtimpact-sent-message-action', '_txtimpact_nonce' ); ?>
<div class="txtimpact_messages_container">
<div class="tablenav top">
<div class="alignleft actions">
<select name="action">
<option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'txtimpact' ); ?></option>
<option value="delete"><?php _e( 'Delete', 'txtimpact' ); ?></option>
</select>
<input type="submit" name="submit" class="button-secondary action" value="Apply">
</div>
<?php echo txtimpact_build_pager_controll($current_page, $page_size, $total_items, $total_page,"message");?>
</div>
<table class="wp-list-table widefat fixed" cellspacing="0" cellpadding="0">
<thead>
<tr>
<th scope="col" id="cb" class="manage-column check-column colum-checker"><input type="checkbox"/></th>
<th scope="col" class="manage-column column-created"><span><?php _e( 'Sent Date&Time', 'txtimpact' )?></span></th>
<th scope="col" class="manage-column column-message"><span><?php _e( 'Message', 'txtimpact' )?></span></th>
<th scope="col" class="manage-column column-open"><span><?php _e( 'Phone Numbers', 'txtimpact' )?></span></th>
</tr>
</thead>
<tfoot>
<tr>
<th scope="col" class="manage-column check-column colum-checker"><input type="checkbox"/></th>
<th scope="col" class="manage-column column-created"><span><?php _e( 'Sent Date&Time', 'txtimpact' )?></span></th>
<th scope="col" class="manage-column column-message"><span><?php _e( 'Message', 'txtimpact' )?></span></th>
<th scope="col" class="manage-column column-open"></th>
</tr>
</tfoot>
<tbody id="the-list">
<?php 
foreach($messages as $message):?>
<tr class="alternate <?php echo ( $message->opt_out ) ? 'txtimpact_opt_out' : ''?>">
<th scope="row" class="check-column colum-checker"><input type="checkbox" name="message_ids[]" value="<?php echo $message->ID?>"/></th>
<td><?php echo date_i18n('F j, Y g:i a' ,strtotime($message->created));?></td>
<td><?php echo $message->message?></td>
<td class="arrow-msg arrow-closed-msg"><span></span></td>
</tr>
<tr><td colspan="3" class="content-message"><?php echo $message->phone_numbers?></td></tr>
<?php endforeach ?>
</tbody>
</table>
<div class="tablenav bottom">
<div class="alignleft actions">
<select name="action2">
<option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'txtimpact' ); ?></option>
<option value="delete"><?php _e( 'Delete', 'txtimpact' ); ?></option>
</select>
<input type="submit" name="submit" class="button-secondary action" value="Apply">
</div>
<?php echo txtimpact_build_pager_controll($current_page, $page_size, $total_items, $total_page,"message");?>
</div>
</div>
</form>
</div>