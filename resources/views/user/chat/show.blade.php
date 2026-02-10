@extends('layouts.user')

@section('title', 'Chat with ' . $admin->username)
@section('header', 'Chat dengan ' . $admin->username)

@section('content')
<div class="flex flex-col h-[calc(100vh-12rem)]">
    <div class="flex-1 bg-white p-4 rounded-t-xl shadow-sm border border-gray-200 overflow-y-auto" id="chat-container">
        @foreach($messages as $message)
            <div class="flex mb-4 {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                <div class="relative max-w-[70%] rounded-lg px-4 py-2 {{ $message->sender_id === Auth::id() ? 'bg-indigo-600 text-white cursor-pointer hover:opacity-90 transition' : 'bg-gray-100 text-gray-800' }}"
                     @if($message->sender_id === Auth::id()) onclick="toggleDelete({{ $message->id }})" @endif>
                    <p>{{ $message->message }}</p>
                    <p class="text-xs mt-1 {{ $message->sender_id === Auth::id() ? 'text-indigo-200' : 'text-gray-500' }}">
                        {{ $message->created_at->format('H:i') }}
                    </p>
                    
                    @if($message->sender_id === Auth::id())
                        <div id="delete-btn-{{ $message->id }}" class="hidden absolute top-1/2 -translate-y-1/2 right-full mr-2 z-10">
                            <form action="{{ route('user.chat.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1.5 rounded-full hover:bg-red-600 transition shadow-sm text-xs font-bold flex items-center gap-1 whitespace-nowrap">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-white p-4 rounded-b-xl shadow-sm border border-t-0 border-gray-200">
        <form action="{{ route('user.chat.store') }}" method="POST" class="flex gap-2">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $admin->id }}">
            <input type="text" name="message" class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Tulis pesan..." required>
            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<script>
    // Scroll to bottom
    const chatContainer = document.getElementById('chat-container');
    chatContainer.scrollTop = chatContainer.scrollHeight;

    function toggleDelete(id) {
        // Hide all other delete buttons first
        document.querySelectorAll('[id^="delete-btn-"]').forEach(el => {
            if (el.id !== 'delete-btn-' + id) {
                el.classList.add('hidden');
            }
        });
        
        // Toggle the clicked one
        const btn = document.getElementById('delete-btn-' + id);
        if (btn) {
            btn.classList.toggle('hidden');
        }
    }

    // Hide delete button when clicking outside
    document.addEventListener('click', function(event) {
        const isMessage = event.target.closest('[onclick^="toggleDelete"]');
        const isDeleteBtn = event.target.closest('[id^="delete-btn-"]');
        
        if (!isMessage && !isDeleteBtn) {
            document.querySelectorAll('[id^="delete-btn-"]').forEach(el => {
                el.classList.add('hidden');
            });
        }
    });
</script>
@endsection
