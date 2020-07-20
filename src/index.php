<?php require('db/db.php');	require('inc/header.php'); ?>


<div  style="height: 100vh; width: 98vw;">
  	<div class="row" style="height:100%;">
	    <div class="col-4">

	    	<!--  Text area showing response from the API  -->
			<div id="textarea_container" style="width:100%; height: 70%; padding-left: 20px; padding-top:20px; padding-bottom: 20px;" name="textarea-name">
				<textarea id="raw_response" style="height:100%; width:100%; border:none;"></textarea>
			</div>

			<!--  "Test query" button  -->
			<div id="test_button" style="height:30%">
				<h1>Test query</h1>
			</div>
	    </div>


	    <div class="col-8" style="margin: 0; padding:0;">

	    	<!-- 1st row of 3 endpoints -->
	    	<div class="row endpoint bg-light" style="height: 35%"> 
				<div class="col-4 active_endpoint" data-point="1">
					<span>1. Init database</span>
					<p>Creates fresh database with 4 tables and populates them with some entries.</p>
				</div>

				<div class="col-4" data-point="2">
					<span>2. All cars</span>
					<p>Responds with all car types info. Output can be limited by optional parameters.</p>
				</div>

				<div class="col-4" data-point="2a">
					<span>2a. Single car</span>
					<p>Responds with single car type info given its' id.</p>
				</div>
	    	</div>

	    	<!-- 2nd row of 3 endpoints -->
	    	<div class="row endpoint bg-light" style="height: 35%"> 
				<div class="col-4" data-point="2b">
					<span>2b. New car type</span>
					<p>Creates new entry in car_type table and returns information about it. 
					   First it checks that the manufacturer with supplied name exists.</p>
				</div>

				<div class="col-4" data-point="3">
					<span>3. Users</span>
					<p>Returns all users including the cars they own. Output can be limited by optional "hasCarCount" parameter which shows only the users who own specific number of cars.</p>
				</div>

				<div class="col-4" data-point="4">
					<span>4. Manufacturers</span>
					<p>Returns all manufacturers. Output can be limited by optional parameters. Optional parameters are: name, year.</p>
				</div>
	    	</div>

	    	<!-- Params row -->
			<div class="row bg-dark" style="height:20%; padding-bottom: 20px;"> 
				<form id="options_form" class="form-inline params">
					<div id="option_1" class="input-group option">
					  <div class="input-group-prepend">
					    <span class="input-group-text option_name" id="inputGroup-sizing-lg"></span>
					  </div>
					  <input type="text" class="form-control" placeholder="" aria-label="Large" aria-describedby="inputGroup-sizing-lg">
					</div>
					<div id="option_2" class="input-group option">
					  <div class="input-group-prepend">
					    <span class="input-group-text option_name" id="inputGroup-sizing-lg"></span>
					  </div>
					  <input type="text" class="form-control" placeholder="" aria-label="Large" aria-describedby="inputGroup-sizing-lg">
					</div>
					<div id="option_3" class="input-group option">
					  <div class="input-group-prepend">
					    <span class="input-group-text option_name" id="inputGroup-sizing-lg"></span>
					  </div>
					  <input type="text" class="form-control" placeholder="" aria-label="Large" aria-describedby="inputGroup-sizing-lg">
					</div>
					<div id="option_4" class="input-group option">
					  <div class="input-group-prepend">
					    <span class="input-group-text option_name" id="inputGroup-sizing-lg"></span>
					  </div>
					  <input type="text" class="form-control" placeholder="" aria-label="Large" aria-describedby="inputGroup-sizing-lg">
					</div>
					<div id="option_5" class="input-group option">
					  <div class="input-group-prepend">
					    <span class="input-group-text option_name" id="inputGroup-sizing-lg"></span>
					  </div>
					  <input type="text" class="form-control" placeholder="" aria-label="Large" aria-describedby="inputGroup-sizing-lg">
					</div>
				</form>	
	    	</div>

	    	<!--  Query preview row  -->
	   		<div class="row bg-dark align-items-center" style="height:30%; padding-top: 5px"> 
				<form style="height:100%; padding-left: 25px; padding-right: 25px; width:100%;">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text" id="inputGroup-sizing-lg">Query preview</span>
						</div>
						<input class="form-control query_preview" type="text" placeholder="" aria-label="Large" aria-describedby="inputGroup-sizing-lg" readonly>
					</div>
				</form>	
			</div>	
	    	
	    </div>
	</div>
</div>



<!-- javascript -->
<script>
	var base_path = '<?php echo ROOT_URL; ?>'; //'http://localhost/individual-challenge/';
	var selected_point = 0; 


	// On page ready
	$(function() {
		$('textarea').val('');

		/*  Limit the size of the input boxes (for optional parameters). Without this limit the boxes didn't fit
			because there were too many of them when testing point 2b (add_car.php).  */
		$('.option input').css('width', '100px');

		presentPoint('1');
	});


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

	/*  Enables/disables input boxes depending on which endpoint is meant to be tested.
		It also updates the query preview box.  */
	$('.endpoint').click(function() {
		var point = $('.endpoint').find(".active_endpoint").attr('data-point');
		selected_point = point;
		presentPoint(point);
	}); 
	
	/*  This allows to keep the active state of the selected 
	square and define its' appearance using CSS. */
	$('.endpoint div[class^="col-"]').click(function() {
		$('.endpoint').find(".active_endpoint").removeClass("active_endpoint");
		$(this).addClass('active_endpoint');
	});

	/*  Hides the optional input boxes of previosuly selected endpoint and displays
		the optional input boxes for the currently selected endpoint. fadeIn and fadeOut methods 
		make it more aesthetically pleasing than using show/hide methods.  */
	function presentPoint(p) {
		/*  Clear values of all optional input boxes  */
		$('.option').each(function() {
			$(this).find('input').val('');
		});

		/*  Hide all optional input boxes, set their names to the ones that are currently desired and show them.  */
		$('.option').fadeOut(200).promise().done(function() {
			endpoints[p]['options'].forEach(function(item, i) { 
				$('#option_' + (i+1)).find('.option_name').html(item);
			 	$('#option_' + (i+1)).fadeIn(200);
			 }
			);
		});

		/*  Set the query preview to the currently desired one.  */
		$('.query_preview').attr('placeholder', endpoints[p]['path']);
	}


	function getFullQuery() {
		return base_path + $('.query_preview').attr('placeholder');
	}


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


	/*  Behaveiour of the bottom-left "Test query" button.  */
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

</script>


<?php require('inc/footer.php'); ?>