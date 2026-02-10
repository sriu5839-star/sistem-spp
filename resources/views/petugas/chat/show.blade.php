@extends('layouts.petugas')

@section('title', 'Chat with ' . $user->username)

@section('content')
<div class="flex flex-col h-[calc(100vh-8rem)]">
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-gray-900">Chat dengan {{ $user->username }}</h1>
    </div>

    <div class="flex-1 flex flex-col bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex-1 p-4 overflow-y-auto" id="chat-container">
            @foreach($messages as $message)
                <div class="flex mb-4 {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[70%] rounded-lg px-4 py-2 {{ $message->sender_id === Auth::id() ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800' }}">
                        <p>{{ $message->message }}</p>
                        <p class="text-xs mt-1 {{ $message->sender_id === Auth::id() ? 'text-blue-200' : 'text-gray-500' }}">
                            {{ $message->created_at->format('H:i') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-4 bg-gray-50 border-t border-gray-200">
            <form action="{{ route('petugas.chat.store') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                <input type="text" name="message" class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tulis pesan..." required>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-paper-plane"></i> Kirim
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const chatContainer = document.getElementById('chat-container');
    chatContainer.scrollTop = chatContainer.scrollHeight;
</script>
@endsection
