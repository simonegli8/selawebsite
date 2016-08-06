<?php defined('is_running') or die('Not an entry point...'); ?>

<h3><?php echo gpOutput::SelectText('Latest Blocked Spammers'); ?></h3>

<?php if (strlen($this->config['gadget_form'])) : ?>
	<p><?php echo sprintf(gpOutput::SelectText('The latest spammers identified by their %s while using the %s form:'), $this->config['gadget_criteria'], $this->forms[$this->config['gadget_form']]['name']); ?></p>
<?php else: ?>
	<p><?php echo sprintf(gpOutput::SelectText('The latest spammers identified by their %s:'), $this->config['gadget_criteria']); ?></p>
<?php endif; ?>

<ul id="AntiSpamSFS_gadget">
<?php foreach ($this->log_search as $log) : ?>
    <li>
		<span title="<?php echo $log['title']; ?>"><?php echo $log[$this->config['gadget_criteria']]; ?></span>
		&nbsp;
		<a href="<?php echo $log['link']; ?>" target="_blank">&raquo;</a>
	</li>
<?php endforeach; ?>
</ul>

<p><?php echo sprintf(gpOutput::SelectText('More information on the %s website.'), '<a href="http://www.stopforumspam.com" target="_blank">stopforumspam.com</a>'); ?></p>




