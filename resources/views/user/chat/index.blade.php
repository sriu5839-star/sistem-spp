@extends('layouts.user')

@section('title', 'Chat Admin')
@section('header', 'Chat dengan Admin')
 
 @section('content')
 <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
     <h3 class="text-xl font-bold text-gray-900 mb-4">Pilih Admin untuk Chat</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($admins as $admin)
        <a href="{{ route('user.chat.show', $admin->id) }}" class="block p-4 border rounded-lg hover:bg-gray-50 transition">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xl">
                        {{ substr($admin->username, 0, 1) }}
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-baseline">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ $admin->username }}
                        </p>
                        @if($admin->last_message)
                        <span class="text-xs text-gray-400">
                            {{ $admin->last_message->created_at->format('d M') }}
                        </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 truncate">
                        @if($admin->last_message)
                            <span class="{{ $admin->last_message->sender_id === Auth::id() ? 'text-gray-400' : 'text-gray-600 font-medium' }}">
                                {{ $admin->last_message->sender_id === Auth::id() ? 'Anda: ' : '' }}{{ Str::limit($admin->last_message->message, 20) }}
                            </span>
                        @else
                            {{ ucfirst($admin->role) }}
                        @endif
                    </p>
                </div>
                <div>
                    @if($admin->sentMessages()->where('receiver_id', Auth::id())->where('is_read', false)->exists())
                        <span class="bg-red-500 w-3 h-3 rounded-full block" title="Pesan Baru"></span>
                    @else
                        <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection
