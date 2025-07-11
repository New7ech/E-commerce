<section class="space-y-6">
    {{-- Header removed --}}

    <!-- Button to trigger modal -->
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        {{ __('Delete Account') }}
    </button>

    <!-- Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}" class="p-0"> {{-- Removed p-6 from form, will be on modal-body/footer --}}
                    @csrf
                    @method('delete')

                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="confirmUserDeletionModalLabel">{{ __('Are you sure you want to delete your account?') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-sm text-gray-600"> {{-- text-gray-600 can be updated to Bootstrap muted or similar if needed --}}
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <div class="mt-3">
                            <label for="password_delete_account" class="form-label visually-hidden">{{ __('Password') }}</label> {{-- sr-only equivalent --}}
                            <input
                                id="password_delete_account"
                                name="password"
                                type="password"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                placeholder="{{ __('Password') }}"
                                required
                            />
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger ms-2">{{ __('Delete Account') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
