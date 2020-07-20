<?php require('../db/db.php');

	/*  Sanitizing the strings removes html tags if they're present. That prevents cross-site scripting vulnerability.
		Validating numeric values it probably not needed that much (since "bind_param" method is used anyway) but it 
		prevents from making unnecessary database query if the supplied params are invalid.  */
	$manufacturer_name = filter_input(INPUT_GET, 'manufacturerName', FILTER_SANITIZE_STRING);
	$model = filter_input(INPUT_GET, 'model', FILTER_SANITIZE_STRING);
	$body_type = filter_input(INPUT_GET, 'bodyType', FILTER_SANITIZE_STRING);
	$year = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT);
	$retail_price = filter_input(INPUT_GET, 'retailPrice', FILTER_VALIDATE_FLOAT);

	$all_args = [ $manufacturer_name, $model, $body_type, $year, $retail_price ];

	/*  Make sure all arguments were provided  */ 
	if (!all($all_args)) {
		echo "Some required arguments were not provided, arguments: \n";
		foreach ($all_args as $val)
			echo $val."\n";

		die();
	}

	/*  Allowing negative values as prices may lead to vulnerabilities 
		where customers gain money by buying things.  */
	if ($retail_price < 0)
		die('Retail price must be a positive value');

	/*  Get manufacturer id from name, and verify that it exists.  */
	$manufacturer_id = getManufacturerID($conn, $manufacturer_name);

	$query = "INSERT INTO car_type(manufacturer_id, model, body_type, year, retail_price) VALUES (?, ?, ?, ?, ?);";
	$statement = $conn->prepare($query);
	$statement->bind_param('sssid', $manufacturer_id, $model, $body_type, $year, $retail_price);
	
	/*  Respond to user if the statement was executed successfully.  */
	if ($statement->execute()) {
		$response = array (
			'id' => $conn->insert_id,
			'manufacturer_name' => $manufacturer_name,
			'model' => $model,
			'body_type' => $body_type,
			'year' => $year,
			'retail_price' => $retail_price
		);
		echo json_encode($response, JSON_PRETTY_PRINT);;
	}
	else {
		echo "Car couldn't be added, error=".$conn->error;
	}



	function all($collection) { foreach ($collection as $item) { if (!$item) { return false; } } return true; }
?>