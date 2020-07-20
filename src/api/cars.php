<?php require('../db/db.php');

	if ($id = getIdFromURI_Route())
		$response = getSingle($conn, 'car_type', $id);
	else
		$response = getAll($conn, 'car_type', getOptionalLimits());
	
	echo json_encode($response, JSON_PRETTY_PRINT);



	/*  Returns id if it's supplied at the end of the query path.
		E.g. returns 3 if "/cars.php/3" is entered.  */
	function getIdFromURI_Route() {
		$last_part_of_url = explode('/', $_SERVER['REQUEST_URI']);
		return filter_var(end($last_part_of_url), FILTER_VALIDATE_INT);
	}

	/*  Returns string like:
		" WHERE (year = 111111111 AND price_from >= 1.1 AND price_to <= 22.22 AND manufacturerId = 5)" 

		That string can be inserted into the sql query to get only specific cars.  
		It is ensured that supplied parameters are not strings (so the potentially 
		vulnerable "getAll" function (from db.php) can be used.   */
	function getOptionalLimits() {
		$year = filter_input(INPUT_GET, "year", FILTER_VALIDATE_INT);
		$price_from = filter_input(INPUT_GET, "priceFrom", FILTER_VALIDATE_FLOAT);
		$price_to = filter_input(INPUT_GET, "priceTo", FILTER_VALIDATE_FLOAT);
		$manufacturer_id = filter_input(INPUT_GET, "manufacturerId", FILTER_VALIDATE_INT);

		$limits = "";
		$and = "";
		if (any ([ $year, $price_from, $price_to ])) {
			$limits = " WHERE (";
			if ($year) {
				$limits .= "year = ".$year;
				$and = " AND ";
			}

			if ($price_from) {
				$limits .= $and . "retail_price >= " . $price_from;
				$and = " AND ";
			}

			if ($price_to) {
				$limits .= $and . "retail_price <= " . $price_to;
				$and = " AND ";
			}

			if ($manufacturer_id) {
				$limits .= $and . "manufacturer_id = " . $manufacturer_id;
			}

			$limits .= ")";
		}
		return $limits;
	}

	/*  Just like in python (is any of the items in the collections not NULL)  */
	function any($collection) { foreach ($collection as $item) { if ($item) { return true; } } return false; }
?>