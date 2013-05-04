<?php
	$status = $_REQUEST['status'];
	$itemNumber = $_REQUEST['itemNumber'];
	$title = $_REQUEST['title'];
	$picture = $_REQUEST['picture'];
	$location = $_REQUEST['location'];
	$endTime = $_REQUEST['endTime'];
	$quantity = $_REQUEST['quantity'];
	$quantityBuy = $_REQUEST['qtyBuy'];
	$price = $_REQUEST['price'];
	$sellerID = $_REQUEST['sellerID'];
	$shipping = $_REQUEST['shipping'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>EBAY COST CALCULATOR</title>
</head>
<body>
<div id="results" class="myform" style="overflow: auto;">
	<form id="orderform" name="orderform" action="shipto.php">
	
		<?php
			if ($status == 'Completed') {
				echo "<div class='completed'>Item Sold</div>";
			} 
		?>
		<div class="spanimg"><img src="<?php echo $picture?>" /></div>
		
		<span class="spanleft">Status:</span>
		 <span class="spanright"><?php echo $status; ?></span>
		 
		<span class="spanleft">Item Number:</span>
		<span class="spanright"> <?php echo $itemNumber?></span>
		
		<span class="spanleft">Title:</span>
		 <span class="spanright"><?php echo $title?></span>
		 
		
		<span class="spanleft">Seller ID:</span>
		<span class="spanright"> <?php echo $sellerID?></span>
		
		<span class="spanleft">Item Location: </span>
		<span class="spanright"><?php echo $location ?></span>
		
		<span class="spanleft">End Time:</span>
		<span class="spanright"><?php echo $endTime ?></span>
		
		<span class="spanleft">Quantity Available:</span>
		<span class="spanright"><?php echo $quantity ?></span>
		
		<span class="spanleft">Quantity to Buy:</span>
		<span class="spanright"><?php echo $quantityBuy ?></span>
		
			<div class="maximumbid">
				<span class="spanleft">Price (USD):</span>
				<span class="spanright"><?php echo $price;?></span>
			</div>	
		
		<span class="spanleft">USD to IDR rate:</span>
		<span class="spanright"><?php echo $rate?></span>
		
		<span class="spanleft">Offer Price (IDR):</span>
		<span class="spanright"> <?php echo $price*$rate; ?></span>
				
		<span class="spanleft">Shipping (USD):</span>
		<span class="spanright"> <?php echo $shipping ?></span>
		
		<span class="spanleft">Shipping (IDR):</span>
		<span class="spanright"> <?php echo $shipping*$rate ?></span>
		
		<span class="spanleft">Fee (IDR):</span><span class="spanright"> 15.000</span>
		<span class="spanleft">Total Cost:</span><span class="spanright"> <?php echo $price*$rate+$shipping*$rate+15000 ?></span>
		
		<div class="spacer"></div>
		
		<?php 
			if ($status != 'Completed') {
				echo "<div><button type='submit' class='submit'>Order</button></div>";
			}
		?>
	</form>	
</div>	
</body>
</html>