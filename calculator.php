<?php
include 'config.php';
require_once 'Item.php';
require_once('class.phpmailer.php');

if (isset($_GET['action']) && ($_GET['action'] == 'input' || $_GET['action'] == 'bidding' || $_GET['action'] == 'variation' || $_GET['action'] == 'offering')) {
	getSingleItem();
}


$item = null;

function getSingleItem() {
	$itemNumber = '';
	$error = '';
	
	$item = new Item();
	$r = $_GET;
	autoSetFields($item);
	
	if ($item->quantity <= 0) {
		$quantity = 1;
	}
	else 
	{
		$quantity = $item->quantity;
	}
	
	if ($item->itemNumber || $item->urlLink) {
		if ($item->itemNumber) {
			$itemNumber = $item->itemNumber;
		}
		else
		{
			//get the item number when the url link is passed (ebay URL link field)
			$pos = strrpos($item->urlLink, '/')+1;
			if (strrpos($item->urlLink, "?")) {
				$posEnd = strrpos($item->urlLink, "?")-$pos;
				$id = substr($item->urlLink,$pos, $posEnd);
			} else {
				$id  = substr($item->urlLink,$pos);
			} 
			
			$itemNumber = $id;
		}
	
		$apicallb = prepareCall($itemNumber);
		
		$resp = simplexml_load_file($apicallb);
		
		validations($resp,$quantity);

		$variations = $resp->Item->Variations;
		$variationLink = NULL;
		$action =$item->action;
		

		//check if is a variation, then display the item variation picture
		if ($resp->Item->PictureURL && $action != 'variation') {
			$picURL = $resp->Item->PictureURL;
		} elseif ($resp->Item->Variations->Pictures->VariationSpecificPictureSet->PictureURL) {
			$picURL = $resp->Item->Variations->Pictures->VariationSpecificPictureSet->PictureURL;
		} else {
			$picURL = "http://pics.ebaystatic.com/aw/pics/express/icons/iconPlaceholder_96x96.gif";
		}//

		// Check for shipping cost information
		if ($resp->Item->ShippingCostSummary->ShippingServiceCost) {
			$shippingCost = getShippingInfo($itemNumber, $quantity);
// 			$shippingCost = $resp->Item->ShippingCostSummary->ShippingServiceCost;
		} else {
			$shippingCost = "Not Specified";
		}//
		
		if ($variations && $action != 'variation') {
			$variationLink = getVariations($variations,$item);
		}
		
		//filling the object Item to be returned to the view page
		$item->status = $resp->Item->ListingStatus;
		$item->itemNumber = $resp->Item->ItemID;
		$item->title = $resp->Item->Title;
		$item->picture = $picURL;
		$item->location = $resp->Item->Location;
		$item->endTime = date("d M Y H:i:s", strtotime($resp->Item->EndTime));
		$item->quantity = $resp->Item->Quantity;
		$item->sellerID = $resp->Item->Seller->UserID;
		$item->minimumBid = $resp->Item->MinimumToBid;
		
		$item->shippingCost = $shippingCost;
		$item->quantityBuy = $quantity;
		$item->quantity = $resp->Item->Quantity;
		$item->shipping = $shippingCost;
		$item->url = $resp->Item->ViewItemURLForNaturalSearch;
		//end of filling Item
		
		redirect($item, $resp,$variationLink);
	}
	else
	{
		$_REQUEST['error'] = "<div class='error'> Please fill Item Number or ebay URL link </div>";
		$_REQUEST['listing'] = ''; 
		require_once 'index.php';
	}
}

/**
 * Method responsible for setting the parameters and return the Ebay Api Call string
 * @param $itemNumber
 * @return api call string
 */
function prepareCall($itemNumber) {
	$endpoint = 'http://open.api.ebay.com/shopping?';  // URL to call
	$version = '1.0.0';  // API version supported by your application
	$globalid = 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)
	// 	$appid = 'PedroHen-1160-44c9-883c-003d4bb788cb';  // Replace with your own AppID
	$responseEncoding='XML';
	
	$apicallb  = "$endpoint";
	$apicallb .= "callname=GetSingleItem";
	$apicallb .= "&version=563";
	$apicallb .= "&appid=".APP_ID;
	$apicallb .= "&itemid=$itemNumber";
	
	$j = 0;
	
	//add the variation to filter using the VariationSpecifics from Ebay Api
	//allowing to return the specific variation when the user click on the link
	foreach ($_GET as $key=>$value) {
		if ($key == "name$j")
		{
			$apicallb .= "&VariationSpecifics.NameValueList($j).Name=".rawurlencode($value);
		}
		else if($key == "value$j")
		{
			$apicallb .= "&VariationSpecifics.NameValueList($j).Value=".rawurlencode($value);
			$j++;
		}
	}
	//end variations filter
	
	
	$apicallb .= "&responseencoding=$responseEncoding";
	$apicallb .= "&includeselector=Details,ShippingCosts,Variations";
	
	return $apicallb;
}

/**
 * Get the shipping info to Indonesia
 * @param  $itemNumber
 * @param $quantity
 * @return shippingCost
 */
function getShippingInfo($itemNumber,$quantity) {
	$shippingCost = '';
	
	$call= "http://open.api.ebay.com/shopping?";
	$call .= "callname=GetShippingCosts";
	$call .= "&responseencoding=XML";
    $call.= "&appid=".APP_ID;
    $call.="&siteid=0";
    $call.="&version=517";
    $call.="&ItemID=".$itemNumber;
    $call.="&DestinationCountryCode=ID";
    $call.="&IncludeDetails=true";
    $call.="&QuantitySold=".$quantity;

	$result = simplexml_load_file($call);
	$shippingCost = $result->ShippingDetails->InternationalShippingServiceOption->ShippingServiceCost;
	
	return $shippingCost;
}

