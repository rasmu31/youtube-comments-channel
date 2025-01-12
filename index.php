<?php

error_reporting(E_ALL);

/***********************************************************************
 * @name  AnyWhereInFiles
 * @author Faisal Shaikh
 * @abstract This project is to find out a part of string from anywhere in any folder
 * @version 1.4
 * @package anywhereinfiles
 *
 *************************************************************************/

function getChannelInfos($youtube_url_channel) {
	$channelInfos = array('idchannel' => '', 'username' => '', 'name' => '');

	$pageDocument = @file_get_contents($youtube_url_channel);
	if ($pageDocument !== false) {
		// Parse it to get only ytInitialData and in json format
		// We want :  start : var ytInitialData = { end : }};<script
		preg_match('/(var ytInitialData = {.*}};<\/script)/', $pageDocument, $matches, PREG_OFFSET_CAPTURE);
		if (count($matches) > 0) {
			$find = $matches[0][0];	
			$ytInitialData = str_replace("var ytInitialData = ", "", $find);
			$ytInitialData = str_replace(";</script", "", $ytInitialData);
		
			$json = json_decode($ytInitialData, false);
			$channelInfos['idchannel'] = $json->metadata->channelMetadataRenderer->externalId;
			$channelInfos['username'] = str_replace("http://www.youtube.com/", "", $json->metadata->channelMetadataRenderer->vanityChannelUrl);
			$channelInfos['name'] = $json->microformat->microformatDataRenderer->title;
		}
	}
	
	return $channelInfos;
}

function path2url($file, $Protocol='https://') {
    return $Protocol.$_SERVER['HTTP_HOST'].str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);
}

function getDirContents($dir, &$results = array()) {
	ini_set('max_execution_time', 300);   
	
	$files = scandir($dir);
	foreach ($files as $key => $value) {
		if ($value != "." && $value != ".." && $value != ".htaccess") {
			$path = realpath($dir . DIRECTORY_SEPARATOR . $value);
			if (!is_dir($path)) {				
				$results[] = $path;
			} 
			else {
				getDirContents($path, $results);
			}
		}
	}  

	return $results;
}

function search_in_files($files = array(), &$resultsFiles = array(), $count = 0) {
	if (!isset($_POST['case']))
		$string = strtolower($_POST['search']);
	else
		$string = $_POST['search'];
	
	foreach ($files as $key => $value) {
		if (!isset($_POST['case']))
			$content = strtolower(file_get_contents($value));
		else
			$content = file_get_contents($value);

		if (strpos($content, $string) !== false) {
			$resultsFiles[] = array("filename" => $value, "count" => substr_count($content, $string));
			$count++;
		}
	}
		
	return array("countFiles" => $count, "result" => $resultsFiles);
}

// Default printing : case of no search or search with bad parameters
function print_alldirs_files($dir_files) {
	$resultsFiles = getDirContents($dir_files);
	echo '<div class="has_table"><table>';
	echo '<tr><td><input id="all_files" type="checkbox" name="all_files"><label for="all_files">All files</label></td></tr>';
	foreach ($resultsFiles as $r) {
		echo '<tr>';
		echo '<td class="filename">
		<input type="checkbox" class="files_checkbox" value="' . basename($r) . '" name="files[]">' . 
		'<a target="_blank" href="' . path2url($r) . '">' . basename($r) . '</a></td>';
		echo '</tr>';
	}
	echo '</table></div></form>';
}

$root_url = "https://example.fr/";
$app_url = $root_url . "youtubecomments/";
$name_dir_files = "youtubecomments/files/";
$base_url = $root_url . $name_dir_files;

$root_dir = "/home/user/example.fr/";
$dir_files = $root_dir . $name_dir_files;

if (isset($_POST['getIdChannel']) && isset($_POST['username']) && $_POST['username'] != "") {
	// To have a json without \u, we use JSON_UNESCAPED_UNICODE, see flags here https://www.php.net/manual/en/json.constants.php#119565
	echo json_encode(getChannelInfos("https://youtube.com/" . $_POST['username']), JSON_UNESCAPED_UNICODE);
	exit(0);
}

if (isset($_POST['getUsername']) && isset($_POST['idchannel']) && $_POST['idchannel'] != "") {
	echo json_encode(getChannelInfos("https://youtube.com/channel/" . $_POST['idchannel']), JSON_UNESCAPED_UNICODE);
	exit(0);
}

?>

<!doctype html>
<html lang="fr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Search in Youtube comments</title>
	<link rel="stylesheet" href="app.css">
	<script src="jquery-latest.min.js"></script>
    <script src="app.js"></script>
