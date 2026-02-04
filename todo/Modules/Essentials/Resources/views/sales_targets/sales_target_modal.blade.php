<div class="modal-dialog" role="document">
  	<div class="modal-content">
  	    
  	    
  	  
  		{!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\SalesTargetController::class, 'saveSalesTarget']), 'method' => 'post' ]) !!}
  		<input type="hidden" name="user_id" value="{{$user->id}}">
  		<div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      	<h4 class="modal-title">
	      	Set Review Points {{$user->user_full_name}}
	      	</h4>
	    </div>
	    <div class="modal-body">
	    	<table class="table" id="target_table">
	    		<thead>
	    			<tr>
	    				<th>
	    				Parameter Name
	    				</th>
	    				<th>
	    				Admin Review
	    				</th>
	    				<th>
	    				Self Review
	    				</th> 
	    			</tr>
	    			</thead>
	    			<tbody>
	    				@foreach($sales_targets as $sales_target)
	    					<tr>
			    				<td>
			    	                {{$sales_target->parameter_name}}
			    				</td>
			    				<td>
			    				    <input class="form-control input-sm input_number" value="{{$sales_target->id}}" name="edit_target[{{$sales_target->id}}][parameter_id]" type="hidden" >
			    				    <input class="form-control input-sm input_number" required="" name="edit_target[{{$sales_target->id}}][admin_review]" type="text" >
 			    				</td>
			    				<td>
                                <input class="form-control input-sm input_number" required="" name="edit_target[{{$sales_target->id}}][self_review]" type="text" >

			    			 	</td>
			    				 
			    			</tr>
	    				@endforeach
	    			</tbody>
	    	</table>
	    </div>
	    <div class="modal-footer">
	      	<button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
	      	<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	    {!! Form::close() !!}
	  
  	</div>
</div>