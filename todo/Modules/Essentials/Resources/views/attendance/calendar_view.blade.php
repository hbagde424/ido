<div class="calendar-container">
    <!-- Modern Header Section -->
    <div class="calendar-header">
        <div class="employee-filter-section">
            @can('essentials.crud_all_attendance')
            <div class="filter-group">
                <label class="filter-label">
                    <i class="fas fa-users"></i> Select Employee
                </label>
                <select id="calendar_employee_filter" class="modern-select">
                    <option value="">🌟 All Employees</option>
                    @if(isset($employees))
                        @foreach($employees as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            @else
            <!-- Hidden filter for normal users - automatically set to their own ID -->
            <input type="hidden" id="calendar_employee_filter" value="{{ auth()->id() }}">
            @endcan
            
            <div class="legend-container">
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #10b981, #059669);"></div>
                    <span><i class="fas fa-check-circle"></i> Present</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #ef4444, #dc2626);"></div>
                    <span><i class="fas fa-times-circle"></i> Absent</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #f59e0b, #d97706);"></div>
                    <span><i class="fas fa-calendar-check"></i> Leave (Approved)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #fb7185, #f43f5e);"></div>
                    <span><i class="fas fa-calendar-times"></i> Leave (Pending)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #64748b, #475569);"></div>
                    <span><i class="fas fa-calendar-minus"></i> Leave (Rejected)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);"></div>
                    <span><i class="fas fa-gift"></i> Holiday</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #06b6d4, #0891b2);"></div>
                    <span><i class="fas fa-clock"></i> Half Day</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #f97316, #ea580c);"></div>
                    <span><i class="fas fa-sun"></i> Sunday</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modern Calendar Container -->
    <div class="calendar-main">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px;">
            <h3 style="margin: 0; font-size: 24px; font-weight: 700; color: #1a202c; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-calendar-alt" style="color: #667eea;"></i> 
                Attendance Calendar
            </h3>
            <div style="display: flex; gap: 10px;">
                <button id="calendar_refresh" class="btn btn-sm" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 8px; padding: 8px 16px;">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
                <button id="calendar_today" class="btn btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 8px; padding: 8px 16px;">
                    <i class="fas fa-calendar-day"></i> Today
                </button>
            </div>
        </div>
        <div id="calendar_loading" style="display: none; text-align: center; padding: 50px;">
            <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #667eea; margin-bottom: 20px;"></i>
            <p style="color: #64748b; font-size: 16px; margin: 0;">Loading calendar data...</p>
        </div>
        <div id="attendance_calendar" style="min-height: 650px; border-radius: 15px; overflow: hidden;"></div>
    </div>
        
    <!-- Modern Attendance Details Panel -->
    <div class="details-panel" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 15px; padding: 25px; margin-top: 25px; box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
        <h4 style="margin: 0 0 20px 0; font-size: 20px; font-weight: 600; color: #1a202c; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-info-circle" style="color: #667eea;"></i> 
            Attendance Details
        </h4>
        <div id="attendance_details">
            <div style="text-align: center; padding: 40px; color: #64748b;">
                <i class="fas fa-mouse-pointer" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                <p style="margin: 0; font-size: 16px;">Click on a calendar event to view detailed attendance information</p>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Calendar Styling */
#attendance_calendar {
    font-family: 'Inter', sans-serif;
    background: white;
    border-radius: 15px;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
}

.fc-event {
    border-radius: 8px !important;
    border: none !important;
    color: white !important;
    font-weight: 600 !important;
    font-size: 11px !important;
    padding: 2px 6px !important;
    margin: 1px !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
}

.fc-event:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
}

