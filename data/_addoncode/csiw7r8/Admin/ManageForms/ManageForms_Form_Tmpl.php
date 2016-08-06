<?php defined('is_running') or die('Not an entry point...'); ?>

<?php global $langmessage; ?>

<form name="fform" action="<?php echo common::GetUrl('Admin_AntiSpamSFS_ManageForms'); ?>" method="post" class="AntiSpamSFS_Form">

<fieldset>
<legend>General</legend>

<label>Name</label>
<input type="text" name="name" size="30" maxlength="30" value="<?php echo $this->form['name']; ?>" /><br/>

<label>Published</label>
<input type="radio" name="published" value="1" <?php echo ($this->form['published'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="published" value="0" <?php echo ($this->form['published'] == 0 ? 'checked="checked"' : ''); ?> /> No
<br/>

<label>Description</label>
<textarea name="description" rows="5" cols="50"><?php echo $this->form['description']; ?></textarea>
<br/>

<label>Hook</label>
<select name="hook" id="hook">
	<option value="AntiSpam_Check" <?php echo ($this->form['hook'] == 'AntiSpam_Check' ? 'selected="selected"' : ''); ?>>
		AntiSpam_Check
	</option>
	<option value="PageRunScript" <?php echo ($this->form['hook'] == 'PageRunScript' ? 'selected="selected"' : ''); ?>>
		PageRunScript
	</option>
</select>

</fieldset>

<fieldset>
<legend>Control</legend>

<label>Page Title</label>
<select name="control_title" id="control_title" onchange="$('#control_title_index').html(this.options[selectedIndex].value)">
	<?php global $gp_index; ?>
	<option value="">&nbsp;</option>
	<?php foreach ($gp_index as $title => $index) : ?>
		<?php if( strpos($index,'special_') === 0 ) : ?>
		<option value="<?php echo $index; ?>" 
			<?php echo ($this->form['control_title'] == $index ? 'selected="selected"' : ''); ?>>
			<?php echo common::GetLabel($title); ?>
		</option>
		<?php endif; ?>
	<?php endforeach; ?>
</select>
&nbsp;
<span id="control_title_index">
	<?php echo $this->form['control_title']; ?>
</span>
<br/>

<label><span id="control_cmd_name_editable"><?php echo $this->form['control_cmd_name']; ?></span></label>
<input type="hidden" name="control_cmd_name" id="control_cmd_name" value="<?php echo $this->form['control_cmd_name']; ?>" />
<input type="text" name="control_cmd_value" size="30" value="<?php echo $this->form['control_cmd_value']; ?>" />

</fieldset>

<fieldset>
<legend>Criteria</legend>

<label for="dp">Use Email</label>
<input type="radio" name="criteria_email" value="1" <?php echo ($this->form['criteria_email'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="criteria_email" value="0" <?php echo ($this->form['criteria_email'] == 0 ? 'checked="checked"' : ''); ?> /> No
<br />

<label>Email Alias</label>
<input type="text" name="criteria_email_alias" size="30" value="<?php echo $this->form['criteria_email_alias']; ?>" />
<br/>

<label for="dp">Use Username</label>
<input type="radio" name="criteria_username" value="1" <?php echo ($this->form['criteria_username'] == 1 ? 'checked="checked"' : ''); ?> /> Yes
<input type="radio" name="criteria_username" value="0" <?php echo ($this->form['criteria_username'] == 0 ? 'checked="checked"' : ''); ?> /> No
<br />

<label>Username Alias</label>
<input type="text" name="criteria_username_alias" size="30" value="<?php echo $this->form['criteria_username_alias']; ?>" />

</fieldset>

<input type="hidden" name="count_hits" id="count_hits" value="<?php echo $this->form['count_hits']; ?>" />
<input type="hidden" name="count_spammers" id="count_hits" value="<?php echo $this->form['count_spammers']; ?>" />
<input type="hidden" name="formid" value="<?php echo $this->formid; ?>" />

<input type="hidden" name="cmd" value="save_form" />

<input type="submit" name="save_form" value="<?php echo $langmessage['save']; ?>" class="gpsubmit" />

</form>