/**
 * 
 * @param unknown $resp
 * @param unknown $quantity
 */
function validations($resp,$quantity) {
	//validate if the item can be shipped to Indonesia
	if (!shipToIndonesia($resp)) {
		$_REQUEST['error'] = "<div class='error'>Error: Seller are not willing to send to Indonesia </div>";
		$_REQUEST['listing'] = '';
		require_once 'index.php';
		return ;
	}
	
	//check the response
	if ($resp->Ack == 'Failure') {
		$_REQUEST['error'] = "<div class='error'>Error: Invalid Item Number/Ebay URL link </div>";
		$_REQUEST['listing'] = '';
		require_once 'index.php';
		return ;
	}
	
	//validate if the quantity desired by the customer is not
	//higher than available quantity
	if ($quantity > $resp->Item->Quantity)
	{
		$_REQUEST['error'] = "<div class='error'>Error: Item quantity not available. </div>";
		$_REQUEST['listing'] = '';
		require_once 'index.php';
		return;
	}
	
	
}

/**
 * Get the request and set to the item object
 * @param Item $obj
 */
function autoSetFields($obj) {
	$keys = array_keys($_REQUEST);

	foreach($keys as $key) {
		$obj->$key = $_REQUEST[$key];
	}
}

/**
 * Get the list of variations of a item and display them as links
 * @param $variations
 * @param Item $item
 * @return string
 */
function getVariations($variations,$item) {
	$variationLink ='';
	$variationLine = '';
	$variationEspecifics = '';
	
	foreach ($variations->children() as $variation) {
		$variationLine = "";
		$name = array();
		$value = array();
		if ($variation->VariationSpecifics) {
			$i=0;
			foreach ($variation->VariationSpecifics->NameValueList as $especifics) {
				$variationEspecifics .= "&name$i=".urlencode($especifics->Name)."&value$i=".urlencode($especifics->Value);
				if ($i == 0) {
					$variationLine .= $especifics->Name .' : '.$especifics->Value;
				}
				else
				{
					$variationLine .= ' - '.$especifics->Name .' : '.$especifics->Value;
				}
	
				$i++;
			}
		}
		$variationLink .= '<a href="calculator.php?action=variation&quantity='.$item->quantity.'&itemNumber='.$item->itemNumber.'&listing='.$item->listing.$variationEspecifics.'">'.$variationLine.'</a></br>';
	}
	
	return $variationLink;
}

/**
 * Method responsible for checking if Indonesia is on the list of Exclude Ship Location
 * @param unknown $resp
 * @return boolean
 */
function shipToIndonesia($resp) {
	foreach ($resp->Item->ExcludeShipToLocation as $excluded) {
		if ($excluded == 'Asia' || $excluded == 'Indonesia') {
			return false;
		}
	}
	
	return true;
}

/**
 * Redirects to the appropriate listing page
 * @param Item $item
 * @param $resp
 * @param $variationLink
 */
function redirect($item,$resp,$variationLink) {
	$listingType = $resp->Item->ListingType;
	$buyItNowAvailable = $resp->Item->BuyItNowAvailable;
	$bestOfferEnabled = $resp->Item->BestOfferEnabled;
	$availableQuantity = $resp->Item->Quantity;
	
	switch ($item->listing)
	{
		case 'buy_now':
			if ($listingType == 'FixedPriceItem' || $listingType == 'StoresFixedPrice'  || $buyItNowAvailable ) {
				if ($buyItNowAvailable) {
					$item->price = $resp->Item->ConvertedBuyItNowPrice;
				}
				else
				{
					$item->price = $resp->Item->ConvertedCurrentPrice;
				}
	
				if (isset($variationLink)) {
					$_REQUEST['variationLink'] = $variationLink;
				}
				$_REQUEST['item'] = $item;
				require_once 'index.php';
				break;
			}
			else
			{
				$_REQUEST['error'] = "<div class='error'>Error: Buy It Now/Fixed Price not available for this item. </div>";
			}
			$_REQUEST['listing'] = '';
			require_once 'index.php';
			break;
		case 'best_offer':
			if ($bestOfferEnabled) {
				if ($_GET['action'] == 'offering') {
					$item->price = $item->offer;
				}
				else
				{
					$item->price = $resp->Item->ConvertedCurrentPrice;
				}
				$item->quantity = $availableQuantity;
				$_REQUEST['listing'] = 'bestoffer';
			}
			else
			{
				$_REQUEST['error'] = "<div class='error'>Error: Best Offer not available for this item. </div>";
				$_REQUEST['listing'] = '';
			}
			require_once 'index.php';
			break;
		case 'auction':
			if ($listingType == 'Chinese') {
				if ($_GET['action'] == 'bidding') {
					if ($item->maximumBid < $item->minimumBid) {
						$_REQUEST['biderror'] = "<div class='error'>Maximum bid should be higher or equal to ".$item->minimumBid."</div>";
						$_REQUEST['$maximumBid'] = $resp->Item->ConvertedCurrentPrice;
						$item->maximumBid = $item->minimumBid;
					}
				}
				else {
					$item->maximumBid = $resp->Item->MinimumToBid;
				}
			}
			else
			{
				$_REQUEST['error'] = "<div class='error'>Error: Auction not available for this item. </div>";
				$_REQUEST['listing'] = '';
			}
			require_once 'index.php';
			break;
	}
}
?>