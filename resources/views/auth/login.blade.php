@extends('layouts.app')

@section('title', 'Login - ERP System')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm mt-5">
                <div class="card-header text-center bg-primary text-white">
                    <h4><i class="fas fa-sign-in-alt me-2"></i>Login</h4>
                </div>
                <div class="card-body">
                    <!-- Session Expired Alert -->
                    @if(session('message'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                    </form>

                    <!-- Demo Credentials -->
                    <div class="mt-4 text-center">
                        <small class="text-muted">Demo Credentials:</small>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="fillCredentials('admin@erp.com', 'password')">Admin</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="fillCredentials('employee@erp.com', 'password')">Employee</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="fillCredentials('client@erp.com', 'password')">Client</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-refresh CSRF token periodically to prevent expiration
setInterval(function() {
    fetch('/csrf-token')
        .then(response => response.json())
        .then(data => {
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
            document.querySelector('input[name="_token"]').value = data.csrf_token;
        })
        .catch(console.error);
}, 300000); // Refresh every 5 minutes

// Fill demo credentials
function fillCredentials(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}

// Handle form submission errors
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
    submitBtn.disabled = true;
});
</script>
@endpush
@endsection