</head>
<body>
<div id="content">
	<h2>Search in Youtube comments</h2>
	<div>
		<b>Source code :</b> <a href="https://github.com/rasmu31/youtube-comments-channel">https://github.com/rasmu31/youtube-comments-channel</a>
	</div>

	<?php if (! isset($_POST['submit']) || (isset($_POST['submit']) && (!isset($_POST['search']) || $_POST['search'] == "")) || (!isset($_POST['files']) || !is_array($_POST['files']) || count($_POST['files']) == 0)):?>
	<h3>Match id channel to @handle</h3>
	<div>If you want to get YT id channel from @handle, type @handle in form below :</div>
	<form id="form_getIdChannel" action="" method="POST">
		<input type="text" id="username" name="" class="" required>
		<input type="submit" id="getIdChannel" name="getIdChannel">
		<div id="result_idchannel"></div>
	</form>
	<br>
	
	<div>On the contrary, if you want @handle, type YT id channel here :</div>
	<form id="form_getUsername" action="" method="POST">
		<input type="text" id="idchannel" name="" class="" required>
		<input type="submit" id="getUsername" name="getUsername">
		<div id="result_username"></div>
	</form>
	
	<br>
	
	<h3>Perform a search :</h3>
	<form action="<?php echo $app_url;?>" method="POST" id="searchForm">
		<div class="has_table">
			<table id="searchTable">		
				<tr>
					<td class="head"><label for="search">Terms</label></td>
					<td id="searchBoxes">
						<input id="search" class="inputvalue" type="text" name="search" value="<?php if (isset($_POST['search'])) echo $_POST['search']; ?>" required>
					</td>
				</tr>
				<tr>
					<td class="head"><label for="case">Case sensitive</label></td>
					<td><input id="case" type="checkbox" name="case"<?php if (isset($_POST['case'])) echo "checked "; ?>></td>
				</tr>
				<tr>
					<td class="head"><label for="case">Channels</label></td>
					<td><select id="channels" name="channels" multiple>
						<!-- Add channels Add the channels whose comments you have exported 
							<option value="channel id">(display what you want) I print @handle channel id</option>
						-->
						</select>
					</td>
				</tr>
				<tr>
					<td></td>
					<td class="td_button_submit">
						<input id="submitbutton" type="submit" name="submit" value="Rechercher">
						<?php if (isset($_POST['submit'])): ?>
							<?php if (!isset($_POST['search']) || $_POST['search'] == ""): ?>
							<div class="errorForm">You need to type a term to search for.</div>
							<?php endif; ?>
							<?php if (!isset($_POST['files']) || !is_array($_POST['files']) || count($_POST['files']) == 0): ?>
							<div class="errorForm">You need to select at leat one file.</div>
							<?php endif; ?>
						<?php endif; ?>
					</td>
				</tr>
			
			</table>
		</div>
		<br>

	<?php endif;?>

	<?php
	if (isset($_POST['submit'])) {
		if (isset($_POST['search']) && $_POST['search'] != "") {
			if (isset($_POST['files']) && is_array($_POST['files']) && count($_POST['files']) > 0) {
				echo '<br><a id="button_link" href="#" onclick="history.back();">Get back to search</a>';
				echo '<h3>Results for "' . $_POST['search'] . '" (' . (isset($_POST['case']) ? "case sensitive" : "not case sensitive") . ') :</h3>';

				$list_files = array();
				foreach ($_POST['files'] as $f) {
					// Check if $f is in $dir_files and file exists
					if (dirname($dir_files . $f) . DIRECTORY_SEPARATOR == $dir_files && file_exists($dir_files . $f))
						$list_files[] = $dir_files . $f;
				}
				
				$resultsFiles = search_in_files($list_files);
			
				if($resultsFiles["countFiles"] == 0) 
					echo "No result found.";
				else {
					echo '<div class="has_table"><table>';
						foreach ($resultsFiles["result"] as $r) {
							echo '<tr>';
							echo '<td class="filename">
							<a target="_blank" href="' . path2url($r["filename"]) . '">' . basename($r["filename"]) . '</a></td>';
							echo '<td class="occurences"><span class="label">' . $r["count"] . '</span></td>';
							echo '</tr>';
						}
					echo '</table></div>';
				}
			}
			else {
				print_alldirs_files($dir_files);
			
			}
		}
		else {
			print_alldirs_files($dir_files);
		}
	}
	else {
		print_alldirs_files($dir_files);
	}

	?>

</div>
</body>
</html>