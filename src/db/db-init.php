<?php
	/*  This function can be used to create/reset database 'from scratch'.
		It was moved to separate file because it's big.  */
	function InitDatabase() {
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
		$sql_init_sequence = [
			'DROP DATABASE IF EXISTS '.DB_NAME,
			'CREATE DATABASE '.DB_NAME,
			'USE '.DB_NAME,
			
			"CREATE TABLE manufacturer (
			id INT AUTO_INCREMENT PRIMARY KEY, 
			name VARCHAR(100) NOT NULL, 
			country VARCHAR(100) NOT NULL
			)",		

			"INSERT INTO manufacturer
				(name, country) 
			VALUES 
		/* 1 */	('BMW', 'Germany'),  
		/* 2 */ ('Audi', 'Germany'), 
		/* 3 */ ('Toyota', 'Japan'),
		/* 4 */ ('Volkswagen', 'Germany'),
		/* 5 */ ('Hyundai', 'South Korea'),
		/* 6 */ ('General Motors', 'United States'),
		/* 7 */ ('Ford', 'United States'),
		/* 8 */ ('Nissan', 'Japan'),
		/* 9 */ ('Honda', 'Japan'),
	   /* 10 */ ('Renault', 'France'),
	   /* 11 */ ('PSA', 'France'),
	   /* 12 */ ('Suzuki', 'Japan'),
	   /* 13 */ ('SAIC', 'China'),
	   /* 14 */ ('Daimler', 'Germany'),
	   /* 15 */ ('Geely', 'China')",

			"CREATE TABLE car_type (
			id INT AUTO_INCREMENT PRIMARY KEY, 
			manufacturer_id INT NOT NULL, 
			model VARCHAR(100) NOT NULL, 
			body_type VARCHAR(100) NOT NULL, 
			year INT NOT NULL, 
			retail_price DOUBLE NOT NULL,
			FOREIGN KEY (manufacturer_id)
		        REFERENCES manufacturer(id)
		        ON DELETE CASCADE
			)",

			"INSERT INTO car_type 
				(manufacturer_id, model, body_type, year, retail_price) 
			VALUES 
				('1', '315', 'Saloon', '1981', '5000.19'),   /* https://www.teoalida.com/cardatabase/ */
				('1', '316i', 'Saloon', '1991', '15000.28'),
				('1', '318i', 'Saloon', '1987', '7500.37'),
				('2', 'R8', 'Coupe', '2004', '35000.46'),
				('2', 'A4', 'Saloon', '1994', '28000.55')",

			"CREATE TABLE user (
			id INT AUTO_INCREMENT PRIMARY KEY, 
			name VARCHAR(100) NOT NULL, 
			post_code VARCHAR(100) NOT NULL
			)",	

			"INSERT INTO user 
				(name, post_code) 
			VALUES 
				('John Doe', 'CO4 3SQ'),
				('Lionel Messi', 'AAA AAA'),
				('Adam Malysz', 'BBB BBB'),
				('Andres Iniesta', 'CCC CCC'),
				('Vinicius Junior', 'DDD DDD')",

			"CREATE TABLE car (
			id INT AUTO_INCREMENT PRIMARY KEY, 
			owner_id INT, 
			type_id INT NOT NULL,
			purchase_price DOUBLE,
			plate_number VARCHAR(20),
			FOREIGN KEY (owner_id)
		        REFERENCES user(id)
		        ON DELETE SET NULL,
			FOREIGN KEY (type_id)
		        REFERENCES car_type(id)
		        ON DELETE CASCADE
			)",	

			"INSERT INTO car 
				(owner_id, type_id, purchase_price, plate_number) 
			VALUES 
				('1', '2', '10000.11', 'IHZ 5734'), /* John Doe owns the car of type ('1', '316i', 'Saloon', '1991', '10000.0'), which is BMW*/
				('1', '3', '20000.22', 'RKZ 1638'), /* In total he owns 4 cars */
				('1', '4', '30000.33', 'LFM 374N'),
				('1', '5', '40000.44', 'OSM 957'),
				('2', '1', '50000.55', '313 HYN'),  /* Lionel Messi owns 2 cars */
				('2', '2', '40000.14', '918 JTA'),  
				('3', '4', '60000.66', '2 KXU'),    /* Adam Malysz owns only 1 car */
				('4', '4', '70000.77', 'NJZ 2621'), /* Andres Iniesta owns only 1 car */
				('5', '1', '80000.88', 'RKZ 8363'), /* Vinicius Junior owns 7 cars */
				('5', '1', '90000.99', 'TLJ 262R'),
				('5', '2', '100000.12', 'RTY 768'),
				('5', '3', '110000.23', '395 UPF'),
				('5', '5', '120000.24', 'GJZ 9021'),
				('5', '4', '130000.25', 'GUI 8018'),
				('5', '2', '140000.26', 'LCZ 5841')",

		];

		foreach ($sql_init_sequence as $sql) {
			//echo '<br><br>'.$sql.'<br>';
			if (!$conn->query($sql)) {
				echo "Error: " . $conn->error;
			}
		}
	}
?> 