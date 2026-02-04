@extends('layouts.app')

@section('title', __('essentials::lang.todo'))

@section('css')
<style>
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 20px;
    }
    
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        padding: 20px 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        text-align: center;
        min-height: 90px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .stats-card.clickable-card {
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .stats-card.clickable-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    .stats-card.clickable-card:active {
        transform: translateY(-1px);
    }
    
    .stats-card.clickable-card::after {
        content: '👆 Click to filter';
        position: absolute;
        bottom: -30px;
        left: 0;
        right: 0;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 5px;
        font-size: 10px;
        transition: all 0.3s ease;
        opacity: 0;
    }
    
    .stats-card.clickable-card:hover::after {
        bottom: 0;
        opacity: 1;
    }
    
    .stats-card.active-filter {
        border: 3px solid #fff;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        transform: scale(1.05);
    }
    
    .stats-card.active-filter::before {
        content: '✓ Active Filter';
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(255,255,255,0.9);
        color: #333;
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 9px;
        font-weight: bold;
    }
    
    .stats-card h4 {
        margin: 0;
        font-size: 2.2em;
        font-weight: bold;
        line-height: 1.1;
        white-space: nowrap;
        overflow: visible;
    }
    
    .stats-card p {
        margin: 8px 0 0 0;
        opacity: 0.9;
        font-size: 0.85em;
        white-space: nowrap;
    }
    
    .stats-card.completed {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    
    .stats-card.progress {
        background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%);
    }
    
    .stats-card.incomplete {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    }
    
    .chart-legend {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 10px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin: 3px 10px;
        font-size: 12px;
    }
    
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
        margin-right: 5px;
    }
    
    .dashboard-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    
    .employee-bar {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        padding: 5px;
        background: #f8f9fa;
        border-radius: 5px;
    }
    
    .employee-name {
        font-weight: bold;
        min-width: 120px;
        font-size: 12px;
        color: #333;
    }
    
    .task-bars {
        flex: 1;
        display: flex;
        margin: 0 10px;
        height: 20px;
        border-radius: 10px;
        overflow: hidden;
        background: #e9ecef;
    }
    
    .task-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 10px;
        font-weight: bold;
    }
    
    .task-bar.completed { background-color: #28a745; }
    .task-bar.in_progress { background-color: #17a2b8; }
    .task-bar.incomplete { background-color: #dc3545; }
    
    .task-count {
        font-weight: bold;
        color: #666;
        min-width: 40px;
        text-align: right;
        font-size: 11px;
    }
    
    .refresh-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 100;
    }
    
    .clickable-employee:hover {
        color: #0056b3 !important;
        font-weight: bold;
        background-color: #f8f9fa;
        padding: 2px 5px;
        border-radius: 3px;
    }
    
    .task-bar:hover {
        opacity: 0.8;
        transform: scale(1.02);
    }
    
    .filter-controls {
        text-align: center;
        margin: 15px 0;
    }
    
    .filter-btn {
        height: 40px;
        min-width: 150px;
        margin: 0 5px;
        font-size: 14px;
        font-weight: 500;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .filter-btn i {
        margin-right: 8px;
    }
    
    .current-filters {
        background: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 5px;
        padding: 8px;
        margin: 10px 0;
        font-size: 12px;
    }
    
    /* Responsive fixes for stats cards */
    @media (max-width: 768px) {
        .stats-card {
            margin-bottom: 10px;
            padding: 15px 10px;
            min-height: 80px;
        }
        
        .stats-card h4 {
            font-size: 1.8em;
        }
        
        .stats-card p {
            font-size: 0.8em;
        }
    }
    
    @media (max-width: 576px) {
        .stats-card h4 {
            font-size: 1.6em;
        }
    }
    
    /* Estimated time column styling */
    .estimated-time-label {
        font-size: 11px;
        padding: 3px 6px;
        border-radius: 3px;
        white-space: nowrap;
    }
    
    .estimated-time-label.label-info {
        background-color: #5bc0de;
        color: white;
    }
    
    .estimated-time-label.label-success {
        background-color: #5cb85c;
        color: white;
    }
    
    .estimated-time-label.label-warning {
        background-color: #f0ad4e;
        color: white;
    }
    
    .estimated-time-label.label-danger {
        background-color: #d9534f;
        color: white;
    }
    
    .estimated-time-label.text-muted {
        color: #6c757d;
        font-style: italic;
    }
</style>
@endsection

@section('content')
@include('essentials::layouts.nav_essentials')
<section class="content">
	<!-- Dashboard Section with Charts -->
	<div class="dashboard-section">
		<div class="row">
			<div class="col-md-12">
				<h4><i class="fas fa-chart-bar"></i> Task Status Dashboard</h4>
			</div>
		</div>
		
		<!-- Statistics Cards -->
		<div class="row" id="stats-cards">
			<div class="col-md-3 col-sm-6">
				<div class="stats-card clickable-card" data-filter="all" title="Click to show all tasks">
					<h4 id="total-tasks">-</h4>
					<p><i class="fas fa-tasks"></i> Total Tasks</p>
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="stats-card completed clickable-card" data-filter="Completed" title="Click to filter completed tasks">
					<h4 id="completed-tasks">-</h4>
					<p><i class="fas fa-check-circle"></i> Completed</p>
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="stats-card progress clickable-card" data-filter="In progress" title="Click to filter in progress tasks">
					<h4 id="progress-tasks">-</h4>
					<p><i class="fas fa-spinner"></i> In Progress</p>
				</div>
			</div>
			<div class="col-md-3 col-sm-6">
				<div class="stats-card incomplete clickable-card" data-filter="Incomplete" title="Click to filter incomplete tasks">
					<h4 id="incomplete-tasks">-</h4>
					<p><i class="fas fa-exclamation-triangle"></i> Incomplete</p>
				</div>
			</div>
		</div>

		<!-- Filter Controls -->
		<div class="filter-controls">
			<button class="btn btn-warning filter-btn" onclick="clearAllFilters()">
				<i class="fas fa-times"></i> Clear All Filters
			</button>
			<button class="btn btn-info filter-btn" onclick="refreshAllData()">
				<i class="fas fa-sync-alt"></i> Refresh Charts
			</button>
		</div>
		
		<!-- Current Filters Display -->
		<div id="current-filters" class="current-filters" style="display: none;">
			<strong>Active Filters:</strong>
			<span id="filter-display"></span>
		</div>

		<!-- Charts Row -->
		<div class="row">
			<!-- Pie Chart -->
			<div class="col-md-6">
				<div style="position: relative;">
					<button class="btn btn-xs btn-default refresh-btn" onclick="loadChartData()">
						<i class="fas fa-sync-alt"></i>
					</button>
					<h5><i class="fas fa-chart-pie"></i> Status Distribution</h5>
					<div class="chart-container">
						<canvas id="statusPieChart"></canvas>
					</div>
					<div class="chart-legend" id="pie-legend"></div>
				</div>
			</div>

			<!-- Employee Bar Chart -->
			<div class="col-md-6">
				<h5><i class="fas fa-users"></i> Tasks by Employee <small class="text-muted">(Click names to filter)</small></h5>
				<div id="employee-chart-container" style="max-height: 300px; overflow-y: auto;">
					<div class="text-center">
						<i class="fas fa-spinner fa-spin"></i> Loading...
					</div>
				</div>
			</div>
		</div>
	</div>

	@component('components.filters', ['title' => __('report.filters'), 'class' => 'box-solid'])
		@if(auth()->user()->roleId == 14)
			<div class="col-md-12">
				<div class="alert alert-info" style="margin-bottom: 15px;">
					<i class="fa fa-info-circle"></i> <strong>Sub-Admin View:</strong> You can view and filter tasks for all users in your permitted locations.
				</div>
			</div>
		@endif
		@can('essentials.assign_todos')
			<div class="col-md-3">
				<div class="form-group">
				{!! Form::label('user_id_filter', __('essentials::lang.assigned_to') . ':') !!}
					<div class="input-group">
						<span class="input-group-addon">
							<i class="fa fa-user"></i>
						</span>
						{!! Form::select('user_id_filter', $users, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all')]); !!}
					</div>
				</div>
			</div>
		@endcan
		<div class="col-md-3">
			<div class="form-group">
				{!! Form::label('priority_filter', __('essentials::lang.priority') . ':') !!}
				{!! Form::select('priority_filter', $priorities, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all')]); !!}
			</div>
		</div>
		<div class="col-md-3">
			<div class="form-group">
				{!! Form::label('status_filter', __('sale.status') . ':') !!}
				{!! Form::select('status_filter', $task_statuses, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all')]); !!}
			</div>
		</div>
		<div class="col-md-3">
            <div class="form-group">
                {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                {!! Form::text('date_range_filter', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
	@endcomponent
	@component('components.widget', ['title' => __('essentials::lang.todo_list'), 'icon' => '<i class="ion ion-clipboard"></i>', 'class' => 'box-solid'])
		@slot('tool')
			<div class="box-tools">
				<a href="{{action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'calendar'])}}" class="btn btn-info btn-sm">
					<i class="fas fa-calendar-alt"></i> @lang('lang_v1.calendar_view')
				</a>
				@can('essentials.add_todos')
				<button class="btn btn-primary btn-modal" data-href="{{action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'create'])}}" 
				data-container="#task_modal">
					<i class="fa fa-plus"></i> @lang( 'messages.add' )
				</button>
				@endcan
			</div>
		@endslot
		<div class="table-responsive">
			<table class="table table-bordered table-striped " id="task_table">
				<thead>
					<tr >
						<th>@lang('lang_v1.added_on')</th>
						<th> @lang('essentials::lang.task_id')</th>
						<th class="col-md-2"> @lang('essentials::lang.task')</th>
						<th class="col-md-2"> Description</th>
						<th> Priority</th>
						<th> @lang('sale.status')</th>
						<th> @lang('business.start_date')</th>
						<th> @lang('essentials::lang.end_date')</th>
						<th> @lang('essentials::lang.estimated_hours')</th>
						<th> Estimated Time</th>
						<th> @lang('essentials::lang.assigned_by')</th>
						<th>Department</th>
						<th> @lang('essentials::lang.assigned_to')</th>
						<th> @lang('essentials::lang.action')</th>
					</tr>
				</thead>
			</table>
		</div>
	@endcomponent
</section>
@include('essentials::todo.update_task_status_modal')
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="text/javascript">
	let pieChart = null;
	// Permission/context flags
	const CAN_ASSIGN_TODOS = @json(auth()->user()->can('essentials.assign_todos'));
	const LOGGED_IN_USER_ID = {{ auth()->id() }};
	const USER_ROLE_ID = {{ auth()->user()->roleId ?? 0 }};
	
	$(document).ready(function(){
		// Load chart data on page load
		loadChartData();
		
		// Auto-refresh charts every 2 minutes
		setInterval(loadChartData, 120000);
		task_table = $('#task_table').DataTable({
	        processing: true,
	        serverSide: true,
	        ajax: {
	        	url: '/essentials/todo',
	        data: function(d) {
	        	var selectedUserId = $('#user_id_filter').length ? $('#user_id_filter').val() : '';
	        	// Apply role-based filtering: admins (1) and subadmins (14) can see all users in their scope, others see only their own
	        	if (USER_ROLE_ID !== 1 && USER_ROLE_ID !== 14 && (!selectedUserId || !$('#user_id_filter').length)) {
	        		selectedUserId = LOGGED_IN_USER_ID;
	        	}
	        	d.user_id = selectedUserId;
	        		d.priority = $('#priority_filter').val();
	        		d.status = $('#status_filter').val();
	        		var start = '';
	                var end = '';
	                if ($('#date_range_filter').val()) {
	                    start = $('input#date_range_filter')
	                        .data('daterangepicker')
	                        .startDate.format('YYYY-MM-DD');
	                    end = $('input#date_range_filter')
	                        .data('daterangepicker')
	                        .endDate.format('YYYY-MM-DD');
	                }
	                d.start_date = start;
	                d.end_date = end;
	        	}
	        },
	        columnDefs: [
	            {
	                targets: [7, 8, 9, 10],
	                orderable: false,
	                searchable: false,
	            },
	        ],
	        aaSorting: [[0, 'desc']],
            columns: [
            	{ data: 'created_at', name: 'created_at' },
            	{ data: 'task_id', name: 'task_id' },
                // Render task column as clickable link that opens task view page
                { data: 'task', name: 'task', render: function(data, type, row) {
                    if (type === 'display') {
                        // Use the view_url provided by the server (same as the view button)
                        var viewUrl = row.view_url || '#';
                        return `<a href="${viewUrl}" title="View task" style="color: #007bff; text-decoration: underline;">${data}</a>`;
                    }
                    return data;
                }},
                { data: 'description', name: 'description' },
                { data: 'priority', name: 'priority' },
            
                { data: 'status', name: 'status', render: function(data, type, row) {
                    if (type !== 'display') { return data; }
                    // Strip any HTML and normalize
                    var raw = (data == null ? '' : String(data));
                    var text = raw.replace(/<[^>]*>/g, '').trim();
                    var key = text.toLowerCase().replace(/\s+/g, '');
                    var color = '#6c757d';
                    if (key === 'completed') color = '#28a745';
                    else if (key === 'inprogress') color = '#fd7e14';
                    else if (key === 'incomplete') color = '#dc3545';
                    return '<span class="label" style="background-color:' + color + '; color:#fff;">' + text + '</span>';
                } },
                { data: 'date', name: 'date' },
                { data: 'end_date', name: 'end_date' },
                { data: 'estimated_hours', name: 'estimated_hours' },
                { data: 'estimated_time', name: 'estimated_time' },
                { data: 'assigned_by'},
                        { data: 'department'},
                { data: 'users'},
                { data: 'action', name: 'action' },
            ],
	    });

	    $('#date_range_filter').daterangepicker(
        dateRangeSettings,
	        function (start, end) {
	            $('#date_range_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
	           task_table.ajax.reload();
	        }
	    );
	    $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
	        $('#date_range_filter').val('');
	        task_table.ajax.reload();
	    });

		//delete a task
		$(document).on('click', '.delete_task', function(e){
			e.preventDefault();
			var url = $(this).data('href');
			swal({
		      title: LANG.sure,
		      icon: "warning",
		      buttons: true,
		      dangerMode: true,
		    }).then((confirmed) => {
		        if (confirmed) {
					$.ajax({
						method: "DELETE",
						url: url,
						dataType: "json",
						success: function(result){
							if(result.success == true){
								toastr.success(result.msg);
								task_table.ajax.reload();
							} else {
								toastr.error(result.msg);
							}
						}
					});
				   }	
			  });
		});

	    //event on date chnage
		$(document).on('change', "#priority_filter, #user_id_filter, #status_filter", function(){
			task_table.ajax.reload();
			// Also reload chart data when filters change
			setTimeout(loadChartData, 500);
			// Update filter display
			updateFilterDisplay();
		});
		
		// Update filter display on page load
		updateFilterDisplay();
		
		// Add click handlers for stats cards
		$(document).on('click', '.clickable-card', function() {
			const filter = $(this).data('filter');
			const cardText = $(this).find('p').text().replace(/[^\w\s]/gi, '').trim();
			
			if (filter === 'all') {
				// Clear all filters to show all tasks
				clearAllFilters();
				toastr.info('Showing all tasks');
			} else {
				// Set status filter
				$('#status_filter').val(filter).trigger('change');
				updateFilterDisplay();
				toastr.info(`Filtering by: ${cardText}`);
			}
		});
	});

function clearAllFilters() {
    // Show toast so user knows filters are being cleared
    toastr.info('Clearing filters and reloading the page...');

    // Clear all filter inputs (UI only) before reload
    $('#user_id_filter').val('').trigger('change.select2');
    $('#priority_filter').val('').trigger('change.select2'); 
    $('#status_filter').val('').trigger('change.select2');
    $('#date_range_filter').val('');

    // Clear date range picker if present
    if ($('#date_range_filter').data('daterangepicker')) {
        $('#date_range_filter').data('daterangepicker').setStartDate(moment());
        $('#date_range_filter').data('daterangepicker').setEndDate(moment());
    }

    // Delay briefly to allow the toast to show, then perform a full page reload
    setTimeout(function() {
        // Force a full reload so server-side state / session filters also reset
        window.location.reload();
    }, 300);
}

function refreshAllData() {
    toastr.info('Refreshing all data...');
    
    // Reset stats cards
    $('#total-tasks, #completed-tasks, #progress-tasks, #incomplete-tasks').text('-');
    
    // Show loading in charts
    $('#employee-chart-container').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Refreshing charts...</div>');
    
    // Reload table
    task_table.ajax.reload();
    
    // Reload charts
    setTimeout(function() {
        loadChartData();
        toastr.success('Data refreshed successfully');
    }, 300);
}

function updateFilterDisplay() {
    let filters = [];
    
    // Remove active-filter class from all cards
    $('.stats-card').removeClass('active-filter');
    
    if ($('#user_id_filter').val()) {
        let employeeName = $('#user_id_filter option:selected').text();
        filters.push('Employee: ' + employeeName);
    }
    
    if ($('#priority_filter').val()) {
        filters.push('Priority: ' + $('#priority_filter').val());
    }
    
    if ($('#status_filter').val()) {
        let statusValue = $('#status_filter').val();
        filters.push('Status: ' + statusValue);
        
        // Highlight the corresponding card
        $('.clickable-card[data-filter="' + statusValue + '"]').addClass('active-filter');
    }
    
    if ($('#date_range_filter').val()) {
        filters.push('Date: ' + $('#date_range_filter').val());
    }
    
    // If no specific status filter, highlight "all tasks" card
    if (!$('#status_filter').val() && !$('#user_id_filter').val() && !$('#priority_filter').val() && !$('#date_range_filter').val()) {
        $('.clickable-card[data-filter="all"]').addClass('active-filter');
    }
    
    if (filters.length > 0) {
        $('#current-filters').show();
        $('#filter-display').html(filters.join(' | '));
    } else {
        $('#current-filters').hide();
    }
}

function loadChartData() {
    // Show loading for employee chart
    $('#employee-chart-container').html(`
        <div class="text-center">
            <i class="fas fa-spinner fa-spin"></i> Loading employee data...
        </div>
    `);
    
    // Get current filter values
    var filters = {
        user_id: $('#user_id_filter').length ? $('#user_id_filter').val() : '',
        priority: $('#priority_filter').val(),
        status: $('#status_filter').val()
    };
    // Apply role-based filtering: only admins can see all users, others see only their own
    if (USER_ROLE_ID !== 1 && (!filters.user_id || !$('#user_id_filter').length)) {
        filters.user_id = LOGGED_IN_USER_ID;
    }
    
    // Add date range if exists
    if ($('#date_range_filter').val()) {
        filters.start_date = $('input#date_range_filter')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        filters.end_date = $('input#date_range_filter')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
    }
    
    $.ajax({
        url: '{{ url("/essentials/todo-chart-data") }}',
        method: 'GET',
        data: filters,
        dataType: 'json',
        success: function(data) {
            updateStatistics(data);
            renderPieChart(data.pie_data);
            renderEmployeeChart(data.employee_data);
        },
        error: function(xhr, status, error) {
            console.error('Error loading chart data:', error);
            toastr.error('Failed to load chart data');
            
            $('#employee-chart-container').html(`
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Failed to load data</p>
                    <button class="btn btn-xs btn-primary" onclick="loadChartData()">
                        <i class="fas fa-sync-alt"></i> Retry
                    </button>
                </div>
            `);
        }
    });
}

function updateStatistics(data) {
    $('#total-tasks').text(data.total_tasks);
    
    let completed = 0, inProgress = 0, incomplete = 0;
    
    data.pie_data.forEach(function(item) {
        if (item.name === 'Completed') completed = item.value;
        else if (item.name === 'In Progress') inProgress = item.value;
        else if (item.name === 'Incomplete') incomplete = item.value;
    });
    
    $('#completed-tasks').text(completed);
    $('#progress-tasks').text(inProgress);
    $('#incomplete-tasks').text(incomplete);
}

function renderPieChart(pieData) {
    const ctx = document.getElementById('statusPieChart').getContext('2d');
    
    // Destroy existing chart
    if (pieChart) {
        pieChart.destroy();
    }
    
    const colors = {
        'Completed': '#28a745',
        'In Progress': '#17a2b8',
        'Incomplete': '#dc3545'
    };
    
    const chartData = {
        labels: pieData.map(item => item.name),
        datasets: [{
            data: pieData.map(item => item.value),
            backgroundColor: pieData.map(item => colors[item.name] || '#6c757d'),
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    };
    
    pieChart = new Chart(ctx, {
        type: 'doughnut',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const item = pieData[context.dataIndex];
                            return `${item.name}: ${item.value} tasks (${item.percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                duration: 800
            }
        }
    });
    
    // Custom legend
    renderPieLegend(pieData, colors);
}

function renderPieLegend(pieData, colors) {
    let legendHtml = '';
    pieData.forEach(function(item) {
        legendHtml += `
            <div class="legend-item">
                <div class="legend-color" style="background-color: ${colors[item.name]}"></div>
                <span>${item.name}: ${item.value} (${item.percentage}%)</span>
            </div>
        `;
    });
    $('#pie-legend').html(legendHtml);
}

function renderEmployeeChart(employeeData) {
    if (employeeData.length === 0) {
        $('#employee-chart-container').html(`
            <div class="text-center text-muted">
                <i class="fas fa-info-circle"></i>
                <p>No employee data available</p>
            </div>
        `);
        return;
    }
    
    let chartHtml = '';
    
    employeeData.forEach(function(employee) {
        const total = employee.total;
        const completedWidth = total > 0 ? (employee.Completed / total) * 100 : 0;
        const progressWidth = total > 0 ? (employee['In progress'] / total) * 100 : 0;
        const incompleteWidth = total > 0 ? (employee.Incomplete / total) * 100 : 0;
        
        chartHtml += `
            <div class="employee-bar">
                <div class="employee-name clickable-employee" 
                     data-employee-id="${employee.user_id || ''}" 
                     data-employee-name="${employee.name}"
                     style="cursor: pointer; color: #007bff; text-decoration: underline;"
                     title="Click to filter tasks for ${employee.name}">
                    ${employee.name}
                </div>
                <div class="task-bars">
                    ${employee.Completed > 0 ? `<div class="task-bar completed clickable-status" data-status="Completed" data-employee-id="${employee.user_id || ''}" data-employee-name="${employee.name}" style="flex: ${completedWidth}; cursor: pointer;" title="${employee.name} - Completed: ${employee.Completed} (click to filter)">${employee.Completed}</div>` : ''}
                    ${employee['In progress'] > 0 ? `<div class="task-bar in_progress clickable-status" data-status="In progress" data-employee-id="${employee.user_id || ''}" data-employee-name="${employee.name}" style="flex: ${progressWidth}; cursor: pointer;" title="${employee.name} - In Progress: ${employee['In progress']} (click to filter)">${employee['In progress']}</div>` : ''}
                    ${employee.Incomplete > 0 ? `<div class="task-bar incomplete clickable-status" data-status="Incomplete" data-employee-id="${employee.user_id || ''}" data-employee-name="${employee.name}" style="flex: ${incompleteWidth}; cursor: pointer;" title="${employee.name} - Incomplete: ${employee.Incomplete} (click to filter)">${employee.Incomplete}</div>` : ''}
                </div>
                <div class="task-count">${total}</div>
            </div>
        `;
    });
    
    $('#employee-chart-container').html(chartHtml);
    
    // Add click handlers for employee names
    $(document).off('click', '.clickable-employee').on('click', '.clickable-employee', function() {
        const employeeId = $(this).data('employee-id');
        const employeeName = $(this).data('employee-name');
        
        if (employeeId) {
            // Set the filter
            $('#user_id_filter').val(employeeId).trigger('change');
            updateFilterDisplay();
            toastr.info(`Filtering tasks for: ${employeeName}`);
        }
    });
    
    // Add click handlers for status bars
    $(document).off('click', '.clickable-status').on('click', '.clickable-status', function(e) {
        e.stopPropagation();
        const status = $(this).data('status');
        const employeeId = $(this).data('employee-id');
        const employeeName = $(this).data('employee-name');

        if (employeeId) {
            $('#user_id_filter').val(employeeId).trigger('change');
        }
        $('#status_filter').val(status).trigger('change');
        updateFilterDisplay();
        if (employeeName) {
            toastr.info(`Filtering ${status} tasks for: ${employeeName}`);
        } else {
            toastr.info(`Filtering tasks by status: ${status}`);
        }
    });
}

$(document).on('click', '.change_status', function(e){
	e.preventDefault();
	var task_id = $(this).data('task_id');
	var status = $(this).data('status');

	$('#update_task_status_modal').modal('show');
	$('#update_task_status_modal').find('#updated_status').val(status);
	$('#update_task_status_modal').find('#task_id').val(task_id);
	
	// Show/hide completion notice based on initial status
	if (status === 'Completed') {
		$('#completion_notice').show();
	} else {
		$('#completion_notice').hide();
	}
});

// Show/hide completion notice when status changes
$(document).on('change', '#updated_status', function(){
	if ($(this).val() === 'Completed') {
		$('#completion_notice').show();
	} else {
		$('#completion_notice').hide();
	}
});

$(document).on('click', '#update_status_btn', function(){
	var task_id = $('#update_task_status_modal').find('#task_id').val();
	var status = $('#update_task_status_modal').find('#updated_status').val();

	// Show confirmation for completion with auto end date
	if (status === 'Completed') {
		if (!confirm('Are you sure you want to mark this task as completed? The end date will be automatically set to the current date and time.')) {
			return;
		}
	}

	var url = "/essentials/todo/" + task_id;
	$.ajax({
		method: "PUT",
		url: url,
		data: {status: status, only_status: true},
		dataType: "json",
		success: function(result){
			if(result.success == true){
				if (status === 'Completed') {
					toastr.success(result.msg + ' End date has been automatically set.');
				} else {
					toastr.success(result.msg);
				}
				$('#update_task_status_modal').modal('hide');
				task_table.ajax.reload();
			} else {
				toastr.error(result.msg);
			}
		}
	});

});

$(document).on('click', '.view-shared-docs', function () {
	var url = $(this).data('href');
	$.ajax({
		method: "get",
		url: url,
		dataType: "html",
		success: function(result){
			$('.view_modal').html(result).modal('show');
		}
	});
});

// Task name links now work as direct links to the task view page
// No JavaScript needed - they work exactly like the existing view action button
</script>
@endsection