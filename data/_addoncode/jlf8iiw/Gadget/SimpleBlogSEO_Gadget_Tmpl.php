<?php defined('is_running') or die('Not an entry point...'); ?>

<h3><?php echo gpOutput::SelectText('Most Read Blog Posts'); ?></h3>

<<?php echo $this->_config['gadget_list_type']; ?> id="SimpleBlogSEO_gadget">
<?php foreach ($this->_hits as $id => $hits) : ?>
    <li>
		<a href="<?php echo common::GetUrl('Special_Blog','id='.$id); ?>" 
			<?php echo ($this->_config['gadget_title_excerpt'] ? 'title="'.$this->_createTitle($id, false).'"' : ''); ?>
		>
			<?php echo $this->_createTitle($id); ?>
		</a>
		<?php echo sprintf(gpOutput::SelectText('(read %s times)'), $hits); ?>
	</li>
<?php endforeach; ?>
</<?php echo $this->_config['gadget_list_type']; ?>>





