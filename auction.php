<?php
	$amountPaid = $item->getTotalCostAuction();
	$maximumBid = $item->maximumBid;
?>
<script type="text/javascript">
function CalcBidPrice() {
	if (isNaN(document.orderform.maximumBid.value)) {
		alert("Maximum bid must be numeric.");
	} else {
		document.inputform.itemNumber.value = <?php echo $itemNumber?>;
		document.inputform.maximumBid.value = document.orderform.maximumBid.value;
		document.inputform.listing.options.selectedIndex = 2;
		document.inputform.action.value = 'bidding';
		document.inputform.btnSubmit.click();
	}
}
	
</script>

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
		<span class="spanright"><a href='<?php echo $item->url ?>' target="_new"><?php echo $item->itemNumber ?></a></span>
		
		<span class="spanleft">Title:</span>
		 <span class="spanright"><?php echo $item->title;?></span>
		 
		
		<span class="spanleft">Seller ID:</span>
		<span class="spanright"> <?php echo $item->sellerID?></span>
		
		<span class="spanleft">Item Location: </span>
		<span class="spanright"><?php echo $item->location; ?></span>
		
		<span class="spanleft">End Time:</span>
		<span class="spanright"><?php echo $item->endTime; ?></span>
		
			<div class="maximumbid">
				<span class="spanleft">Maximum Bid (USD):</span>
				<?php if ($item->status == 'Completed') {
						echo '<span class = "spanright">$'. $item->maximumBid.'</span>';
					  }	
					  else 
					  {
					  	echo '<span><input type="text" name="maximumBid" value="'.$item->maximumBid.'"></span>';
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
		<span class="spanright"><?php echo $item->rate;?></span>
		
		<span class="spanleft">Maximum Bid (IDR):</span>
		<span class="spanright"> <?php echo "Rp ".$item->getMaximumBidIDR(); ?></span>
				
		<span class="spanleft">Shipping (USD):</span>
		<span class="spanright"> <?php echo "$".$item->shipping ?></span>
		
		<span class="spanleft">Shipping (IDR):</span>
		<span class="spanright"> <?php echo "Rp ".$item->getShippingIDR(); ?></span>
		
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