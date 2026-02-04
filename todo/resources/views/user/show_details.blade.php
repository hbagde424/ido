@php
    $custom_labels = json_decode(session('business.custom_labels'), true);
@endphp
<div class="row">
	<div class="col-md-12">
		<div class="col-md-12">
			<h4>@lang('lang_v1.more_info')</h4>
		</div>
		<div class="col-md-4">
			<p><strong>@lang( 'lang_v1.dob' ):</strong> @if(!empty($user->dob)) {{@format_date($user->dob)}} @endif</p>
			<p><strong>@lang( 'lang_v1.gender' ):</strong> @if(!empty($user->gender)) @lang('lang_v1.' .$user->gender) @endif</p>
			<p><strong>@lang( 'lang_v1.marital_status' ):</strong> @if(!empty($user->marital_status)) @lang('lang_v1.' .$user->marital_status) @endif</p>
			<p><strong>@lang( 'lang_v1.blood_group' ):</strong> {{$user->blood_group ?? ''}}</p>
			<p><strong>@lang( 'lang_v1.mobile_number' ):</strong> {{$user->contact_number ?? ''}}</p>
			<p><strong>@lang( 'business.alternate_number' ):</strong> {{$user->alt_number ?? ''}}</p>
			<p><strong>@lang( 'lang_v1.family_contact_number' ):</strong> {{$user->family_number ?? ''}}</p>
		</div>
		<div class="col-md-4">
			<p><strong>@lang( 'lang_v1.fb_link' ):</strong> {{$user->fb_link ?? ''}}</p>
			<p><strong>@lang( 'lang_v1.twitter_link' ):</strong> {{$user->twitter_link ?? ''}}</p>
			<p><strong>@lang( 'lang_v1.social_media', ['number' => 1] ):</strong> {{$user->social_media_1 ?? ''}}</p>
			<p><strong>@lang( 'lang_v1.social_media', ['number' => 2] ):</strong> {{$user->social_media_2 ?? ''}}</p>
		</div>
		<div class="col-md-4">
			<p><strong>{{ $custom_labels['user']['custom_field_1'] ?? __('lang_v1.user_custom_field1' )}}:</strong> {{$user->custom_field_1 ?? ''}}</p>
			<p><strong>{{ $custom_labels['user']['custom_field_2'] ?? __('lang_v1.user_custom_field2' )}}:</strong> {{$user->custom_field_2 ?? ''}}</p>
			<p><strong>{{ $custom_labels['user']['custom_field_3'] ?? __('lang_v1.user_custom_field3' )}}:</strong> {{$user->custom_field_3 ?? ''}}</p>
			<p><strong>{{ $custom_labels['user']['custom_field_4'] ?? __('lang_v1.user_custom_field4' )}}:</strong> {{$user->custom_field_4 ?? ''}}</p>
		</div>
		<div class="clearfix"></div>
		<div class="col-md-4">
			<p><strong>@lang('lang_v1.id_proof_name'):</strong>
			{{$user->id_proof_name ?? ''}}</p>
		</div>
		<div class="col-md-4">
			<p><strong>@lang('lang_v1.id_proof_number'):</strong>
			{{$user->id_proof_number ?? ''}}</p>
		</div>
		<div class="clearfix"></div>
		<hr>
		<div class="col-md-6">
			<strong>@lang('lang_v1.permanent_address'):</strong><br>
			<p>{{$user->permanent_address ?? ''}}</p>
		</div>
		<div class="col-md-6">
			<strong>@lang('lang_v1.current_address'):</strong><br>
			<p>{{$user->current_address ?? ''}}</p>
		</div>
		<div class="clearfix"></div>
		<hr>
		<div class="col-md-12">
			<h4>@lang('lang_v1.bank_details'):</h4>
		</div>
		@php
			$bank_details = !empty($user->bank_details) ? json_decode($user->bank_details, true) : [];
		@endphp
		<div class="col-md-4">
			<p><strong>@lang('lang_v1.account_holder_name'):</strong> {{$bank_details['account_holder_name'] ?? ''}}</p>
			<p><strong>@lang('lang_v1.account_number'):</strong> {{$bank_details['account_number'] ?? ''}}</p>
		</div>
		<div class="col-md-4">
			<p><strong>@lang('lang_v1.bank_name'):</strong> {{$bank_details['bank_name'] ?? ''}}</p>
			<p><strong>@lang('lang_v1.bank_code'):</strong> {{$bank_details['bank_code'] ?? ''}}</p>
		</div>
		<div class="col-md-4">
			<p><strong>@lang('lang_v1.branch'):</strong> {{$bank_details['branch'] ?? ''}}</p>
			<p><strong>@lang('lang_v1.tax_payer_id'):</strong> {{$bank_details['tax_payer_id'] ?? ''}}</p>
		</div>
		<div class="clearfix"></div>
		<hr>
		<div class="col-md-12">
			<h4>@lang('lang_v1.documents'):</h4>
		</div>
		@php
			$document_types = [
				'resume' => __('lang_v1.resume'),
				'id_proof_document' => __('lang_v1.id_proof_document'),
				'address_proof_document' => __('lang_v1.address_proof_document'),
				'photo_document' => __('lang_v1.photo_document'),
				'nda_document' => __('lang_v1.nda_document'),
				'other_docs' => __('lang_v1.other_docs')
		];
		$user_media = method_exists($user, 'allMedia') ? $user->allMedia : collect([]);
	@endphp
		@foreach($document_types as $doc_key => $doc_label)
		<div class="col-md-6 mb-3">
			<strong>{{ $doc_label }}:</strong>
			@php
				$doc = $user_media->where('model_media_type', $doc_key)->first();
			@endphp
			@if($doc)
				<div class="mt-2" style="margin-top: 10px;">
					@php
						$fileExtension = strtolower(pathinfo($doc->file_name, PATHINFO_EXTENSION));
						$isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
						
						// Check file location - try public/uploads/media first, then storage/app/media
						$publicPath = public_path('uploads/media/' . $doc->file_name);
						$storagePath = storage_path('app/media/' . $doc->file_name);
						
						// Default to public path URL
						$fileUrl = asset('uploads/media/' . rawurlencode($doc->file_name));
						
						// Check if file exists in public path
						if (file_exists($publicPath)) {
							$fileUrl = asset('uploads/media/' . rawurlencode($doc->file_name));
						} elseif (file_exists($storagePath)) {
							// File is in storage, need to serve via route or copy to public
							// For now, try to use storage URL (requires symlink)
							$fileUrl = url('storage/media/' . rawurlencode($doc->file_name));
						}
						
						// Ensure URL is properly encoded
						$fileUrl = str_replace(' ', '%20', $fileUrl);
					@endphp
					@if($isImage)
						<div class="document-preview-container" style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
							<a href="{{ $fileUrl }}" target="_blank" data-toggle="modal" data-target="#documentModal{{ $doc->id }}" style="display: inline-block; text-decoration: none;">
								<img src="{{ $fileUrl }}" alt="{{ $doc->display_name }}" 
									style="max-width: 200px; max-height: 200px; border: 2px solid #ddd; padding: 5px; cursor: pointer; border-radius: 4px; background: white; display: block; margin: 0 auto;" 
									onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
								<div style="display: none; padding: 20px; color: #ef4444;">
									<i class="fa fa-exclamation-triangle fa-2x"></i>
									<p>Image not found</p>
								</div>
							</a>
							<p class="mt-2" style="margin-top: 10px; text-align: center;">
								<small style="color: #6b7280; display: block; margin-bottom: 8px; word-break: break-all;">{{ $doc->display_name }}</small>
								<a href="{{ $fileUrl }}" download="{{ $doc->display_name }}" class="btn btn-xs btn-primary" style="font-size: 11px; padding: 4px 12px;">
									<i class="fa fa-download"></i> Download
								</a>
							</p>
						</div>
						<!-- Modal for image preview -->
						<div class="modal fade" id="documentModal{{ $doc->id }}" tabindex="-1" role="dialog">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
										<h4 class="modal-title">{{ $doc_label }}</h4>
									</div>
									<div class="modal-body text-center" style="padding: 20px;">
										<img src="{{ $fileUrl }}" alt="{{ $doc->display_name }}" 
											style="max-width: 100%; max-height: 70vh; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);" 
											onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\'%3EImage not found%3C/text%3E%3C/svg%3E';" />
									</div>
									<div class="modal-footer">
										<a href="{{ $fileUrl }}" download="{{ $doc->display_name }}" target="_blank" class="btn btn-primary">
											<i class="fa fa-download"></i> @lang('lang_v1.download')
										</a>
										<button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
									</div>
								</div>
							</div>
						</div>
					@else
						<div class="document-preview-container" style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0; text-align: center;">
							<div>
								<i class="fa fa-file fa-4x" style="color: #667eea; margin-bottom: 10px;"></i>
								<p style="margin: 10px 0; color: #6b7280; word-break: break-all; font-size: 12px;">
									<small>{{ $doc->display_name }}</small>
								</p>
								<div style="margin-top: 10px;">
									<a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-info" style="margin-right: 5px;">
										<i class="fa fa-eye"></i> View
									</a>
									<a href="{{ $fileUrl }}" download="{{ $doc->display_name }}" class="btn btn-sm btn-primary">
										<i class="fa fa-download"></i> Download
									</a>
								</div>
							</div>
						</div>
					@endif
				</div>
			@else
				<p class="text-muted"><em>@lang('lang_v1.no_document_uploaded')</em></p>
			@endif
		</div>
		@endforeach
		@if(!empty($view_partials))
	      @foreach($view_partials as $partial)
	        {!! $partial !!}
	      @endforeach
	    @endif
	</div>
</div>