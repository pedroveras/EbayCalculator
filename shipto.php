<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>EBAY COST CALCULATOR</title>
<link type="text/css" rel="stylesheet" href="stylesheet.css">
</head>
<body>
<h2>EBAY COST CALCULATOR</h2>

<div id="stylized" class="myform">
	<form id="form" action="mail.php" method="get" style="width: 460px;">
		<input type="hidden" name="action" value="shipto">
		<label for="name">Name:</label>
		<input name="name" type="text" id="name">
		
		<label for="address">Address (Line 1):</label> 
		<input name="address1" type="text" id="address1"/>
		
		<label for="address2">Address (Line 2):</label> 
		<input name="address2" type="text" id="address2"/>
		
		<label for="city">City:</label> 
		<input name="city" type="text" id="city"/>
		
		<label for="zip">Zip Code:</label> 
		<input name="zip" type="text" id="zip"/>
		
		<label for="phone">Phone:</label> 
		<input name="phone" type="text" id="phone"/>
		
		<label for="note">Note to Seller:</label> 
		<textarea name="note" id="note"></textarea>
		
		<span class="spanleft">Amount Paid:</span>
		
		<div class="spacer"></div>
		
		<button type="submit">Confirm</button>		
		<div class="spacer"></div>
	</form>
</div>
</body>
</html>