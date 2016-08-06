<?php defined('is_running') or die('Not an entry point...'); ?>

<?php global $langmessage; ?>

<div class="SimpleBlogSEO_EditConfig">

<h2>
	<a href="<?php echo common::GetUrl('Admin_SimpleBlogSEO_EditConfig'); ?>">
		SimpleBlogSEO - <?php echo $langmessage['Settings']; ?>
	</a>
</h2>

<?php if (empty($this->_id2slug)) : ?>
<div style="padding: 5px 0 5px;">
Please note that SEF Urls will be automatically recreated at the next <a href="<?php echo common::GetUrl('Admin_SimpleBlogSEO_EditConfig'); ?>">click</a>.
</div>
<?php endif; ?>

<form action="<?php echo common::GetUrl('Admin_SimpleBlogSEO_EditConfig'); ?>" method="post" name="SimpleBlogSEO_EditConfig">

<div class="collapsible">

<h4 class="head one"><a href="#" name="collapsible">Info</a></h4>

<div class="collapsearea">
<table cellpadding="4" class="bordered configuration">

<tr>
<td style="white-space:nowrap">SimpleBlog Post Count</td>
<td><?php echo $this->_post_count; ?></td>
</tr>

<tr>
<td style="white-space:nowrap">SEF Urls Count</td>
<td><?php echo count($this->_id2slug); ?> </td>
</tr>

<tr>
<td style="white-space:nowrap">SEF Urls Coverage</td>
<td><?php echo $this->_sef_cover; ?></td>
</tr>

<tr>
<td style="white-space:nowrap">Hits Count</td>
<td><?php echo array_sum($this->_hits); ?></td>
</tr>

</table>
</div>

<h4 class="head one"><a href="#" name="collapsible">SEF Urls</a></h4>

<div class="collapsearea nodisplay">
<table cellpadding="4" class="bordered configuration">

<tr>
<td style="white-space:nowrap">Url Rewriting</td>
<td>
<input type="hidden" name="url_rewriting" value="0" <?php echo ($this->_config['url_rewriting'] == 0 ? 'checked="checked"' : ''); ?> /> 
<input type="checkbox" name="url_rewriting" value="1" <?php echo ($this->_config['url_rewriting'] == 1 ? 'checked="checked"' : ''); ?> /> 
</td>
</tr>

