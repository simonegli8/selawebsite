<?php defined('is_running') or die('Not an entry point...'); ?>

<?php global $langmessage; ?>

<h2 style="margin-bottom: 20px;"><?php echo $langmessage['Settings']; ?></h2>

<form action="<?php echo common::GetUrl('Admin_AntiSpamSFS_EditConfig'); ?>" method="post" class="AntiSpamSFS_EditConfig">

<fieldset>
<legend>General</legend>

<label for="dp">Remote Server Url (read only)</label>
<input type="text" id="base_url" name="base_url" value="<?php echo $this->config['base_url']; ?>" readonly="readonly" style="width:220px;"/>
<br />
<label for="dp">Filter Power</label>
<input type="radio" name="filter_power" value="min" <?php echo ($this->config['filter_power'] == 'min' ? 'checked="checked"' : ''); ?> /> Min
<input type="radio" name="filter_power" value="max" <?php echo ($this->config['filter_power'] == 'max' ? 'checked="checked"' : ''); ?> /> Max

</fieldset>

<fieldset>
<legend>Criteria</legend>

<label for="dp">Use Email</label>
<input type="radio" name="use_email" value="1" <?php echo ($this->config['use_email'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="use_email" value="0" <?php echo ($this->config['use_email'] == 0 ? 'checked="checked"' : ''); ?> /> No
<br />
<label for="dp">Use Username</label>
<input type="radio" name="use_username" value="1" <?php echo ($this->config['use_username'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="use_username" value="0" <?php echo ($this->config['use_username'] == 0 ? 'checked="checked"' : ''); ?> /> No
<br />
<label for="dp">Use IP</label>
<input type="radio" name="use_ip" value="1" <?php echo ($this->config['use_ip'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="use_ip" value="0" <?php echo ($this->config['use_ip'] == 0 ? 'checked="checked"' : ''); ?> /> No

</fieldset>

<fieldset>
<legend>Log</legend>

<label for="dp">Log Spammers</label>
<input type="radio" name="log_spammers" value="1" <?php echo ($this->config['log_spammers'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="log_spammers" value="0" <?php echo ($this->config['log_spammers'] == 0 ? 'checked="checked"' : ''); ?> /> No
<br />		
<label for="dp">Log Spammers Limit</label>
<select name="log_spammers_limit">
	<?php $limit_list = array(5, 10, 15, 20, 25, 30, 40, 50, 100); ?>
	<?php foreach ($limit_list as $limit) : ?>
		<option value="<?php echo $limit; ?>" 
			<?php echo ($this->config['log_spammers_limit'] == $limit ? 'selected="selected"' : ''); ?>>
			<?php echo $limit; ?>
		</option>
	<?php endforeach; ?>
</select>
<br />
<label for="color_not_tested">Color Criteria - Not Tested</label>
<input id="color_not_tested" name="color_not_tested" type="text" value="<?php echo $this->config['color_not_tested']; ?>" />
<br />
<label for="color_positive">Color Criteria - Positive</label>
<input id="color_positive" name="color_positive" type="text" value="<?php echo $this->config['color_positive']; ?>" />
<br />
<label for="color_negative">Color Criteria - Negative</label>
<input id="color_negative" name="color_negative" type="text" value="<?php echo $this->config['color_negative']; ?>" />

</fieldset>

<fieldset>
<legend>Admin Notification</legend>

<label for="dp">Admin Notification</label>
<input type="radio" name="notify" value="1" <?php echo ($this->config['notify'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="notify" value="0" <?php echo ($this->config['notify'] == 0 ? 'checked="checked"' : ''); ?> /> No
<br />		
<label for="dp">Admin Notification - Email</label>
<input type="text" id="notify_email" name="notify_email" value="<?php echo $this->config['notify_email']; ?>" style="width: 220px" />

</fieldset>

<fieldset>
<legend>Form Statistic</legend>

<label for="percent_decimals">Percent - Number of Decimals</label>
<?php $decimals = array(0, 1, 2); ?>
<select name="percent_decimals">
	<?php foreach ($decimals as $decimal) : ?>
		<option value="<?php echo $decimal; ?>" 
			<?php echo ($this->config['percent_decimals'] == $decimal ? 'selected="selected"' : ''); ?>>
			<?php echo $decimal; ?>
		</option>
	<?php endforeach; ?>
</select>

<br />

<label for="percent_color">Percent - Colorization</label>
<input type="radio" name="percent_color" value="1" <?php echo ($this->config['percent_color'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="percent_color" value="0" <?php echo ($this->config['percent_color'] == 0 ? 'checked="checked"' : ''); ?> /> 
No
<br />	

<label for="percent_threshold">Percent - Red vs Blue Threshold</label>
<div id="percent_threshold_slider"></div>
<span id="percent_threshold_slider_value"></span>
<input type="hidden" name="percent_threshold" value="<?php echo $this->config['percent_threshold']; ?>" id="percent_threshold" />
<!--
<select name="percent_threshold">
	<?php for ($i=1;$i<=99;$i++) : ?>
		<option value="<?php echo $i; ?>" 
			<?php echo ($this->config['percent_threshold'] == $i ? 'selected="selected"' : ''); ?>>
			<?php echo $i; ?>
		</option>
	<?php endfor; ?>
</select>
-->

</fieldset>

<fieldset style="padding: 10px; border:1px solid #ccc; margin: 0 0 10px 0;">
<legend>cUrl Timeout Options</legend>

<label for="dp">cUrl Option - Timeout (sec.)</label>
<select name="curlopt_timeout">
	<?php for ($i=1;$i<=10;$i++) : ?>
		<option value="<?php echo $i; ?>" 
			<?php echo ($this->config['curlopt_timeout'] == $i ? 'selected="selected"' : ''); ?>>
			<?php echo $i; ?>
		</option>
	<?php endfor; ?>
</select>
<br />
<label for="dp">cUrl Option - Connexion Timeout (sec.)</label>
<select name="curlopt_connect_timeout">
	<?php for ($i=1;$i<=10;$i++) : ?>
		<option value="<?php echo $i; ?>" 
			<?php echo ($this->config['curlopt_connect_timeout'] == $i ? 'selected="selected"' : ''); ?>>
			<?php echo $i; ?>
		</option>
	<?php endfor; ?>
</select>

</fieldset>

<fieldset>

<legend>Gadget</legend>

<label for="gadget_form">Selected Form</label>
<select name="gadget_form" id="gadget_form">
	<option value="" <?php echo (!strlen($this->config['gadget_form']) ? 'selected="selected"' : ''); ?>>&nbsp;</option>
	<?php foreach ($this->forms as $key => $form) : ?>
		<option value="<?php echo $key; ?>" 
			<?php echo (strval($this->config['gadget_form']) == strval($key) ? 'selected="selected"' : ''); ?>>
			<?php echo $form['name']; ?>
		</option>
	<?php endforeach; ?>
</select>
<br />
<label for="gadget_criteria">Displayed Criteria</label>
<select name="gadget_criteria" id="gadget_criteria">
	<?php $criteria_list = array('email', 'username', 'ip'); ?>
	<?php foreach ($criteria_list as $criteria) : ?>
		<option value="<?php echo $criteria; ?>" 
			<?php echo ($this->config['gadget_criteria'] == $criteria ? 'selected="selected"' : ''); ?>>
			<?php echo $criteria; ?>
		</option>
	<?php endforeach; ?>
</select>
<br />
<label for="gadget_limit">List Limit</label>
<select name="gadget_limit" id="gadget_limit">
	<?php $limit_list = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15); ?>
	<?php foreach ($limit_list as $limit) : ?>
		<option value="<?php echo $limit; ?>" 
			<?php echo ($this->config['gadget_limit'] == $limit ? 'selected="selected"' : ''); ?>>
			<?php echo $limit; ?>
		</option>
	<?php endforeach; ?>
</select>

</fieldset>

<?php if ($this->isSuperAdmin) : ?>
<fieldset>
<legend>User Permissions</legend>

<label for="dp">Save Config</label>
<input type="radio" name="p_save_config" value="1" <?php echo ($this->config['p_save_config'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="p_save_config" value="0" <?php echo ($this->config['p_save_config'] == 0 ? 'checked="checked"' : ''); ?> /> No
<br />
<label for="dp">Save Form</label>
<input type="radio" name="p_save_form" value="1" <?php echo ($this->config['p_save_form'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="p_save_form" value="0" <?php echo ($this->config['p_save_form'] == 0 ? 'checked="checked"' : ''); ?> /> No
<br />
<label for="dp">Delete Form</label>
<input type="radio" name="p_delete_form" value="1" <?php echo ($this->config['p_delete_form'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="p_delete_form" value="0" <?php echo ($this->config['p_delete_form'] == 0 ? 'checked="checked"' : ''); ?> /> No
<br />
<label for="dp">Publish Form</label>
<input type="radio" name="p_publish_form" value="1" <?php echo ($this->config['p_publish_form'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="p_publish_form" value="0" <?php echo ($this->config['p_publish_form'] == 0 ? 'checked="checked"' : ''); ?> /> No
<br />
<label for="dp">Delete Log</label>
<input type="radio" name="p_delete_log" value="1" <?php echo ($this->config['p_delete_log'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="p_delete_log" value="0" <?php echo ($this->config['p_delete_log'] == 0 ? 'checked="checked"' : ''); ?> /> No

</fieldset>
<?php endif; ?>

<input type="submit" name="save_config" value="<?php echo $langmessage['save']; ?>" class="gpsubmit" style="float:left" />

</form>

