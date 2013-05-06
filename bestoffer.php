<?php
$amountPaid = $item->getTotalCost();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>EBAY COST CALCULATOR</title>

<script type="text/javascript">
function CalcBidPrice() {
	if (isNaN(document.orderform.offer.value)) {
		alert("Maximum bid must be numeric.");
	} else {
		document.inputform.itemNumber.value = <?php echo $itemNumber?>;
		document.inputform.offer.value = document.orderform.offer.value;
		document.inputform.listing.options.selectedIndex = 1;
		document.inputform.action.value = 'offering';
		document.inputform.btnSubmit.click();
	}
}
	
</script>
</head>
<body>
<div id="results" class="myform" style="overflow: auto;">
	<form id="orderform" name="orderform" method="post" action="">
	
		<?php
			if ($item->status == 'Completed') {
				echo "<div class='completed'>Item Sold</div>";
			} 
		?>
		<div class="spanimg"><img src="<?php echo $item->picture?>" style="width: 140px; height: 140px;"/></div>
		
		<span class="spanleft">Status:</span>
		 <span class="spanright"><?php echo $item->status; ?></span>
		 
		<span class="spanleft">Item Number:</span>
		<span class="spanright"> <?php echo $item->itemNumber ?></span>
		
		<span class="spanleft">Title:</span>
		 <span class="spanright"><?php echo $item->title ?></span>
		 
		
		<span class="spanleft">Seller ID:</span>
		<span class="spanright"> <?php echo $item->sellerID ?></span>
		
		<span class="spanleft">Item Location: </span>
		<span class="spanright"><?php echo $item->location ?></span>
		
		<span class="spanleft">End Time:</span>
		<span class="spanright"><?php echo $item->endTime ?></span>
		
		<span class="spanleft">Quantity Available:</span>
		<span class="spanright"><?php echo $item->quantity ?></span>
		
		<span class="spanleft">Quantity to Buy:</span>
		<span class="spanright"><?php echo $item->quantityBuy ?></span>
		
			<div class="maximumbid">
				<span class="spanleft">Offer Price (USD):</span>
				<span><input type="text" name="offer" value="<?php echo $item->price;?>"></span>
				<button type="button" onclick="javascript:CalcBidPrice();">Calculate price</button>
			</div>	
		
		<span class="spanleft">USD to IDR rate:</span>
		<span class="spanright"><?php echo $item->rate?></span>
		
		<span class="spanleft">Offer Price (IDR):</span>
		<span class="spanright"> <?php echo $item->getOfferPrice(); ?></span>
				
		<span class="spanleft">Shipping (USD):</span>
		<span class="spanright"> <?php echo $item->shipping ?></span>
		
		<span class="spanleft">Shipping (IDR):</span>
		<span class="spanright"> <?php echo $item->getShippingIDR(); ?></span>
		
		<span class="spanleft">Fee (IDR):</span><span class="spanright"> Rp 15.000</span>
		<span class="spanleft">Total Cost:</span><span class="spanright"> <?php echo "Rp ". $amountPaid; ?></span>
		
		<div class="spacer"></div>
		
		<?php 
			if ($item->status != 'Completed') {
				echo "<div><button type='submit' name='submit' class='submit'>Order</button></div>";
			}
		?>
	</form>	
</div>
</body>
</html>