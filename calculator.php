<?php
if (isset($_GET['action']) && ($_GET['action'] == 'input' || $_GET['action'] == 'bidding' || $_GET['action'] == 'variation')) {
	getSingleItem();
}

function getSingleItem() {
	$endpoint = 'http://open.api.ebay.com/shopping?';  // URL to call
	$version = '1.0.0';  // API version supported by your application
	$globalid = 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)
	$appid = 'PedroHen-1160-44c9-883c-003d4bb788cb';  // Replace with your own AppID
	$responseEncoding='XML';
	$itemNumber = '';
	$error = '';

	$r = $_GET;

	if (isset($r['quantity'])) {
		$quantity = $r['quantity'];
		if ($quantity <= 0) {
			$quantity = 1;
		}
	}

	if (isset($r['itemNumber'])) {
		$itemNumber = $r['itemNumber'];

		$apicallb  = "$endpoint";
		$apicallb .= "callname=GetSingleItem";
		$apicallb .= "&version=563";
		$apicallb .= "&appid=$appid";
		$apicallb .= "&itemid=$itemNumber";
// 		$apicallb .= "&VariationSpecifics.NameValueList(0).Name=Color";
// 		$apicallb .= "&VariationSpecifics.NameValueList(0).Value=Pinks";
// 		$apicallb .= "&VariationSpecifics.NameValueList(1).Name=Size Type";
// 		$apicallb .= "&VariationSpecifics.NameValueList(1).Value=Regular";
// 		$apicallb .= "&VariationSpecifics.NameValueList(2).Name=(Men's)";
// 		$apicallb .= "&VariationSpecifics.NameValueList(2).Value=M&#61; US XS";
		$j = 0;
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
		
		$apicallb .= "&responseencoding=$responseEncoding";
		$apicallb .= "&includeselector=Details,ShippingCosts,Variations";
		
		$resp = simplexml_load_file($apicallb);
		$resp ->asXML("result");

		$listingType = $resp->Item->ListingType;
		$buyItNowAvailable = $resp->Item->BuyItNowAvailable;
		$bestOfferEnabled = $resp->Item->BestOfferEnabled;
		$availableQuantity = $resp->Item->Quantity;
		$variations = $resp->Item->Variations;
		$variationLink = NULL;
		$variationEspecifics = "";
		$action =$r['action'];
		
		if ($resp->Ack == 'Failure') {
			$_REQUEST['error'] = "<div class='error'>Error: Invalid Item Number/Ebay URL link </div>";
			require_once 'index.php';
			return ;
		}
		
		if ($quantity > $availableQuantity)
		{
			echo "Item quantity not available";
		}

		if ($resp->Item->PictureURL && $action != 'variation') {
			$picURL = $resp->Item->PictureURL;
		} elseif ($action == 'variation' && $resp->Item->Variations->Pictures->VariationSpecificPictureSet->PictureURL) {
			$picURL = $resp->Item->Variations->Pictures->VariationSpecificPictureSet->PictureURL;
		} else {
			$picURL = "http://pics.ebaystatic.com/aw/pics/express/icons/iconPlaceholder_96x96.gif";
		}

		// Check for shipping cost information
		if ($resp->Item->ShippingCostSummary->ShippingServiceCost) {
			$shippingCost = $resp->Item->ShippingCostSummary->ShippingServiceCost;
		} else {
			$shippingCost = "Not Specified";
		}
		
		if ($variations && $action != 'variation') {
			$nameValueList = array();
			foreach ($variations->children() as $variation) {
				$variationLine = "";
				$name = array();
				$value = array();
				if ($variation->VariationSpecifics) {
					$i=0;
					foreach ($variation->VariationSpecifics->NameValueList as $especifics) {
						
// 						$variationEspecifics .= "&VariationSpecifics.NameValueList($i).Name=.$especifics->Name";
// 						$variationEspecifics .= "&VariationSpecifics.NameValueList($i).Value=.$especifics->Value";
						
						$nameValueList[$i] = array('Name' => $especifics->Value, 'Value' => $especifics->Value); 
						
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
				$variationLink .= '<a href="calculator.php?action=variation&quantity='.$quantity.'&itemNumber='.$itemNumber.'&variationSKU='.$variation->SKU.'&listing='.$r['listing'].$variationEspecifics.'">'.$variationLine.'</a></br>';
			}
			
		}

		$_REQUEST['status'] = $resp->Item->ListingStatus;
		$_REQUEST['itemNumber'] = $resp->Item->ItemID;
		$_REQUEST['title'] = $resp->Item->Title;
		$_REQUEST['picture'] = $picURL;
		$_REQUEST['location'] = $resp->Item->Location;
		$_REQUEST['endTime'] = $resp->Item->EndTime;
		$_REQUEST['quantity'] = $resp->Item->Quantity;
		$_REQUEST['sellerID'] = $resp->Item->Seller->UserID;
		$minimumBid = $resp->Item->MinimumToBid;
		$_REQUEST['maximumBid'] = $minimumBid;
		$_REQUEST['shipping'] = $shippingCost;

		//scrapping BCA to get the rates
		$source = file_get_contents('http://www.bca.co.id/id/biaya-limit/kurs_counter_bca/kurs_counter_bca_landing.jsp');
		$dom = new DOMDocument("1.0","UTF-8");
		@$dom->loadHTML($source);
		$dom->preserveWhiteSpace = false;

		$rate = $dom->getElementsByTagName('tr')->item(3)->childNodes->item(2)->nodeValue;
		$_REQUEST['rate'] = $rate;
		//end of scrapping
		
		switch ($r['listing'])
		{
			case 'buy_now':
				if ($listingType == 'FixedPriceItem' || $listingType == 'StoresFixedPrice'  || $buyItNowAvailable ) {
					if ($buyItNowAvailable) {
						$_REQUEST['price'] = $resp->Item->ConvertedBuyItNowPrice;
					}
					else
					{
						$_REQUEST['price'] = $resp->Item->ConvertedCurrentPrice;
					}
						
					$_REQUEST['listing'] = 'buy_now';
					$_REQUEST['qtyBuy'] = $quantity;
					if (isset($variationLink)) {
						$_REQUEST['variationLink'] = $variationLink;
					}
					require_once 'index.php';
					break;
				}
			case 'best_offer':
				if ($bestOfferEnabled) {
					$_REQUEST['qtyBuy'] = $quantity;
					$_REQUEST['quantity'] = $availableQuantity;
					$_REQUEST['price'] = $resp->Item->ConvertedCurrentPrice;
					$_REQUEST['listing'] = 'bestoffer';
					require_once 'index.php';
					break;
				}
			case 'auction':
				if ($listingType == 'Chinese') {
					if ($_GET['action'] == 'bidding') {
						if (isset($_REQUEST['bidprice']) && $_REQUEST['bidprice'] < $minimumBid) {
							$_REQUEST['biderror'] = "<div class='error'>Maximum bid should be higher or equal to ".$minimumBid."</div>";
							$_REQUEST['$maximumBid'] = $resp->Item->ConvertedCurrentPrice;
						}
						else {
							$_REQUEST['maximumBid'] = $_REQUEST['bidprice'];
						}
					} 
					$_REQUEST['listing'] = 'auction';
					require_once 'index.php';
					break;
				}
			default: 
				if ($r['listing'] == 'buy_now') {
					$_REQUEST['error'] = "<div class='error'>Error: Buy It Now/Fixed Price not avialable for this item. </div>";
				} 
				elseif ($r['listing'] == 'auction') {
					$_REQUEST['error'] = "<div class='error'>Error: Auction not avialable for this item. </div>";
				} 
				elseif ($r['listing'] == 'best_offer') {
					$_REQUEST['error'] = "<div class='error'>Error: Best Offer not avialable for this item. </div>";
				} 
				require_once 'index.php';	
		}

	}
	else
	{
		$_REQUEST['error'] = "<div class='erorr'> Please fill item number</div>"; 
		require_once 'index.php';
	}
}

function buyItNow($resp) {
	require_once 'buyitnow.php';
}

function auction($resp) {
	$resp->Item->ConvertedCurentPrice;
	require_once 'auction.php';
}

function getShippingInformation () {

}

function validations ($itemNumber,$quantity) {


}



error_reporting(E_ALL);  // Turn on all errors, warnings and notices for easier debugging
// API request variables
// $endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';  // URL to call
$endpoint = 'http://open.api.ebay.com/shopping?';  // URL to call
$version = '1.0.0';  // API version supported by your application
$globalid = 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)
$appid = 'PedroHen-1160-44c9-883c-003d4bb788cb';  // Replace with your own AppID
$responseEncoding='XML';
$i = '0';  // Initialize the item filter index to 0


