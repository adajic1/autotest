<?php
	$fileLastId="last_id.txt";
	$fileData=getVar("fileData");
	if (!file_exists($fileData)) {
		if (!isset($ifNoThenCreate) || $ifNoThenCreate==0) {
			printError("File <$fileData> does not exist!");
			exit;
		} else {
			// Create autotest file with passed common settings
			saveJson($fileData, getDefDecodedJson());
		}
	}
	function getVar($var) {
		// Get a variable passed with POST or GET in a safe way
		// Returns NULL if the variable does not exist
		if (isset($_POST[$var])) return get_magic_quotes_gpc() ? stripslashes($_POST[$var]) : $_POST[$var];
		else if (isset($_GET[$var])) return get_magic_quotes_gpc() ? stripslashes($_GET[$var]) : $_GET[$var];
		else return NULL;
	}
	function getIntVar($var) {
		// Get integer variable in a safe way
		// Returns NULL if the variable does not exist
		$stringVal=getVar($var);
		if ($stringVal===NULL) return NULL;
		else return intval($stringVal);
	}
	function getBoolVar($var) {
		// Get boolean variable in a safe way
		// Returns true if it's set, otherwise false
		// Returns NULL if the variable does not exist
		if (isset($_POST[$var])) return "true";
		else if (isset($_GET[$var])) return "true";
		else return "false";
	}
	function getConsoleVar($arg) {
		// Example of cmd command where this is useful: php integrate.php OR 11 "Zada\u0107a 1" 1 "C:\Users\BlackArrow\Desktop\zadaca1.txt" 345
	    // NOTE: nonascii unicode characters should be given using \uxxxx notation!
	    // NOTE: if a console argument has 2 or more words it should be given in quotation marks
		return json_decode('"'.$arg.'"', true);
	}
	function printError($errorText) {
		print "<font class='simpleText info'>ERROR: $errorText</font>";
	}
	function finishAll() {
		print "<br><input type='button' onclick='safeLinkBackForw(-1);' value='Back'>";
		print "</body></html>";
		exit;
	}
	function saveJson($path, $json) {
		if ($path=="") {
			printError("It's not possible to use the function saveJson() if path is not provided.");
			finishAll();
		}
		if(!($fw = fopen($path, "c+"))) {
			printError("Problem opening the file <$path>.");
			finishAll();
		}	
		if(flock($fw, LOCK_EX)){
			ftruncate($fw, 0); // Clear content of the file
			rewind($fw); // Move cursor to the beginning of the file
			// Write new content to the file
			fwrite($fw, replace_rn_ln(json_encode($json, JSON_PRETTY_PRINT)));
			fflush($fw);
			flock($fw, LOCK_UN);
			fclose($fw);
		}
	}	
	function deleteDir($dirPath) {
	    if (!is_dir($dirPath)) {
	        printError("$dirPath must be a folder.");
	        finishAll();
	    }
	    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
	        $dirPath .= '/';
	    }
	    $files = glob($dirPath . '*', GLOB_MARK);
	    foreach ($files as $file) {
	        if (is_dir($file)) {
	            self::deleteDir($file);
	        } else {
	            unlink($file);
	        }
	    }
	    rmdir($dirPath);
	}
	function crEmptyFile($path) {
		if ($path=="") {
			printError("Name of the file in function crEmptyFile() can't be an empty string.");
			finishAll();
		}
		if(!($fw = fopen($path, "c+"))) {
			printError("Problem opening the file <$path>.");
			finishAll();
		}	
		if(flock($fw, LOCK_EX)){	
			ftruncate($fw, 0); // Clear content of the file
			fflush($fw);		
			flock($fw, LOCK_UN);
			fclose($fw);
		}	
	}	
	function replace_ln_n_br($sentence) {
		return str_replace(array("\\r\\n", "\\r", "\\n", "\r\n","\r","\n"),"<br>", $sentence);
	}
	function replace_n_br($sentence) {
		return str_replace(array("\r\n","\r","\n"),"<br>", $sentence);
	}
	function replace_ln_br($sentence) {
		return str_replace(array("\\r\\n", "\\r","\\n"),"<br>", $sentence);
	}
	function replace_ln_n($sentence) {
		return str_replace(array("\\r\\n", "\\r","\\n"),"\n", $sentence);
	}
	function replace_n_ln($sentence) {
		return str_replace(array("\r\n","\r","\n"),"\\n", $sentence);
	}
	function replace_rn_ln($sentence) {
		return str_replace(array("\\r\\n","\\r"),"\\n", $sentence);
	}
	function replace_space_nbsp($sentence) {
		$sentence=str_replace(array(" "),"&nbsp;", $sentence);
		$sentence=str_replace(array("\t"),"&nbsp;&nbsp;&nbsp;&nbsp;", $sentence);		
		return $sentence;
	}
	function previewformat($sentence) {
		$sentence=htmlentities($sentence);
		$sentence=replace_space_nbsp($sentence);
		$sentence=replace_n_br($sentence);
		return $sentence;
	}	
	function getNewId() {
		global $fileLastId;
		if ($fileLastId=="") {
			printError("Name of the file fileLastId in function getNewId() can't be empty.");
			finishAll();
		}
		if(!($fw = fopen($fileLastId, "c+"))) {
			printError("Problem opening the file <$fileLastId>.");
			finishAll();
		}	
		if(flock($fw, LOCK_EX)){
			rewind($fw); // Back to beginning of the file, ready to read			
			$id = intval(trim(fgets($fw)));
			$id = ($id>=PHP_INT_MAX)?1:$id+1; 
			ftruncate($fw, 0); // Clear content of the file
			rewind($fw); // Move cursor to the beginning of the file again
			fwrite($fw, $id); // Write new content
			fflush($fw);
			flock($fw, LOCK_UN);
			fclose($fw);
		}
		return $id;
	}
	function getDefAT($id=1) {
		$json='{
            "id": '.$id.',
            "require_symbols": [],
            "replace_symbols": [],
            "code": "",
            "global_above_main": "",
            "global_top": "",
            "running_params": {
                "timeout": "10",
                "vmem": "1000000",
                "stdin": ""
            },
            "expected": [
                ""
            ],
            "expected_exception": "false",
            "expected_crash": "false",
            "ignore_whitespace": "false",
            "regex": "false",
            "substring": "false"
        }';
        return $json;
	}
	function getDefDecodedJson() { // Called if there is no autotest file + we need to create a new one	
		$def_name=getVar("def_name");
		$def_language=getVar("def_language");
		$def_required_compiler=getVar("def_required_compiler");
		$def_preferred_compiler=getVar("def_preferred_compiler");
		
		$def_compiler_features=getVar("def_compiler_features");
		$def_compiler_options=getVar("def_compiler_options");
		$def_compiler_options_debug=getVar("def_compiler_options_debug");
		
		$def_compile=getBoolVar("def_compile"); 
		$def_run=getBoolVar("def_run");
		$def_test=getBoolVar("def_test");
		$def_debug=getBoolVar("def_debug");
		$def_profile=getBoolVar("def_profile");
		
		$json='
			{  
			    "id":'.getNewId().',
			    "name":"'.$def_name.'",
			    "language":"'.$def_language.'",
			    "required_compiler":"'.$def_required_compiler.'",
			    "preferred_compiler":"'.$def_preferred_compiler.'",
			    "compiler_features":[  
			    	'.$def_compiler_features.'
			    ],
			    "compiler_options":"'.$def_compiler_options.'",
			    "compiler_options_debug":"'.$def_compiler_options_debug.'",
			    "compile":"'.$def_compile.'",
			    "run":"'.$def_run.'",
			    "test":"'.$def_test.'",
			    "debug":"'.$def_debug.'",
			    "profile":"'.$def_profile.'",
			    "test_specifications":[ '.getDefAT(1).' ]
		    }
		';
		return json_decode($json, true);
	}
?>