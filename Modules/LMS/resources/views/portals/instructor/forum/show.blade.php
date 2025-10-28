<x-dashboard-layout>
    <x-slot:title>{{ $post->title }}</x-slot:title>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $post->title }}</h3>
                        <div class="card-tools">
                            <span class="badge badge-primary">{{ $post->user->name ?? 'Unknown User' }}</span>
                            <span class="text-muted">{{ $post->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>{{ $post->description }}</p>
                    </div>
                </div>

                {{-- Reply Form for Instructor --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ translate('Post a Reply') }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('instructor.forum.reply', $post->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="description">{{ translate('Your Reply') }}</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="5" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">{{ translate('Submit Reply') }}</button>
                        </form>
                    </div>
                </div>

                {{-- Display Replies --}}
                @if($post->replies->count() > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ translate('Replies') }} ({{ $post->replies->count() }})</h3>
                        </div>
                        <div class="card-body">
                            @foreach($post->replies as $reply)
                                <div class="media mb-3">
                                    <img src="{{ $reply->user->avatar ?? asset('assets/images/avatar.png') }}" class="mr-3 rounded-circle" alt="User Avatar" width="40">
                                    <div class="media-body">
                                        <h5 class="mt-0">{{ $reply->user->name ?? 'Unknown User' }}
                                            @if ($reply->user && $reply->user->userable_type === 'Modules\\LMS\\Models\\Auth\\Instructor')
                                                <span class="badge badge-info ml-2">{{ translate('Instructor') }}</span>
                                            @endif
                                            <small class="text-muted float-right">{{ $reply->created_at->diffForHumans() }}</small>
                                        </h5>
                                        <p>{{ $reply->description }}</p>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <hr>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body">
                            <p>{{ translate('No replies yet. Be the first to reply!') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dashboard-layout>
