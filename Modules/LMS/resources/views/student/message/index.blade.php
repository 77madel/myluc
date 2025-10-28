@if (Auth::user()->hasRole('Student'))
    @extends('portal::student.layouts.master')
@elseif (Auth::user()->hasRole('Instructor'))
    @extends('portal::instructor.layouts.master')
@endif

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h4>Vos conversations</h4>
                <div class="list-group">
                    @foreach ($conversations as $conversation)
                        @if (Auth::user()->hasRole('Student'))
                            <a href="{{ route('student.messages.show', $conversation->id) }}" class="list-group-item list-group-item-action">
                        @elseif (Auth::user()->hasRole('Instructor'))
                            <a href="{{ route('instructor.messages.show', $conversation->id) }}" class="list-group-item list-group-item-action">
                        @endif
                            @php
                                $otherUser = $conversation->user1_id == Auth::id() ? $conversation->user2 : $conversation->user1;
                            @endphp
                            {{ $otherUser->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-8">
                <h4>Démarrer une nouvelle conversation</h4>
                <div class="list-group">
                    @foreach ($users as $other_user)
                        @if (Auth::user()->hasRole('Student'))
                            <form action="{{ route('student.messages.start') }}" method="POST">
                        @elseif (Auth::user()->hasRole('Instructor'))
                            <form action="{{ route('instructor.messages.start') }}" method="POST">
                        @endif
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $other_user->id }}">
                            <button type="submit" class="list-group-item list-group-item-action">{{ $other_user->name }}</button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
