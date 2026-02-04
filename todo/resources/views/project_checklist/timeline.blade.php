<div class="timeline-container">
    <h4>@lang('Task Timeline') - {{ $task->task_name ?? 'Task' }}</h4>
    
    @if(isset($comments) && $comments->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>@lang('Date')</th>
                        <th>@lang('Comment')</th>
                        <th>@lang('Document')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comments as $comment)
                        <tr>
                            <td style="width: 20%;">
                                {{ $comment->created_at ? $comment->created_at->format('d/m/Y H:i') : '' }}
                            </td>
                            <td style="width: 50%;">
                                <div style="white-space: pre-wrap; max-height: 100px; overflow-y: auto;">
                                    {{ $comment->comment ?? '' }}
                                </div>
                            </td>
                            <td style="width: 30%;">
                                @if(!empty($comment->document_path))
                                    <a href="{{ action([\App\Http\Controllers\ProjectChecklistController::class, 'downloadDocument'], $comment->id) }}" 
                                       class="btn btn-xs btn-primary" target="_blank">
                                        <i class="fa fa-download"></i> {{ $comment->document_name ?? 'Download' }}
                                    </a>
                                @else
                                    <span class="text-muted">@lang('No document')</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-muted text-center">@lang('No comments yet')</p>
    @endif
</div>

