<?php
	require 'functions.php';
	$advanced=getIntVar("adv"); // boolean -> display advanced options in forms
	if ($advanced===NULL) $advanced=0;
	if ($advanced!=0) $advanced=1;
	$mod=getIntVar("mod"); 
	// mod=0 to edit the file, 
	// mod=1 to delete the file,
	// mod=2 to edit a single AT
	// mod=3 to erase a single AT
	// mod=4 to add a new AT
	// mod=5 to edit custom settings
	// mod=6 to add a new AT with data
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
	<script src="functions.js" type="text/javascript"></script>
</head>
<body style="margin: 5px; padding: 10px;">
<form action="preview.php" method="post" id="previewFile">
	<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
	<input type="hidden" id="fileData" name="fileData" value="<?php print $fileData; ?>">
</form>
<form action="edit.php" method="post" id="editFile">
	<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
	<input type="hidden" id="fileData" name="fileData" value="<?php print $fileData; ?>">
</form>
<?php
	if ($mod==1) { // Mod to delete a file
		unlink($fileData);
		if (file_exists($fileData)) {
			printError("The file '$fileData' is not deleted!");
			finishAll();
		}
		print "The file is deleted!";
	} else if ($mod==0) { // Mod to edit all ATs together
		// Save all passed values into the file as json
		$json=json_decode(file_get_contents($fileData),true);
		
		$name=getVar("name");
		if ($name!==NULL) {
			$json["name"]=$name;
		}  
		$language=getVar("language");
		if ($language!==NULL) {
			$json["language"]=$language;
		} 
		$required_compiler=getVar("required_compiler");
		if ($required_compiler!==NULL) {
			$json["required_compiler"]=$required_compiler;
		} 
		$preferred_compiler=getVar("preferred_compiler");
		if ($preferred_compiler!==NULL) {
			$json["preferred_compiler"]=$preferred_compiler;
		} 
		$compiler_features=getVar("compiler_features");
		if ($compiler_features!==NULL) {
			$p=json_decode("[$compiler_features]", true);
			$json["compiler_features"]=$p;
		} 
		$compiler_options=getVar("compiler_options");
		if ($compiler_options!==NULL) {
			$json["compiler_options"]=$compiler_options;
		} 	
		$compiler_options_debug=getVar("compiler_options_debug");
		if ($compiler_options_debug!==NULL) {
			$json["compiler_options_debug"]=$compiler_options_debug;
		} 
		
		$compile=getBoolVar("compile");		
		$json["compile"]=$compile;
		
		$run=getBoolVar("run");
		$json["run"]=$run; 
		
		$test=getBoolVar("test");
		$json["test"]=$test;

		$debug=getBoolVar("debug");
		$json["debug"]=$debug;
		
		$profile=getBoolVar("profile");
		$json["profile"]=$profile;
				
		$numATs=getIntVar("numATs");
		if ($numATs===NULL) {
			$numATs=count($json["test_specifications"]);
		} else if ($numATs<0) {
			printError("'numATs' must be >=0.");
			finishAll();
		}
		
		$lastId=0;
		for ($i=$numATs-1; $i>=0; $i--) {
			if (isset($json["test_specifications"][$i]["id"])) {
				$lastId=$json["test_specifications"][$i]["id"];
				break;
			}
		}
		for ($i=0; $i<$numATs; $i++) {
			$lastId++;
			$json["test_specifications"][$i]["id"]=$lastId;
			
			$require_symbols=getVar("require_symbols_".($i+1));
			if ($require_symbols!==NULL) {
				$p=json_decode("[$require_symbols]", true);
				$json["test_specifications"][$i]["require_symbols"]=$p;
			} else if (!isset($json["test_specifications"][$i]["require_symbols"])) {
				$p=json_decode("[]", true);
				$json["test_specifications"][$i]["require_symbols"]=$p;
			} // Otherwise everything stays unchanged
			$replace_symbols=getVar("replace_symbols_".($i+1));
			if ($replace_symbols!==NULL) {
				$p=json_decode("[$replace_symbols]", true);
				$json["test_specifications"][$i]["replace_symbols"]=$p;
			} else if (!isset($json["test_specifications"][$i]["replace_symbols"])) {
				$p=json_decode("[]", true);
				$json["test_specifications"][$i]["replace_symbols"]=$p;
			} // Otherwise everything stays unchanged
			$code=getVar("code_".($i+1));
			if ($code!==NULL) {
				$json["test_specifications"][$i]["code"]=$code;
			} 
			$global_above_main=getVar("global_above_main_".($i+1));
			if ($global_above_main!==NULL) {
				$json["test_specifications"][$i]["global_above_main"]=$global_above_main;
			} 
			$global_top=getVar("global_top_".($i+1));
			if ($global_top!==NULL) {
				$json["test_specifications"][$i]["global_top"]=$global_top;
			} 			
			if (!isset($json["test_specifications"][$i]["running_params"]))
					$json["test_specifications"][$i]["running_params"]=array();
			$timeout=getVar("timeout_".($i+1));
			if ($timeout!==NULL) {					
				$json["test_specifications"][$i]["running_params"]["timeout"]=$timeout;
			} 
			$vmem=getVar("vmem_".($i+1));
			if ($vmem!==NULL) {					
				$json["test_specifications"][$i]["running_params"]["vmem"]=$vmem;
			} 
			$stdin=getVar("stdin_".($i+1));
			if ($stdin!==NULL) {					
				$json["test_specifications"][$i]["running_params"]["stdin"]=$stdin;
			} 
			if (!isset($json["test_specifications"][$i]["expected"]))
				$json["test_specifications"][$i]["expected"]=array();	
			$numVar=getIntVar("numVar_".($i+1));
			if ($numVar===NULL) {
				$numVar=count($json["test_specifications"][$i]["expected"]);
			} else if ($numVar<0) {
				printError("'numVar_x' must be >=0.");
				finishAll();
			}
			for ($k=0; $k<$numVar; $k++) {
				// If there is a set variable, take it and insert it
				// If some variable is not set then just insert "";
				$expected=getVar("expected_".($i+1)."_".($k+1));
				if ($expected!==NULL) {
					$json["test_specifications"][$i]["expected"][$k]=replace_n_ln($expected);
				} else if (!isset($json["test_specifications"][$i]["expected"][$k])) {
					$json["test_specifications"][$i]["expected"][$k]="";
				} // Otherwise it will keep its old value				
			}
			if (count($json["test_specifications"][$i]["expected"])>$numVar) {
				// Erase all other variants!
				array_splice($json["test_specifications"][$i]["expected"], $numVar);
			}
			$expected_exception=getBoolVar("expected_exception_".($i+1));			
			$json["test_specifications"][$i]["expected_exception"]=$expected_exception;			
			$expected_crash=getBoolVar("expected_crash_".($i+1));
			$json["test_specifications"][$i]["expected_crash"]=$expected_crash;
			$ignore_whitespace=getBoolVar("ignore_whitespace_".($i+1));
			$json["test_specifications"][$i]["ignore_whitespace"]=$ignore_whitespace;
			$regex=getBoolVar("regex_".($i+1));
			$json["test_specifications"][$i]["regex"]=$regex;
			$substring=getBoolVar("substring_".($i+1));
			$json["test_specifications"][$i]["substring"]=$substring;								
		}
		if (count($json["test_specifications"])>$numATs) {
			// Erase all additional ATs from the json
			array_splice($json["test_specifications"], $numATs);
		}
		// Json has been created
		saveJson($fileData, $json);	
		print "Editing done successfully!";	
	} else if ($mod==2) { // Edit of a single AT
		// Save accepted data into a file as json
		$json=json_decode(file_get_contents($fileData),true);
		$numATs=count($json["test_specifications"]);
		$id=getIntVar("id");
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
			printError("There is no autotest with the given id: $id. (someone modified the .autotest file). You have 2 options:<br>");
			// Check which ordinal number is passed, just to know how to get passed values for new AT
			foreach($_POST as $key=>$value) {
				$num=intval(strrev($key));
				if ($num!=0) break;
			}
			if ($num==0) {
				foreach($_GET as $key=>$value) {
					$num=intval(strrev($key));
					if ($num!=0) break;
				}
			}	
			// Create a form with AT data ready to be added as a new AT to this file
		    ?>
		    <form action="api.php" method="post" id="addNewAT">
				<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
				<input type="hidden" id="fileData" name="fileData" value="<?php print $fileData; ?>">
				<input type="hidden" id="mod" name="mod" value="6">
				<input type="hidden" id="require_symbols" name="require_symbols" value='<?php print getVar("require_symbols_".$num); ?>'>
				<input type="hidden" id="replace_symbols" name="replace_symbols" value='<?php print getVar("replace_symbols_".$num); ?>'>
				<input type="hidden" id="code" name="code" value="<?php print getVar("code_".$num); ?>">
				<input type="hidden" id="global_above_main" name="global_above_main" value="<?php print getVar("global_above_main_".$num); ?>">
				<input type="hidden" id="global_top" name="global_top" value="<?php print getVar("global_top_".$num); ?>">
				<input type="hidden" id="timeout" name="timeout" value="<?php print getVar("timeout_".$num); ?>">
				<input type="hidden" id="vmem" name="vmem" value="<?php print getVar("vmem_".$num); ?>">
				<input type="hidden" id="stdin" name="stdin" value="<?php print getVar("stdin_".$num); ?>">
				<input type="hidden" id="numVar" name="numVar" value="<?php print getIntVar("numVar_".$num); ?>">
				<?php
				$numVar = getIntVar("numVar_".$num);
				for ($k=0; $k<$numVar; $k++) {
					$vrj=getVar("expected_".$num."_".($k+1));
					print('<input type="hidden" id="expected_'.($k+1).'" name="expected_'.($k+1).'" value="'.$vrj.'">');		
				}
				?>
				<?php 
				$bol=getBoolVar("expected_exception_".$num);
				if ($bol=='true') print "<input type='hidden' name='expected_exception' value='1'>";
				$bol=getBoolVar("expected_crash_".$num);
				if ($bol=='true') print "<input type='hidden' name='expected_crash' value='1'>";
				$bol=getBoolVar("ignore_whitespace_".$num);
				if ($bol=='true') print "<input type='hidden' name='ignore_whitespace' value='1'>";
				$bol=getBoolVar("regex_".$num);
				if ($bol=='true') print "<input type='hidden' name='regex' value='1'>";
				$bol=getBoolVar("substring_".$num);
				if ($bol=='true') print "<input type='hidden' name='substring' value='1'>";
				?>
			</form>
		    <input type='button' onclick="document.getElementById('addNewAT').submit();" value='Add this AT to the end of the .autotest file'>&nbsp;
		    <input type='button' onclick="document.getElementById('previewFile').submit();" value='Ignore AT and go to preview'>
		    <?php
			exit;
		}
		$i--;			
		$require_symbols=getVar("require_symbols_".($i+1));
		if ($require_symbols!==NULL) {
			$p=json_decode("[$require_symbols]", true);
			$json["test_specifications"][$i]["require_symbols"]=$p;
		} else if (!isset($json["test_specifications"][$i]["require_symbols"])) {
			$p=json_decode("[]", true);
			$json["test_specifications"][$i]["require_symbols"]=$p;
		} // Otherwise everything stays as it was
		$replace_symbols=getVar("replace_symbols_".($i+1));
		if ($replace_symbols!==NULL) {
			$p=json_decode("[$replace_symbols]", true);
			$json["test_specifications"][$i]["replace_symbols"]=$p;
		} else if (!isset($json["test_specifications"][$i]["replace_symbols"])) {
			$p=json_decode("[]", true);
			$json["test_specifications"][$i]["replace_symbols"]=$p;
		} // Otherwise everything stays as it was
		$code=getVar("code_".($i+1));
		if ($code!==NULL) {
			$json["test_specifications"][$i]["code"]=$code;
		} 
		$global_above_main=getVar("global_above_main_".($i+1));
		if ($global_above_main!==NULL) {
			$json["test_specifications"][$i]["global_above_main"]=$global_above_main;
		} 
		$global_top=getVar("global_top_".($i+1));
		if ($global_top!==NULL) {
			$json["test_specifications"][$i]["global_top"]=$global_top;
		} 			
		if (!isset($json["test_specifications"][$i]["running_params"]))
				$json["test_specifications"][$i]["running_params"]=array();
		$timeout=getVar("timeout_".($i+1));
		if ($timeout!==NULL) {					
			$json["test_specifications"][$i]["running_params"]["timeout"]=$timeout;
		} 
		$vmem=getVar("vmem_".($i+1));
		if ($vmem!==NULL) {					
			$json["test_specifications"][$i]["running_params"]["vmem"]=$vmem;
		} 
		$stdin=getVar("stdin_".($i+1));
		if ($stdin!==NULL) {					
			$json["test_specifications"][$i]["running_params"]["stdin"]=$stdin;
		} 
		if (!isset($json["test_specifications"][$i]["expected"]))
			$json["test_specifications"][$i]["expected"]=array();	
		$numVar=getIntVar("numVar_".($i+1));
		if ($numVar===NULL) {
			$numVar=count($json["test_specifications"][$i]["expected"]);
		} else if ($numVar<0) {
			printError("'numVar_x' must be >=0.");
			finishAll();
		}
		for ($k=0; $k<$numVar; $k++) {
			// If the variable is set, take it and insert it
			// If some variable is not set, then just insert "";
			$expected=getVar("expected_".($i+1)."_".($k+1));
			if ($expected!==NULL) {
				$json["test_specifications"][$i]["expected"][$k]=replace_n_ln($expected);
			} else if (!isset($json["test_specifications"][$i]["expected"][$k])) {
				$json["test_specifications"][$i]["expected"][$k]="";
			} // Otherwise keep the old value				
		}
		if (count($json["test_specifications"][$i]["expected"])>$numVar) {
			// Erase all additional variants!
			array_splice($json["test_specifications"][$i]["expected"], $numVar);
		}
		$expected_exception=getBoolVar("expected_exception_".($i+1));			
		$json["test_specifications"][$i]["expected_exception"]=$expected_exception;			
		$expected_crash=getBoolVar("expected_crash_".($i+1));
		$json["test_specifications"][$i]["expected_crash"]=$expected_crash;
		$ignore_whitespace=getBoolVar("ignore_whitespace_".($i+1));
		$json["test_specifications"][$i]["ignore_whitespace"]=$ignore_whitespace;
		$regex=getBoolVar("regex_".($i+1));
		$json["test_specifications"][$i]["regex"]=$regex;
		$substring=getBoolVar("substring_".($i+1));
		$json["test_specifications"][$i]["substring"]=$substring;								
		// Json has been created
		saveJson($fileData, $json);	
		print "Editing done successfully!";	
	} else if ($mod==3) { // Erase a single AT
		$json=json_decode(file_get_contents($fileData),true);
		$numATs=count($json["test_specifications"]);
		
		$id=getIntVar("id");
		// Get ordinal of the AT with the given id
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
		$i--;
		// Delete AT from json
		for ($k=$i; $k<$numATs-1; $k++) {
			$json["test_specifications"][$k]=$json["test_specifications"][$k+1];
		}
		$numATs--;
		array_splice($json["test_specifications"], $numATs); // Delete the last element from array
		saveJson($fileData, $json);	
		print "Erasing done successfully!";
	} else if ($mod==4) { // Add 1 autotest
		$json=json_decode(file_get_contents($fileData),true);
		$numATs=count($json["test_specifications"]);
		$lastId=$json["test_specifications"][$numATs-1]['id'];
		$newATjson=json_decode(getDefAT($lastId+1),true);
		$json["test_specifications"][$numATs]=$newATjson;
		saveJson($fileData, $json);	
		?>
		New autotest added successfully.<br>
		<form action="edit_1.php" id="editAT" method="post">
			<input type="hidden" name="id" value="<?php print $newATjson["id"]; ?>" >
			<input type="hidden" name="adv" value="<?php print $advanced; ?>" >
			<input type="hidden" name="fileData" value="<?php print $fileData; ?>" >
			<input type="submit" value="Edit new AT">
		</form><br>
		<?php
	} else if ($mod==5) { // Edit custom settings for the given file
		$json=json_decode(file_get_contents($fileData),true);
		
		$name=getVar("name");
		if ($name!==NULL) {
			$json["name"]=$name;
		}  
		$language=getVar("language");
		if ($language!==NULL) {
			$json["language"]=$language;
		} 
		$required_compiler=getVar("required_compiler");
		if ($required_compiler!==NULL) {
			$json["required_compiler"]=$required_compiler;
		} 
		$preferred_compiler=getVar("preferred_compiler");
		if ($preferred_compiler!==NULL) {
			$json["preferred_compiler"]=$preferred_compiler;
		} 
		$compiler_features=getVar("compiler_features");
		if ($compiler_features!==NULL) {
			$p=json_decode("[$compiler_features]", true);
			$json["compiler_features"]=$p;
		} 
		$compiler_options=getVar("compiler_options");
		if ($compiler_options!==NULL) {
			$json["compiler_options"]=$compiler_options;
		} 	
		$compiler_options_debug=getVar("compiler_options_debug");
		if ($compiler_options_debug!==NULL) {
			$json["compiler_options_debug"]=$compiler_options_debug;
		} 
		
		$compile=getBoolVar("compile");		
		$json["compile"]=$compile;
		
		$run=getBoolVar("run");
		$json["run"]=$run; 
		
		$test=getBoolVar("test");
		$json["test"]=$test;

		$debug=getBoolVar("debug");
		$json["debug"]=$debug;
		
		$profile=getBoolVar("profile");
		$json["profile"]=$profile;
		
		// Json has been created
		saveJson($fileData, $json);	
		print "Editing done successfully!";
	} else if ($mod==6) { // Add completelly new AT with provided data
		$json=json_decode(file_get_contents($fileData),true);
		$numATs=count($json["test_specifications"]);
		$lastId=$json["test_specifications"][$numATs-1]['id'];
		$newATjson=json_decode(getDefAT($lastId+1),true);
		
		$require_symbols=getVar("require_symbols");
		if ($require_symbols!==NULL) {
			$p=json_decode("[$require_symbols]", true);
			$newATjson["require_symbols"]=$p;
		} else if (!isset($newATjson["require_symbols"])) {
			$p=json_decode("[]", true);
			$newATjson["require_symbols"]=$p;
		} // Otherwise everything stays as it was
		$replace_symbols=getVar("replace_symbols");
		if ($replace_symbols!==NULL) {
			$p=json_decode("[$replace_symbols]", true);
			$newATjson["replace_symbols"]=$p;
		} else if (!isset($newATjson["replace_symbols"])) {
			$p=json_decode("[]", true);
			$newATjson["replace_symbols"]=$p;
		} // Otherwise everything stays as it was
		$code=getVar("code");
		if ($code!==NULL) {
			$newATjson["code"]=$code;
		} 
		$global_above_main=getVar("global_above_main");
		if ($global_above_main!==NULL) {
			$newATjson["global_above_main"]=$global_above_main;
		} 
		$global_top=getVar("global_top");
		if ($global_top!==NULL) {
			$newATjson["global_top"]=$global_top;
		} 			
		if (!isset($newATjson["running_params"]))
				$newATjson["running_params"]=array();
		$timeout=getVar("timeout");
		if ($timeout!==NULL) {					
			$newATjson["running_params"]["timeout"]=$timeout;
		} 
		$vmem=getVar("vmem");
		if ($vmem!==NULL) {					
			$newATjson["running_params"]["vmem"]=$vmem;
		} 
		$stdin=getVar("stdin");
		if ($stdin!==NULL) {					
			$newATjson["running_params"]["stdin"]=$stdin;
		} 
		if (!isset($newATjson["expected"]))
			$newATjson["expected"]=array();	
		$numVar=getIntVar("numVar");
		if ($numVar===NULL) {
			$numVar=count($newATjson["expected"]);
		} else if ($numVar<0) {
			printError("'numVar_x' must be >=0.");
			finishAll();
		}
		for ($k=0; $k<$numVar; $k++) {
			// If the variable is set, take it and insert it
			// If some variable is not set, then just insert "";
			$expected=getVar("expected_".($k+1));
			if ($expected!==NULL) {
				$newATjson["expected"][$k]=replace_n_ln($expected);
			} else if (!isset($newATjson["expected"][$k])) {
				$newATjson["expected"][$k]="";
			} // Otherwise keep the old value				
		}
		if (count($newATjson["expected"])>$numVar) {
			// Erase all additional variants!
			array_splice($newATjson["expected"], $numVar);
		}
		$expected_exception=getBoolVar("expected_exception");			
		$newATjson["expected_exception"]=$expected_exception;			
		$expected_crash=getBoolVar("expected_crash");
		$newATjson["expected_crash"]=$expected_crash;
		$ignore_whitespace=getBoolVar("ignore_whitespace");
		$newATjson["ignore_whitespace"]=$ignore_whitespace;
		$regex=getBoolVar("regex");
		$newATjson["regex"]=$regex;
		$substring=getBoolVar("substring");
		$newATjson["substring"]=$substring;		
		
		$json["test_specifications"][$numATs]=$newATjson;	
		saveJson($fileData, $json);	
		?>New autotest added successfully.<br><?php
	}
?>
<br>
<input type='button' onclick='safeLinkBackForw(-1);' value='Back'>
<input type='button' onclick="document.getElementById('previewFile').submit();" value='Preview all'>
<input type='button' onclick="document.getElementById('editFile').submit();" value='Edit all'>

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