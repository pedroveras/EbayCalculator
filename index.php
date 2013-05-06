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
	<form id="inputform" name="inputform" action="calculator.php" method="get">
		<input type="hidden" name="action" value="input">
		<input type="hidden" name="maximumBid">
		<input type="hidden" name="offer">
		<?php
			if (isset($_REQUEST['error'])) { 
				echo $_REQUEST['error'];
			}
		 ?>
		<label for="itemNumber" class="lblsearch">Item Number:</label>
		<input name="itemNumber" class="search" type="text" id="itemNumber">

		<div style="vertical-align: middle;">
		<label for="urlLink">ebay URL link:</label> 
		<input name="urlLink" type="text" id="urlLink" class="search"/></div>
		
		<label for="quantity">Quantity:</label> 
		<input name="quantity" type="text" value="1" id="quantity" class="search">
		
		<label>Listing: </label>
		<select name="listing" id="listing" class="search">
				<option value="buy_now">Buy It Now / Fixed Price</option>
				<option value="best_offer">Best Offer</option>
				<option value="auction">Auction</option>
		</select>
		<div class="spacer"></div>
		
		<button type="submit" class="submit" name="btnSubmit">Submit</button>		
		<div class="spacer"></div>
	</form>
</div>
<?php

if (isset($_REQUEST['listing']) && !isset($_REQUEST['variationLink'])) {
	if ($_REQUEST['listing'] == 'auction') {
		require_once 'auction.php';
	} elseif ($_REQUEST['listing'] == 'bestoffer') {
		require_once 'bestoffer.php';
	} elseif ($_REQUEST['listing'] == 'buy_now') {
		require_once 'buyitnow.php';
	}
} 
elseif (isset($_REQUEST['variationLink'])) {
	echo '<div id="results" class="myform" style="overflow: auto;">';
	echo $_REQUEST['variationLink'];
	echo '</div>';
}

if (isset($_POST['submit'])) {
	require_once 'shipto.php';
}

?>
</body>
</html>