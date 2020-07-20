# Written progress report

## Monday (13/01/2020)
* decided to go with PHP  
* watched few [video tutorials](https://www.youtube.com/watch?v=oJbfyzaA2QA&list=PLillGF-Rfqbap2IB6ZS4BBBcYPagAjpjn) about PHP by Brad Traversy (on youtube)  
* installed software (xampp, including Apache and MySQL servers)  
* got familiar with db-demo.php file  
* decided to avoid using PDO (as used in db-demo.php) and go with "mysqli object" instead  
* created config.php as suggested in the "PHP front to back" tutorial series, this allows to keep all the configuration names in one place (e.g. database username, password, name)  
```php
<?php 
    define('ROOT_URL', 'http://localhost/individual-challenge/');
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');  // to be supplied by tutor
    define('DB_PASS', '-');     // to be supplied by tutor
    define('DB_NAME', 'ce292_mb17308');
?>
```

* created db-init.php with "InitDatabase()" function which creates/resets the database, 3 tables (car, user, manufacturer), and populates them with some entries that will allow to test the API. The InitDatabase function contains an array of SQL commands that are executed one after another.  
```php
foreach ($sql_init_sequence as $sql) {
    if (!$conn->query($sql)) {
        echo "Error: " . $conn->error;
    }
}
```

* created db.php which provides the following:  
    * $conn object (with established mysql connection)  
    * "getSingle" function - to get single object from table by its' id
    * "getAll" function - to get all object from table  
* created cars.php, including the optional parameters. Initially I made just tried to make it work, then I modified it to be concise (by encapsulating code in functions where possible), resulting in the simple:  
```php
if ($id = getIdFromURI_Route()) {
    echo getSingle($conn, 'car_type', $id);
}
else {
    echo getAll($conn, 'car_type', getOptionalLimits());
}
```


## Tuesday (14/01/2020)  
* implemented add_car.php which creates new car_type entry, before inserting the car_type into database it sanitizes and validates the input, making sure that all arguments are supplied. It additionally queries the database checking that the manufacturer having provided name exists in the database (code below is responsible for that).  
```php 
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
```
* added new table to the InitDatabase sequence which "connects" cars with users and car_types. Currently that's how the initialized and populated database looks:  

### Tables:   
![image couldn't be displayed](https://raw.githubusercontent.com/michalmonday/files/master/ce292_individual_challenge/tables.png)  

### user table  
![image couldn't be displayed](https://raw.githubusercontent.com/michalmonday/files/master/ce292_individual_challenge/table_user.png)  

### car_type table
![image couldn't be displayed](https://raw.githubusercontent.com/michalmonday/files/master/ce292_individual_challenge/table_car_type.png)  

### car table (linking car with user and type)
![image couldn't be displayed](https://raw.githubusercontent.com/michalmonday/files/master/ce292_individual_challenge/table_car.png)  

### manufacturer table
![image couldn't be displayed](https://raw.githubusercontent.com/michalmonday/files/master/ce292_individual_challenge/table_manufacturer.png)  

* implemented users.php and manufacturers.php just as described in the task instructions on moodle. Initially when I created users.php, I made it work properly but it was overcomplicated. I solved that problem by searching on google for more information about SQL queries to get what I want directly from database instead of unnecessarily manipulating the data afterwards. This can be seen by looking at users.php in [this commit](https://cseegit.essex.ac.uk/2019_ce291/challenge-week-software/challenge-week-software_mb19424/commit/1c4689edf6dc01f1759aa0d71058639eb74f1af1). At the end of the day both, manufacturers.php and users.php logic were implemented in relatively simple way:  
```php
// part of manufacturers.php

$year = filter_input(INPUT_GET, 'year', FILTER_VALIDATE_INT);
$name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING);

if ($name)
    $manufacturers = [getManufacturerByName($conn, $name)];
else 
    $manufacturers = getAll($conn, 'manufacturer', '');

foreach ($manufacturers as &$manufacturer)
    $manufacturer['car_types'] = getCarTypesOfManufacturer($conn, $manufacturer['id'], $year);

echo json_encode($manufacturers, JSON_PRETTY_PRINT);
``` 

```php
// part of users.php

$users = getAll($conn, 'user', '');

$has_car_count = filter_input(INPUT_GET, 'hasCarCount', FILTER_VALIDATE_INT);
$car_count_only = ($has_car_count === 0 || $has_car_count);

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
```

* started working on the website dedicated to test the web API  

# Wednesday (15/01/2020)  
* deleted the "test_points" folder and decided to store everything in the index.php with javascript changing query paths upon press of the menu button (each dedicated for testing different point)  
* changed the bootstrap theme to [Flatly](https://bootswatch.com/flatly/)  
* integrated [JSONedtr](https://www.jqueryscript.net/demo/visual-json-editor-jsonedtr/) plugin to display the response in formatted table aside from showing raw response on the left panel
![image couldn't be displayed](https://raw.githubusercontent.com/michalmonday/files/master/ce292_individual_challenge/JSONedtr.png)  

That's the current state of the website:  
![image couldn't be displayed](https://raw.githubusercontent.com/michalmonday/files/master/ce292_individual_challenge/website_look_on_wednesday.png)  

# Thursday (16/01/2020)  
At this point, the API satisfies all the points from the assignment instructions. The website is very simple, there's still 1 day left until presentation so it possibly could be improved. The main aim is to meet all the marking criteria (which states that look and feel of the GUI is worth 20%), for that reason it would be a good idea to ask lab tutors if it's required or not (in order to get good marks).    

After asking lab tutors for advice I decided to create additional input boxes for optional parameters. This will require rearranging the interface.  

So today I created the new page to test web API "from scratch", trying to implement the input boxes for optional parameters and trying to make it look aesthetically pleasing at the same time. I decided to use the newer bootstrap version (4) instead of the previously used one (3) which I used only because of an old tutorial on youtube, I also started relying more on the documentation of the boostrap, rathen than relying on the examples found at external sources. I also decided to get rid of the navbar and get rid of the JSONedtr plugin, both of these were not really needed.  

![image couldn't be displayed](https://raw.githubusercontent.com/michalmonday/files/master/ce292_individual_challenge/website_look_on_thursday.png)  

I also added some comments around the PHP, javascript code and some html elements. The new website is using significantly more javascript code, which is responsible for recognition of user input into text boxes (for optional parameters), that in turn is used to neatly update the preview of the query. Thanks to fadeIn/fadeOut methods, the input boxes for optional paremeters appear/hide in a smooth way. I removed the description from the "endpoints" object in javascript and added "options":  
```javascript
/*  Object holding names (e.g. 2b), paths (e.g. api/cars.php) and optional parameter names for specific enpoints.  */
var endpoints = {
'1' : { 
    'path' : 'db/db.php?init=1',
    'options': []
},
'2' : { 
    'path' : 'api/cars.php',  //'api/cars.php?year=2004&priceFrom=1.1&priceTo=50000.0&manufacturerId=2',
    'options': ['year', 'priceFrom', 'priceTo', 'manufacturerId']
},
'2a' : { 
    'path' : 'api/cars.php/',  //api/cars.php/3',
    'options': ['id']
},
'2b' : { 
    'path' : 'api/add_car.php',  //'api/add_car.php?manufacturerName=BMW&model=E12&bodyType=Saloon&year=1981&retailPrice=25000.99',
    'options': ['manufacturerName', 'model', 'bodyType', 'year', 'retailPrice']
},
'3' : { 
    'path' : 'api/users.php',  //'api/users.php?hasCarCount=7',
    'options': ['hasCarCount']
},
'4' : { 
    'path' : 'api/manufacturers.php', //'api/manufacturers.php?year=2004',
    'options': ['name', 'year']
}
};

```

The function below is responsible for updating the query whenever something is typed/deleted from one of the optional input boxes.  
```javascript
/*  When user types something into the input box, then automatically update the query preview box.
    Reference: https://stackoverflow.com/a/31037145/4620679  */
$('.option').on('input', ':text', function(event){ 
    
    /*  Single car id is supposed to be passed as a part of the route of the query
        not like a "standard get" parameter. That's why it is just appended to the 2a-endpoint path.  */
    if (selected_point == '2a') {
        $('.query_preview').attr('placeholder', endpoints['2a']['path'] + $(event.target).val());
        return;
    }
    
    var param_name = $(event.target).prev().find('.option_name').html();
    var current_url = $('.query_preview').attr('placeholder');

    /*  Reference: https://stackoverflow.com/a/51059663/4620679  */
    var url = new URL(current_url, base_path);
    var param_val = $(event.target).val();

    /*  Reference: https://developer.mozilla.org/en-US/docs/Web/API/URLSearchParams/delete  */
    if (!param_val && url.searchParams.has(param_name))
        url.searchParams.delete(param_name);			
    else 
        url.searchParams.set(param_name, param_val); 
    
    $('.query_preview').attr('placeholder', url.href.replace(base_path, ''));
});
```

The 2 functions below determine what happens what happens when "Test query" button is pressed. That involves GET request being sent without reloading the page, which is also referred to as AJAX request.  
```javascript
/*  Behaviour of the bottom-left "Test query" button.  */
$('#test_button').click(function() {
    var url = getFullQuery();
    testQuery(url);
});


/*  Function below handles what happens when "Test" button is pressed.  */
function testQuery(url) {
    /*  Ajax call to the supplied query path. Function supplied as 
        2nd parameters determines what happens once response arrives.  */
    $.get(url, function(data) {
        $('#raw_response').val(data);
    });
}
```

The responsive feel of the page was to some extent achieved by using css instructions such as:  
```css
.endpoint div[class^="col-"]:not(.active_endpoint):hover { border: 1px dashed gray; }
#test_button:hover { background-color: #23272B; }
#test_button:active { background-color: #282B30; }
.active_endpoint { border: 2px solid gray; }
```

# Friday (17/01/2020) 
* fixed manufacturers bug (when name is supplied that isn't in the database). Now it responds with a message (Name does not exist in the database) instead of returning SQL error message.  
* uploaded the website to the free 000webhost hosting, it can be accessed at [this link](http://lsgc-web.000webhostapp.com/individual-challenge/)  
