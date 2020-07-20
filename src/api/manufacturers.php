<?php require('../db/db.php');

	/*  Get parameters if they're supplied and validate/sanitize them.
		Validate to prevent "year" being a non-number. Sanitize to 
		prevent "name" containing html tags that would lead to cross-site 
		scripting vulnerability.  */
	$year = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT);
	$name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);

	if ($name) {
		$manufacturer = getManufacturerByName($conn, $name);
		if (!$manufacturer) {
			die($name." manufacturer doesn't exist.");
		}
		$manufacturers = [$manufacturer];
	} else { 
		$manufacturers = getAll($conn, 'manufacturer', '');
	}
	
	foreach ($manufacturers as &$manufacturer)
		$manufacturer['car_types'] = getCarTypesOfManufacturer($conn, $manufacturer['id'], $year);

	echo json_encode($manufacturers, JSON_PRETTY_PRINT);



	function getCarTypesOfManufacturer($conn, $manufacturer_id, $year) {

		$query = "SELECT car_type.id, manufacturer.name AS 'manufacturer_name', car_type.model, car_type.body_type, car_type.year, car_type.retail_price  
				  FROM manufacturer
				  INNER JOIN car_type ON car_type.manufacturer_id=manufacturer.id 
				  WHERE manufacturer.id=".$manufacturer_id . ($year ? ' AND year='.$year: '');

		$result = $conn->query($query);
		if(!$result)
			die($conn->error);

		return $result->fetch_all(MYSQLI_ASSOC);
	}


	function getManufacturerByName($conn, $name) {
		if ($stmt = $conn->prepare("SELECT * FROM manufacturer WHERE name=?")) {
			$stmt->bind_param("s", $name);
			$stmt->execute();

			if (!$result = $stmt->get_result()) 
				die($conn->error);

			$manufacturer =  $result->fetch_assoc();
			$stmt->close();
			return $manufacturer;
		}
		return 0;
	}

?>