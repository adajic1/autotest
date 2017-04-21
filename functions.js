var inProgress=0;
RegExp.quote = function(str) {
	return str.replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
};
function get_i_j(fullid) {
	// Get ordinal of this AT, and ordinal of the expected output (if it's set)
	var hArray=fullid.split("_");
	var ret=[];
	ret[0]=parseInt(hArray[1], 10); // i
	if (hArray.length>2)
		ret[1]=parseInt(hArray[2], 10); // j
	return ret;
}

function eraseVar(i, j) {
	$("#variant_"+i+"_"+j).remove();
	// For all with j bigger than this, lessen j and textual number
	j++;
	var numVar=parseInt($('[name="numVar_'+i+'"]').val(), 10); // Number of variants of this AT
	$('[name="numVar_'+i+'"]').val(numVar-1);	
	while (j<=numVar) {
		$("#num_"+i+"_"+j).html(j-1);
		$("#num_"+i+"_"+j).attr("id","num_"+i+"_"+(j-1));
		$("#variant_"+i+"_"+j).attr("id","variant_"+i+"_"+(j-1));
		$("#erase_"+i+"_"+j).attr("id","erase_"+i+"_"+(j-1));
		$('[name="expected_'+i+'_'+j+'"]').attr("name","expected_"+i+"_"+(j-1));
		j++;
	}
}
function animate_eraseVar(i, j) {
	$("#variant_"+i+"_"+j).hide('slow', function() {eraseVar(i, j); inProgress=0;});
}
function eraseVariant(id_erase) {
	if (inProgress) {
		alert("Please wait a few seconds!");
		return 0;
	}
	inProgress=1;
	var hArray=get_i_j(id_erase);
	var i=hArray[0]; // Starta od 1
	var j=hArray[1]; // Starta od 1
	animate_eraseVar(i, j);	    			   	
}

function prepareVariant(template, i, j) {
	var re = new RegExp(RegExp.quote("NUMI"), "g");
	template=template.replace(re, i);
	re = new RegExp(RegExp.quote("NUMJ"), "g");
	template=template.replace(re, j);			
	return template;
}
function addVariant(id_add) {
	if (inProgress) {
		alert("Please wait a few seconds!");
		return 0;
	}
	inProgress=1;
	var hArray=get_i_j(id_add);
	var i=hArray[0]; // Starting from 1
	// Insert variant with i=i, j=numVar, but numVar increased by 1
	var numVar=parseInt($('[name="numVar_'+i+'"]').val(), 10);
	numVar++; // Increase number of variants for this AT
	$('[name="numVar_'+i+'"]').val(numVar);
	var j=numVar; // j is ordinal of last variant, it's equal to the total number of variants
	$("#cell_"+i).append(prepareVariant($("#variant_template").html(), i, j));
	inProgress=0;
}

function eraseAt(i) {
	$("#atTable_"+i).remove();
	var totalATs=parseInt($("[name='numATs']").val(), 10);	    	
	$("[name='numATs']").val(totalATs-1);
	if (totalATs-1==0) {
		$("#allATs").html("<font class='simpleText info'>If you confirm changes there will be zero autotests for this task. Custom settings defined above will be gone as well...</font><br><br>");
	}			
	customize(i+1, totalATs);	
}
function animate_eraseAt(i) {			
	$("#atTable_"+i).hide('slow', function() {eraseAt(i); inProgress=0;});
}
function customize(from, to) {
	for (k=from; k<=to; k++) {
		$("#atTable_"+k).attr("id", "atTable_"+(k-1));
		$("#atNum_"+k).html(k-1);
		$("#atNum_"+k).attr("id", "atNum_"+(k-1));
		$("#atErase_"+k).attr("id", "atErase_"+(k-1));
		$("[name='require_symbols_"+k+"']").attr("name", "require_symbols_"+(k-1));
		$("[name='replace_symbols_"+k+"']").attr("name", "replace_symbols_"+(k-1));
		$("[name='code_"+k+"']").attr("name", "code_"+(k-1));
		$("[name='global_above_main_"+k+"']").attr("name", "global_above_main_"+(k-1));
		$("[name='global_top_"+k+"']").attr("name", "global_top_"+(k-1));
		$("[name='timeout_"+k+"']").attr("name", "timeout_"+(k-1));
		$("[name='vmem_"+k+"']").attr("name", "vmem_"+(k-1));
		$("[name='stdin_"+k+"']").attr("name", "stdin_"+(k-1));
		$("#cell_"+k).attr("id", "cell_"+(k-1));
		$("[name='numVar_"+k+"']").attr("name", "numVar_"+(k-1));
		for (t=1; t<=parseInt($("[name='numVar_"+(k-1)+"']").val(), 10); t++) {
			$("#variant_"+k+"_"+t).attr("id", "variant_"+(k-1)+"_"+t);
			$("#num_"+k+"_"+t).attr("id", "num_"+(k-1)+"_"+t);
			$("#erase_"+k+"_"+t).attr("id", "erase_"+(k-1)+"_"+t);
			$("[name='expected_"+k+"_"+t+"']").attr("name", "expected_"+(k-1)+"_"+t);
		}
		$("#addVar_"+k).attr("id", "addVar_"+(k-1));
		$("[name='expected_exception_"+k+"']").attr("name", "expected_exception_"+(k-1));
		$("[name='expected_crash_"+k+"']").attr("name", "expected_crash_"+(k-1));	
		$("[name='ignore_whitespace_"+k+"']").attr("name", "ignore_whitespace_"+(k-1));				
		$("[name='regex_"+k+"']").attr("name", "regex_"+(k-1));	
		$("[name='substring_"+k+"']").attr("name", "substring_"+(k-1));
	}
}
function atErase(at_id) {
	// Erase the whole table
	// Change ordinal of the AT, and all places where 'i' is mentioned for all of ATs below
	// Decrease total number of ATs
	if (inProgress) {
		alert("Please wait a few seconds!");
		return 0;
	}
	inProgress=1;
	var hArray=get_i_j(at_id);
	var i=hArray[0]; // Starting from 1	    	
	animate_eraseAt(i); // Deletion of the AT number i
}

