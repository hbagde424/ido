@extends('layouts.app')
@section('content')
<div class="container">
    <h4>Your Conversations</h4>
    <ul>
        @foreach($conversations as $conversation)
            <li>
                <a href="{{ route('chat.show', $conversation->id) }}">
                    {{ $conversation->userOne->id == auth()->id() ? $conversation->userTwo->first_name : $conversation->userOne->first_name }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
