<!DOCTYPE html>
<html>
<body>
<head>
	<title>Calculator</title>
</head>
	<form method="post" action=<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?> >
	<fieldset style="width:300px;">
		<h1 style="text-align:center; margin: auto;">Calculator</h1>
		<hr />
		</p>
	<ul>
		<li>Only numbers and +,-,* and / operators are allowed in the expression.</li>
		<li>The evaluation follows the standard operator precedence.</li>
		<li>The calculator does not support parentheses.</li>
		<li>The calculator handles invalid input "gracefully". It does not output PHP error messages.</li>
	</ul>
		<p>
			<input type="text" name="expression">
			<input type="submit" value="Calculate" name="submit">
		</p>
	

<?php

if(isset($_POST['submit']) && isset($_POST['expression'])){
	check_expression($_POST['expression']);
}

function check_expression($expr) {
/*GLOBAL VARIABLES*/
	$invalid = false;

	/*1. CHECK FOR INVALID CHARACTERS ERROR */  
	/*
		allowed characters:
		1.+
		2.-
		3.*
		4./
		5.0-9
	*/
	if(preg_match("/[^\+\-\*\/\.0-9]/",trim($expr),$matches, PREG_OFFSET_CAPTURE)) {
			$invalid = true;
	}
	
	/*2. CHECK FOR INVALID OPERANDS */
	
	//EXTRACT OPERANDS
	preg_match_all("/[^\+\-\*\/]+/",$expr,$op_match, PREG_OFFSET_CAPTURE); // escape - and * sign // get all operands excluding signs


	$arr = $op_match[0];
	foreach ($arr as $element) {
		/*check  periods or singular period */
		if(preg_match("#(\.{1}[^\.]*\.)|(^\.$)#",trim($element[0]),$matches,PREG_OFFSET_CAPTURE)) //use trim to remove white space from beginning and end
		{
			$invalid = true;
		}
		
		/*check for white space*/
		if(preg_match("/[0-9][\s]+[0-9]/",trim($element[0]),$matches,PREG_OFFSET_CAPTURE)) //use trim to remove white space from beginning and end
		{
			$invalid = true;
		}
	}	 

	/*3. CHECK FOR OPERATORS */ 
	
	//OPERATORS AT BEGINNING OR END OF STRING
	if(preg_match("#(^([\+\*\/])|(([\+\-\/\*]$)))#",$expr,$matches,PREG_OFFSET_CAPTURE))
		{
			$invalid = true;
		}
	//TWO OPERATORS (not including [+*/]- ) or - [+-*/] 
	if(preg_match("#([\+\-\*\/]{1}[\s]*[\+\*\/]{1})|(\-[\s]*[\+\-\*\/]{1})#",$expr,$matches,PREG_OFFSET_CAPTURE))
		{
			$invalid = true;
		}
	//DIVISION BY 0  Case divided by +-.0 at end | (Case where divided by +-.0 in string | Case divided by +-.0.|Case divided by +-0. at end of string)
	if(preg_match("#(/[-.]*0$)|(\/[-.]*0[^\.0-9])|(\/[-.]*0\.[^0-9])|(\/[-.]*0\.$)#",$expr,$matches,PREG_OFFSET_CAPTURE))
		{
			$invalid = true;
		}
		
	if($invalid || (trim($expr)=="" && $expr!="" )) 
		{
			echo "		<p>\n			Invalid Expression: " . "<b>".$expr."</b>\n		</p>\n";
			
		}
	else 
		{
			if($expr!="")//if not empty 
			{
				
				eval("\$evaluation = $expr;");
				echo "		<p>\n			<b>".$expr ."</b>" . " evaluates to " . "<b>".$evaluation."</b>\n		</p>\n";
				
			}
		}
}
?>
	</fieldset>
	</form>
</body>
</html>