<tr>
<td style="white-space:nowrap">Url Rewriting - Custom Delimiters</td>
<td>
<input type="text" name="url_rewriting_replace" value="<?php echo $this->_config['url_rewriting_replace']; ?>"  />
<br />List of characters (without any separator) that should be considered as equivalent to a blank space while "slugifying" a post title (e.g.: *.`)
</td>
</tr>

<tr>
<td style="white-space:nowrap">Url Rewriting - Words Case</td>
<td>
<select name="url_rewriting_case" style="width: 225px;">
	<?php $case_list = array('', 'lc', 'uc'); ?>
	<?php $case_list_text = array('lc' => 'Lowercase All', 'uc' => 'Lowercase All + Uppercase First'); ?>
	<?php foreach ($case_list as $case) : ?>
		<option value="<?php echo $case; ?>" 
			<?php echo ($this->_config['url_rewriting_case'] == $case ? 'selected="selected"' : ''); ?>>
			<?php echo (!empty($case) ? $case_list_text[$case] : 'Unchanged'); ?>
		</option>
	<?php endforeach; ?>
</select>
</td>
</tr>

</table>
</div>

<h4 class="head one"><a href="#" name="collapsible">Redirections</a></h4>

<div class="collapsearea nodisplay">
<table cellpadding="4" class="bordered configuration">

<tr>
<?php if($this->_config['url_rewriting']) : ?>
<td style="white-space:nowrap">Redirect non-SEF to SEF Urls (301)</td>
<?php else : ?>
<td style="white-space:nowrap">Redirect SEF to non-SEF Urls (301)</td>
<?php endif; ?>
<td>
<input type="hidden" name="url_redirect" value="0" <?php echo ($this->_config['url_redirect'] == 0 ? 'checked="checked"' : ''); ?> />
<input type="checkbox" name="url_redirect" value="1" <?php echo ($this->_config['url_redirect'] == 1 ? 'checked="checked"' : ''); ?> />
Only works for not logged in visitors (e.g. standard human visitors and search engine robots)
</td>
</tr>

<tr>
<td style="white-space:nowrap">Redirect Not Found Urls (404)</td>
<td>
<input type="hidden" name="not_found_redirect" value="0" <?php echo ($this->_config['not_found_redirect'] == 0 ? 'checked="checked"' : ''); ?> />
<input type="checkbox" name="not_found_redirect" value="1" <?php echo ($this->_config['not_found_redirect'] == 1 ? 'checked="checked"' : ''); ?> />
Same as above
</td>
</tr>

<tr>
<td style="white-space:nowrap">Redirect Old SEF Urls to New SEF Urls (301)</td>
<td>
<input type="hidden" name="old_sefurl_redirect" value="0" <?php echo ($this->_config['old_sefurl_redirect'] == 0 ? 'checked="checked"' : ''); ?> />
<input type="checkbox" name="old_sefurl_redirect" value="1" <?php echo ($this->_config['old_sefurl_redirect'] == 1 ? 'checked="checked"' : ''); ?> />Turn this option on if you previously used an older version of SimpleBlogSEO (< 1.3)
</td>
</tr>

</table>
</div>


<h4 class="head one"><a href="#" name="collapsible">Post Title Rewriting</a></h4>

<div class="collapsearea nodisplay">
<table cellpadding="4" class="bordered configuration">

<tr>
<td style="white-space:nowrap">Convert h2 to h1</td>
<td>
<input type="hidden" name="h2_rewriting" value="0" <?php echo ($this->_config['h2_rewriting'] == 0 ? 'checked="checked"' : ''); ?> />
<input type="checkbox" name="h2_rewriting" value="1" <?php echo ($this->_config['h2_rewriting'] == 1 ? 'checked="checked"' : ''); ?> />
If checked, post titles will be wrapped into a h1 tag (this setting only impacts the single post pages, not the main blog page)
</td>
</tr>

<tr>
<td style="white-space:nowrap">Remove Title Link</td>
<td>
<input type="hidden" name="remove_link" value="0" <?php echo ($this->_config['remove_link'] == 0 ? 'checked="checked"' : ''); ?> />
<input type="checkbox" name="remove_link" value="1" <?php echo ($this->_config['remove_link'] == 1 ? 'checked="checked"' : ''); ?> />
If checked, post titles will no longer be clickable (this setting only impacts the single post pages, not the main blog page)
</td>
</tr>

</table>
</div>


<h4 class="head one"><a href="#" name="collapsible">Generate Meta Data</a></h4>

<div class="collapsearea nodisplay">
<table cellpadding="4" class="bordered configuration">

<tr>
<td style="white-space:nowrap">Generate Description</td>
<td>

<select name="meta_desc" style="width: 300px;">
	<?php $desc_list = array(0, 1, 2); ?>
	<?php $desc_list_text = array(0 => 'No', 1 => 'Yes - Use first paragraph', 2 => 'Yes - Use first sentence of the first paragraph'); ?>
	<?php foreach ($desc_list as $desc) : ?>
		<option value="<?php echo $desc; ?>" 
			<?php echo ($this->_config['meta_desc'] == $desc ? 'selected="selected"' : ''); ?>>
			<?php echo $desc_list_text[$desc]; ?>
		</option>
	<?php endforeach; ?>
</select>
</td>
</tr>

<tr>
<td style="white-space:nowrap">Generate Keywords</td>
<td>
<input type="hidden" name="meta_keywords" value="0" <?php echo ($this->_config['meta_keywords'] == 0 ? 'checked="checked"' : ''); ?> /> 
<input type="checkbox" name="meta_keywords" value="1" <?php echo ($this->_config['meta_keywords'] == 1 ? 'checked="checked"' : ''); ?> />
A keyword is a text fragment wrapped in a &lt;span class="keywords"&gt;text fragment&lt;span/&gt; tag
</td>
</tr>

<tr>
<td style="white-space:nowrap">Remove Keywords Tags from Posts</td>
<td>
<input type="hidden" name="remove_keywords_tags" value="0" <?php echo ($this->_config['remove_keywords_tags'] == 0 ? 'checked="checked"' : ''); ?> />
<input type="checkbox" name="remove_keywords_tags" value="1" <?php echo ($this->_config['remove_keywords_tags'] == 1 ? 'checked="checked"' : ''); ?> />
If checked, the keywords html wrappers will be removed from the text sent to the web browser (but of course not from the text stored in your gpEasy install)
</td>
</tr>

</table>
</div>


<h4 class="head one"><a href="#" name="collapsible">Hits Counter</a></h4>

<div class="collapsearea nodisplay">
<table cellpadding="4" class="bordered configuration">

<tr>
<td style="white-space:nowrap">Count Hits</td>
<td>
<input type="hidden" name="count_hits" value="0" <?php echo ($this->_config['count_hits'] == 0 ? 'checked="checked"' : ''); ?> />
<input type="checkbox" name="count_hits" value="1" <?php echo ($this->_config['count_hits'] == 1 ? 'checked="checked"' : ''); ?> />
Only not logged in visitors are taken into account
</td>
</tr>

<tr>
<td style="white-space:nowrap">Show Hits</td>
<td>
<input type="hidden" name="show_hits" value="0" <?php echo ($this->_config['show_hits'] == 0 ? 'checked="checked"' : ''); ?> />
<input type="checkbox" name="show_hits" value="1" <?php echo ($this->_config['show_hits'] == 1 ? 'checked="checked"' : ''); ?> />
The number of hits will be displayed right next to the other information just below the post title
</td>
</tr>

</table>
</div>


<h4 class="head one"><a href="#" name="collapsible">Gadget - Most Read Blog Posts</a></h4>

<div class="collapsearea nodisplay">
<table cellpadding="4" class="bordered configuration">

<tr>
<td style="white-space:nowrap">List Type</td>
<td>
<select name="gadget_list_type">
	<option value="ul" <?php echo ($this->_config['gadget_list_type'] == 'ul' ? 'selected="selected"' : ''); ?>>
		Unordered List (UL)
	</option>
	<option value="ol" <?php echo ($this->_config['gadget_list_type'] == 'ol' ? 'selected="selected"' : ''); ?>>
		Ordered List (OL)
	</option>
</select>
</td>
</tr>

<tr>
<td style="white-space:nowrap">Maximum Number of Items</td>
<td>
<select name="gadget_list_length" style="width: 50px;">
	<?php $limit_list = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15); ?>
	<?php foreach ($limit_list as $limit) : ?>
		<option value="<?php echo $limit; ?>" 
			<?php echo ($this->_config['gadget_list_length'] == $limit ? 'selected="selected"' : ''); ?>>
			<?php echo $limit; ?>
		</option>
	<?php endforeach; ?>
</select>
</td>
</tr>

<tr>
<td style="white-space:nowrap">Title Excerpt</td>
<td>
<input type="hidden" name="gadget_title_excerpt" value="0" <?php echo ($this->_config['gadget_title_excerpt'] == 0 ? 'checked="checked"' : ''); ?> />
<input type="checkbox" name="gadget_title_excerpt" value="1" <?php echo ($this->_config['gadget_title_excerpt'] == 1 ? 'checked="checked"' : ''); ?> />
</td>
</tr>

<tr>
<td style="white-space:nowrap">Title Excerpt Lenght</td>
<td>
<select name="gadget_excerpt_lenght" style="width: 50px;">
	<?php for ($i=1; $i<100; $i++) : ?>
		<option value="<?php echo $i; ?>" 
			<?php echo ($this->_config['gadget_excerpt_lenght'] == $i ? 'selected="selected"' : ''); ?>>
			<?php echo $i; ?>
		</option>
	<?php endfor; ?>
</select>
</td>
</tr>

</table>
</div>

</div><!-- Collapsible -->

<div style="margin:1em 0">

<input type="submit" name="save_config" value="<?php echo gpOutput::SelectText('Save Configuration'); ?>" class="gpsubmit" style="float:left" />
<?php if (count($this->_hits)) : ?>
<input type="submit" onclick="if(!confirm('Reset hits counter?')){return false;}" name="reset_hits" value="<?php echo gpOutput::SelectText('Reset Hits Counter'); ?>" class="gpsubmit" style="float:right" />
<?php endif; ?>
<?php if (/* $this->_is_dev_install && */count($this->_id2slug)) : ?>
<input type="submit" onclick="if(!confirm('Delete all SEF urls?')){return false;}" name="clear_sefurls" value="<?php echo gpOutput::SelectText('Clear SEF Urls'); ?>" class="gpsubmit" style="float:right" />
<?php endif; ?>

</div>

</form>

</div>
