<?php require('../db/db.php');

	/*  Get all users  */
	$users = getAll($conn, 'user', '');

	/*  Get "haCarCount" parameter if it was supplied and validate that it's a whole number.  */
	$has_car_count = filter_input(INPUT_GET, 'hasCarCount', FILTER_VALIDATE_INT);
	$car_count_only = ($has_car_count === 0 || $has_car_count);


	/*  If the "hasCarCount" was supplied then remove users from the array 
		if the number of their cars does not match the "hasCarCount".  
		Otherwise create "cars" key for each user and set cars data
		as its' value.  */
	foreach ($users as $i => &$user) {
		$cars = getCarsOfUser($conn, $user['id']);
		if (!$car_count_only || count($cars) == $has_car_count) {
			$user['cars'] = $cars;
		}
		else {
			unset($users[$i]);
			$users = array_values($users);
		}
	}

	echo json_encode($users, JSON_PRETTY_PRINT);



	/*  "getCarsOfUser" returns array containing the car_type data of each car owned by the user.  
		Example return:

		[
	        {
	            "id": "8",
	            "plate_number": "RKZ 8363",
	            "manufacturer_name": "BMW",
	            "model": "315",
	            "body_type": "Saloon",
	            "year": "1981",
	            "purchase_price": "80000.88",
	            "retail_price": "5000.19"
	        },
	        {
	            "id": "9",
	            "plate_number": "TLJ 262R",
	            "manufacturer_name": "BMW",
	            "model": "315",
	            "body_type": "Saloon",
	            "year": "1981",
	            "purchase_price": "90000.99",
	            "retail_price": "5000.19"
	        }
	    ]
		*/
	function getCarsOfUser($conn, $user_id) {
		$query = "SELECT car.id, car.plate_number, manufacturer.name AS 'manufacturer_name', car_type.model, car_type.body_type, car_type.year, car.purchase_price, car_type.retail_price  
				  FROM car 
				  INNER JOIN user ON user.id=car.owner_id 
				  INNER JOIN car_type ON car_type.id=car.type_id 
				  INNER JOIN manufacturer ON car_type.manufacturer_id=manufacturer.id 
				  WHERE user.id=".$user_id;

		$result = $conn->query($query);
		if(!$result)
			die($conn->error);

		return $result->fetch_all(MYSQLI_ASSOC);
	}




	/* WORKING BUT UGLY PREVIOUSLY WRITTEN CODE
	$users = getAll($conn, 'user', '');

	$has_car_count = filter_input(INPUT_GET, 'hasCarCount', FILTER_VALIDATE_INT);
	$car_count_only = ($has_car_count === 0 || $has_car_count);

	$required_car_types_id = [];

	foreach ($users as $i=>$user) { 
		$cars = getAll($conn, 'car', ' WHERE owner_id='.$user['id']);
		foreach ($cars as $car)
			array_push($required_car_types_id, $car['type_id']);

		if (!$car_count_only || count($cars) == $has_car_count) {
			$users[$i]['cars'] = $cars;
		}
		else {
			unset($users[$i]);
		}
	}
	$required_car_types_id = array_unique($required_car_types_id);
	
	$types = [];
	foreach ($required_car_types_id as $type) 
		$types[$type] = getSingle($conn, 'car_type', $type);

	foreach ($users as $i=>$user) {
		$cars = $users[$i]['cars'];
		$users[$i]['cars'] = [];
		foreach ($cars as $car) {
			array_push($users[$i]['cars'], $types[$car['type_id']]);
		}
	}

	echo json_encode($users, JSON_PRETTY_PRINT);
	*/



		//foreach ($user as $key => $val) 
		//	echo $key. ' - ' .$val. '<br>';


	/*  Funtion created to avoid retrieving car_type data from type_id multiple times.
		Having a set of all car types required will decrease sql queries count.  */
	/*function getSetOfRequiredCarTypes($users) {
		foreach ($users as $user) { 
			$cars = getAll($conn, 'car', ' WHERE owner_id='.$user['id']);
			$cars = ResolveManufacturerNames($conn, $cars);

			//unset($array['key-here']);
			echo '<br><br>';
		}
	}*/

	/*
	function ResolveManufacturerNames($conn, $cars) {
		// $cars is regular array (not associative), so $i is index
		foreach ($cars as $i=>$car) {
			$id = $car['manufacturer_id']
			unset($car['manufacturer_id']);
			//var_dump($cars[$i]);
		}


		foreach ($cars as $i=>$car) {
			var_dump($car);
		}		
	}*/

?>