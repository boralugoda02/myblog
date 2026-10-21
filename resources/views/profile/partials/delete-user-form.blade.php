<p class="text-muted small mb-3">
    Once your account is deleted, all of its resources and data will be permanently deleted.
    Before deleting your account, please download any data or information that you wish to retain.
</p>

<button type="button" class="btn btn-outline-danger rounded-pill px-4"
        data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
    <i class="bi bi-trash me-1"></i> Delete Account
</button>

{{-- Delete Account Modal --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center px-4 pt-4 pb-2">

                <div style="width:72px;height:72px;background:#fee2e2;border-radius:50%;
                            display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="bi bi-trash3-fill text-danger" style="font-size:32px;"></i>
                </div>

                <h5 class="fw-bold mb-2">Are you sure?</h5>
                <p class="text-muted small mb-3">
                    Please enter your password to confirm you would like to permanently delete your account.
                </p>

                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <div class="mb-3 text-start">
                        <label class="form-label fw-semibold small">
                            <i class="bi bi-lock me-1"></i> Password
                        </label>
                        <input type="password" name="password"
                               class="form-control rounded-3 @error('password','userDeletion') is-invalid @enderror"
                               placeholder="Enter your password">
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2 justify-content-center pb-2">
                        <button type="button"
                                class="btn btn-outline-secondary rounded-pill px-4"
                                data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">
                            <i class="bi bi-trash me-1"></i> Yes, Delete My Account
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>