@extends('layouts.app')
@section('title', 'Policies')

@section('content')
@include('essentials::layouts.nav_hrm')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Policies Management</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">All Policies</h3>
                    <div class="box-tools pull-right">
                        <a href="{{ action('\Modules\Essentials\Http\Controllers\EssentialsPolicyController@create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Add New Policy
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Filter by User</label>
                                <select id="user_filter" class="form-control">
                                    <option value="">All Users</option>
                                    @foreach($users as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Filter by Policy Type</label>
                                <select id="policy_type_filter" class="form-control">
                                    <option value="">All Policies</option>
                                    @foreach($policy_types as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <table class="table table-striped table-hover" id="policies_table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Policy Type</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Signed Date</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal for viewing/editing -->
<div class="modal fade" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalLabel">Policy Details</h4>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        var policies_table = $('#policies_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action('\Modules\Essentials\Http\Controllers\EssentialsPolicyController@index') }}",
                data: function(d) {
                    d.user_id = $('#user_filter').val();
                    d.policy_type = $('#policy_type_filter').val();
                }
            },
            columns: [
                { data: 'user', name: 'user' },
                { data: 'policy_type', name: 'policy_type' },
                { data: 'title', name: 'title' },
                { data: 'status', name: 'status' },
                { data: 'signed_date', name: 'signed_date' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#user_filter, #policy_type_filter').change(function() {
            policies_table.draw();
        });

        // Modal handling
        $(document).on('click', '.btn-modal', function(e) {
            e.preventDefault();
            var href = $(this).data('href');
            $.get(href, function(data) {
                $('#view_modal .modal-body').html(data);
                $('#view_modal').modal('show');
            });
        });

        // Delete handling
        $(document).on('click', '.delete-row', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this policy?')) {
                var href = $(this).data('href');
                $.post(href, {_method: 'DELETE', _token: '{{ csrf_token() }}'}, function(data) {
                    policies_table.draw();
                    alert('Policy deleted successfully');
                });
            }
        });
    });
</script>
@endsection