.fc-event.present {
    background: linear-gradient(135deg, #10b981, #059669) !important;
}

.fc-event.absent {
    background: linear-gradient(135deg, #ef4444, #dc2626) !important;
}

.fc-event.leave {
    background: linear-gradient(135deg, #f59e0b, #d97706) !important;
    color: white !important;
}

.fc-event.leave-pending {
    background: linear-gradient(135deg, #fb7185, #f43f5e) !important;
    color: white !important;
}

.fc-event.leave-rejected {
    background: linear-gradient(135deg, #64748b, #475569) !important;
    color: white !important;
}

.fc-event.holiday {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
}

.fc-event.half-day {
    background: linear-gradient(135deg, #06b6d4, #0891b2) !important;
}

.fc-event.sunday {
    background: linear-gradient(135deg, #f97316, #ea580c) !important;
    color: white !important;
}

.fc-event.debug {
    background: linear-gradient(135deg, #ec4899, #be185d) !important;
    color: white !important;
    font-weight: bold !important;
}

.fc-day-grid-event {
    margin: 1px 0;
}

.tooltip-attendance {
    max-width: 250px;
    font-size: 12px;
    line-height: 1.4;
}

.calendar-legend {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}

/* Enhanced Responsive Design */
@media (max-width: 768px) {
    .calendar-container {
        padding: 15px;
        margin: 10px;
        border-radius: 15px;
    }
    
    .calendar-header {
        padding: 20px;
    }
    
    .employee-filter-section {
        flex-direction: column;
        gap: 15px;
    }
    
    .filter-group {
        min-width: auto;
    }
    
    .legend-container {
        flex-direction: column;
        gap: 8px;
        align-items: stretch;
    }
    
    .legend-container .legend-item {
        font-size: 12px;
        padding: 6px 10px;
    }
    
    .legend-item {
        justify-content: flex-start;
    }
    
    .calendar-main {
        padding: 20px;
        border-radius: 15px;
    }
    
    .fc-header-toolbar {
        flex-direction: column;
        gap: 15px;
    }
    
    .fc-center h2 {
        font-size: 24px !important;
    }
    
    .fc-button {
        padding: 6px 12px !important;
        font-size: 12px !important;
    }
    
    .fc-event {
        font-size: 10px !important;
        padding: 1px 4px !important;
    }
    
    .attendance-detail-item {
        padding: 15px;
    }
    
    .detail-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .detail-row strong {
        min-width: auto;
    }
}

@media (max-width: 480px) {
    .fc-day-header {
        font-size: 12px !important;
        padding: 10px 0 !important;
    }
    
    .fc-day-number {
        font-size: 14px !important;
    }
    
    .calendar-header h3 {
        font-size: 20px !important;
    }
}

/* Modern FullCalendar button styling */
.fc-button {
    background: linear-gradient(135deg, #667eea, #764ba2) !important;
    border: none !important;
    color: white !important;
    border-radius: 8px !important;
    padding: 8px 16px !important;
    font-weight: 500 !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
}

.fc-button:hover {
    background: linear-gradient(135deg, #5a67d8, #6b46c1) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
}

.fc-button-primary:not(:disabled):active,
.fc-button-primary:not(:disabled).fc-button-active {
    background: linear-gradient(135deg, #4c51bf, #553c9a) !important;
    transform: translateY(0) !important;
}

.fc-today {
    background: linear-gradient(135deg, #fef3c7, #fde68a) !important;
    border: 2px solid #f59e0b !important;
}

.fc-day-header {
    background: linear-gradient(135deg, #1f2937, #374151) !important;
    color: white !important;
    font-weight: 600 !important;
    padding: 15px 0 !important;
    font-size: 14px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

.fc-day-number {
    font-weight: 600 !important;
    color: #374151 !important;
    padding: 8px !important;
}

.fc-day-grid .fc-day-number {
    padding: 8px 12px !important;
}

.fc-other-month .fc-day-number {
    color: #9ca3af !important;
}

/* Highlight Sundays in calendar grid */
.fc-sun {
    background-color: #fef3c7 !important;
}

.fc-sun .fc-day-number {
    color: #d97706 !important;
    font-weight: 700 !important;
}

/* Highlight today */
.fc-today {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe) !important;
    border: 2px solid #3b82f6 !important;
}

.fc-today .fc-day-number {
    color: #1d4ed8 !important;
    font-weight: 700 !important;
}

.fc-header-toolbar {
    padding: 20px 0 !important;
    margin-bottom: 20px !important;
}

.fc-center h2 {
    font-size: 28px !important;
    font-weight: 700 !important;
    color: #1f2937 !important;
    margin: 0 !important;
}

/* Modern Attendance Details Panel */
#attendance_details {
    min-height: 120px;
}

.attendance-detail-item {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: 12px;
    padding: 20px;
    margin: 10px 0;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.attendance-detail-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}

.attendance-detail-item h5 {
    margin: 0 0 15px 0;
    color: #1a202c;
    font-size: 18px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
}

.attendance-detail-item h5 i {
    color: #667eea;
    font-size: 20px;
}

.detail-row {
    margin: 10px 0;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid #f1f5f9;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-row strong {
    color: #374151;
    font-weight: 600;
    min-width: 120px;
}

.detail-row .label {
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.label-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.label-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.label-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.label-default {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
}

.label-sunday {
    background: linear-gradient(135deg, #f97316, #ea580c);
    color: white;
}

.label-success {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.label-warning {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.label-danger {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}
</style>

<script>
// Calendar specific jQuery functions will be loaded after the main document ready

// Calendar specific functionality is now handled in the main attendance page
</script>