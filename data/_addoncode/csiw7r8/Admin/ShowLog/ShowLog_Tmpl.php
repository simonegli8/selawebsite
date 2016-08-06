<?php defined('is_running') or die('Not an entry point...'); ?>

<?php global $addonRelativeCode, $langmessage; ?>

<h2 style="margin-bottom: 20px;"><?php echo gpOutput::SelectText('Logs'); ?></h2>

<div style="margin: 0 0 5px 0; float: left"> 
<?php if ($this->do_search) : ?>
<?php echo count($this->log) . ' displayed item(s) out of ' . count($this->log_search) . ' found item(s) out of ' . count($this->log_backup) . ' item(s)'; ?>
<?php else: ?>
<?php echo count($this->log) . ' displayed item(s) out of ' . count($this->log_backup) . ' item(s)'; ?>
<?php endif; ?>
</div>

<div style="margin: 0 0 5px 0; float: right"> 

<form name="AntiSpamSFS_delete_log" method="post" action="<?php echo $this->root_url; ?>"> 
	<input type="hidden" name="cmd" value="delete_log"> 
	<input type="hidden" name="page" value="<?php echo $this->page; ?>"> 
	<?php if ($this->do_search) : ?>
	<input type="hidden" name="search_key" value="<?php echo $this->search_key; ?>"> 		
	<input type="hidden" name="search_val" value="<?php echo $this->search_val; ?>"> 	
	<?php endif; ?>
	<?php if ($this->do_sort) : ?>
	<input type="hidden" name="sort_by" value="<?php echo $this->sort_by; ?>"> 		
	<input type="hidden" name="sort_dir" value="<?php echo $this->sort_dir; ?>"> 	
	<?php endif; ?>
	<input type="hidden" name="verified" value="<?php echo common::new_nonce('post',true); ?>"/>
</form>

<a href="#" onclick="if(confirm('Delete the selected items')){document.forms['AntiSpamSFS_delete_log'].submit();};return false;">
	<img src="<?php echo $addonRelativeCode; ?>/Images/log-trash.png" title="Delete the selected items" />
</a> 

</div>

<table style="width:100%; table-layout:fixed; border:1px solid #ccc; padding:1em 1em 1em 1em;">

	<colgroup>
		<col style="width:25%"/>
		<col style="width:20%"/>
		<col style="width:15%"/>
		<col style="width:20%"/>
		<col style="width:20%"/>
	</colgroup>

	<tr>
		<th><a href="<?php echo $this->ordering['email']['url']; ?>">Email</a>&nbsp;<?php echo $this->ordering['email']['arrow']; ?></th>
		<th><a href="<?php echo $this->ordering['username']['url']; ?>">Username</a>&nbsp;<?php echo $this->ordering['username']['arrow']; ?></th>
		<th><a href="<?php echo $this->ordering['ip']['url']; ?>">IP</a>&nbsp;<?php echo $this->ordering['ip']['arrow']; ?></th>
		<th><a href="<?php echo $this->ordering['date']['url']; ?>">Date</a>&nbsp;<?php echo $this->ordering['date']['arrow']; ?></th>
		<th><a href="<?php echo $this->ordering['formid']['url']; ?>">Form</a>&nbsp;<?php echo $this->ordering['formid']['arrow']; ?></th>
	</tr>

	<tr>
		<td style="text-align: center">
		<?php $url = $this->root_url . ($this->sort_query ? '?' . $this->sort_query : ''); ?>
		<?php $html = '<a href="' . $url . '">&rarr;&nbsp;' . $this->search_val . '</a>'; ?>
		<?php echo ($this->do_search && ($this->search_key == 'email') ? $html : '&nbsp;') ?>
		</td>
		<td style="text-align: center">
		<?php echo ($this->do_search && ($this->search_key == 'username') ? $html : '&nbsp;') ?>
		</td>
		<td style="text-align: center">
		<?php echo ($this->do_search && ($this->search_key == 'ip') ? $html : '&nbsp;') ?>
		</td>
		<td style="text-align: center">
		<?php echo ($this->do_search && ($this->search_key == 'date') ? '<a href="' . $this->root_url . '">&rarr;&nbsp;' . strftime('%m/%d/%y', $this->search_val) . '</a>' : '&nbsp;') ?>
		</td>
		<td style="text-align: center">
		<?php echo ($this->do_search && ($this->search_key == 'formid') ? '<a href="' . $url . '">&rarr;&nbsp;' . $this->forms[$this->search_val]['name'] . '</a>' : '&nbsp;') ?>
		</td>
	</tr>

	<?php foreach ($this->log as $item) : ?>

	<tr>
		<td><?php echo $item['email'] ?></td>
		<td><?php echo $item['username'] ?></td>
		<td><?php echo $item['ip'] ?></td>
		<td><?php echo $item['date'] ?></td>
		<td><?php echo $item['formid'] ?></td>
	</tr>

	<?php endforeach; ?>

	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>

</table>

<div style="margin: 5px 0 0 0; float: left"> 

<?php if ($this->pagination['first']) : ?>
<a href="<?php echo $this->pagination['first']; ?>" title="first page">
&lt;&lt;
</a> 
<?php else: ?>
&lt;&lt;
<?php endif; ?>
&nbsp;
<?php if ($this->pagination['prev']) : ?>
<a href="<?php echo $this->pagination['prev']; ?>" title="previous page">
&lt;
</a> 
<?php else: ?>
&lt;
<?php endif; ?>
&nbsp;
<?php echo $this->page . '/' . $this->total; ?>
&nbsp;
<?php if ($this->pagination['next']) : ?>
<a href="<?php echo $this->pagination['next']; ?>" title="next page">
&gt;
</a> 
<?php else: ?>
&gt;
<?php endif; ?>
&nbsp;
<?php if ($this->pagination['last']) : ?>
<a href="<?php echo $this->pagination['last']; ?>" title="last page">
&gt;&gt;
</a> 
<?php else: ?>
&gt;&gt;
<?php endif; ?>

</div>

<div style="margin: 5px 0 0 0; float: right">

<form name="AntiSpamSFS_set_limit" method="post" action="<?php echo $this->root_url; ?>"> 

	<?php echo gpOutput::SelectText('#items per page:'); ?>
	<select name="limit" onchange="document.forms['AntiSpamSFS_set_limit'].submit();">
	<?php $limit_list = array(1, 5, 10, 15, 20, 25, 30, 40, 50, 100); ?>
	<?php foreach ($limit_list as $limit) : ?>
		<option value="<?php echo $limit; ?>" 
		<?php echo ($this->limit == $limit ? 'selected="selected"' : ''); ?>>
		<?php echo $limit; ?>
		</option>
	<?php endforeach; ?>

	</select>
	<?php if ($this->do_search) : ?>
	<input type="hidden" name="search_key" value="<?php echo $this->search_key; ?>"> 		
	<input type="hidden" name="search_val" value="<?php echo $this->search_val; ?>"> 	
	<?php endif; ?>
	<?php if ($this->do_sort) : ?>
	<input type="hidden" name="sort_by" value="<?php echo $this->sort_by; ?>"> 		
	<input type="hidden" name="sort_dir" value="<?php echo $this->sort_dir; ?>"> 	
	<?php endif; ?>
	<input type="hidden" name="cmd" value="set_limit"> 
	<input type="hidden" name="verified" value="<?php echo common::new_nonce('post',true); ?>"/>
</form> 

</div>




