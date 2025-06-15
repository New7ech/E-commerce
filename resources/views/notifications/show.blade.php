@extends('layouts.app') {{-- Changed from layouts/app to layouts.app --}}

@section('title', 'Détail Notification') {{-- Added title section --}}

@section('content') {{-- Changed from contenus to content --}}
<div class="page-header">
    <h3 class="fw-bold mb-3">Détail de la Notification</h3>
    <ul class="breadcrumbs">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ route('notifications.index') }}">Notifications</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">Détail</li>
    </ul>
</div>

<div class="container"> {{-- Kept container from original show --}}
    <h1>Détails de la Notification</h1>

    @if ($notification)
        <div class="card">
            <div class="card-header">
                Notification ID: {{ $notification->id }}
            </div>
            <div class="card-body">
                <p><strong>Message:</strong> {{ $notification->data['message'] ?? 'N/A' }}</p>
                <p><strong>Reçue le:</strong> {{ $notification->created_at->format('d/m/Y H:i:s') }}</p>
                <p><strong>Statut:</strong> 
                    @if($notification->read_at)
                        Lue le {{ $notification->read_at->format('d/m/Y H:i:s') }}
                    @else
                        Non lue
                    @endif
                </p>

                @if(!$notification->read_at)
                    <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-success">Marquer comme lue</button>
                    </form>
                @endif

                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-primary">Retour à toutes les notifications</a>
            </div>
        </div>
    @else
        <p class="text-center text-muted">Notification non trouvée ou accès non autorisé.</p>
    @endif
</div>
@endsection