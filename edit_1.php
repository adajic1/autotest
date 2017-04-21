<?php
	// 4 parameters:
	// 'mod' -> (optional) if mod=4, first of all a new AT is added
    // 'id' -> id of the autotest we want to edit; if mod=4 this parameter won't be passed because id is generated automatically
	// 'fileData' -> path to the file;
	// 'adv' -> (optional, 1 or 0) show advanced options	
	require 'functions.php';
	$mod=getIntVar("mod"); 
	// mod=4 to add a new autotest
	if ($mod==4) { // Adding 1 autotest
		$json=json_decode(file_get_contents($fileData),true);
		$numATs=count($json["test_specifications"]);
		$lastId=$json["test_specifications"][$numATs-1]['id'];
		$newATjson=json_decode(getDefAT($lastId+1),true);
		$json["test_specifications"][$numATs]=$newATjson;
		saveJson($fileData, $json);	
		$id=$newATjson["id"];
	} else {
		$json=json_decode(file_get_contents($fileData), true);
		$id=getIntVar("id");
	}
	$numATs=count($json["test_specifications"]);
	$advanced=getIntVar("adv"); // Show additional options in forms, or not
	if ($advanced===NULL) $advanced=0;
	if ($advanced!=0) $advanced=1;
	
	// Get ordinal of the autotest with the given id
	$i=0;
	$k=1;
	while ($k<=$numATs) {
		if ($json["test_specifications"][$k-1]["id"]==$id) {	
			$i=$k;
			break;
		}
		$k++;
	}
	if ($i==0) {
		printError("There is no autotest with the given id: $id.");
		finishAll();
	}
	$thisAT=$json["test_specifications"][$i-1];	
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
Name of the task: <font color="red"><?php print $json["name"]; ?></font>
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
		
	<form action="api.php" method="post" id="eraseAutotest">
		<input type="hidden" name="mod" value="3">
		<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
		<input type="hidden" value="<?php print $fileData; ?>" name="fileData" id="fileData">
		<input type="hidden" value="<?php print $id; ?>" name="id" id="id">
	</form>
	
	<form action="api.php" method="post" id="editAutotest">
	<input type="hidden" value="2" name="mod" id="mod">
	<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
	<input type="hidden" value="<?php print $fileData; ?>" name="fileData" id="fileData">
	<input type="hidden" value="<?php print $id; ?>" name="id" id="id">	
	<span id="allATs">
		<span id="atTable_<?php print $i; ?>">
		<table cellspacing="0" cellpadding="0" class="niceTable" style="background-color: #DDEEFF;">
		<tr bgcolor="#ffbdbd">
			<td class="simpleText tableCell smaller stronger darkBlueColor" style="border-right: none;">ID <font color="red"><?php print $id; ?></font> | Autotest <font color="red" id="atNum_<?php print $i; ?>"><?php print $i; ?></font></td>
			<td align="left" class="simpleText tableCell">
			<input type="button" value="Delete" onclick="document.getElementById('mod').value='3';document.getElementById('editAutotest').submit();">
			<input type='button' onclick='safeLinkBackForw(-1);' value='Back'>	
			<input name="adv_button" onclick="showAdvanced();" type="button" value="<?php if ($advanced) print "Hide additional options"; else print "Show additional options"; ?>">			
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
			<td class="simpleText tableCell smaller stronger" align="left" style="border-top: none; border-bottom: none; border-right: none;" bgcolor="#fafdbb">Expected output</td>
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
	</span>
	<div style="height:1px; visibility:hidden; margin:0;"></div>
	<input type="submit" value="Confirm changes">
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