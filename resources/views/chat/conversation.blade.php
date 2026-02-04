@extends('layouts.app')
@section('content')
<div class="container">
    <h4>Chat</h4>
    <div id="chat-box">
        @foreach($conversation->messages as $message)
            <div>
                <strong>{{ $message->sender->first_name }}:</strong> {{ $message->message }}
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('chat.send', $conversation->id) }}">
        @csrf
        <input type="text" name="message" placeholder="Type a message..." required>
        <button type="submit">Send</button>
    </form>
</div>
@endsection
