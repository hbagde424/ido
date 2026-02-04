@extends('layouts.app')

@section('title', __('essentials::lang.todo') . ' - Dashboard')

@section('css')
<style>
    .chart-container {
        position: relative;
        height: 400px;
        margin-bottom: 20px;
    }
    
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .stats-card h3 {
        margin: 0;
        font-size: 2.5em;
        font-weight: bold;
    }
    
    .stats-card p {
        margin: 5px 0 0 0;
        opacity: 0.9;
    }
    
    .chart-legend {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 15px;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        margin: 5px 15px;
        font-size: 14px;
    }
    
    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 3px;
        margin-right: 8px;
    }
    
    .dashboard-header {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .dashboard-header h1 {
        margin: 0;
        font-size: 2.2em;
        font-weight: 300;
    }
    
    .chart-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    
    .chart-card h4 {
        margin-top: 0;
        color: #333;
        text-align: center;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
    }
    
    .employee-bar {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #007bff;
    }
    
    .employee-name {
        font-weight: bold;
        min-width: 150px;
        color: #333;
    }
    
    .task-bars {
        flex: 1;
        display: flex;
        margin: 0 15px;
        height: 25px;
        border-radius: 12px;
        overflow: hidden;
        background: #e9ecef;
    }
    
    .task-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: bold;
        text-shadow: 1px 1px 1px rgba(0,0,0,0.3);
        transition: all 0.3s ease;
    }
    
    .task-bar:hover {
        transform: scale(1.05);
        z-index: 1;
    }
    
    .task-bar.completed {
        background-color: #28a745;
    }
    
    .task-bar.in_progress {
        background-color: #17a2b8;
    }
    
    .task-bar.incomplete {
        background-color: #dc3545;
    }
    
    .task-count {
        font-weight: bold;
        color: #666;
        min-width: 60px;
        text-align: right;
    }
    
    .loading-spinner {
        text-align: center;
        padding: 50px;
    }
    
    .view-toggle {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .view-toggle .btn {
        margin: 0 5px;
        border-radius: 25px;
        padding: 8px 20px;
    }
    
    .refresh-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 100;
    }
    
    @media (max-width: 768px) {
        .employee-bar {
            flex-direction: column;
            align-items: stretch;
        }
        
        .employee-name {
            min-width: auto;
            margin-bottom: 10px;
        }
        
        .task-bars {
            margin: 0;
        }
        
        .task-count {
            margin-top: 10px;
            text-align: center;
        }
    }
</style>
@endsection

@section('content')
@include('essentials::layouts.nav_essentials')

<section class="content">
    <div class="dashboard-header">
        <h1><i class="fas fa-chart-pie"></i> Task Status Dashboard</h1>
        <p>Real-time overview of task distribution and employee performance</p>
    </div>
    
    <div class="view-toggle">
        <a href="{{ url('/essentials/todo') }}" class="btn btn-outline-primary">
            <i class="fas fa-list"></i> List View
        </a>
        <a href="{{ url('/essentials/todo?view=dashboard') }}" class="btn btn-primary">
            <i class="fas fa-chart-bar"></i> Dashboard View
        </a>
        <button id="refresh-data" class="btn btn-outline-success">
            <i class="fas fa-sync-alt"></i> Refresh Data
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row" id="stats-cards">
        <div class="col-md-3">
            <div class="stats-card">
                <h3 id="total-tasks">-</h3>
                <p><i class="fas fa-tasks"></i> Total Tasks</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <h3 id="completed-tasks">-</h3>
                <p><i class="fas fa-check-circle"></i> Completed</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #17a2b8 0%, #007bff 100%);">
                <h3 id="progress-tasks">-</h3>
                <p><i class="fas fa-spinner"></i> In Progress</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card" style="background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);">
                <h3 id="incomplete-tasks">-</h3>
                <p><i class="fas fa-exclamation-triangle"></i> Incomplete</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Pie Chart -->
        <div class="col-md-6">
            <div class="chart-card">
                <button class="btn btn-sm btn-outline-secondary refresh-btn" onclick="loadChartData()">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <h4><i class="fas fa-chart-pie"></i> Task Status Distribution</h4>
                <div class="chart-container">
                    <canvas id="statusPieChart"></canvas>
                </div>
                <div class="chart-legend" id="pie-legend"></div>
            </div>
        </div>

        <!-- Employee Bar Chart -->
        <div class="col-md-6">
            <div class="chart-card">
                <h4><i class="fas fa-users"></i> Task Status by Employee</h4>
                <div id="employee-chart-container">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Loading employee data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add ToDo Button -->
    @can('essentials.add_todos')
    <div class="text-center" style="margin-top: 30px;">
        <button class="btn btn-lg btn-success btn-modal" data-href="{{action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'create'])}}" 
        data-container="#task_modal">
            <i class="fa fa-plus"></i> Add New Task
        </button>
    </div>
    @endcan
</section>

<!-- Loading Modal -->
<div class="modal fade" id="loading-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p>Loading chart data...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    loadChartData();
    
    // Auto-refresh every 5 minutes
    setInterval(loadChartData, 300000);
    
    $('#refresh-data, #refresh-chart').click(function() {
        loadChartData();
    });
});

let pieChart = null;

function loadChartData() {
    // Show loading for employee chart
    $('#employee-chart-container').html(`
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p>Loading employee data...</p>
        </div>
    `);
    
    $.ajax({
        url: '{{ url("/essentials/todo-chart-data") }}',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            updateStatistics(data);
            renderPieChart(data.pie_data);
            renderEmployeeChart(data.employee_data);
        },
        error: function(xhr, status, error) {
            console.error('Error loading chart data:', error);
            toastr.error('Failed to load chart data. Please try again.');
            
            $('#employee-chart-container').html(`
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x"></i>
                    <p>Failed to load data. Please refresh.</p>
                    <button class="btn btn-primary" onclick="loadChartData()">
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
                duration: 1000
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
                <i class="fas fa-info-circle fa-2x"></i>
                <p>No employee task data available</p>
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
                <div class="employee-name">${employee.name}</div>
                <div class="task-bars">
                    ${employee.Completed > 0 ? `<div class="task-bar completed" style="flex: ${completedWidth}" title="Completed: ${employee.Completed}">${employee.Completed}</div>` : ''}
                    ${employee['In progress'] > 0 ? `<div class="task-bar in_progress" style="flex: ${progressWidth}" title="In Progress: ${employee['In progress']}">${employee['In progress']}</div>` : ''}
                    ${employee.Incomplete > 0 ? `<div class="task-bar incomplete" style="flex: ${incompleteWidth}" title="Incomplete: ${employee.Incomplete}">${employee.Incomplete}</div>` : ''}
                </div>
                <div class="task-count">${total} total</div>
            </div>
        `;
    });
    
    $('#employee-chart-container').html(chartHtml);
}
</script>
@endsection