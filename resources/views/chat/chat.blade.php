@extends('layouts.app')

@section('content')

<!-- CSS for Chat Layout -->
<style>
  .chat-container {
    height: calc(100vh - 60px);
  }

  .user-list {
    height: 100%;
    overflow-y: auto;
    border-right: 1px solid #dee2e6;
  }

  .chat-header {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    background: #fff;
  }

  .chat-box {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    background: #f8f9fa;
  }

  .chat-input {
    height: 70px;
    border-top: 1px solid #dee2e6;
    background: #fff;
    padding: 0.75rem;
  }

  .chat-bubble {
    max-width: 75%;
    padding: .6rem .9rem;
    border-radius: 15px;
    margin-bottom: .5rem;
    word-wrap: break-word;
    position: relative;
  }

  .chat-bubble.incoming {
    background-color: #f1f1f1;
    color: #000;
    align-self: flex-start;
  }

  .chat-bubble.outgoing {
    background-color: #6f42c1;
    color: #fff;
    align-self: flex-end;
  }

  .chat-bubble small {
    display: block;
    text-align: right;
    font-size: 10px;
    color: #ccc;
    margin-top: 4px;
  }

  .list-group-chat {
    max-height: calc(100vh - 100px);
    overflow-y: auto;
  }
</style>

<div class="container-fluid chat-container">
  <div class="row h-100">
    <!-- User List -->
    <div class="col-md-4 bg-white d-flex flex-column user-list">
      <div class="p-3 border-bottom fw-bold text-primary">👥 User List</div>
      <div class="list-group list-group-chat">
        @foreach($users as $user)
        <a href="{{ route('chat.show', $user->id) }}" class="list-group-item list-group-item-action d-flex align-items-start">
          <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
          </div>
          <div class="ms-3 flex-grow-1">
            <div class="fw-semibold">{{ $user->first_name }}</div>
            <small class="text-muted">{{ $user->last_message ? Str::limit($user->last_message->message, 25) : 'Start Chat' }}</small>
          </div>
          @if($user->unread_count > 0)
          <span class="badge bg-danger rounded-pill align-self-center">{{ $user->unread_count }}</span>
          @endif
        </a>
        @endforeach
      </div>
    </div>

    <!-- Chat Section -->
    <div class="col-md-8 d-flex flex-column">
      @if(isset($selectedUser))
      <!-- Header -->
      <div class="chat-header d-flex align-items-center">
        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px; height:40px;">
          {{ strtoupper(substr($selectedUser->first_name, 0, 1)) }}
        </div>
        <div class="ms-3">
          <div class="fw-bold">{{ $selectedUser->first_name }}</div>
          <small class="text-success">{{ $isTyping ? 'Typing...' : 'Online' }}</small>
        </div>
      </div>

      <!-- Chat Messages -->
      <div class="chat-box d-flex flex-column">
        @foreach($messages as $msg)
        <div class="d-flex {{ $msg->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
          <div class="chat-bubble {{ $msg->sender_id == auth()->id() ? 'outgoing' : 'incoming' }}">
            {!! nl2br(e($msg->message)) !!}
            <small>{{ $msg->created_at->format('h:i A') }}</small>
          </div>
        </div>
        @endforeach
      </div>

      <!-- Input -->
      <form action="{{ route('chat.send', $selectedUser->id) }}" method="POST" class="chat-input d-flex align-items-center">
        @csrf
        <input name="message" type="text" class="form-control me-2 rounded-pill" placeholder="Write a message..." required />
        <button class="btn btn-primary rounded-pill px-4">Send</button>
      </form>
      @else
      <div class="d-flex justify-content-center align-items-center h-100 text-secondary">
        Select a conversation to start chatting
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
