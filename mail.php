<?php
require_once('class.phpmailer.php');
include 'config.php';

class mail {
	private $item;
	private $mail;
	public function __construct($item) {
		$this->item = $item;
		$this->mail = new PHPMailer();  // create a new object
		$this->mail->IsSMTP(); // enable SMTP
		$this->mail->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only
		$this->mail->SMTPAuth = true;  // authentication enabled
		$this->mail->SMTPSecure = SECURE; 
		$this->mail->Host = HOST;
		$this->mail->Port = PORT;
		$this->mail->Username = USERNAME;
		$this->mail->Password = PASSWORD;
		$this->mail->SetFrom(EMAIL, FROM);
	}
	
	function send() {
		autoSetFields($this->item);
		$hasError = false;
		$error = "<div class='error'>";
		
		if ($this->item->name == '') {
			$hasError = true;
			$error .= "Please fill Name field<br> \n";
		} 
		
		if ($this->item ->address1 == '') {
			$hasError = true;
			$error .= "Please fill Address field<br> \n";
		}
		
		if ($this->item->city == '') {
			$hasError = true;
			$error .= "Please fill City field<br> \n";
		}
		
		if ($this->item->zip == '') {
			$hasError = true;
			$error .= "Please fill Zip Code field<br> \n";
		}
		
		if ($this->item->phone == '') {
			$hasError = true;
			$error .= "Please fill Phone field<br> \n";
		}
		
		if ($this->item->email == '') {
			$hasError = true;
			$error .= "Please fill Email field";
		}
		
		$error.= "</div>";
		
		if (!$hasError) {
			$this->sendBuyerInformation();
			$this->sendConfirmationCustomer();
			header("Location: confirmation.php");
		}
		
		return $error;
	}
	
	function sendBuyerInformation() {
		autoSetFields($this->item);
		$this->mail->Subject = SUBJECT_USER_INFORMATION;

		$body = '';
		$body .= "<table width=\"100%\" cellpadding=\"5\"><tr> \n";
		$body .= "<td  width=\"50%\">\n";
		$body .= "<div align=\"left\"> <!-- left align item details --> \n";
		$body .= "Name: <b>" .  $this->item->name . "</b><br> \n";
		$body .= "Address (Line 1): <b>" .  $this->item->address1 . "</b><br> \n";
		$body .= "Address (Line 2): <b>" . $this->item->address . "</b><br> \n";
		$body .= "City: <b>" . $this->item->city . "</b><br> \n";
		$body .= "Zip Code: <b>" . $this->item->zip . "</b><br> \n";
		$body .= "Phone: <b>" . $this->item->phone . "</b><br>\n";
		$body .= "Email: <b>" . $this->item->email . "</b><br>\n";
		$body .= "Note to seller: <b>" . $this->item->note . "</b><br>\n";
		$body .= "Amount Paid: <b>" . $this->item->amountPaid . "</b><br>\n";
		$body .= "Bank: <b>" . $this->item->bank . "</b><br>\n";
		$body .= "<b>================================================================ </b><br>\n";
		$body .= $this->getMailBody();
		
		$this->mail->MsgHTML($body);
		$this->mail->AddAddress('pedroveras@gmail.com', 'Ebay Cost Calculator');
		if(!$this->mail->Send()) {
			echo 'Mail error: '.$this->mail->ErrorInfo;
		} else {
			echo 'Message sent!';
		}
	}
	
	function sendConfirmationCustomer () {
		autoSetFields($this->item);
		$this->mail->Subject = SUBJECT_TO_CUSTOMER;

		$this->mail->MsgHTML($this->getMailBody());
// 		$mail->AddAddress('pedrow_veras@hotmail.com', 'Pedro');
		$this->mail->AddAddress($this->item->email, $this->item->name);
		if(!$this->mail->Send()) {
			echo 'Mail error: '.$this->mail->ErrorInfo;
		} else {
			echo 'Message sent!';
		}
	}
	
	public function getMailBody() {
		$body = '';
		$body .= "<table width=\"100%\" cellpadding=\"5\"><tr> \n";
		$body .= "<td  width=\"50%\">\n";
		//       $retnb .= "<img src=\"$picURL\"/>";
		$body .= "<div align=\"left\"> <!-- left align item details --> \n";
		$body .= "Photo: <img src='" .  $this->item->picture . "'/></b><br> \n";
		$body .= "Status: <b>" .  $this->item->status . "</b><br> \n";
		$body .= "Item Number: <b>" . $this->item->itemNumber . "</b><br> \n";
		$body .= "Title: <b>" . $this->item->title . "</b><br> \n";
		$body .= "Seller ID: <b>" . $this->item->sellerID . "</b><br> \n";
		$body .= "Item Location: <b>" . $this->item->location . "</b><br>\n";
		$body .= "End Time: <b>" . $this->item->endTime . "</b><br>\n";
		$method = $this->item->listing;
		$body.=$this->$method($this->item);
		
		
		$body .= "Shipping (USD): <b>$" . $this->item->shipping . "</b><br>\n";
		$body .= "Shipping (IDR): <b>Rp " . $this->item->getShippingIDR() . "</b><br>\n";
		$body .= "Fee (IDR) : <b> Rp 15.000,00</b><br>\n";
		$body .= "Total Cost: <b>Rp " . $this->item->amountPaid . "</b><br>\n";
		$body .= "</div></td> \n";
		$body .= "</div></td></tr></table> \n<!-- finish table in getSingleItemResults --> \n";
		
		return $body;
	}
	
	public function buy_now($item) {
		$body = '';
		$body .= "Quantity Available: <b>" . $this->item->quantity . "</b><br>\n";
		$body .= "Quantity to Buy: <b>" . $this->item->quantityBuy . "</b><br>\n";
		$body .= "Price (USD): <b>\$" . $this->item->price . "</b><br>\n";
		$body .= "USD to IDR rate: <b>" . $this->item->rate . "</b><br>\n";
		$body .= "Price (IDR): <b>Rp " . $this->item->getOfferPrice() . "</b><br>\n";
		
		return $body;
	}
	
	public function auction($item) {
		$body = '';
		$body .= "Maximum Bid (USD): <b>\$" . $this->item->maximumBid . "</b><br>\n";
		$body .= "USD to IDR rate: <b>" . $this->item->rate . "</b><br>\n";
		$body .= "Maximum Bid (IDR): <b>Rp " . $this->item->getMaximumBidIDR() . "</b><br>\n";
		
		return  $body;
	}
	
	public function bestoffer($item) {
		$body = '';
		$body .= "Quantity Available: <b>" . $this->item->quantity . "</b><br>\n";
		$body .= "Quantity to Buy: <b>" . $this->item->quantityBuy . "</b><br>\n";
		$body .= "Offer Price (USD): <b>\$" . $this->item->price . "</b><br>\n";
		$body .= "USD to IDR rate: <b>" . $this->item->rate . "</b><br>\n";
		$body .= "Offer Price (IDR): <b>Rp " . $this->item->getOfferPrice() . "</b><br>\n";
		
		return  $body;
	}
	
	function autoSetFields($obj, $prefixName = null) {
		$keys = array_keys($_REQUEST);
	
		foreach($keys as $key) {
			$obj->$key = $_REQUEST[$key];
		}
	}
	
}


?>