// Construct the GetSingleItem HTTP GET call
$apicallb  = "$endpoint";
$apicallb .= "callname=GetSingleItem";
$apicallb .= "&version=563";
$apicallb .= "&appid=$appid";
$apicallb .= "&itemid=140959446978";
$apicallb .= "&responseencoding=$responseEncoding";
$apicallb .= "&includeselector=Details,ShippingCosts";
// $apicall .= "OPERATION-NAME=GetSingleItem";
// $apicall .= "&SERVICE-VERSION=$version";
// $apicall .= "&SECURITY-APPNAME=$appid";
// $apicall .= "&GLOBAL-ID=$globalid";
// $apicall .= "&itemFilter(0).name=ListingType";
// $apicall .= "&itemFilter(0).value=Auction";
// $apicall .= "&itemFilter(0).name=BestOfferOnly";
// $apicall .= "&itemFilter(0).value=true";
// $apicall .= "$urlfilter";

$resp = simplexml_load_file($apicallb);
if (isset($_POST['listing'])) {
	$listing = $_POST['listing'];
}

$retnb  = '';
$result = '';

if ($resp) {

	// If there is a response check for a picture of the item to display
	if ($resp->Item->PictureURL) {
		$picURL = $resp->Item->PictureURL;
	} else {
		$picURL = "http://pics.ebaystatic.com/aw/pics/express/icons/iconPlaceholder_96x96.gif";
	}

	// Check for shipping cost information
	if ($resp->Item->ShippingCostSummary->ShippingServiceCost) {
		$shippingCost = $resp->Item->ShippingCostSummary->ShippingServiceCost;
	} else {
		$shippingCost = "Not Specified";
	}

	// Build a table of item and user details for the selected most watched item
	$retnb .= "<!-- start table in getSingleItemResults --> \n";
	$retnb .= "<table width=\"100%\" cellpadding=\"5\"><tr> \n";
	$retnb .= "<td  width=\"50%\">\n";
	//       $retnb .= "<img src=\"$picURL\"/>";
	$retnb .= "<div align=\"left\"> <!-- left align item details --> \n";
	$retnb .= "Status: <b>" . $resp->Item->ListingStatus . "</b><br> \n";
	$retnb .= "Buy It Now: <b>" . $resp->Item->BuyItNowAvailable . "</b><br> \n";
	$retnb .= "Best Offer: <b>" . $resp->Item->BestOfferEnabled . "</b><br> \n";
	$retnb .= "Current price: <b>\$" . $resp->Item->ConvertedCurrentPrice . "</b><br> \n";
	$retnb .= "Shipping cost: <b>" . $shippingCost . "</b><br>\n";
	$retnb .= "</div></td> \n";
	$retnb .= "<td><div align=\"left\"> <!-- left align item details --> \n";
	$retnb .= "Seller ID: <b>" . $resp->Item->Seller->UserID . "</b><br> \n";
	$retnb .= "Feedback score: <b>" . $resp->Item->Seller->FeedbackScore . "</b><br> \n";
	$retnb .= "Positive Feedback: <b>" . $resp->Item->Seller->PositiveFeedbackPercent . "</b><br>\n";
	$retnb .= "</div></td></tr></table> \n<!-- finish table in getSingleItemResults --> \n";

} else {
	// If there was no response, print an error
	$retnb = "Dang! Must not have got the GetSingleItem response!";
}  // if $resp

$_REQUEST['status'] = $resp->Item->ListingStatus;
$_REQUEST['itemNumber'] = $resp->Item->ItemID;
$_REQUEST['title'] = $resp->Item->Title;
$_REQUEST['picture'] = $picURL;
$_REQUEST['location'] = $resp->Item->Location;
$_REQUEST['endTime'] = $resp->Item->EndTime;
$_REQUEST['quantity'] = $resp->Item->Quantity;
$_REQUEST['price'] = $resp->Item->ConvertedCurrentPrice;
$_REQUEST['sellerID'] = $resp->Item->Seller->UserID;
$_REQUEST['maximumBid'] = $resp->Item->MaximumBid;
$_REQUEST['shipping'] = $shippingCost;
$source = file_get_contents('http://www.bca.co.id/id/biaya-limit/kurs_counter_bca/kurs_counter_bca_landing.jsp');
$dom = new DOMDocument("1.0","UTF-8");
@$dom->loadHTML($source);
$dom->preserveWhiteSpace = false;

$rate = $dom->getElementsByTagName('tr')->item(3)->childNodes->item(2)->nodeValue;
$_REQUEST['rate'] = $rate;

// echo $retnb;

?>