@extends('admindashboardLayout')
@section('title','Edit Role | The Cross Pharmacy')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-md-8 mx-auto card">
            <div class="card-header bg-light py-3">
                <h5>Edit Role</h5>
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
                <form id="formAccountSettings" action="{{ route('role.update', $role->id)}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mt-3">
                        <label for="rolename" class="form-label">Role Name</label>
                        <input
                            class="form-control"
                            type="text"
                            id="rolename"
                            name="name"
                            placeholder="Admin"
                            value="{{ $role->name }}"
                            autofocus
                            required />
                    </div>
                    <div class="mt-3">
                        <label for="roleType" class="form-label">Role Type</label>
                        <select id="roleType" name="role_type" class="form-select" required>
                            <option value="" selected disabled>Select Role Type</option>
                            <option value="Admin" {{ $role->role_type === 'Admin' ? 'selected' : ''}} >Admin</option>
                            <option value="Staff" {{ $role->role_type === 'Staff' ? 'selected' : ''}} >Staff</option>
                            <option value="Cashier" {{ $role->role_type === 'Cashier' ? 'selected' : ''}} >Cashier</option>
                        </select>
                    </div>
                    <div class="mt-3">
                        <label for="roledescription" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="roledescription" placeholder="System administrator" rows="3" required>{{ $role->description }}</textarea>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="btn btn-primary me-3">Save Changes</button>
                        <a href="{{ route('role.index')}}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection