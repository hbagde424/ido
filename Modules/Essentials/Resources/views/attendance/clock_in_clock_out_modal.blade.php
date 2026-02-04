<div class="modal fade" id="clock_in_clock_out_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
	  <div class="modal-content">

	    {!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'clockInClockOut']), 'method' => 'post', 'id' => 'clock_in_clock_out_form' ]) !!}
	    <div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      	<h4 class="modal-title"><span id="clock_in_text">@lang( 'essentials::lang.clock_in' )</span>
	      	<span id="clock_out_text">@lang( 'essentials::lang.clock_out' )</span></h4>
	    </div>
	    <div class="modal-body">
	    	<div class="row">
	    		<input type="hidden" name="type" id="type">
		      	<div class="form-group col-md-12">
		      		<strong>@lang( 'essentials::lang.ip_address' ): {{$ip_address}}</strong>
		      	</div>
		      	<div class="form-group col-md-12 clock_in_note @if(!empty($clock_in)) hide @endif">
		        	{!! Form::label('clock_in_note', __( 'essentials::lang.clock_in_note' ) . ':') !!}
		        	{!! Form::textarea('clock_in_note', null, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_in_note'), 'rows' => 3 ]); !!}
		      	</div>
		      	<div class="form-group col-md-12 clock_out_note @if(empty($clock_in)) hide @endif">
		        	{!! Form::label('clock_out_note', __( 'essentials::lang.clock_out_note' ) . ':') !!}
		        	{!! Form::textarea('clock_out_note', null, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_out_note'), 'rows' => 3 ]); !!}
		      	</div>
		      	<input type="hidden" name="clock_in_out_location" id="clock_in_out_location" value="">
	    	</div>
	    	@if($is_location_required)
		    	<div class="row">
		    		<div class="col-md-12">
		    			<b>@lang('messages.location'):</b> <button type="button" class="btn btn-primary btn-xs" id="get_current_location"> <i class="fas fa-map-marker-alt"></i> @lang('essentials::lang.get_current_location')</button>
		    			<br><span class="clock_in_out_location"></span>
		    		</div>
		    		<div class="col-md-12 ask_location" style="display: none;">
		    			<span class="location_required error"></span>
		    			{{-- <button type="button" class="btn btn-sm btn-primary allow_location">
		    				<i class="fas fa-map-marker"></i>
		    				@lang('essentials::lang.allow_location')
		    			</button> --}}
		    		</div>
		    	</div>
		    @endif
	    </div>

	    <div class="modal-footer">
	      <button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
	      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>

	    {!! Form::close() !!}

	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
        	
</div>
<script>
$(document).ready(function() {
    let currentLocation = null;
    let locationAttempts = 0;
    const maxLocationAttempts = 3;
    const isLocationRequired = {!! json_encode($is_location_required) !!};

    // Get Current Location Function
    function getCurrentLocation() {
        locationAttempts++;
        
        // Show loading status
        showLocationLoading();
        $('#get_current_location').prop('disabled', true);

        if (!navigator.geolocation) {
            showLocationError('Geolocation is not supported by this browser.');
            return;
        }

        const options = {
            enableHighAccuracy: true,
            timeout: 15000, // 15 seconds timeout
            maximumAge: 300000 // 5 minutes cache
        };

        navigator.geolocation.getCurrentPosition(
            function(position) {
                handleLocationSuccess(position);
            },
            function(error) {
                handleLocationError(error);
            },
            options
        );
    }

    // Handle successful location retrieval
    function handleLocationSuccess(position) {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;
        const accuracy = position.coords.accuracy;
        const timestamp = new Date().toLocaleString();

        // Store location data - THIS IS THE KEY FIX
        const locationStr = latitude + ',' + longitude;
        currentLocation = {
            raw: locationStr,
            lat: latitude,
            lng: longitude,
            accuracy: Math.round(accuracy),
            timestamp: timestamp
        };

        // Set the hidden input value and KEEP IT SET
        $('#clock_in_out_location').val(currentLocation.raw);

        // Display location info to user - MAKE IT PERSISTENT
        const locationDisplay = `
            <strong>📍 Location Captured:</strong><br>
            <span style="font-family: monospace; background: #f8f9fa; padding: 5px; border-radius: 3px;">
                ${currentLocation.raw}
            </span><br>
            <small class="text-muted">Accuracy: ±${currentLocation.accuracy}m | ${currentLocation.timestamp}</small>
        `;
        
        $('.clock_in_out_location').html(locationDisplay);
        
        // Hide error messages
        $('.ask_location').hide();
        $('.location_required').empty();

        // Show success message
        showLocationSuccess('✅ Location captured successfully!');

        // Re-enable the button for refresh option
        $('#get_current_location').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Refresh Location');

        console.log('Location stored:', currentLocation);
    }

    // Handle location errors
    function handleLocationError(error) {
        let errorMessage = '';
        
        switch(error.code) {
            case error.PERMISSION_DENIED:
                errorMessage = "Location access denied. Please allow location access and try again.";
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage = "Location information is unavailable.";
                break;
            case error.TIMEOUT:
                errorMessage = "Location request timed out. Please try again.";
                break;
            default:
                errorMessage = "An unknown error occurred while retrieving location.";
        }

        if (locationAttempts < maxLocationAttempts) {
            errorMessage += ` (Attempt ${locationAttempts}/${maxLocationAttempts})`;
        }

        showLocationError(errorMessage);
        
        // Re-enable button for retry
        $('#get_current_location').prop('disabled', false);
    }

    // Show loading status
    function showLocationLoading() {
        $('.clock_in_out_location').html(`
            <span class="text-info">
                <i class="fas fa-spinner fa-spin"></i> Getting your location...
            </span>
        `);
        $('.ask_location').hide();
    }

    // Show success status
    function showLocationSuccess(message) {
        // Create or update success message
        if ($('.location_success').length === 0) {
            $('.clock_in_out_location').after(`
                <div class="location_success text-success mt-2" style="font-weight: bold;">
                    ${message}
                </div>
            `);
        } else {
            $('.location_success').html(message);
        }
        
        // Auto-hide success message after 3 seconds
        setTimeout(() => {
            $('.location_success').fadeOut();
        }, 3000);
    }

    // Show error status
    function showLocationError(message) {
        $('.ask_location').show();
        $('.location_required').html(`
            <span class="text-danger">
                <i class="fas fa-exclamation-triangle"></i> ${message}
            </span>
        `);
        
        // Clear any location display
        $('.clock_in_out_location').html('');
        currentLocation = null;
        $('#clock_in_out_location').val('');
    }

    // Event handler for get location button
    $('#get_current_location').on('click', function(e) {
        e.preventDefault();
        getCurrentLocation();
    });

    // Form submission validation - ENHANCED
    $('#clock_in_clock_out_form').on('submit', function(e) {
        const location = $('#clock_in_out_location').val();
        
        console.log('Form submitting - Location value:', location);
        console.log('Current location object:', currentLocation);
        
        if (isLocationRequired && (!location || location.trim() === '')) {
            e.preventDefault();
            showLocationError('Please capture your location before submitting.');
            
            // Scroll to location section
            $('html, body').animate({
                scrollTop: $('#get_current_location').offset().top - 100
            }, 500);
            
            return false;
        }

        // Log successful submission data
        console.log('✅ Form submitting with valid location:', location);
    });

    // Preserve location data when modal is shown/hidden
    $('#clock_in_clock_out_modal').on('show.bs.modal', function() {
        // If we had a location, restore it
        if (currentLocation) {
            $('#clock_in_out_location').val(currentLocation.raw);
            $('.clock_in_out_location').html(`
                <strong>📍 Current Location:</strong><br>
                <span style="font-family: monospace; background: #f8f9fa; padding: 5px; border-radius: 3px;">
                    ${currentLocation.raw}
                </span>
            `);
        }
    });

    // Only clear location when explicitly closing modal (not when switching modes)
    $('#clock_in_clock_out_modal').on('hidden.bs.modal', function() {
        // Only clear if user explicitly closed modal
        setTimeout(() => {
            currentLocation = null;
            locationAttempts = 0;
            $('#clock_in_out_location').val('');
            $('.clock_in_out_location').html('');
            $('.ask_location').hide();
            $('.location_success').remove();
            $('#get_current_location').prop('disabled', false).html('<i class="fas fa-map-marker-alt"></i> Get Current Location');
        }, 100);
    });

    // Debug function (remove in production)
    window.debugLocation = function() {
        console.log('Current location object:', currentLocation);
        console.log('Hidden input value:', $('#clock_in_out_location').val());
        console.log('Display content:', $('.clock_in_out_location').html());
    };
});
</script>
<!--<script>-->
<!--$(document).ready(function () {-->
<!--    $('#get_current_location').on('click', function (e) {-->
<!--        e.preventDefault();-->

<!--        if (navigator.geolocation) {-->
<!--            navigator.geolocation.getCurrentPosition(-->
<!--                function (position) {-->
<!--                    var latitude = position.coords.latitude;-->
<!--                    var longitude = position.coords.longitude;-->

<!--                    var locationStr = latitude + ',' + longitude;-->

                    // Set the hidden input value
<!--                    $('#clock_in_out_location').val(locationStr);-->

                    // Display to user
<!--                    $('.clock_in_out_location').text(locationStr);-->

                    // Hide error message
<!--                    $('.ask_location').hide();-->
<!--                },-->
<!--                function (error) {-->
<!--                    console.error("Error getting location:", error);-->
<!--                    $('.ask_location').show();-->
<!--                    $('.location_required').text("Location access denied or unavailable.");-->
<!--                }-->
<!--            );-->
<!--        } else {-->
<!--            $('.ask_location').show();-->
<!--            $('.location_required').text("Geolocation is not supported by this browser.");-->
<!--        }-->
<!--    });-->

    // Prevent form submission if location is required but not set
<!--    $('#clock_in_clock_out_form').on('submit', function(e) {-->
<!--        const isLocationRequired = {!! json_encode($is_location_required) !!};-->
<!--        const location = $('#clock_in_out_location').val();-->

<!--        if (isLocationRequired && (!location || location.trim() === '')) {-->
<!--            e.preventDefault();-->
<!--            $('.ask_location').show();-->
<!--            $('.location_required').text("Please click 'Get Current Location' before submitting.");-->
<!--        }-->
<!--    });-->
<!--});-->
<!--</script>-->
