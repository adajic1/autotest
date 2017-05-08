<?php
	// Accepts 2 parameters
	// + optional custom parameters for new autotest file 
	//  (if there are no custom parameters passed, fields will be empty - check function getDefDecodedJson)
	// 'fileData' -> path to the file
	// 'adv' -> show advanced options (optional parameter, 1 or 0)
	$ifNoThenCreate=1; // Existance of the file 'fileData'. If there is no such file, it will be created.
	require 'functions.php';
	$json=json_decode(file_get_contents($fileData), true);
	$numATs=count($json["test_specifications"]);
	$advanced=getIntVar("adv"); // Show additional options in forms, or not
	if ($advanced===NULL) $advanced=0;
	if ($advanced!=0) $advanced=1;
?>
<!DOCTYPE html>
<html>
<head>
	<title>Autotest editor</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="description" content="ATeditor">
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="jquery-1.11.3.js"></script>
	<script type="text/javascript">
		var advanced=<?php print $advanced; ?>;
	</script>
	<script src="functions.js" type="text/javascript"></script>
</head>
<body style="margin: 5px; padding: 10px;" onload="otherHistory();">
<font class="simpleText">
Number of autotests in the moment of loading: <font color="red"><?php print $numATs; ?> </font>
</font>
<input type="hidden" id="history" value="0">

<form action="api.php" method="post" id="eraseFile">
	<input type="hidden" name="mod" value="1">
	<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
	<input type="hidden" id="fileData" name="fileData" value="<?php print $fileData; ?>">
</form>
<form action="edit_1.php" method="post" id="addingAutotest">
	<input type="hidden" name="mod" value="4">
	<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
	<input type="hidden" id="fileData" name="fileData" value="<?php print $fileData; ?>">
</form>
<form action="edit.php" method="post" id="editFile">
	<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
	<input type="hidden" id="fileData" name="fileData" value="<?php print $fileData; ?>">
</form>

<form action="api.php" method="post" id="editFile">
	<input type="hidden" value="5" name="mod">
	<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
	<input type="hidden" value="<?php print $fileData; ?>" name="fileData" id="fileData">
	<table cellspacing="0" cellpadding="0" class="niceTable" style="background-color: #BBCCFF;">
		<tr bgcolor="#DDDDDD">
			<td class="simpleText tableCell smaller stronger darkBlueColor" align="left" style="border-right: none;">ID <font color="red"><?php print $json["id"]; ?></font> | Custom settings for ATs below</td>
			<td align="left" class="simpleText tableCell">
			<input type="button" value="Edit all" title="Edit custom settings and autotests" onclick="document.getElementById('editFile').submit();">
			<input type="button" value="Delete the file with autotests" title="Deletion of the file with autotests" onclick="document.getElementById('eraseFile').submit();">		
			<input type='button' onclick='safeLinkBackForw(-1);' value='Back'>	
			<input name="adv_button" onclick="showAdvanced();" type="button" value="<?php if ($advanced) print "Hide additional options"; else print "Show additional options"; ?>">
			</td>
		</tr>
		<tr>
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Name of the task</td>
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;"><input type="text" value='<?php print $json["name"]; ?>' name="name" style="width: 700px;"></td>	
		</tr>
		<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Programming language</td>
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;"><input type="text" value='<?php print $json["language"]; ?>' name="language" style="width: 150px;"></td>	
		</tr>
		<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Required compiler</td>
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;"><input type="text" value='<?php print $json["required_compiler"]; ?>' name="required_compiler" style="width: 150px;"></td>			
		</tr>
		<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Preferred compiler</td>	
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;"><input type="text" value='<?php print $json["preferred_compiler"]; ?>' name="preferred_compiler" style="width: 150px;"></td>		
		</tr>		
		<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Compiler options</td>
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;"><input type="text" value='<?php print $json["compiler_options"]; ?>' name="compiler_options" style="width: 700px;"></td>			
		</tr>	
		<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Compiler options debug</td>
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;"><input type="text" value='<?php print $json["compiler_options_debug"]; ?>' name="compiler_options_debug" style="width: 700px;"></td>			
		</tr>
		<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Compiler features<br>(array of strings like: "1", "2")</td>	
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
			<input type="text" value='<?php 
				$features=$json["compiler_features"];
				for ($i=0; $i<count($features); $i++) {
					print "\"".$features[$i]."\"";
					if ($i<count($features)-1) print ", ";
				}
			?>' name="compiler_features" style="width: 700px;">
			</td>		
		</tr>
		<tr>
			<td colspan=2 class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none;">
			Compile 
			<?php if ($json["compile"]=="true") print "<input type='checkbox' checked name='compile'>"; else print "<input type='checkbox' name='compile'>"; ?> | 
			Run
			<?php if ($json["run"]=="true") print "<input type='checkbox' checked name='run'>"; else print "<input type='checkbox' name='run'>"; ?>	|	
			Test
			<?php if ($json["test"]=="true") print "<input type='checkbox' checked name='test'>"; else print "<input type='checkbox' name='test'>"; ?> |
			Debug
			<?php if ($json["debug"]=="true") print "<input type='checkbox' checked name='debug'>"; else print "<input type='checkbox' name='debug'>"; ?> |
			Profile
			<?php if ($json["profile"]=="true") print "<input type='checkbox' checked name='profile'>"; else print "<input type='checkbox' name='profile'>"; ?>
			</td>		
		</tr>
		<tr>
			<td colspan=2 class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none;">
			<input type="submit" value="Confirm changes in custom settings">
			</td>
		</tr>
	</table><br>
