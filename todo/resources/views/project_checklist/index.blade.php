@extends('layouts.app')
@section('title', __('Project Checklists'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('Project List')
        <small>@lang('Manage your project checklists')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $total_projects }}</h3>
                    <p>@lang('Total Projects')</p>
                </div>
                <div class="icon">
                    <i class="fa fa-folder"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $complete_projects }}</h3>
                    <p>@lang('Complete Projects')</p>
                </div>
                <div class="icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $in_progress_projects }}</h3>
                    <p>@lang('In Progress Projects')</p>
                </div>
                <div class="icon">
                    <i class="fa fa-spinner"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $incomplete_projects }}</h3>
                    <p>@lang('Incomplete Projects')</p>
                </div>
                <div class="icon">
                    <i class="fa fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Extra Status Cards -->
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $not_started_projects }}</h3>
                    <p>@lang('Not Started Yet')</p>
                </div>
                <div class="icon">
                    <i class="fa fa-hourglass-start"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $overdue_projects }}</h3>
                    <p>@lang('Overdue Projects')</p>
                </div>
                <div class="icon">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $on_hold_projects }}</h3>
                    <p>@lang('On Hold Projects')</p>
                </div>
                <div class="icon">
                    <i class="fa fa-pause-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $cancelled_projects }}</h3>
                    <p>@lang('Cancelled Projects')</p>
                </div>
                <div class="icon">
                    <i class="fa fa-ban"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart and Filter -->
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('Project Status Distribution')</h3>
                </div>
                <div class="box-body">
                    <canvas id="projectStatusChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('Filter Projects')</h3>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label>@lang('Filter by Status')</label>
                        <select class="form-control" id="status_filter">
                            <option value="">@lang('All Projects')</option>
                            <option value="Complete">@lang('Complete')</option>
                            <option value="In Progress">@lang('In Progress')</option>
                            <option value="Incomplete">@lang('Incomplete')</option>
                            <option value="Not Started Yet">@lang('Not Started Yet')</option>
                            <option value="Overdue">@lang('Overdue')</option>
                            <option value="On Hold">@lang('On Hold')</option>
                            <option value="Cancelled">@lang('Cancelled')</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('All Projects')])
        @can('project_checklist.create')
            @slot('tool')
                <div class="box-tools" style="display: flex; gap: 10px; align-items: center;">
                    <div class="btn-group">
                        <button type="button" id="view_dropdown_btn" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-eye"></i> @lang('View') <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#" id="table_view_option"><i class="fa fa-table"></i> @lang('Table View')</a></li>
                            <li><a href="#" id="kanban_view_option"><i class="fa fa-th-large"></i> @lang('Kanban View')</a></li>
                            <li><a href="#" id="gantt_view_option"><i class="fa fa-bar-chart"></i> @lang('Gantt View')</a></li>
                        </ul>
                    </div>
                    <a href="{{action([\App\Http\Controllers\ProjectChecklistController::class, 'create'])}}" class="btn btn-block btn-primary">
                        <i class="fa fa-plus"></i> @lang('Add Project')</a>
                </div>
            @endslot
        @endcan
        @canany(['project_checklist.view', 'project_checklist.create'])
            <!-- Table View -->
            <div id="table_view" class="table-responsive">
                <table class="table table-bordered table-striped" id="project_checklist_table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">@lang('Project Name')</th>
                            <th>@lang('Start Date')</th>
                            <th>@lang('End Date')</th>
                            <th>@lang('Project Lead')</th>
                            <th style="width: 20%;">@lang('Assigned Users')</th>
                            <th>@lang('Created By')</th>
                            <th>@lang('Created At')</th>
                            <th>@lang('Project Status')</th>
                            <th style="width: 100%;">@lang('Project Progress')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                            @php
                                // Calculate progress
                                $total_tasks = $project->tasks->count();
                                if ($total_tasks == 0) {
                                    $progress = 0;
                                } else {
                                    $completed_tasks = $project->tasks->where('status', 1)->count();
                                    $progress = round(($completed_tasks / $total_tasks) * 100);
                                }
                                
                                // Get project status using the model method
                                $statusData = $project->getProjectStatus();
                                $old_status = $statusData['old_status'];
                                $old_badge_color = $statusData['old_badge_color'];
                                $extra_status = $statusData['extra_status'];
                                $extra_badge_color = $statusData['extra_badge_color'];
                                
                                // Determine progress bar color
                                $progress_color = '#dc3545'; // Red (0%)
                                if ($progress > 70) {
                                    $progress_color = '#28a745'; // Green (above 70%)
                                } elseif ($progress >= 1) {
                                    $progress_color = '#fd7e14'; // Orange (1% to 70%)
                                }
                            @endphp
                            <tr>
                                <td style="word-wrap: break-word; overflow-wrap: break-word; max-width: 150px;">
                                    @php
                                        $projectName = $project->project_name;
                                        $maxLength = 20; // Characters per line
                                        if (strlen($projectName) > $maxLength) {
                                            $wrappedName = '';
                                            for ($i = 0; $i < strlen($projectName); $i += $maxLength) {
                                                $wrappedName .= substr($projectName, $i, $maxLength);
                                                if ($i + $maxLength < strlen($projectName)) {
                                                    $wrappedName .= '<br>';
                                                }
                                            }
                                            echo $wrappedName;
                                        } else {
                                            echo $projectName;
                                        }
                                    @endphp
                                </td>
                                <td>{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($project->projectLead)
                                        {{ $project->projectLead->user_full_name ?? trim(($project->projectLead->surname ?? '') . ' ' . ($project->projectLead->first_name ?? '') . ' ' . ($project->projectLead->last_name ?? '')) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($project->users && $project->users->count() > 0)
                                        {{ $project->users->pluck('user_full_name')->join(', ') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $project->createdBy ? $project->createdBy->user_full_name : '' }}</td>
                                <td>{{ optional($project->created_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="label label-{{ $old_badge_color }}">{{ $old_status }}</span>
                                    @if($extra_status)
                                        <span class="label label-{{ $extra_badge_color }}">{{ $extra_status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="progress" style="height: 25px; margin-bottom: 0; border: 1px solid #000;">
                                        <div class="progress-bar animated-progress" role="progressbar" 
                                             style="width: {{ $progress }}%; background-color: {{ $progress_color }}; color: white; line-height: 25px; font-weight: bold; transition: width 1.5s ease-in-out;" 
                                             aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ $progress }}%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @can('project_checklist.update')
                                    <button data-href="{{ action([\App\Http\Controllers\ProjectChecklistController::class, 'edit'], [$project->id]) }}" class="btn btn-xs btn-primary edit_project_button"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                                    @endcan
                                    &nbsp;
                                    @if(auth()->user()->hasRole('Admin#'.session('business.id')))
                                        @can('project_checklist.delete')
                                        <button data-href="{{ action([\App\Http\Controllers\ProjectChecklistController::class, 'destroy'], [$project->id]) }}" class="btn btn-xs btn-danger delete_project_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                                        @endcan
                                    @endif
                                    <a href="{{ action([\App\Http\Controllers\ProjectChecklistController::class, 'show'], [$project->id]) }}" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-eye-open"></i> @lang("messages.view")</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Kanban View -->
            <div id="kanban_view" style="display: none;">
                <div class="kanban-container" style="display: flex; gap: 15px; overflow-x: auto; padding: 10px; min-height: 500px;">
                    <!-- Not Started Yet Column -->
                    <div class="kanban-column" data-status="Not Started Yet" style="min-width: 280px; max-width: 280px; background-color: #f5f5f5; border-radius: 5px; padding: 10px;">
                        <div class="kanban-column-header" style="background-color: #dc3545; color: white; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-weight: bold;">
                            <i class="fa fa-hourglass-start"></i> @lang('Not Started Yet')
                            <span class="badge pull-right" id="count_not_started">0</span>
                        </div>
                        <div class="kanban-column-body" id="kanban_not_started" style="min-height: 400px;">
                            <!-- Projects will be loaded here -->
                        </div>
                    </div>
                    
                    <!-- Incomplete Column -->
                    <div class="kanban-column" data-status="Incomplete" style="min-width: 280px; max-width: 280px; background-color: #f5f5f5; border-radius: 5px; padding: 10px;">
                        <div class="kanban-column-header" style="background-color: #dc3545; color: white; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-weight: bold;">
                            <i class="fa fa-times-circle"></i> @lang('Incomplete')
                            <span class="badge pull-right" id="count_incomplete">0</span>
                        </div>
                        <div class="kanban-column-body" id="kanban_incomplete" style="min-height: 400px;">
                            <!-- Projects will be loaded here -->
                        </div>
                    </div>
                    
                    <!-- In Progress Column -->
                    <div class="kanban-column" data-status="In Progress" style="min-width: 280px; max-width: 280px; background-color: #f5f5f5; border-radius: 5px; padding: 10px;">
                        <div class="kanban-column-header" style="background-color: #fd7e14; color: white; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-weight: bold;">
                            <i class="fa fa-spinner"></i> @lang('In Progress')
                            <span class="badge pull-right" id="count_in_progress">0</span>
                        </div>
                        <div class="kanban-column-body" id="kanban_in_progress" style="min-height: 400px;">
                            <!-- Projects will be loaded here -->
                        </div>
                    </div>
                    
                    <!-- Complete Column -->
                    <div class="kanban-column" data-status="Complete" style="min-width: 280px; max-width: 280px; background-color: #f5f5f5; border-radius: 5px; padding: 10px;">
                        <div class="kanban-column-header" style="background-color: #28a745; color: white; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-weight: bold;">
                            <i class="fa fa-check-circle"></i> @lang('Complete')
                            <span class="badge pull-right" id="count_complete">0</span>
                        </div>
                        <div class="kanban-column-body" id="kanban_complete" style="min-height: 400px;">
                            <!-- Projects will be loaded here -->
                        </div>
                    </div>
                    
                    <!-- Overdue Column -->
                    <div class="kanban-column" data-status="Overdue" style="min-width: 280px; max-width: 280px; background-color: #f5f5f5; border-radius: 5px; padding: 10px;">
                        <div class="kanban-column-header" style="background-color: #dc3545; color: white; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-weight: bold;">
                            <i class="fa fa-exclamation-triangle"></i> @lang('Overdue')
                            <span class="badge pull-right" id="count_overdue">0</span>
                        </div>
                        <div class="kanban-column-body" id="kanban_overdue" style="min-height: 400px;">
                            <!-- Projects will be loaded here -->
                        </div>
                    </div>
                    
                    <!-- On Hold Column -->
                    <div class="kanban-column" data-status="On Hold" style="min-width: 280px; max-width: 280px; background-color: #f5f5f5; border-radius: 5px; padding: 10px;">
                        <div class="kanban-column-header" style="background-color: #dc3545; color: white; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-weight: bold;">
                            <i class="fa fa-pause-circle"></i> @lang('On Hold')
                            <span class="badge pull-right" id="count_on_hold">0</span>
                        </div>
                        <div class="kanban-column-body" id="kanban_on_hold" style="min-height: 400px;">
                            <!-- Projects will be loaded here -->
                        </div>
                    </div>
                    
                    <!-- Cancelled Column -->
                    <div class="kanban-column" data-status="Cancelled" style="min-width: 280px; max-width: 280px; background-color: #f5f5f5; border-radius: 5px; padding: 10px;">
                        <div class="kanban-column-header" style="background-color: #dc3545; color: white; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-weight: bold;">
                            <i class="fa fa-ban"></i> @lang('Cancelled')
                            <span class="badge pull-right" id="count_cancelled">0</span>
                        </div>
                        <div class="kanban-column-body" id="kanban_cancelled" style="min-height: 400px;">
                            <!-- Projects will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gantt View -->
            <div id="gantt_view" style="display: none;">
                <div class="gantt-container" style="overflow-x: auto; padding: 20px;">
                    <div id="gantt_chart" style="min-width: 100%; position: relative;">
                        <!-- Gantt chart will be rendered here -->
                    </div>
                </div>
            </div>
        @endcanany
    @endcomponent

    <div class="modal fade project_modal" tabindex="-1" role="dialog"
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Animate progress bars on page load
        function animateProgressBars() {
            $('.progress-bar').each(function() {
                var $bar = $(this);
                var width = $bar.attr('aria-valuenow') || $bar.css('width');
                $bar.css('width', '0%');
                setTimeout(function() {
                    $bar.css('width', width + '%');
                }, 100);
            });
        }
        
        // Animate progress bars after a short delay
        setTimeout(animateProgressBars, 300);
        
        // View Toggle Functionality
        var currentView = localStorage.getItem('project_view') || 'table';
        
        function showTableView() {
            $('#table_view').show();
            $('#kanban_view').hide();
            $('#gantt_view').hide();
            currentView = 'table';
            localStorage.setItem('project_view', 'table');
            // Update dropdown button text
            $('#view_dropdown_btn').html('<i class="fa fa-table"></i> {{ __("Table View") }} <span class="caret"></span>');
        }
        
        function showKanbanView() {
            $('#table_view').hide();
            $('#kanban_view').show();
            $('#gantt_view').hide();
            currentView = 'kanban';
            localStorage.setItem('project_view', 'kanban');
            loadKanbanView();
            // Update dropdown button text
            $('#view_dropdown_btn').html('<i class="fa fa-th-large"></i> {{ __("Kanban View") }} <span class="caret"></span>');
        }
        
        function showGanttView() {
            $('#table_view').hide();
            $('#kanban_view').hide();
            $('#gantt_view').show();
            currentView = 'gantt';
            localStorage.setItem('project_view', 'gantt');
            loadGanttView();
            // Update dropdown button text
            $('#view_dropdown_btn').html('<i class="fa fa-bar-chart"></i> {{ __("Gantt View") }} <span class="caret"></span>');
        }
        
        // Initialize view based on saved preference
        if (currentView === 'kanban') {
            showKanbanView();
        } else if (currentView === 'gantt') {
            showGanttView();
        } else {
            showTableView();
        }
        
        // Dropdown menu item click handlers
        $('#table_view_option').on('click', function(e) {
            e.preventDefault();
            showTableView();
        });
        
        $('#kanban_view_option').on('click', function(e) {
            e.preventDefault();
            showKanbanView();
        });
        
        $('#gantt_view_option').on('click', function(e) {
            e.preventDefault();
            showGanttView();
        });
        
        // Load Kanban View
        function loadKanbanView() {
            // Clear all columns
            $('.kanban-column-body').empty();
            $('.kanban-column-header .badge').text('0');
            
            // Get projects data
            var projects = {!! json_encode($projects->map(function($project) {
                $statusData = $project->getProjectStatus();
                $total_tasks = $project->tasks->count();
                $progress = 0;
                if ($total_tasks > 0) {
                    $completed_tasks = $project->tasks->where('status', 1)->count();
                    $progress = round(($completed_tasks / $total_tasks) * 100);
                }
                $progress_color = '#dc3545';
                if ($progress > 70) {
                    $progress_color = '#28a745';
                } elseif ($progress >= 1) {
                    $progress_color = '#fd7e14';
                }
                
                return [
                    'id' => $project->id,
                    'name' => $project->project_name,
                    'old_status' => $statusData['old_status'],
                    'extra_status' => $statusData['extra_status'],
                    'progress' => $progress,
                    'progress_color' => $progress_color,
                    'start_date' => $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : null,
                    'end_date' => $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') : null,
                    'created_by' => $project->createdBy ? $project->createdBy->user_full_name : '',
                    'project_lead' => $project->projectLead ? ($project->projectLead->user_full_name ?? trim(($project->projectLead->surname ?? '') . ' ' . ($project->projectLead->first_name ?? '') . ' ' . ($project->projectLead->last_name ?? ''))) : null,
                    'assigned_users' => $project->users->pluck('user_full_name')->toArray()
                ];
            })) !!};
            
            // Group projects by status
            projects.forEach(function(project) {
                var status = project.extra_status || project.old_status;
                
                // Map status names to column IDs
                var statusMap = {
                    'Not Started Yet': 'kanban_not_started',
                    'Incomplete': 'kanban_incomplete',
                    'In Progress': 'kanban_in_progress',
                    'Complete': 'kanban_complete',
                    'Overdue': 'kanban_overdue',
                    'On Hold': 'kanban_on_hold',
                    'Cancelled': 'kanban_cancelled'
                };
                
                var countMap = {
                    'Not Started Yet': 'count_not_started',
                    'Incomplete': 'count_incomplete',
                    'In Progress': 'count_in_progress',
                    'Complete': 'count_complete',
                    'Overdue': 'count_overdue',
                    'On Hold': 'count_on_hold',
                    'Cancelled': 'count_cancelled'
                };
                
                var columnId = statusMap[status] || 'kanban_incomplete';
                var countId = countMap[status] || 'count_incomplete';
                
                if ($('#' + columnId).length) {
                    // Break long project names into multiple lines
                    var projectName = project.name;
                    var maxLength = 30; // Characters per line
                    var wrappedName = '';
                    if (projectName.length > maxLength) {
                        for (var i = 0; i < projectName.length; i += maxLength) {
                            wrappedName += projectName.substring(i, i + maxLength);
                            if (i + maxLength < projectName.length) {
                                wrappedName += '<br>';
                            }
                        }
                    } else {
                        wrappedName = projectName;
                    }
                    
                    var cardHtml = '<div class="kanban-card" data-project-id="' + project.id + '" style="background: white; border: 1px solid #ddd; border-radius: 5px; padding: 12px; margin-bottom: 10px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1); word-wrap: break-word; overflow-wrap: break-word;">' +
                        '<h4 style="margin-top: 0; margin-bottom: 8px; font-size: 14px; font-weight: bold; word-wrap: break-word; overflow-wrap: break-word; line-height: 1.4;">' + wrappedName + '</h4>' +
                        '<div style="margin-bottom: 8px;">' +
                        '<div class="progress" style="height: 20px; margin-bottom: 5px; border: 1px solid #000;">' +
                        '<div class="progress-bar animated-progress" role="progressbar" style="width: ' + project.progress + '%; background-color: ' + project.progress_color + '; color: white; line-height: 20px; font-weight: bold; font-size: 11px; transition: width 1.5s ease-in-out;">' +
                        project.progress + '%</div></div></div>';
                    
                    if (project.start_date || project.end_date) {
                        cardHtml += '<div style="font-size: 11px; color: #666; margin-bottom: 5px;">';
                        if (project.start_date) {
                            cardHtml += '<i class="fa fa-calendar"></i> Start: ' + project.start_date + '<br>';
                        }
                        if (project.end_date) {
                            cardHtml += '<i class="fa fa-calendar"></i> End: ' + project.end_date;
                        }
                        cardHtml += '</div>';
                    }
                    
                    if (project.project_lead) {
                        cardHtml += '<div style="font-size: 11px; color: #666; margin-bottom: 5px;"><i class="fa fa-user"></i> Lead: ' + project.project_lead + '</div>';
                    }
                    
                    if (project.assigned_users && project.assigned_users.length > 0) {
                        cardHtml += '<div style="font-size: 11px; color: #666; margin-bottom: 5px;"><i class="fa fa-users"></i> ' + project.assigned_users.slice(0, 2).join(', ') + (project.assigned_users.length > 2 ? ' +' + (project.assigned_users.length - 2) : '') + '</div>';
                    }
                    
                    var editBtn = '';
                    @can('project_checklist.update')
                    editBtn = '<button data-href="{{ action([\App\Http\Controllers\ProjectChecklistController::class, "edit"], ["__ID__"]) }}" class="btn btn-xs btn-primary edit_project_button" style="margin-right: 5px;"><i class="fa fa-edit"></i> Edit</button>';
                    @endcan
                    
                    cardHtml += '<div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #eee;">' +
                        '<a href="{{ action([\App\Http\Controllers\ProjectChecklistController::class, "show"], ["__ID__"]) }}" class="btn btn-xs btn-info" style="margin-right: 5px;"><i class="fa fa-eye"></i> View</a>' +
                        editBtn +
                        '</div></div>';
                    
                    cardHtml = cardHtml.replace(/__ID__/g, project.id);
                    $('#' + columnId).append(cardHtml);
                    
                    // Animate progress bar after appending
                    var $progressBar = $('#' + columnId).find('.progress-bar').last();
                    if ($progressBar.length) {
                        var progressWidth = project.progress + '%';
                        $progressBar.css('width', '0%');
                        setTimeout(function() {
                            $progressBar.css('width', progressWidth);
                        }, 100);
                    }
                    
                    // Update count
                    var currentCount = parseInt($('#' + countId).text()) || 0;
                    $('#' + countId).text(currentCount + 1);
                }
            });
        }
        
        // Load Gantt View
        function loadGanttView() {
            var projects = {!! json_encode($projects->map(function($project) {
                $statusData = $project->getProjectStatus();
                $total_tasks = $project->tasks->count();
                $progress = 0;
                if ($total_tasks > 0) {
                    $completed_tasks = $project->tasks->where('status', 1)->count();
                    $progress = round(($completed_tasks / $total_tasks) * 100);
                }
                $progress_color = '#dc3545';
                if ($progress > 70) {
                    $progress_color = '#28a745';
                } elseif ($progress >= 1) {
                    $progress_color = '#fd7e14';
                }
                
                $start_date = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : \Carbon\Carbon::parse($project->created_at);
                $end_date = $project->end_date ? \Carbon\Carbon::parse($project->end_date) : ($start_date->copy()->addDays(30));
                
                return [
                    'id' => $project->id,
                    'name' => $project->project_name,
                    'start_date' => $start_date->format('Y-m-d'),
                    'end_date' => $end_date->format('Y-m-d'),
                    'progress' => $progress,
                    'progress_color' => $progress_color,
                    'status' => $statusData['extra_status'] || $statusData['old_status']
                ];
            })) !!};
            
            if (projects.length === 0) {
                $('#gantt_chart').html('<div class="alert alert-info text-center">@lang("No projects found")</div>');
                return;
            }
            
            // Find min and max dates
            var minDate = new Date(projects[0].start_date);
            var maxDate = new Date(projects[0].end_date);
            
            projects.forEach(function(project) {
                var start = new Date(project.start_date);
                var end = new Date(project.end_date);
                if (start < minDate) minDate = start;
                if (end > maxDate) maxDate = end;
            });
            
            // Add padding
            minDate.setDate(minDate.getDate() - 7);
            maxDate.setDate(maxDate.getDate() + 7);
            
            // Calculate cell width
            var daysDiff = Math.ceil((maxDate - minDate) / (1000 * 60 * 60 * 24));
            var cellWidth = Math.max(20, Math.min(50, 1500 / daysDiff));
            
            // Generate month headers
            var monthHeaders = [];
            var currentDate = new Date(minDate);
            var currentMonth = '';
            
            while (currentDate <= maxDate) {
                var monthKey = currentDate.getFullYear() + '-' + (currentDate.getMonth() + 1);
                if (monthKey !== currentMonth) {
                    currentMonth = monthKey;
                    var monthStart = new Date(currentDate);
                    var monthEnd = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
                    monthEnd = monthEnd > maxDate ? maxDate : monthEnd;
                    var monthDays = Math.ceil((monthEnd - monthStart) / (1000 * 60 * 60 * 24)) + 1;
                    monthHeaders.push({
                        start: new Date(monthStart),
                        end: monthEnd,
                        days: monthDays,
                        label: currentDate.toLocaleDateString('en-US', { month: 'short', year: 'numeric' })
                    });
                }
                currentDate.setDate(currentDate.getDate() + 1);
            }
            
            // Build Gantt HTML
            var ganttHtml = '<div style="border: 1px solid #ddd; border-radius: 5px; overflow: hidden; background: white;">';
            
            // Month headers
            ganttHtml += '<div style="display: flex; background-color: #f8f9fa; border-bottom: 2px solid #ddd;">';
            ganttHtml += '<div style="width: 250px; padding: 10px; font-weight: bold; border-right: 1px solid #ddd; position: sticky; left: 0; background-color: #f8f9fa; z-index: 20;">@lang("Project")</div>';
            monthHeaders.forEach(function(month) {
                ganttHtml += '<div style="flex: ' + month.days + '; padding: 10px; text-align: center; border-right: 1px solid #ddd; font-weight: bold;">' + month.label + '</div>';
            });
            ganttHtml += '</div>';
            
            // Date headers
            ganttHtml += '<div style="display: flex; background-color: #e9ecef; border-bottom: 1px solid #ddd;">';
            ganttHtml += '<div style="width: 250px; padding: 5px; border-right: 1px solid #ddd; position: sticky; left: 0; background-color: #e9ecef; z-index: 20;"></div>';
            currentDate = new Date(minDate);
            var dayIndex = 0;
            while (currentDate <= maxDate) {
                var dayOfMonth = currentDate.getDate();
                var isWeekend = currentDate.getDay() === 0 || currentDate.getDay() === 6;
                var bgColor = isWeekend ? '#f0f0f0' : '#fff';
                ganttHtml += '<div style="width: ' + cellWidth + 'px; min-width: ' + cellWidth + 'px; padding: 5px; text-align: center; border-right: 1px solid #ddd; background-color: ' + bgColor + '; font-size: 11px;">' + dayOfMonth + '</div>';
                currentDate.setDate(currentDate.getDate() + 1);
                dayIndex++;
            }
            ganttHtml += '</div>';
            
            // Project rows
            projects.forEach(function(project) {
                var startDate = new Date(project.start_date);
                var endDate = new Date(project.end_date);
                
                var startOffset = Math.ceil((startDate - minDate) / (1000 * 60 * 60 * 24));
                var duration = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                
                var barWidth = duration * cellWidth;
                var barLeft = startOffset * cellWidth;
                
                ganttHtml += '<div style="display: flex; border-bottom: 1px solid #eee; min-height: 35px; align-items: center; position: relative;">';
                
                // Project name (sticky)
                // Break long project names into multiple lines
                var projectName = project.name;
                var maxLength = 25; // Characters per line
                var wrappedProjectName = '';
                if (projectName.length > maxLength) {
                    for (var i = 0; i < projectName.length; i += maxLength) {
                        wrappedProjectName += projectName.substring(i, i + maxLength);
                        if (i + maxLength < projectName.length) {
                            wrappedProjectName += '<br>';
                        }
                    }
                } else {
                    wrappedProjectName = projectName;
                }
                
                ganttHtml += '<div style="width: 250px; padding: 10px; border-right: 1px solid #ddd; position: sticky; left: 0; background-color: white; z-index: 10; min-height: 35px; display: flex; flex-direction: column; justify-content: center; word-wrap: break-word; overflow-wrap: break-word;">';
                ganttHtml += '<div style="margin-bottom: 5px; font-size: 14px; font-weight: bold; word-wrap: break-word; overflow-wrap: break-word; line-height: 1.4;">' + wrappedProjectName + '</div>';
                ganttHtml += '<div style="font-size: 11px; color: #666;">' + project.start_date + ' - ' + project.end_date + '</div>';
                ganttHtml += '<div style="font-size: 11px; color: #666;">Progress: ' + project.progress + '%</div>';
                ganttHtml += '</div>';
                
                // Timeline
                ganttHtml += '<div style="flex: 1; position: relative; min-height: 35px; padding: 7px 0;">';
                
                // Grid background
                currentDate = new Date(minDate);
                dayIndex = 0;
                while (currentDate <= maxDate) {
                    var isWeekend = currentDate.getDay() === 0 || currentDate.getDay() === 6;
                    var bgColor = isWeekend ? '#f9f9f9' : '#fff';
                    ganttHtml += '<div style="position: absolute; left: ' + (dayIndex * cellWidth) + 'px; width: ' + cellWidth + 'px; height: 100%; background-color: ' + bgColor + '; border-right: 1px solid #eee;"></div>';
                    currentDate.setDate(currentDate.getDate() + 1);
                    dayIndex++;
                }
                
                // Project bar
                var barColor = project.progress_color;
                
                // Calculate max characters that can fit in the bar
                var maxChars = Math.floor(barWidth / 7); // Approximate 7px per character
                var displayName = project.name;
                var needsTruncation = false;
                
                if (project.name.length > maxChars && maxChars > 0) {
                    displayName = project.name.substring(0, Math.max(10, maxChars - 3)) + '...';
                    needsTruncation = true;
                }
                
                // If bar is too narrow, show only progress percentage
                if (barWidth < 60) {
                    displayName = project.progress + '%';
                }
                
                ganttHtml += '<div style="position: absolute; left: ' + barLeft + 'px; width: ' + barWidth + 'px; height: 25px; background-color: ' + barColor + '; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center; color: black; font-weight: bold; font-size: 11px; cursor: pointer; overflow: hidden; word-wrap: break-word; overflow-wrap: break-word;" title="' + project.name + ' (' + project.progress + '%)" onclick="window.location.href=\'{{ action([\App\Http\Controllers\ProjectChecklistController::class, "show"], ["__ID__"]) }}\'.replace(\'__ID__\', ' + project.id + ')">';
                
                if (barWidth >= 60) {
                    // For wider bars, show name with proper wrapping
                    ganttHtml += '<div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding: 0 5px; max-width: 100%; word-break: break-word;">' + displayName + '</div>';
                } else {
                    // For narrow bars, just show percentage
                    ganttHtml += '<div style="padding: 0 5px; text-align: center;">' + displayName + '</div>';
                }
                
                ganttHtml += '</div>';
                
                // Progress indicator
                if (project.progress > 0) {
                    var progressWidth = (project.progress / 100) * barWidth;
                    ganttHtml += '<div style="position: absolute; left: ' + barLeft + 'px; width: ' + progressWidth + 'px; height: 3px; background-color: rgba(0,0,0,0.4); bottom: 5px; border-radius: 2px;"></div>';
                }
                
                ganttHtml += '</div></div>';
            });
            
            ganttHtml += '</div>';
            $('#gantt_chart').html(ganttHtml);
        }
        
        // Initialize Pie Chart (only old statuses)
        var ctx = document.getElementById('projectStatusChart').getContext('2d');
        var projectStatusChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [
                    '@lang("Complete")', 
                    '@lang("In Progress")', 
                    '@lang("Incomplete")'
                ],
                datasets: [{
                    data: [
                        {{ $complete_projects }}, 
                        {{ $in_progress_projects }}, 
                        {{ $incomplete_projects }}
                    ],
                    backgroundColor: [
                        '#28a745', // Green for Complete
                        '#ffc107', // Yellow for In Progress
                        '#dc3545'  // Red for Incomplete
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed || 0;
                                var total = {{ $total_projects }};
                                var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        //Project checklist table (client-side)
        var project_checklist_table = $('#project_checklist_table').DataTable({
            processing: true,
            // Default order: Created At (7th column, zero-based index 6) descending => newest first
            order: [[6, 'desc']],
            columnDefs: [
                { width: "5%", targets: 0 }, // Project Name
                { width: "20%", targets: 4 }, // Assigned Users (5th column, index 4)
                { width: "100%", targets: 8 }  // Project Progress
            ]
        });

        // Status filter
        var statusFilter = '';
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'project_checklist_table') {
                    return true;
                }
                if (statusFilter === '') {
                    return true;
                }
                var rowStatusCell = $(project_checklist_table.row(dataIndex).node()).find('td:eq(7)');
                var allStatuses = rowStatusCell.text().trim();
                // Check if the filter status appears in the status cell (can be old status or extra status)
                return allStatuses.indexOf(statusFilter) !== -1;
            }
        );

        $('#status_filter').on('change', function() {
            statusFilter = $(this).val();
            project_checklist_table.draw();
        });

        $(document).on('click', '.edit_project_button', function(e) {
            e.preventDefault();
            var href = $(this).data('href');
            $.ajax({
                url: href,
                dataType: 'html',
                success: function(result) {
                    $('.project_modal').html(result).modal('show');
                }
            });
        });

        $(document).on('click', '.delete_project_button', function(e) {
            e.preventDefault();
            var href = $(this).data('href');
            // Ask for confirmation before deleting
            if (! confirm('@lang("messages.are_you_sure")')) {
                return;
            }
            $.ajax({
                url: href,
                method: 'DELETE',
                dataType: 'json',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function(result) {
                    if (result.success) {
                        toastr.success(result.msg);
                        // reload page to refresh table since it's client-side
                        location.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function() {
                    toastr.error('@lang("messages.something_went_wrong")');
                }
            });
        });

        $('.project_modal').on('shown.bs.modal', function() {
            $('.project_modal input').focus();
            
            // Re-initialize Select2 in modal if needed
            var $modal = $(this);
            setTimeout(function() {
                try {
                    // Destroy existing select2 if any
                    $('.select2', $modal).each(function() {
                        if ($(this).hasClass('select2-hidden-accessible')) {
                            $(this).select2('destroy');
                        }
                    });
                    
                    // Initialize select2 with proper configuration for modal
                    $('.select2', $modal).select2({ 
                        width: '100%', 
                        dropdownParent: $modal,
                        closeOnSelect: false
                    });
                    
                    // For multiple select (Assign Users), close dropdown after selection
                    $('select.select2[multiple]', $modal).off('select2:select select2:unselect').on('select2:select select2:unselect', function (e) {
                        var $select = $(this);
                        setTimeout(function() {
                            $select.select2('close');
                        }, 150);
                    });
                } catch (e) {
                    console.log('Select2 re-initialization error:', e);
                }
            }, 200);
        });

        // Handle project form submit inside modal via AJAX so we can redirect to index after update
        $(document).on('submit', 'form#project_form', function(e) {
            e.preventDefault();
            var $form = $(this);
            var url = $form.attr('action');
            var method = ($form.attr('method') || 'POST').toUpperCase();
            var data = $form.serialize();

            $.ajax({
                url: url,
                method: method,
                data: data,
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        $('.project_modal').modal('hide');
                        toastr.success(result.msg);
                        // Redirect to project checklist index
                        window.location.href = '{{ action([\App\Http\Controllers\ProjectChecklistController::class, "index"]) }}';
                    } else {
                        toastr.error(result.msg);
                    }
                },
                error: function() {
                    toastr.error('@lang("messages.something_went_wrong")');
                }
            });
        });
    });
</script>

<style>
    /* Progress bar animation */
    .animated-progress {
        animation: progressAnimation 1.5s ease-in-out;
        transition: width 1.5s ease-in-out, background-color 0.3s ease;
    }
    
    @keyframes progressAnimation {
        from {
            width: 0%;
        }
    }
    
    /* Smooth animation on load */
    .progress-bar {
        position: relative;
        overflow: hidden;
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background-image: linear-gradient(
            -45deg,
            rgba(255, 255, 255, 0.2) 25%,
            transparent 25%,
            transparent 50%,
            rgba(255, 255, 255, 0.2) 50%,
            rgba(255, 255, 255, 0.2) 75%,
            transparent 75%,
            transparent
        );
        background-size: 1rem 1rem;
        animation: progressBarStripes 1s linear infinite;
        opacity: 0.3;
    }
    
    @keyframes progressBarStripes {
        0% {
            background-position: 0 0;
        }
        100% {
            background-position: 1rem 0;
        }
    }
</style>
@endpush
@endsection