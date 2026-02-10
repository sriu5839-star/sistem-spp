@extends('layouts.admin')

@section('title', 'Chat Users')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Chat Users</h1>
        <p class="text-gray-500">Chat dengan siswa/user.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($users as $user)
                <a href="{{ route('admin.chat.show', $user->id) }}" class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                                {{ substr($user->username, 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-baseline">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $user->username }}
                                </p>
                                @if($user->last_message)
                                <span class="text-xs text-gray-400">
                                    {{ $user->last_message->created_at->format('d M') }}
                                </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 truncate">
                                @if($user->last_message)
                                    <span class="{{ $user->last_message->sender_id === Auth::id() ? 'text-gray-400' : 'text-gray-600 font-medium' }}">
                                        {{ $user->last_message->sender_id === Auth::id() ? 'Anda: ' : '' }}{{ Str::limit($user->last_message->message, 20) }}
                                    </span>
                                @else
                                    {{ $user->email }}
                                @endif
                            </p>
                        </div>
                        <div>
                            @if($user->sentMessages()->where('receiver_id', Auth::id())->where('is_read', false)->exists())
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
    </div>
</main>
@endsection
