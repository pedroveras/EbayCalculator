<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<link type="text/css" rel="stylesheet" href="stylesheet.css">
</head>
<body>
<?php
	require_once('mail.php');
	if (isset($_POST['confirmation'])) {
		$mail = new mail($item);
	} 
?>	

<div id="stylized" class="myform">
	<form id="shiptoform" action="" method="post">
	    <?php if (isset($mail)) { echo $mail->send(); }?>
	    
		<input type="hidden" name="submit" value="submit">
		<input type="hidden" name="amountPaid" value="<?php echo $amountPaid;?>">
		<input type="hidden" name="maximumBid" value="<?php if (isset($maximumBid)) {echo $maximumBid;}?>">
		
		<label for="name">Name:</label>
		<input name="name" type="text" id="name" class="shipto">
		
		<label for="address">Address (Line 1):</label> 
		<input name="address1" type="text" id="address1" class="shipto"/>
		
		<label for="address2">Address (Line 2):</label> 
		<input name="address2" type="text" id="address2" class="shipto"/>
		
		<label for="city">City:</label> 
		<input name="city" type="text" id="city" class="shipto"/>
		
		<label for="zip">Zip Code:</label> 
		<input name="zip" type="text" id="zip" class="shipto"/>
		
		<label for="phone">Phone:</label> 
		<input name="phone" type="text" id="phone" class="shipto"/>
		
		<label for="email">Email:</label> 
		<input name="email" type="text" id="email" class="shipto"/>
		
		<label for="note">Note to Seller:</label> 
		<textarea name="note" id="note" class="shipto"></textarea>
		
		<span class="spanleft">Amount Paid:</span>
		<span class="spanright"><?php echo "Rp ". $amountPaid;?></span>
		
		<span class="spanleft">Bank:</span>
		<span class="spanright">
			<select name="bank">
				<option value="BCA">BCA</option>
			</select>
		</span>
		
		<div class="spacer"></div>
		
		<button type="submit" name="confirmation" class="submit">Confirm</button>		
		<div class="spacer"></div>
	</form>
</div>

</body>
</html>