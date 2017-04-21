<?php
	// Accepts 2 parameters + optional custom settings for new autotest file 
	//  (if there are no custom parameters passed, fields will be empty - check function getDefDecodedJson)
	// 'fileData' -> path to the file;
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
	<span id="variant_template" style="display: none;">
		<span id="variant_NUMI_NUMJ">
			<div style="height:1px; visibility:hidden; margin:0;"></div>
			<font class="darkBlueColor">Variant <span id="num_NUMI_NUMJ">NUMJ</span></font>
			<input type="button" value="Delete" id="erase_NUMI_NUMJ" onclick="eraseVariant(this.id);">
			<br>
			<textarea rows="3" cols="80" name="expected_NUMI_NUMJ"></textarea>
			<div style="height:1px; visibility:hidden; margin:0;"></div>
		</span>
	</span>
	<span id="atTemplate" style="display: none;">
		<span id="atTable_NUMI">
			<table cellspacing="0" cellpadding="0" class="niceTable" style="background-color: #DDEEFF;">
			<tr bgcolor="#ffbdbd">
				<td class="simpleText tableCell smaller stronger darkBlueColor" style="border-right: none;">Autotest <font color="red" id="atNum_NUMI">NUMI</font></td>
				<td align="left" class="simpleText tableCell">
				<input type="button" value="Delete this autotest" id="atErase_NUMI" onclick="atErase(this.id);">
				<input type="button" onclick="toTop();" value="Scroll to the top">
				<input type="button" onclick="toBottom();" value="Scroll to the bottom">
				<input type="button" value="Add new autotest" onclick="addingAt();">						
				</td>
			</tr>
			<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
				<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Require symbols<br>(array of strings like: "1", "2")</td>	
				<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
				<input type="text" value='' name="require_symbols_NUMI" style="width: 650px;">
				</td>		
			</tr>
			<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
				<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Replace symbols<br>(array of strings like: "1", "2")</td>	
				<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
				<input type="text" value='' name="replace_symbols_NUMI" style="width: 650px;">
				</td>		
			</tr>
			<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
				<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Global top</td>	
				<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
					<textarea rows="2" cols="80" name="global_top_NUMI"></textarea>
				</td>		
			</tr>
			<tr>
				<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Global above main</td>	
				<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
					<textarea rows="2" cols="80" name="global_above_main_NUMI"></textarea>
				</td>		
			</tr>
			<tr>
				<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Code</td>	
				<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
					<textarea rows="6" cols="80" name="code_NUMI"></textarea>
				</td>		
			</tr>				
			<tr>
				<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Input</td>	
				<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
					<textarea rows="2" cols="80" name="stdin_NUMI"></textarea>
				</td>		
			</tr>
			<tr>
				<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none; border-bottom: none; border-right: none;" bgcolor="#fafdbb">Expected output</td>
				<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none; border-bottom: none;" id="cell_NUMI">
					<input type="hidden" value="1" name="numVar_NUMI">
					<span id="variant_NUMI_1">
						<div style="height:1px; visibility:hidden; margin:0;"></div>
						<font class="darkBlueColor">Variant <span id="num_NUMI_1">1</span></font>
						<input type="button" value="Delete" id="erase_NUMI_1" onclick="eraseVariant(this.id);">
						<br>
						<textarea rows="3" cols="80" name="expected_NUMI_1"></textarea>
						<div style="height:1px; visibility:hidden; margin:0;"></div>
					</span>
				</td>
			</tr>
			<tr>
				<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none; border-right: none;" bgcolor="#fafdbb">&nbsp;</td>	
				<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
					<input type="button" value="Add a variant" id="addVar_NUMI" onclick="addVariant(this.id);">
				</td>
			</tr>	
			<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
				<td colspan=2 class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none;">
				Timeout
				<input type="text" value='10' name="timeout_NUMI" style="width: 100px;">
				Memory
				<input type="text" value='1000000' name="vmem_NUMI" style="width: 100px;">
				</td>					
			</tr>
			<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
				<td colspan=2 class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none;">
				Expected exception	
				<input type="checkbox" name="expected_exception_NUMI"> |
				Expected crash
				<input type="checkbox" name="expected_crash_NUMI"> |
				Ignore whitespace
				<input type="checkbox" name="ignore_whitespace_NUMI"> |
				Regex
				<input type="checkbox" name="regex_NUMI"> |
				Substring
				<input type="checkbox" name="substring_NUMI">
				</td>
			</tr>			
			</table><br>						
		</span>
	</span>
	
	<form action="api.php" method="post" id="eraseFile">
		<input type="hidden" name="mod" value="1">
		<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
		<input type="hidden" value="<?php print $fileData; ?>" name="fileData" id="fileData">
	</form>
	<form action="preview.php" method="post" id="previewFile">
		<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
		<input type="hidden" id="fileData" name="fileData" value="<?php print $fileData; ?>">
	</form>

	<form action="api.php" method="post" id="editFile">
	<input type="hidden" value="0" name="mod" id="mod">
	<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
	<input type="hidden" value="<?php print $fileData; ?>" name="fileData" id="fileData">
	<table cellspacing="0" cellpadding="0" class="niceTable" style="background-color: #BBCCFF;">
		<tr bgcolor="#DDDDDD">
			<td class="simpleText tableCell smaller stronger darkBlueColor" align="left" style="border-right: none;">ID <font color="red"><?php print $json["id"]; ?></font> | Custom settings for ATs below</td>
			<td align="left" class="simpleText tableCell">
			<input type='button' onclick="document.getElementById('previewFile').submit();" value='Preview all'>
			<input type="button" value="Delete the file with autotests" title="Deletion of the file with autotests" 
			onclick="document.getElementById('eraseFile').submit();">
			<input type='button' onclick='safeLinkBackForw(-1);' value='Back'>		
			<input name="adv_button" onclick="showAdvanced();" type="button" value="<?php if ($advanced) print "Hide additional options"; else print "Show additional options"; ?>">
			</td>
		</tr>
		<tr>
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Name of the task</td>
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top:none;"><input type="text" value='<?php print $json["name"]; ?>' name="name" style="width: 700px;"></td>	
		</tr>
		<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Programming language</td>
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top:none;"><input type="text" value='<?php print $json["language"]; ?>' name="language" style="width: 150px;"></td>	
		</tr>
		<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Required compiler</td>
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top:none;"><input type="text" value='<?php print $json["required_compiler"]; ?>' name="required_compiler" style="width: 150px;"></td>			
		</tr>
		<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Preferred compiler</td>	
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top:none;"><input type="text" value='<?php print $json["preferred_compiler"]; ?>' name="preferred_compiler" style="width: 150px;"></td>		
		</tr>		
		<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Compiler options</td>
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top:none;"><input type="text" value='<?php print $json["compiler_options"]; ?>' name="compiler_options" style="width: 700px;"></td>			
		</tr>	
		<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Compiler options debug</td>
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top:none;"><input type="text" value='<?php print $json["compiler_options_debug"]; ?>' name="compiler_options_debug" style="width: 700px;"></td>			
		</tr>
		<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
			<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top: none; border-right: none;">Compiler features<br>(array of strings like: "1", "2")</td>	
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top:none;">
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
			<td colspan=2 class="simpleText tableCell smaller stronger" align="left" bgcolor="#ebecfe" style="border-top:none;">
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
	</table><br>
	<input type="hidden" value="<?php print $numATs; ?>" name="numATs">
	<span id="allATs">
		<?php
			for ($i=1; $i<=$numATs; $i++) {
				$thisAT=$json["test_specifications"][$i-1];			
				?>
					<span id="atTable_<?php print $i; ?>">
					<table cellspacing="0" cellpadding="0" class="niceTable" style="background-color: #DDEEFF;">
					<tr bgcolor="#ffbdbd">
						<td class="simpleText tableCell smaller stronger darkBlueColor" style="border-right: none;">Autotest <font color="red" id="atNum_<?php print $i; ?>"><?php print $i; ?></font></td>
						<td align="left" class="simpleText tableCell">
						<input type="button" value="Delete this autotest" id="atErase_<?php print $i; ?>" onclick="atErase(this.id);">
						<input type="button" onclick="toTop();" value="Scroll to the top">
						<input type="button" onclick="toBottom();" value="Scroll to the bottom">
						<input type="button" value="Add new autotest" onclick="addingAt();">					
						</td>
					</tr>
					<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
						<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Require symbols<br>(array of strings like: "1", "2")</td>	
						<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
						<input type="text" value='<?php 
							$require_symbols=$thisAT["require_symbols"];
							for ($j=0; $j<count($require_symbols); $j++) {
								print "\"".$require_symbols[$j]."\"";
								if ($j<count($require_symbols)-1) print ", ";
							}
						?>' name="require_symbols_<?php print $i; ?>" style="width: 650px;">
						</td>		
					</tr>
					<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
						<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Replace symbols<br>(array of strings like: "1", "2")</td>	
						<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
						<input type="text" value='<?php 
							$replace_symbols=$thisAT["replace_symbols"];
							for ($j=0; $j<count($replace_symbols); $j++) {
								print "\"".$replace_symbols[$j]."\"";
								if ($j<count($replace_symbols)-1) print ", ";
							}
						?>' name="replace_symbols_<?php print $i; ?>" style="width: 650px;">
						</td>		
					</tr>
					<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
						<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Global top</td>	
						<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
							<textarea rows="2" cols="80" name="global_top_<?php print $i; ?>"><?php print $thisAT["global_top"] ?></textarea>
						</td>		
					</tr>
					<tr>
						<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Global above main</td>	
						<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
							<textarea rows="2" cols="80" name="global_above_main_<?php print $i; ?>"><?php print $thisAT["global_above_main"] ?></textarea>
						</td>		
					</tr>
					<tr>
						<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Code</td>	
						<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
							<textarea rows="6" cols="80" name="code_<?php print $i; ?>"><?php print $thisAT["code"] ?></textarea>
						</td>		
					</tr>
					<tr>
						<td class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none; border-right: none;">Input</td>	
						<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
							<textarea rows="2" cols="80" name="stdin_<?php print $i; ?>"><?php print $thisAT["running_params"]["stdin"]; ?></textarea>
						</td>		
					</tr>
					<tr>
						<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none; border-bottom: none;border-right: none;" bgcolor="#fafdbb">Expected output</td>
						<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none; border-bottom: none;" id="cell_<?php print $i; ?>">
						<?php
							$expected=$thisAT["expected"];
							?>
							<input type="hidden" value="<?php print (count($expected)); ?>" name="numVar_<?php print $i; ?>">
							<?php
							for ($j=0; $j<count($expected); $j++) {							
								?>
									<span id="variant_<?php print $i; ?>_<?php print ($j+1); ?>">
										<div style="height:1px; visibility:hidden; margin:0;"></div>
										<font class="darkBlueColor">Variant <span id="num_<?php print $i; ?>_<?php print ($j+1); ?>"><?php print ($j+1); ?></span></font>
										<input type="button" value="Delete" id="erase_<?php print $i; ?>_<?php print ($j+1); ?>" onclick="eraseVariant(this.id);">
										<br>
										<textarea rows="3" cols="80" name="expected_<?php print $i; ?>_<?php print ($j+1); ?>"><?php print $expected[$j]; ?></textarea>
										<div style="height:1px; visibility:hidden; margin:0;"></div>
									</span>
								<?php
							}
						?>
						</td>
					</tr>
					<tr>
						<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none; border-right: none;" bgcolor="#fafdbb">&nbsp;</td>	
						<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none;">
							<input type="button" value="Add a variant" id="addVar_<?php print $i; ?>" onclick="addVariant(this.id);">
						</td>
					</tr>
					<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
						<td colspan=2 class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none;">
						Timeout
						<input type="text" value='<?php print $thisAT["running_params"]["timeout"]; ?>' name="timeout_<?php print $i; ?>" style="width: 100px;">
						Memory
						<input type="text" value='<?php print $thisAT["running_params"]["vmem"]; ?>' name="vmem_<?php print $i; ?>" style="width: 100px;">
						</td>			
					</tr>
					<tr name="adv_display" style="<?php if (!$advanced) print "display: none;"; ?>">
						<td colspan=2 class="simpleText tableCell smaller stronger" align="left" bgcolor="#fafdbb" style="border-top: none;">
						Expected exception
						<?php if ($thisAT["expected_exception"]=="true") print "<input type='checkbox' checked name='expected_exception_".$i."'>"; else print "<input type='checkbox' name='expected_exception_".$i."'>"; ?> |
						Expected crash
						<?php if ($thisAT["expected_crash"]=="true") print "<input type='checkbox' checked name='expected_crash_".$i."'>"; else print "<input type='checkbox' name='expected_crash_".$i."'>"; ?> |
						Ignore whitespace
						<?php if ($thisAT["ignore_whitespace"]=="true") print "<input type='checkbox' checked name='ignore_whitespace_".$i."'>"; else print "<input type='checkbox' name='ignore_whitespace_".$i."'>"; ?> |
						Regex
						<?php if ($thisAT["regex"]=="true") print "<input type='checkbox' checked name='regex_".$i."'>"; else print "<input type='checkbox' name='regex_".$i."'>"; ?> |
						Substring
						<?php if ($thisAT["substring"]=="true") print "<input type='checkbox' checked name='substring_".$i."'>"; else print "<input type='checkbox' name='substring_".$i."'>"; ?>
						</td>	
					</tr>
					</table><br>						
					</span>
				<?php
			}
		?>
	</span>
	<div style="height:1px; visibility:hidden; margin:0;"></div>
	<input type="button" value="Add new autotest" onclick="addingAt();"><br><br>
	<input type="submit" value="Confirm all changes">
	</form>	

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
</body>
</html>