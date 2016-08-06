<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	 <title>Aktueller Rundbrief</title>
</head>
<body>
	<?php

		function curPageURL() {
			$pageURL = 'http';
			if(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
				$_SERVER['HTTPS']='on'; 
			}
			if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {$pageURL .= 's';}
			$pageURL .= '://';
			if (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != '80') {
				$pageURL .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
			} else {
				$pageURL .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			}
			return $pageURL;
		}

		$url = curPageURL();
	
		function HrefEncode($path) {
			global $url;
			if (strpos($path, '://') !== false) return $path;
			$dir = dirname($path);
			if ($dir != '') $dir .= '/';
			
			return ltrim(dirname($url), '.') . '/' . $dir . rawurlencode(basename($path));
		}

		
		$files = glob('data/_uploaded/file/Rundbriefe/*.[pP][dD][fF]');
		$last = 0;
		$newest = '';
		foreach ($files as $file) {
				$created = filemtime($file);
				if ($created > $last) {
					$last = $created;
					$newest = HrefEncode($file);
				}
		}
		if ($newest != '') {
		?>
			<object data="<?php echo $newest ?>" type="application/pdf" style="width:100%; height:1060px;">
				<embed src="<?php echo $newest ?>" type="application/pdf" />
			</object>
		<?php } ?>
</body>
</html>
</body>
</html>
