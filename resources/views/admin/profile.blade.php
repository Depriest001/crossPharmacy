@extends('admindashboardLayout')
@section('title','Profile | The Cross Pharmacy')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        
        @if (session('success') || session('error') || $errors->any())
        <div id="appToast"
            class="bs-toast toast fade show position-fixed bottom-0 end-0 m-3
            {{ session('success') ? 'bg-success' : (session('error') ? 'bg-danger' : 'bg-warning') }}"
            role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="toast-header text-white">
                <i class="icon-base bx bx-bell me-2"></i>
                <div class="me-auto fw-medium">
                @if (session('success'))
                    Success
                @elseif (session('error'))
                    Error
                @else
                    Validation
                @endif
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>

            <div class="toast-body text-white">
                @if (session('success'))
                {{ session('success') }}
                @elseif (session('error'))
                {{ session('error') }}
                @elseif ($errors->any())
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
        @endif

        <div class="card mb-6">
        <!-- Account -->
            <div class="card-header h5 py-2 bg-light">
                Profile Setting
            </div>
            <div class="card-body pt-4">
                <form id="formAccountSettings" 
                    method="POST" 
                    action="{{ route('staff.profile.update') }}" 
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-4">
                        <!-- Full Name -->
                        <div class="col-md-6">
                            <label for="fullName" class="form-label fw-semibold">Full Name</label>
                            <input 
                                class="form-control" 
                                type="text" 
                                id="fullName" 
                                name="full_name" 
                                value="{{ old('full_name', auth('staff')->user()->full_name) }}" 
                                required
                            />
                        </div>

                        <!-- Email (readonly) -->
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input 
                                class="form-control" 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ auth('staff')->user()->email }}" 
                                readonly
                            />
                        </div>

                        <!-- Phone Number -->
                        <div class="col-md-6">
                            <label for="phoneNumber" class="form-label fw-semibold">Phone Number</label>
                            <input 
                                class="form-control" 
                                type="text" 
                                id="phoneNumber" 
                                name="phone_number" 
                                value="{{ old('phone_number', auth('staff')->user()->phone_number) }}" 
                                placeholder="+234 801 2345 678" 
                                required
                            />
                        </div>

                        <!-- Address -->
                        <div class="col-md-6">
                            <label for="address" class="form-label fw-semibold">Address</label>
                            <input 
                                class="form-control" 
                                type="text" 
                                id="address" 
                                name="address" 
                                value="{{ old('address', auth('staff')->user()->address) }}" 
                                placeholder="Lagos, Nigeria" 
                                required
                            />
                        </div>

                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                    </div>
                </form>
            </div>
        <!-- /Account -->
        </div>
        <div class="card">
            <h5 class="card-header py-2 bg-light">Change Password</h5>
            <div class="card-body">
                <form id="formAccountDeactivation" 
                    method="POST" 
                    action="{{ route('staff.password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row pt-3 g-4">
                        <!-- Old Password -->
                        <div class="col-md-4">
                            <label for="Oldpassword" class="form-label fw-semibold">Old Password</label>
                            <input
                                class="form-control"
                                type="password"
                                id="Oldpassword"
                                name="old_password"
                                placeholder="••••••••••••"
                                required />
                        </div>

                        <!-- New Password -->
                        <div class="col-md-4">
                            <label for="Newpassword" class="form-label fw-semibold">New Password</label>
                            <input
                                class="form-control"
                                type="password"
                                id="Newpassword"
                                name="new_password"
                                placeholder="••••••••••••"
                                required />
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-4">
                            <label for="Confirmpassword" class="form-label fw-semibold">Confirm Password</label>
                            <input
                                class="form-control"
                                type="password"
                                id="Confirmpassword"
                                name="new_password_confirmation"
                                placeholder="••••••••••••"
                                required />
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                🔐 Change Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>        
    </div>
@endsection