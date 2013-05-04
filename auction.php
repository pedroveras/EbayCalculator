<?php
	$status = $_REQUEST['status'];
	$itemNumber = $_REQUEST['itemNumber'];
	$title = $_REQUEST['title'];
	$picture = $_REQUEST['picture'];
	$location = $_REQUEST['location'];
	$endTime = $_REQUEST['endTime'];
	$quantity = $_REQUEST['quantity'];
	$sellerID = $_REQUEST['sellerID'];
	$shipping = $_REQUEST['shipping'];
	$maximumBid = $_REQUEST['maximumBid'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>EBAY COST CALCULATOR</title>

<script type="text/javascript">
function CalcBidPrice() {
	if (isNaN(document.orderform.bid.value)) {
		alert("Maximum bid must be numeric.");
	} else {
		document.inputform.itemNumber.value = <?php echo $itemNumber?>;
		document.inputform.bidprice.value = document.orderform.bid.value;
		document.inputform.listing.options.selectedIndex = 2;
		document.inputform.action.value = 'bidding';
		document.inputform.btnSubmit.click();
	}
}
	
</script>
</head>
<body>
<div id="results" class="myform" style="overflow: auto;">
	<form id="orderform" name="orderform" action="shipto.php">
	
		<?php
			if ($status == 'Completed') {
				echo "<div class='completed'>Item Sold</div>";
			} 
		?>
		<div class="spanimg"><img src="<?php echo $picture?>" style="width: 140px; height: 140px;"/></div>
		
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
		
			<div class="maximumbid">
				<span class="spanleft">Maximum Bid (USD):</span>
				<?php if ($status == 'Completed') {
						echo '<span class = "spanright">'. $maximumBid.'</span>';
					  }	
					  else 
					  {
					  	echo '<span><input type="text" name="bid" value="'.$maximumBid.'"></span>';
						echo '<button type="button" onclick="javascript:CalcBidPrice();">Calculate price</button>';
					  }	
				?>
				<?php
					if (isset($_REQUEST['biderror'])) {
						echo $_REQUEST['biderror'];
					}	
				?>
			</div>	
		
		<span class="spanleft">USD to IDR rate:</span>
		<span class="spanright"><?php echo $rate?></span>
		
		<span class="spanleft">Maximum Bid (IDR):</span>
		<span class="spanright"> <?php echo $maximumBid*$rate; ?></span>
				
		<span class="spanleft">Shipping (USD):</span>
		<span class="spanright"> <?php echo $shipping ?></span>
		
		<span class="spanleft">Shipping (IDR):</span>
		<span class="spanright"> <?php echo $shipping*$rate ?></span>
		
		<span class="spanleft">Fee (IDR):</span><span class="spanright"> 15.000</span>
		<span class="spanleft">Total Cost:</span><span class="spanright"> <?php echo $maximumBid*$rate+$shipping*$rate+15000 ?></span>
		
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