@extends('admindashboardLayout')
@section('title','Edit Staff | The Cross Pharmacy')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-md-8 mx-auto card">
            <div class="card-header bg-light py-3">
                <h5>Edit Staff</h5>
            </div>

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
            
            <div class="card-body">
                <form id="formAccountSettings" action="{{ route('staffs.update', $staff->id)}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mt-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input class="form-control" type="text" id="fullName" value="{{ $staff->full_name }}" name="full_name" required />
                    </div>

                    <div class="mt-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input class="form-control" type="email" id="email" value="{{ $staff->email }}" name="email" required />
                    </div>

                    <div class="mt-3">
                        <label for="phoneNumber" class="form-label">Phone Number</label>
                        <input type="text" id="phoneNumber" name="phone_number" value="{{ $staff->phone_number }}" class="form-control" required />
                    </div>

                    <div class="mt-3">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" value="{{ $staff->address }}" name="address" required />
                    </div>

                    <div class="mt-3">
                        <label for="role" class="form-label">Role</label>
                        <select id="role" name="role_id" class="form-select" required>
                            <option value="" selected disabled>Select Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ $staff->role_id === $role->id ? 'selected' : '' }} >{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-3">
                        <label for="branch" class="form-label">Branch</label>
                        <select id="branch" name="branch_id" class="form-select" required>
                            <option value="" selected disabled>Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $staff->branch_id === $branch->id ? 'selected' : '' }} >{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="active" {{ $staff->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $staff->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="btn btn-primary me-3">Save</button>
                        <a href="{{ route('staffs.index')}}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection