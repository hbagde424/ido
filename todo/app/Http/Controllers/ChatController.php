<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Conversation;
use App\Message;
use App\User;
use Illuminate\Support\Facades\Auth;


 

class ChatController extends Controller
{
        public function index()
{
    $users = User::where('id', '!=', Auth::id())->get();

    return view('chat.chat', [
        'users' => $users,
        'messages' => [],
        'selectedUser' => null,
    ]);
}

   public function show($userId)
    {
        $userId = (int) $userId;
    
        $selectedUser = User::findOrFail($userId);
    
        $conversation = Conversation::where(function ($query) use ($userId) {
            $query->where('user_one_id', auth()->id())->where('user_two_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('user_two_id', auth()->id())->where('user_one_id', $userId);
        })->first();
    
        if (!$conversation) {
            return redirect()->route('chat.index')->with('error', 'No conversation found');
        }
    
        $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();
        $users = User::where('id', '!=', auth()->id())
                    ->with(['lastMessage' => function($q) {
                        $q->latest();
                    }])
                    ->get();
    
        return view('chat.chat', compact('users', 'messages', 'selectedUser'));
    }

    public function sendMessage(Request $request, $userId)
{
    $request->validate([
        'message' => 'required|string',
    ]);

    $authId = Auth::id();
    $receiverId = (int) $userId;

    $conversation = Conversation::firstOrCreate(
        [
            ['user_one_id', '=', min($authId, $receiverId)],
            ['user_two_id', '=', max($authId, $receiverId)],
        ],
        [
            'user_one_id' => min($authId, $receiverId),
            'user_two_id' => max($authId, $receiverId),
        ]
    );

    Message::create([
        'conversation_id' => $conversation->id,
        'sender_id' => $authId,
        'message' => $request->message,
    ]);

    return redirect()->route('chat.show', $receiverId);
}

public function chatWithUser($userId)
{
    $userId = (int) $userId;
    $authId = Auth::id();

    $selectedUser = User::findOrFail($userId);

    $conversation = Conversation::firstOrCreate(
        [
            ['user_one_id', '=', min($authId, $userId)],
            ['user_two_id', '=', max($authId, $userId)],
        ],
        [
            'user_one_id' => min($authId, $userId),
            'user_two_id' => max($authId, $userId),
        ]
    );

    $messages = $conversation->messages()->with('sender')->orderBy('created_at')->get();

    $users = User::where('id', '!=', $authId)->get();

    return view('chat.chat', [
        'users' => $users,
        'messages' => $messages,
        'selectedUser' => $selectedUser,
    ]);
}

    public function startConversation(Request $request)
    {
        $receiverId = $request->input('user_id');
        $userId = Auth::id();

        $conversation = Conversation::firstOrCreate(
            [
                ['user_one_id', '=', min($userId, $receiverId)],
                ['user_two_id', '=', max($userId, $receiverId)],
            ],
            [
                'user_one_id' => min($userId, $receiverId),
                'user_two_id' => max($userId, $receiverId),
            ]
        );

        return redirect()->route('chat.show', $conversation->id);
    }
}
