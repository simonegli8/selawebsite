<?php

defined('is_running') or die('Not an entry point...');

require_once('Setup.php');
require_once($addonDataFolder.'/config.php');

function SermonsUploadLink($label) { global $sermonsconfig; echo common::Link('Admin_Browser', $sermonsconfig['upload'], 'dir=%2Fsermons'); }

if (isset($_FILES['file']) && common::LoggedIn()) {
    //save mp3 file
    $name = $_FILES['file']['name'];
    preg_match_all('/^((?P<author>([^.]*)\.)?(?P<topic>[^.]*)(\.(?<date>(\d{1,2})[.-](\d{1,2})[.-](\d{2,4})))?\.[Mm][Pp]3/', $name, $m);
    if (isset($m['author']) && $_POST['author'] == '') $author = $m['author'];
    else $author = $_POST['author'];
    if ($_POST['topic'] == '') $topic = $m['topic'];
    else $topic = $_POST['topic'];
    if (isset($m['date']) && $_POST['date'] == '') $date = $m['date'];
    else $date = $_POST['date'];
    $name = $author.'.'.$topic.'.'.$date.'.mp3';
    move_uploaded_file($_FILES['file']['tmp_name'], $sermonsconfig['path'].'/'.$name);
	 if (isset($_FILES['image'])) {
		 move_uploaded_file($_FILES['file']['tmp_name'], $sermonsconfig['path'].'/'.$author.'.jpg');
	 }
}

class Edit_Sermons {
	function Edit_Sermons() {
		global $sermonsconfig, $addonRelativeCode, $homeurl, $sermonsversion;
?>
<h1>Edit Sermons</h1>

<h2>Upload Sermon</h2>

<form enctype="multipart/form-data" action="__URL__" method="POST">
    <table>
		<tr>
			<td><?php echo  $sermonsconfig['date'] ?></td>
			<td>
				<input type="text" class="input" name="date" value="" style="width:300px;" /></td>
		</tr>
		<tr>
			<td><?php echo  $sermonsconfig['topic'] ?></td>
			<td>
				<input type="text" class="input" name="topic" value="" style="width:300px;" /></td>
		</tr>
		<tr>
			<td><?php echo  $sermonsconfig['preacher'] ?></td>
			<td>
				<input type="text" class="input" name="preacher" value="" style="width:300px;" /></td>
		</tr>
		<tr>
            <td>Picture</td>
            <td>
                <input type="hidden" name="MAX_FILE_SIZE" value="60000000" />
                <input type="file" name="image" size="255" accept="image/jpg" class="plain" />
             </td>
        </tr>
        <tr>
            <td>MP3 File</td>
            <td>
                <input type="hidden" name="MAX_FILE_SIZE" value="60000000" />
                <input type="file" name="file" size="255" accept="audio/mpeg3,audio/x-mpeg-3" class="plain" />
             </td>
        </tr>
    </table>
    
	 <p></p>
    <p>
		<script type="text/javascript">
				<!--
	function toggle_visibility(id) {
		var e = document.getElementById(id);
		if (e.style.display == 'inline')
			e.style.display = 'none';
		else
			e.style.display = 'inline';
	}
	//-->
		</script>
		<input id="submit" type="submit" value="Upload" onclick="toggle_visibility('check');" />
		&nbsp;
		<img id="check" src="<?php echo  $addonRelativeCode.'/loaderB16.gif' ?>" alt="" style="<?php if (!isset($_POST['submit'])) { ?>display:none;<?php } else { ?>display:inline;<?php } ?>" />
	</p>
</form>

<h2>Edit Sermons</h2>

<p><?php echo  SermonsUploadLink("Edit Sermons"); ?></p>

<?php } } ?>