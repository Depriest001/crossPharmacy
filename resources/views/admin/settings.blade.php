@extends('admindashboardLayout')
@section('title','System Setting | The Cross Pharmacy')
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
        <div class="card-header h5 py-2 bg-light">
            System Settings
        </div>
        <div class="card-body pt-4">
            <form id="formSystemSettings" 
                  method="POST" 
                  action="{{ route('system.settings.update') }}" 
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">

                    <!-- System Name -->
                    <div class="col-md-6">
                        <label for="systemName" class="form-label fw-semibold">System Name</label>
                        <input 
                            class="form-control" 
                            type="text" 
                            id="systemName" 
                            name="system_name" 
                            value="{{ old('system_name', $systemInfo->system_name ?? '') }}" 
                            required
                        />
                    </div>

                    <!-- Email -->
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input 
                            class="form-control" 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email', $systemInfo->email ?? '') }}" 
                        />
                    </div>

                    <!-- Phone -->
                    <div class="col-md-6">
                        <label for="phoneNumber" class="form-label fw-semibold">Phone Number</label>
                        <input 
                            class="form-control" 
                            type="text" 
                            id="phoneNumber" 
                            name="phone" 
                            value="{{ old('phone', $systemInfo->phone ?? '') }}" 
                            placeholder="+234 801 2345 678"
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
                            value="{{ old('address', $systemInfo->address ?? '') }}" 
                            placeholder="Lagos, Nigeria"
                        />
                    </div>

                    <!-- Currency -->
                    <div class="col-md-6">
                        <label for="currency" class="form-label fw-semibold">Currency</label>
                        <input 
                            class="form-control" 
                            type="text" 
                            id="currency" 
                            name="currency" 
                            value="{{ old('currency', $systemInfo->currency ?? '₦') }}" 
                            placeholder="₦"
                        />
                    </div>
                    
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-header h5 py-2 bg-light">
            Logo Settings
        </div>
        <div class="card-body pt-4">
            <form id="formSystemSettings" 
                  method="POST" 
                  action="" 
                  enctype="multipart/form-data" onsubmit="return false">
                @csrf
                @method('PUT')

                <!-- Logo -->
                <div class="">
                    <label for="logo" class="form-label fw-semibold">Logo</label>
                    <input 
                        class="form-control" 
                        type="file" 
                        id="logo" 
                        name="logo" 
                        accept="image/*"
                    />
                    @if(!empty($systemInfo->logo))
                        <small>Current: <img src="{{ asset('storage/'.$systemInfo->logo) }}" alt="Logo" height="40"></small>
                    @endif
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-header h5 py-2 bg-light">
            Favicon Settings
        </div>
        <div class="card-body pt-4">
            <form id="formSystemSettings" 
                  method="POST" 
                  action="" 
                  enctype="multipart/form-data" onsubmit="return false">
                @csrf
                @method('PUT')
                
                <!-- Favicon -->
                <div class="">
                    <label for="favicon" class="form-label fw-semibold">Favicon</label>
                    <input 
                        class="form-control" 
                        type="file" 
                        id="favicon" 
                        name="favicon" 
                        accept="image/*"
                    />
                    @if(!empty($systemInfo->favicon))
                        <small>Current: <img src="{{ asset('storage/'.$systemInfo->favicon) }}" alt="Favicon" height="20"></small>
                    @endif
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary me-2">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection