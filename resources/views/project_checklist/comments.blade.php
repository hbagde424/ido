<div class="comments-container">
    <h4>@lang('Task Comments') - {{ $task->task_name ?? 'Task' }}</h4>
    
    @if(isset($comments) && $comments->count() > 0)
        <div class="comments-list" style="max-height: 400px; overflow-y: auto; margin-bottom: 20px;">
            @foreach($comments as $comment)
                <div class="comment-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; background-color: #f9f9f9;">
                    <div class="comment-header" style="margin-bottom: 10px;">
                        <strong>
                            @if($comment->user)
                                {{ $comment->user->user_full_name ?? ($comment->user->first_name . ' ' . $comment->user->last_name) ?? 'Unknown User' }}
                            @else
                                @lang('Unknown User')
                            @endif
                        </strong>
                        <span class="text-muted" style="font-size: 12px; margin-left: 10px;">
                            {{ $comment->created_at ? $comment->created_at->format('d/m/Y H:i') : '' }}
                        </span>
                    </div>
                    <div class="comment-body" style="margin-bottom: 10px;">
                        <p style="white-space: pre-wrap;">{{ $comment->comment ?? '' }}</p>
                    </div>
                    @if(!empty($comment->document_path))
                        <div class="comment-document">
                            <a href="{{ action([\App\Http\Controllers\ProjectChecklistController::class, 'downloadDocument'], $comment->id) }}" 
                               class="btn btn-xs btn-primary" target="_blank">
                                <i class="fa fa-download"></i> {{ $comment->document_name ?? 'Download' }}
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted">@lang('No comments yet')</p>
    @endif
</div>

