<?php
	require('config.php');

	/*  Having something like this allows anyone to reset the database.
		This couldn't be used on real website. It's included here for 
		presentation/testing purposes.  */
	if (filter_input(INPUT_GET, 'init', FILTER_VALIDATE_INT) == '1') {
	   require('db-init.php'); 
	   InitDatabase();
	   echo "Resetted and populated the database.";
	}

	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	if ($conn->connect_error)
		die('Failed to connect to MySQL, error:' . $conn->connect_error);	


	/*  Gets all entries from supplied table, the table, and limits parameters can't be directly supplied by the user,
		that's because it would make it vulnerable to SQL injection attacks.  */
	function getAll($conn, $table, $limits) {
		$query = 'SELECT * FROM '.$table.$limits;
		
		// Get Result
		$result = $conn->query($query);

		// Fetch Data
		$ret =  $result->fetch_all(MYSQLI_ASSOC);
		$result->free_result();
		return array_values($ret); // https://stackoverflow.com/questions/6054033/pretty-printing-json-with-php
	}

	/*  Gets a single entry from the supplied table given its' id. Just as in the "getAll" function,
		the supplied $table parameter can't be supplied by the user because that would be not secure.
		The id parameter could be supplied thanks to the "real_escape_string" method.  */
	function getSingle($conn, $table, $id) {
		$id = filter_var($id, FILTER_VALIDATE_INT);
		$query = 'SELECT * FROM '.$table.' WHERE id='.$id;

		// Get Result
		$result = $conn->query($query);

		// Fetch Data
		$ret =  $result->fetch_assoc();
		$result->free_result();
		return $ret;	
	}


	function getManufacturerID($conn, $name) {
		// Check if manufacturer_id exists first - https://stackoverflow.com/a/18114500/4620679
		$query = "SELECT manufacturer.id FROM manufacturer WHERE manufacturer.name = ?";
		$statement = $conn->prepare($query);
		$statement->bind_param('s', $name);
		if ($statement->execute()) {
			$result = $statement->get_result();
			if($result->num_rows === 0) {
				die("Manufacturer with this name doesn't exist.");
			}
			return $result->fetch_assoc()['id'];
		}

		die("Error: ".$conn->error);
	}
?>