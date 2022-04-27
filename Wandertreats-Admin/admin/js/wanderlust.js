$( document ).ready(function() {

	$('.wrapper').css("opacity",1);
	$('#loading').css("opacity",0);
	$('#loading').css("display","hidden");


   $.ajax({
	  url: "../ajax/ajax_flights.php",
	  cache: false,
	  type: "POST",
	  success: function(data){
	  	$('#flightTable').html(data);
	  }
	});


});

function editFlightStatus(a){

	var status = "";
	var remarks = $('#flightRemarks').val();
	$('#flighStatusModal').modal('toggle');
	$('#flightStatusId').val(a);

	$('#flightStatus').on('change',function(){
        status = $('#flightStatus option:selected').text();
	});

	$("#saveFlightStatusChanges").off("click");
    $("#saveFlightStatusChanges").on("click", function(e) {
        
 	 	
	 	$.ajax({
		  url: "../ajax/ajax_save_flightstatus_changes.php",
		  cache: false,
		  type: "POST",
  		  data: {
  		  	id : a,
  		  	status : status,
  		  	remarks : remarks

  		  },
		  success: function(data){
		  	 $('#flighStatusModal').modal('hide');
		  	 $('#flightTable').html(data);
		  	 
		  }
		});
  	});

}

function viewFlight(bokkingref){


	$('#viewFlightModal').modal('toggle');
	$('#modalTitle').html('Booking Reference : '+bokkingref);


        
 	 	
	 	$.ajax({
		  url: "../ajax/ajax_load_flight_details.php",
		  cache: false,
		  type: "POST",
  		  data: {
  		  	id : bokkingref

  		  },
		  success: function(data){
			$('#modalContent').html(data);
		  }
		});
 
}


function deleteItem(key, id, tablename){


	confirm("Are you sure you want to delete this item?");
 	 	
	 	$.ajax({
		  url: "../ajax/ajax_delete.php",
		  cache: false,
		  type: "POST",
  		  data: {
  		  	key : key,
  		  	id : id,
  		  	tablename : tablename

  		  },
		  success: function(data){
			window.location.href = window.location.protocol +'//'+ window.location.host + window.location.pathname;
		  },
		  error: function (xhr, ajaxOptions, thrownError) {
		  	alert(key+","+id+","+tablename);
	        alert(xhr.status);
	        alert(thrownError);
	      }
		});
 
}




