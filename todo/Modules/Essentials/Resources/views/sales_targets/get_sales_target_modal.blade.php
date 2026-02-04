<div class="modal-dialog" role="document">
  	<div class="modal-content">
  	    
  	    
  	  
  	 	<div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      	<h4 class="modal-title">
	      Performance Report {{$user->user_full_name}}
	      	</h4>
	    </div>
	   <div class="modal-body">
    <div class="form-group">
        <label for="filter_month">Select Month:</label>
        <select class="form-control" id="filter_month">
            @for ($i = 0; $i < 12; $i++)
                @php
                    $monthValue = \Carbon\Carbon::now()->subMonths($i)->format('Y-m');
                    $monthLabel = \Carbon\Carbon::now()->subMonths($i)->format('F Y');
                @endphp
                <option value="{{ $monthValue }}">{{ $monthLabel }}</option>
            @endfor
        </select>
    </div>

    <table class="table" id="target_table">
        <thead>
            <tr>
                <th>Parameter Name</th>
                <th>Admin Review</th>
                <th>Self Review</th>
                <th>Month</th>
            </tr>
        </thead>
        <tbody id="target_table_body">
            @foreach($sales_targets as $target)
                <tr>
                    <td>{{ $target->parameter_name }}</td>
                    <td>{{ $target->admin_review ?? '-' }}</td>
                    <td>{{ $target->self_review ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($target->created_at)->format('F Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

  	</div>
</div>


<script>
$(document).ready(function() {
    $('#filter_month').on('change', function() {
        let selectedMonth = $(this).val();
        let userId = "{{ $user->id }}";

        $.ajax({
            url: "{{ url('hrm/sales-targets/ajax-targets') }}", // create this route
            method: "GET",
            data: {
                user_id: userId,
                month: selectedMonth
            },
            success: function(response) {
                $('#target_table_body').html(response); // response = <tr>...</tr> rows
            }
        });
    });
});
</script>

