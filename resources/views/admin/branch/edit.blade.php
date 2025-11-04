@extends('admindashboardLayout')
@section('title','Edit Branch | The Cross Pharmacy')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="col-md-8 mx-auto card">
            <div class="card-header bg-light py-3">
                <h5>Edit Branch</h5>
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
                <form id="formAccountSettings" action="{{ route('branch.update', $branch->id)}}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="rolename" class="form-label">Role Name</label>
                        <input
                            class="form-control"
                            type="text"
                            id="rolename"
                            name="name"
                            placeholder="Admin"
                            value="{{ $branch->name }}"
                            autofocus
                            required />
                    </div>
                    <div class="form-group mb-3">
                        <label for="address" class="form-label">Location</label>
                        <input
                            type="text"
                            class="form-control"
                            id="address"
                            name="address"
                            value="{{ $branch->address }}"
                            placeholder="Location"
                            required
                        />
                    </div>
                    <div class="form-group mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="active" {{ $branch->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $branch->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="btn btn-primary me-3">Save Changes</button>
                        <a href="{{ route('branch.index')}}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection