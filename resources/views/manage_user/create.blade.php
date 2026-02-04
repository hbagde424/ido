@extends('layouts.app')

@section('title', __( 'user.add_user' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>@lang( 'user.add_user' )</h1>
</section>

<!-- Main content -->
<section class="content">
{!! Form::open(['url' => action([\App\Http\Controllers\ManageUserController::class, 'store']), 'method' => 'post', 'id' => 'user_add_form', 'files' => true ]) !!}
  <div class="row">
    <div class="col-md-12">
  @component('components.widget')
      <div class="col-md-2">
        <div class="form-group">
          {!! Form::label('surname', __( 'business.prefix' ) . ':') !!}
            {!! Form::text('surname', null, ['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
            {!! Form::text('first_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
            {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('email', __( 'business.email' ) . ':*') !!}
            {!! Form::text('email', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.email' ) ]); !!}
        </div>
      </div>

      <div class="col-md-2">
        <div class="form-group">
          <div class="checkbox">
            <br/>
            <label>
                 {!! Form::checkbox('is_active', 'active', true, ['class' => 'input-icheck status']); !!} {{ __('lang_v1.status_for_user') }}
            </label>
            @show_tooltip(__('lang_v1.tooltip_enable_user_active'))
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="form-group">
          <div class="checkbox">
            <br/>
            <label>
                 {!! Form::checkbox('is_enable_service_staff_pin', 1, false, ['class' => 'input-icheck status', 'id' => 'is_enable_service_staff_pin']); !!} {{ __('lang_v1.enable_service_staff_pin') }}
            </label>
            @show_tooltip(__('lang_v1.tooltip_is_enable_service_staff_pin'))
          </div>
        </div>
      </div>
      <div class="col-md-2 hide service_staff_pin_div">
        <div class="form-group">
          {!! Form::label('service_staff_pin', __( 'lang_v1.staff_pin' ) . ':') !!}
            {!! Form::password('service_staff_pin', ['class' => 'form-control', 'required' => true, 'placeholder' => __( 'lang_v1.staff_pin' ) ]); !!}
        </div>
      </div>
  @endcomponent
  </div>
  <div class="col-md-12">
    @component('components.widget', ['title' => __('lang_v1.roles_and_permissions')])
      <div class="col-md-4">
        <div class="form-group">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('allow_login', 1, true, 
                [ 'class' => 'input-icheck', 'id' => 'allow_login']); !!} {{ __( 'lang_v1.allow_login' ) }}
              </label>
            </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="user_auth_fields">
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('username', __( 'business.username' ) . ':') !!}
          @if(!empty($username_ext))
            <div class="input-group">
              {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
              <span class="input-group-addon">{{$username_ext}}</span>
            </div>
            <p class="help-block" id="show_username"></p>
          @else
              {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
          @endif
          <p class="help-block">@lang('lang_v1.username_help')</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('password', __( 'business.password' ) . ':*') !!}
            {!! Form::password('password', ['class' => 'form-control', 'required', 'placeholder' => __( 'business.password' ) ]); !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('confirm_password', __( 'business.confirm_password' ) . ':*') !!}
            {!! Form::password('confirm_password', ['class' => 'form-control', 'required', 'placeholder' => __( 'business.confirm_password' ) ]); !!}
        </div>
      </div>
    </div>
      <div class="clearfix"></div>
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('role', __( 'user.role' ) . ':*') !!} @show_tooltip(__('lang_v1.admin_role_location_permission_help'))
            {!! Form::select('role', $roles, null, ['class' => 'form-control select2']); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-3">
          <h4>@lang( 'role.access_locations' ) @show_tooltip(__('tooltip.access_locations_permission'))</h4>
        </div>
        <div class="col-md-9">
          <div class="col-md-12">
            <div class="checkbox">
                <label>
                  {!! Form::checkbox('access_all_locations', 'access_all_locations', true, 
                ['class' => 'input-icheck']); !!} {{ __( 'role.all_locations' ) }} 
                </label>
                @show_tooltip(__('tooltip.all_location_permission'))
            </div>
          </div>
          @foreach($locations as $location)
          <div class="col-md-12">
            <div class="checkbox">
              <label>
                {!! Form::checkbox('location_permissions[]', 'location.' . $location->id, false, 
                [ 'class' => 'input-icheck']); !!} {{ $location->name }} @if(!empty($location->location_id))({{ $location->location_id}}) @endif
              </label>
            </div>
          </div>
          @endforeach
        </div>
    @endcomponent
  </div>

  </div>
    @include('user.edit_profile_form_part')

    @if(!empty($form_partials))
      @foreach($form_partials as $partial)
        {!! $partial !!}
      @endforeach
    @endif
  <div class="row">
    <div class="col-md-12 text-center">
      <button type="submit" class="btn btn-primary btn-big" id="submit_user_button">@lang( 'messages.save' )</button>
    </div>
  </div>
{!! Form::close() !!}
  @stop
@section('javascript')
<style type="text/css">
/* Force select2 to be visible and properly styled */
.select2-container {
  width: 100% !important;
  z-index: 9999 !important;
}

.select2-container--default .select2-selection--single {
  height: 34px;
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 32px;
}

.select2-dropdown {
  z-index: 10000 !important;
  border: 1px solid #3c8dbc;
}

/* Ensure the dropdown is prominent */
.select2-results__option {
  padding: 8px 12px;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
  background-color: #3c8dbc;
}
</style>
<script type="text/javascript">
  __page_leave_confirmation('#user_add_form');
  $(document).ready(function(){
    $('#is_enable_service_staff_pin').on('ifChecked', function(event){
      $('div.service_staff_pin_div').removeClass('hide');
    });

    $('#is_enable_service_staff_pin').on('ifUnchecked', function(event){
      $('div.service_staff_pin_div').addClass('hide');
      $('#service_staff_pin').val('');
    });

    $('#allow_login').on('ifChecked', function(event){
      $('div.user_auth_fields').removeClass('hide');
    });
    $('#allow_login').on('ifUnchecked', function(event){
      $('div.user_auth_fields').addClass('hide');
    });
    
    // Document preview functionality
    $('.document-upload').on('change', function(e) {
      var input = this;
      var docType = $(this).data('doc-type');
      var previewDiv = $('#preview_' + docType);
      
      if (input.files && input.files[0]) {
        var file = input.files[0];
        var reader = new FileReader();
        
        reader.onload = function(e) {
          var fileExtension = file.name.split('.').pop().toLowerCase();
          var previewHtml = '';
          
          if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            previewHtml = '<div class="document-preview-item mt-2">' +
              '<img src="' + e.target.result + '" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; padding: 5px;" />' +
              '<p class="mt-1"><small>' + file.name + '</small></p>' +
              '</div>';
          } else {
            previewHtml = '<div class="document-preview-item mt-2">' +
              '<i class="fa fa-file fa-3x text-info"></i>' +
              '<p class="mt-1"><small>' + file.name + '</small></p>' +
              '</div>';
          }
          
          previewDiv.html(previewHtml);
        };
        
        reader.readAsDataURL(file);
      }
    });
    
    // Helper function to debug select2
    function debugSelect2(selector) {
      var element = $(selector);
      console.log("Select element:", element);
      console.log("Is visible:", element.is(":visible"));
      console.log("CSS display:", element.css("display"));
      console.log("Parent visibility:", element.parent().is(":visible"));
      console.log("Option count:", element.find("option").length);
      console.log("Selected value:", element.val());
      
      if (element.hasClass("select2-hidden-accessible")) {
        console.log("Select2 initialized");
        var select2Container = element.next(".select2-container");
        console.log("Select2 container:", select2Container);
        console.log("Select2 container visible:", select2Container.is(":visible"));
      } else {
        console.log("Select2 not initialized");
      }
    }
    
    // Highlight the designation dropdown to make it more noticeable
    setTimeout(function() {
      $('select[name="essentials_designation_id"]').next('.select2-container').css({
        'border': '2px solid #3c8dbc',
        'box-shadow': '0 0 10px rgba(60, 141, 188, 0.5)',
        'background-color': '#f8f9fa'
      });
    }, 500);
    
    // Load designations based on pre-selected department (if any)
    var initial_department = $('select[name="essentials_department_id"]').val();
    if (initial_department) {
      $.ajax({
        method: 'GET',
        url: '/taxonomies/designations-by-department',
        data: { department_id: initial_department },
        dataType: 'json',
      success: function(result) {
        if (result.success) {
          var designation_dropdown = $('select[name="essentials_designation_id"]');
          
          // First destroy select2 if it exists
          if (designation_dropdown.hasClass("select2-hidden-accessible")) {
            designation_dropdown.select2('destroy');
          }
          
          // Clear current options (except the first placeholder option)
          designation_dropdown.find('option:not(:first)').remove();
          
          // Add all designation options
          $.each(result.designations, function(key, value) {
            designation_dropdown.append(
              $('<option></option>')
                .attr('value', key)
                .text(value)
            );
          });
          
          console.log("Before select2 initialization:");
          debugSelect2('select[name="essentials_designation_id"]');
          
          // Re-initialize select2 with a small delay
          setTimeout(function() {
            // Make sure the dropdown and its container are visible
            var parentContainer = designation_dropdown.closest('.form-group');
            parentContainer.css('display', 'block');
            designation_dropdown.css('display', 'block');
            
            // Initialize select2 with advanced options
            designation_dropdown.select2({
              width: '100%',
              placeholder: 'Please Select',
              dropdownParent: $('body'), // Attach dropdown to body to avoid visibility issues
              dropdownPosition: 'below',
              dropdownAutoWidth: true,
              allowClear: true
            });
            
            // Force a redraw
            $(window).trigger('resize.select2');
            
            console.log("After select2 initialization:");
            debugSelect2('select[name="essentials_designation_id"]');
            console.log("Initial designations loaded: " + Object.keys(result.designations).length + " options");
          }, 100);
        }
        },
        error: function(xhr, status, error) {
          console.error("Error loading initial designations:", error);
        }
      });
    }    // Add click event to manually open the dropdown
    $(document).on('click', '.select2-container--default .select2-selection--single', function(){
      var selectElement = $(this).parent().siblings('select');
      if(selectElement.hasClass('select2-hidden-accessible')){
        selectElement.select2('open');
      }
    });
  });

  $('form#user_add_form').validate({
                rules: {
                    first_name: {
                        required: true,
                    },
                    email: {
                        email: true,
                        remote: {
                            url: "/business/register/check-email",
                            type: "post",
                            data: {
                                email: function() {
                                    return $( "#email" ).val();
                                }
                            }
                        }
                    },
                    password: {
                        required: true,
                        minlength: 5
                    },
                    confirm_password: {
                        equalTo: "#password"
                    },
                    username: {
                        minlength: 5,
                        remote: {
                            url: "/business/register/check-username",
                            type: "post",
                            data: {
                                username: function() {
                                    return $( "#username" ).val();
                                },
                                @if(!empty($username_ext))
                                  username_ext: "{{$username_ext}}"
                                @endif
                            }
                        }
                    }
                },
                messages: {
                    password: {
                        minlength: 'Password should be minimum 5 characters',
                    },
                    confirm_password: {
                        equalTo: 'Should be same as password'
                    },
                    username: {
                        remote: 'Invalid username or User already exist'
                    },
                    email: {
                        remote: '{{ __("validation.unique", ["attribute" => __("business.email")]) }}'
                    }
                }
            });
  $('#username').change( function(){
    if($('#show_username').length > 0){
      if($(this).val().trim() != ''){
        $('#show_username').html("{{__('lang_v1.your_username_will_be')}}: <b>" + $(this).val() + "{{$username_ext}}</b>");
      } else {
        $('#show_username').html('');
      }
    }
  });

  // AJAX call to get designations when department changes
  $(document).on('change', 'select[name="essentials_department_id"]', function() {
    var department_id = $(this).val();
    var designation_dropdown = $('select[name="essentials_designation_id"]');
    
    console.log("Department changed to:", department_id);
    
    // Fetch designations based on selected department
    $.ajax({
      method: 'GET',
      url: '/taxonomies/designations-by-department',
      data: { department_id: department_id }, // Pass actual department_id for filtering
      dataType: 'json',
      success: function(result) {
        console.log("AJAX response for department_id " + department_id + ":", result);
        if (result.success) {
          // Properly destroy the select2 instance
          if (designation_dropdown.hasClass("select2-hidden-accessible")) {
            designation_dropdown.select2('destroy');
          }
          
          // Clear current options
          designation_dropdown.empty();
          
          // Add placeholder option
          designation_dropdown.append(
            $('<option></option>')
              .attr('value', '')
              .text('Please Select')
          );
          
          // Check if designations exist
          if (result.designations && Object.keys(result.designations).length > 0) {
            // Add all designation options
            $.each(result.designations, function(key, value) {
              designation_dropdown.append(
                $('<option></option>')
                  .attr('value', key)
                  .text(value)
              );
            });
          } else {
            // No designations found for this department
            designation_dropdown.append(
              $('<option></option>')
                .attr('value', '')
                .attr('disabled', true)
                .text('No designations available for this department')
            );
          }
          
          // Re-initialize select2
          setTimeout(function() {
            // Make sure the dropdown and its container are visible
            var parentContainer = designation_dropdown.closest('.form-group');
            parentContainer.css('display', 'block');
            designation_dropdown.css('display', 'block');
            
            designation_dropdown.select2({
              width: '100%',
              placeholder: 'Please Select',
              allowClear: true,
              dropdownParent: $('body'), // Attach dropdown to body to avoid visibility issues
              dropdownPosition: 'below',
              dropdownAutoWidth: true
            });
            
            // Force select2 to recalculate its position
            $(window).trigger('resize.select2');
            
            console.log("Dropdown updated with " + Object.keys(result.designations).length + " options");
          }, 100); // Small delay to ensure DOM is updated
        } else if (result.error) {
          console.error("Error:", result.error);
          alert("Error loading designations: " + result.error);
        }
      },
      error: function(xhr, status, error) {
        console.error("AJAX Error:", error);
        console.log("Response:", xhr.responseText);
      }
    });
  });
  
</script>
@endsection
