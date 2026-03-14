<html>
	<head>
		<title>Payment Process</title>
	</head>
	<body onload="document.payuForm.submit();">
		<section style="text-align: center; padding: 40px; width: 90%;margin: auto;">
			<h2>Processing Payment...</h2>  
			<p>Please do not press refresh or cancel button.</p>
		</section>
		<section style="display: none;">
			<form action="<?php echo $action; ?>" method="post" name="payuForm">
				<input type="hidden" name="key" value="<?php echo $mkey; ?>" />
				<input type="hidden" name="hash" value="<?php echo $hash; ?>"/>
				<input type="hidden" name="txnid" value="<?php echo $txnid; ?>" />
				<input name="amount" value="<?php echo $amount; ?>" />
				<input name="firstname" id="firstname" value="<?php echo $firstname; ?>" />
				<input name="email" id="email" value="<?php echo $email; ?>" />
				<input name="phone" value="<?php echo $phone; ?>" />
				<input name="address1" value="{{ $address }}" />
				<input name="address2" value="{{ $locality }}" />
				<input name="state" value="{{ $state }}" />
				<input name="country" value="{{ Config::get('constants.SHIPPING_COUNTRY') }}" />
				<input name="zipcode" value="{{ $pincode }}" />
				<textarea name="productinfo"><?php echo $productinfo; ?></textarea>
				<input name="udf1" value="<?php echo $udf1; ?>" />
				<input name="surl" value="<?php echo $success; ?>" />
				<input name="furl" value="<?php echo $failure; ?>" />
				<input name="curl" value="<?php echo $cancel; ?>" />
				<input type="submit" value="Submit" />
			</form>
		</section>
	</body>
</html>
