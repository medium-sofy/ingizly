@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Your Notifications</h4>
                <a href="#" class="btn btn-sm btn-outline-primary">Mark All as Read</a>
            </div>
        </div>
        <div class="card-body">
            @forelse($notifications as $notification)
            <div class="border-bottom pb-3 mb-3 {{ $notification->is_read ? '' : 'bg-light' }}">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5>{{ $notification->title }}</h5>
                        <p class="mb-1">{{ $notification->content }}</p>
                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    <div>
                        @if(!$notification->is_read)
                        <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Mark as Read</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="far fa-bell fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No notifications yet</h5>
            </div>
            @endforelse
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection