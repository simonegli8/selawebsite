<?php defined('is_running') or die('Not an entry point...'); ?>

<?php global $addonRelativeCode, $langmessage; ?>

<div style="margin:1em 0">

<span style="font-size:large">Forms List</span>
<br/>

<?php if (count($this->forms)) : ?>

<table style="width:100%" class="bordered">

<tr style="text-align:left">
	<th>#</th>
	<th>Name</th>
	<th>Published</th>
	<th>Hits</th>
	<th>Spams</th>
	<th>%</th>
	<th>&nbsp;</th>
</tr>

<?php foreach ($this->forms as $i => $form) : ?>

<tr>
	<td><?php echo $i; ?></td>
	<td style="word-wrap:break-word; max-width:150px">
		<?php echo common::Link('Admin_AntiSpamSFS_ManageForms',$form['name'],'cmd=edit_form&amp;formid='.$i,'title="'.$langmessage['edit'].'"'); ?>
	</td>
	<td>
	<a href="#" onclick="document.forms['AntiSpamSFS_FormsList'].cmd.value='published_switch';document.forms['AntiSpamSFS_FormsList'].formid.value='<?php echo $i; ?>';document.forms['AntiSpamSFS_FormsList'].submit();return false;">
		<?php echo ($form['published'] ? 'Yes' : 'No'); ?>
	</a> 
	</td>
	<td><?php echo $form['count_hits']; ?></td>
	<td><?php echo ($form['count_spammers'] ? '<a href="Admin_AntiSpamSFS_ShowLog?search_key=formid&search_val=' . $i . '">' . $form['count_spammers'] . '</a>' : $form['count_spammers']); ?></td>
	<td>
		<?php $percent = $this->_calcPercent($form['count_spammers'], $form['count_hits'], $this->config['percent_decimals']); ?>
		<?php if ($this->config['percent_color']) : ?>
		<span style="color: <?php echo $this->_percent2Color($percent, $this->config['percent_threshold']); ?>">
		<?php endif; ?>
		<?php echo $percent; ?>
		<?php if ($this->config['percent_color']) : ?>
		</span>
		<?php endif; ?>
	</td>
	<td>
	<a href="#" onclick="if(confirm('Delete the selected form ?')){document.forms['AntiSpamSFS_FormsList'].cmd.value='delete_form';document.forms['AntiSpamSFS_FormsList'].formid.value='<?php echo $i; ?>';document.forms['AntiSpamSFS_FormsList'].submit();};return false;">
		<img src="<?php echo $addonRelativeCode; ?>/Images/log-trash.png" title="Delete the selected items" />
	</a> 
	</td>
</tr>



<?php endforeach; ?>

</table>
<br/>

<form name="AntiSpamSFS_FormsList" method="post" action="<?php echo $this->root_url; ?>"> 
	<input type="hidden" name="cmd" value="delete_form"> 
	<input type="hidden" name="formid" value=""> 
	<input type="hidden" name="verified" value="<?php echo common::new_nonce('post',true); ?>"/>
</form>

<?php else : ?>

<p>No form found!</p>

<?php endif; ?>

<br/><br/>
<?php echo common::Link('Admin_AntiSpamSFS_ManageForms','New form','cmd=new_form'); ?>
</div>