</form>
<?php
	for ($i=1; $i<=$numATs; $i++) {
		$thisAT=$json["test_specifications"][$i-1];			
		?>
			<table cellspacing="0" cellpadding="0" class="niceTable" style="background-color: #EFFFFF;">
			<tr bgcolor="#ffbdbd">
				<td class="simpleText tableCell smaller stronger darkBlueColor" width="1px" style="border-right: none;">ID <font color="red"><?php print $thisAT['id']; ?></font> | Autotest <font color="red"><?php print $i; ?></font></td>
				<td align="left" class="simpleText tableCell">
					<form action="edit_1.php" method="post" style="display: inline; margin: 0; padding: 0;">
						<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
						<input type="hidden" name="id" value="<?php print $thisAT['id']; ?>">
						<input type="hidden" name="fileData" value="<?php print $fileData; ?>">
						<input type="submit" value="Edit this autotest">
					</form>
					<form action="api.php" method="post" style="display: inline; margin: 0; padding: 0;">
						<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
						<input type="hidden" name="id" value="<?php print $thisAT['id']; ?>">
						<input type="hidden" name="fileData" value="<?php print $fileData; ?>">
						<input type="hidden" name="mod" value="3">
						<input type="submit" value="Delete this autotest">
					</form>
					<input type="button" onclick="toTop();" value="Scroll to the top">
					<input type="button" onclick="toBottom();" value="Scroll to the bottom">
					<input type="button" value="Add new autotest" onclick="document.getElementById('addingAutotest').submit();">
				</td>
			</tr>
			<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
				<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Require symbols</td>	
				<td class="simpleText tableCell stronger greenColorMono" align="left" style="border-top: none; background-color: #FFFFFF;">
				<?php
					$require_symbols=$thisAT["require_symbols"];
					for ($j=0; $j<count($require_symbols); $j++) {
						print '"'.previewformat($require_symbols[$j]).'"';
						if ($j<count($require_symbols)-1) print ', ';
					}
				?>
				</td>		
			</tr>
			<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
				<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Replace symbols</td>	
				<td class="simpleText tableCell stronger greenColorMono" align="left" style="border-top: none; background-color: #FFFFFF;">
				<?php
					$replace_symbols=$thisAT["replace_symbols"];
					for ($j=0; $j<count($replace_symbols); $j++) {
						print '"'.previewformat($replace_symbols[$j]).'"';
						if ($j<count($replace_symbols)-1) print ', ';
					}
				?>
				</td>		
			</tr>
			<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
				<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Global top</td>	
				<td class="simpleText tableCell stronger greenColorMono" align="left" style="border-top: none; background-color: #FFFFFF;"><?php print previewformat($thisAT["global_top"]); ?></td>		
			</tr>
			<tr>
				<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Global above main</td>	
				<td class="simpleText tableCell stronger greenColorMono" align="left" style="border-top: none; background-color: #FFFFFF;"><?php print previewformat($thisAT["global_above_main"]); ?></td>		
			</tr>	
			<tr>
				<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Code</td>	
				<td class="simpleText tableCell stronger greenColorMono" align="left" style="border-top: none; background-color: #FFFFFF;"><?php print previewformat($thisAT["code"]); ?></td>		
			</tr>
			<tr>
				<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Input</td>	
				<td class="simpleText tableCell stronger greenColorMono" align="left" style="border-top: none; background-color: #FFFFFF;"><?php print previewformat($thisAT["running_params"]["stdin"]); ?></td>		
			</tr>
			<tr>
				<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none; border-right: none;" bgcolor="#fafdbb">Expected output</td>
				<td class="simpleText tableCell stronger greenColorMono" align="left" style="border-top: none; background-color: #FFFFFF;">
				<?php
					$expected=$thisAT["expected"];
					for ($j=0; $j<count($expected); $j++) {							
						?>
						<font class="darkBlueColor">Variant <span id="var_<?php print ($j+1); ?>"><?php print ($j+1); ?></span></font><br>
						<?php
						print previewformat($expected[$j])."<br>";
					}
				?>
				</td>
			</tr>			
			<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
				<td colspan=2 class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none;">
				Timeout
				<input disabled type="text" value='<?php print $thisAT["running_params"]["timeout"]; ?>' name="timeout_<?php print $i; ?>" style="width: 100px;">
				Memory
				<input disabled type="text" value='<?php print $thisAT["running_params"]["vmem"]; ?>' name="vmem_<?php print $i; ?>" style="width: 100px;">
				</td>			
			</tr>
			<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
				<td colspan=2 class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none;">
				Expected exception
				<?php if ($thisAT["expected_exception"]=="true") print "<input type='checkbox' disabled checked name='expected_exception_".$i."'>"; else print "<input type='checkbox' disabled name='expected_exception_".$i."'>"; ?> |
				Expected crash
				<?php if ($thisAT["expected_crash"]=="true") print "<input type='checkbox' disabled checked name='expected_crash_".$i."'>"; else print "<input type='checkbox' disabled name='expected_crash_".$i."'>"; ?> |
				Ignore whitespace
				<?php if ($thisAT["ignore_whitespace"]=="true") print "<input type='checkbox' disabled checked name='ignore_whitespace_".$i."'>"; else print "<input type='checkbox' disabled name='ignore_whitespace_".$i."'>"; ?> |
				Regex
				<?php if ($thisAT["regex"]=="true") print "<input type='checkbox' disabled checked name='regex_".$i."'>"; else print "<input type='checkbox' disabled name='regex_".$i."'>"; ?> |
				Substring
				<?php if ($thisAT["substring"]=="true") print "<input type='checkbox' disabled checked name='substring_".$i."'>"; else print "<input type='checkbox' disabled name='substring_".$i."'>"; ?>
				</td>	
			</tr>
			</table><br>
		<?php
	}
?>
<input type="button" value="Add new autotest" onclick="document.getElementById('addingAutotest').submit();">

<center><br>
	<font style="font-family: arial;" size="2" color="#555555">
	Autotest editor for C9 WebIDE by Armin Dajić<br>
	© Elektrotehnički fakultet Sarajevo / Faculty of Electrical Engineering in Sarajevo 2015-2017.
	</font>	
</center>

<script type='text/javascript'>
	// For FF we need custom pageshow event, because onload event won't fire after 'Back'
	if (browser.name.indexOf("Firefox") != -1) {
        $(window).bind('pageshow', function() {
            // Firefox doesn't reload the page when the user uses the back button, or when we call history.go.back().
            // Doc: https://developer.mozilla.org/en-US/docs/Listening_to_events_in_Firefox_extensions 
            FFhistory();
        }); 
    }
</script>
</body></html>