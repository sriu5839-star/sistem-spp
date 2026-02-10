<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')->get();

        // Attach last message
        foreach ($users as $user) {
            $lastMessage = Message::where(function($q) use ($user) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $user->id);
            })->orWhere(function($q) use ($user) {
                $q->where('sender_id', $user->id)->where('receiver_id', Auth::id());
            })->latest()->first();
            
            $user->last_message = $lastMessage;
        }

        // Sort by last message (most recent first)
        $users = $users->sortByDesc(function($user) {
            return $user->last_message ? $user->last_message->created_at : '0000-00-00';
        });

        return view('admin.chat.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        
        // Mark messages as read
        Message::where('sender_id', $id)
            ->where('receiver_id', Auth::id())
            ->update(['is_read' => true]);

        $messages = Message::where(function($query) use ($id) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $id);
            })
            ->orWhere(function($query) use ($id) {
                $query->where('sender_id', $id)
                      ->where('receiver_id', Auth::id());
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.chat.show', compact('user', 'messages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return back();
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        return back();
    }
}
