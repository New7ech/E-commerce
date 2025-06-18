<section>
    {{-- Header removed as it's now in the parent card --}}

    {{-- The main form for updating profile information --}}
    <form method="post" action="{{ route('profile.update') }}" class="mt-3" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="form-group">
            <label for="name">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-3">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            {{-- Start: Conditional Email Verification Section --}}
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail)
                {{-- Hidden form for re-sending verification email --}}
                {{-- This form is only defined if the user model uses email verification --}}
                <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-none">
                    @csrf
                </form>

                @if (! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-sm text-muted">
                            {{ __('Your email address is unverified.') }}
                            {{-- This button triggers the hidden form above --}}
                            <button form="send-verification" class="btn btn-link p-0 text-sm text-primary">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 text-sm text-success">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            @endif
            {{-- End: Conditional Email Verification Section --}}
        </div>

        <div class="form-group mt-3">
            <label for="photo">{{ __('Profile Photo') }}</label>
            <input id="photo" name="photo" type="file" class="form-control-file @error('photo') is-invalid @enderror">
            @error('photo')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        @if ($user->photo)
            <div class="mt-2">
                <p>{{ __('Current Photo:') }}</p>
                <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ __('Current Profile Photo') }}" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
            </div>
        @endif

        <div class="d-flex align-items-center gap-4 mt-4">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <div class="alert alert-success py-2 px-3 mb-0" role="alert">
                   {{ __('Saved.') }}
                </div>
            @endif
        </div>
    </form>
</section>
