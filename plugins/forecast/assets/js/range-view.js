(()=>{

	$('.map').each(function(){
		let lat = $(this).data('lat'),
			long = $(this).data('long');

	  // The location of Uluru
		let uluru = {
					lat: lat, 
					lng: long
				},
				options = {
			  		zoom: 15, 
			  		center: uluru,
			  		disableDefaultUI: true,
			  	};
		// The map, centered at Uluru
		var map = new google.maps.Map(
		  $(this)[0], options);
		// The marker, positioned at Uluru
		var marker = new google.maps.Marker({position: uluru, map: map});
	});

})();