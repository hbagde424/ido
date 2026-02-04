@extends('layouts.app')
@section('title', __( 'user.users' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'user.users' )
        <small>@lang( 'user.manage_users' )</small>
    </h1>
    <!-- <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
    </ol> -->
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'user.all_users' )])
        @can('user.create')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" 
                    href="{{action([\App\Http\Controllers\ManageUserController::class, 'create'])}}" >
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</a>
                 </div>
            @endslot
        @endcan
        @can('user.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="users_table">
                    <thead>
                        <tr>
                            <th>@lang( 'business.username' )</th>
                            <th>@lang( 'user.name' )</th>
                            <th>@lang( 'user.role' )</th>
                            <th>@lang( 'business.email' )</th>
                            <th>@lang( 'essentials::lang.department' )</th>
                            <th>@lang( 'user.mobile' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    <div class="modal fade user_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->
@stop
@section('css')
<style>
    /* Ensure DataTable search box is visible */
    .dataTables_filter {
        float: right !important;
        text-align: right !important;
        margin-bottom: 10px !important;
    }
    
    .dataTables_filter input {
        margin-left: 5px !important;
        border: 1px solid #ddd !important;
        border-radius: 4px !important;
        padding: 5px 10px !important;
        width: 200px !important;
    }
    
    .dataTables_length {
        float: left !important;
        margin-bottom: 10px !important;
    }
    
    .dataTables_length select {
        margin: 0 5px !important;
        border: 1px solid #ddd !important;
        border-radius: 4px !important;
        padding: 3px 5px !important;
    }
    
    /* Ensure proper spacing */
    .dataTables_wrapper .row {
        margin: 0 !important;
    }
    
    .dataTables_wrapper .col-sm-6 {
        padding: 0 5px !important;
    }
</style>
@endsection

@section('javascript')
<script type="text/javascript">
    //Roles table
    $(document).ready( function(){
        console.log('Initializing users DataTable...');
        
        var users_table = $('#users_table').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '/users',
                    columnDefs: [ {
                        "targets": [6], // Action column
                        "orderable": false,
                        "searchable": false
                    } ],
                    "columns":[
                        {"data":"username", "name": "username"},
                        {"data":"full_name", "name": "full_name"},
                        {"data":"role", "name": "role", "orderable": false, "searchable": false},
                        {"data":"email", "name": "email"},
                        {"data":"designation", "name": "designation"},
                        {"data":"contact_number", "name": "contact_number"},
                        {"data":"action", "name": "action", "orderable": false, "searchable": false}
                    ],
                    "language": {
                        "search": "Search:",
                        "searchPlaceholder": "Type to search users..."
                    },
                    "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip', // Show length and search
                    "pageLength": 25,
                    "order": [[1, 'asc']], // Sort by name by default
                    "responsive": true,
                    "autoWidth": false
                });
                
        // Debug: Check if search box is created
        setTimeout(function() {
            var searchBox = $('.dataTables_filter input');
            console.log('Search box found:', searchBox.length);
            if (searchBox.length > 0) {
                console.log('Search box is visible:', searchBox.is(':visible'));
                console.log('Search box value:', searchBox.val());
            } else {
                console.log('Search box not found!');
            }
        }, 1000);
        $(document).on('click', 'button.delete_user_button', function(){
            swal({
              title: LANG.sure,
              text: LANG.confirm_delete_user,
              icon: "warning",
              buttons: true,
              dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: "DELETE",
                        url: href,
                        dataType: "json",
                        data: data,
                        success: function(result){
                            if(result.success == true){
                                toastr.success(result.msg);
                                users_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                }
             });
        });
        
    });
    
    
</script>
@endsection
