@extends('layouts.admin')

@section('title', 'Chat with ' . $user->username)

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-6 flex flex-col h-screen">
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-gray-900">Chat dengan {{ $user->username }}</h1>
    </div>

    <div class="flex-1 flex flex-col bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="flex-1 p-4 overflow-y-auto" id="chat-container">
            @foreach($messages as $message)
                <div class="flex mb-4 {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                    <div class="relative max-w-[70%] rounded-lg px-4 py-2 {{ $message->sender_id === Auth::id() ? 'bg-blue-600 text-white cursor-pointer hover:opacity-90 transition' : 'bg-gray-100 text-gray-800 cursor-pointer hover:bg-gray-200 transition' }}"
                         onclick="toggleDelete({{ $message->id }})">
                        <p>{{ $message->message }}</p>
                        <p class="text-xs mt-1 {{ $message->sender_id === Auth::id() ? 'text-blue-200' : 'text-gray-500' }}">
                            {{ $message->created_at->format('H:i') }}
                        </p>

                        <div id="delete-btn-{{ $message->id }}" class="hidden absolute top-1/2 -translate-y-1/2 {{ $message->sender_id === Auth::id() ? 'right-full mr-2' : 'left-full ml-2' }} z-10">
                            <form action="{{ route('admin.chat.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1.5 rounded-full hover:bg-red-600 transition shadow-sm text-xs font-bold flex items-center gap-1 whitespace-nowrap">
                                    <i class="fas fa-trash-alt"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="p-4 bg-gray-50 border-t border-gray-200">
            <form action="{{ route('admin.chat.store') }}" method="POST" class="flex gap-2">
                @csrf
                <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                <input type="text" name="message" class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tulis pesan..." required>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-paper-plane"></i> Kirim
                </button>
            </form>
        </div>
    </div>
</main>

<script>
    const chatContainer = document.getElementById('chat-container');
    chatContainer.scrollTop = chatContainer.scrollHeight;

    function toggleDelete(id) {
        // Hide all other delete buttons
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