function prepareAt(template, i) {
	var re = new RegExp(RegExp.quote("NUMI"), "g");
	template=template.replace(re, i);						
	return template;
}
function addingAt() {
	if (inProgress) {
		alert("Please wait a few seconds!");
		return 0;
	}
	inProgress=1;
	var totalATs=parseInt($("[name='numATs']").val(), 10);
	if (totalATs==0) {
		$("#allATs").html(""); // Erase info text which appears when there are no autotests
	}	
	totalATs++;    	
	$("[name='numATs']").val(totalATs);
	// Take a template, fill it with values and just import it
	$("#allATs").append(prepareAt($("#atTemplate").html(), totalATs));
	$('html, body').animate({
        scrollTop: $("[id^=atTable_]").last().offset().top
    }, 'slow');
	inProgress=0;
}
function showAdvanced() {
	if (advanced==0) {
		// Show additional options
		advanced=1;
		$("[name='adv_button']").val("Hide additional options");
		$("[name='adv_display']").css("display", "table-row");
		$("[name='adv']").val(advanced);
	} else {
		// Hide additional options 
		advanced=0;
		$("[name='adv_button']").val("Show additional options");
		$("[name='adv_display']").css("display", "none");
		$("[name='adv']").val(advanced);
	}
}
function safeLinkBackForw(gdje) {
    var currentUrl = window.location.href;
    history.go(gdje);
    setTimeout(function(){
        // If location was not changed in 100 ms, then there is no history back
        if(currentUrl === window.location.href){
            // Redirect to site root
            window.open("index.php","_top");
        }
    }, 2500);
}
function get_browser_info() {
    var ua = navigator.userAgent, tem, M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
    if (/trident/i.test(M[1])) {
        tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
        return {name: 'IE ', version: (tem[1] || '')};
    }
    if (M[1] === 'Chrome') {
        tem = ua.match(/\bOPR\/(\d+)/)
        if (tem != null) {
            return {name: 'Opera', version: tem[1]};
        }
    }
    M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
    if ((tem = ua.match(/version\/(\d+)/i)) != null) {
        M.splice(1, 1, tem[1]);
    }
    return {
        name: M[0],
        version: M[1]
    };
}
browser = get_browser_info();
function histReload() {
	vrj=parseInt(document.getElementById("history").value,10);
	if (vrj == 0) {
		// Not history
    	document.getElementById('history').value = "1";    	
    	// Because of universality of the code, cause FF remembers field values after a reload
    	// Commands below are not necessary for other browsers 	 
    	if (advanced==1) {
			// Show additional options
			$("[name='adv_button']").val("Hide additional options");
			$("[name='adv_display']").css("display", "table-row");
			$("[name='adv']").val(advanced);
		} else {
			// Hide additional options 
			$("[name='adv_button']").val("Show additional options");
			$("[name='adv_display']").css("display", "none");
			$("[name='adv']").val(advanced);
		}
    } else {
    	// It's history, we need to refresh
    	document.getElementById('history').value = "0";
    	setTimeout(function () { // Because of universality of the code the reload is in a setTimeout. That's the only way for it to work on FF.
    		window.location.reload();
    	}, 0);
    }
}
function otherHistory() { // For FF there is a separated pageshow event, so this function for FF shouldn't do anything
	if (browser.name.indexOf("Firefox") == -1) {
		histReload();
	}
}
function FFhistory() {
	histReload();
}
function toTop() {
	$("html, body").animate({ scrollTop: 0 }, "slow");
}
function toBottom() {
	$("html, body").animate({ scrollTop: $(document).height() }, "slow");
}