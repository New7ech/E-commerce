@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Notifications</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Notifications</li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        @include('layouts.flash')
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Toutes les Notifications</h4>
                    @if(Auth::user() && Auth::user()->unreadNotifications->isNotEmpty())
                    <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="ms-auto mb-0">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            Marquer toutes comme lues
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if($notifications->isEmpty())
                    <p class="text-center text-muted">Vous n'avez aucune notification.</p>
                @else
                    <ul class="list-group">
                        @foreach($notifications as $notification)
                            <li class="list-group-item @if(!$notification->read_at) list-group-item-info @endif">
                                <a href="{{ route('notifications.show', $notification->id) }}">
                                    {{ $notification->data['title'] ?? ($notification->data['message'] ?? 'Notification') }}
                                </a>
                                <small class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                                @if(!$notification->read_at)
                                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline mt-1">
                                        @csrf
                                        <button type="submit" class="btn btn-link btn-sm p-0">Marquer comme lue</button>
                                    </form>
                                @else
                                    <span class="badge bg-success mt-1">Lue</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    @if($notifications->hasPages())
                        <div class="mt-3">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection