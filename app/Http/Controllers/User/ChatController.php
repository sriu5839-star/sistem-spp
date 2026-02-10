<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        // Get all admins (exclude petugas as requested)
        $admins = User::where('role', 'admin')->get();

        // Attach last message
        foreach ($admins as $admin) {
            $lastMessage = Message::where(function($q) use ($admin) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $admin->id);
            })->orWhere(function($q) use ($admin) {
                $q->where('sender_id', $admin->id)->where('receiver_id', Auth::id());
            })->latest()->first();
            
            $admin->last_message = $lastMessage;
        }

        // Sort by last message (most recent first)
        $admins = $admins->sortByDesc(function($admin) {
            return $admin->last_message ? $admin->last_message->created_at : '0000-00-00';
        });

        return view('user.chat.index', compact('admins'));
    }

    public function show($id)
    {
        $admin = User::findOrFail($id);
        
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

        return view('user.chat.show', compact('admin', 'messages'));
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

        if ($message->sender_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus pesan ini.');
        }

        $message->delete();

        return back()->with('success', 'Pesan berhasil dihapus.');
    }
}
