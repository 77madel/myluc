@if (Auth::user()->hasRole('Student'))
    @extends('portal::student.layouts.master')
@elseif (Auth::user()->hasRole('Instructor'))
    @extends('portal::instructor.layouts.master')
@endif

@section('content')
    <div class="container">
        @php
            $otherUser = $conversation->user1_id == Auth::id() ? $conversation->user2 : $conversation->user1;
        @endphp
        <h1>Chat with {{ $otherUser->name }}</h1>

        <div class="card">
            <div class="card-body">
                @foreach ($conversation->messages as $message)
                    <div class="message {{ $message->sender_id == Auth::id() ? 'sent' : 'received' }}">
                        <p><strong>{{ $message->sender->name }}:</strong> {{ $message->content }}</p>
                        <small>{{ $message->created_at->diffForHumans() }}</small>
                    </div>
                @endforeach
            </div>
        </div>

        @if (Auth::user()->hasRole('Student'))
            <form action="{{ route('student.messages.store') }}" method="POST" class="mt-3">
        @elseif (Auth::user()->hasRole('Instructor'))
            <form action="{{ route('instructor.messages.store', $conversation->id) }}" method="POST" class="mt-3">
        @endif
            @csrf
            <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">
            <div class="form-group">
                <textarea name="content" class="form-control" rows="3" placeholder="Type your message..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send</button>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .message {
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 5px;
    }
    .sent {
        background-color: #dcf8c6;
        text-align: right;
    }
    .received {
        background-color: #f1f0f0;
    }
</style>
@endpush
