@extends('admindashboardLayout')
@section('title','Manage Roles | The Cross Pharmacy')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between">
                <h5>Roles Table</h5>
                <button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBackdrop" aria-controls="offcanvasBackdrop">
                    <i class="icon-base bx bx-plus icon-md"></i>
                    <span class="d-none d-sm-inline-block">Add</span>
                </button>
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

            <div class="table-responsive text-nowrap">
                <table class="table table-sm table-striped table-hover" id="exampleTable">
                    <thead>
                        <tr>
                        <th>s/n</th>
                        <th>Role Name</th>
                        <th>Role Type</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->role_type }}</td>
                                <td>{{ \Illuminate\Support\Str::words($role->description, 6, '...') }}</td>
                                <td>{{ $role->created_at ? $role->created_at->format('d-m-Y') : '—' }}</td>
                                <td>
                                    <a class="btn btn-sm p-1 btn-primary me-1" href="{{ route('role.edit', $role->id) }}">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>

                                    <form action="{{ route('role.destroy', $role->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this role?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm p-1 btn-danger">
                                            <i class="icon-base bx bx-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Add Role Modal -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBackdrop" aria-labelledby="offcanvasBackdropLabel">
        <div class="offcanvas-header">
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body my-auto mx-0 flex-grow-0">
            <h5 id="offcanvasBackdropLabel" class="offcanvas-title mb-5">Create New Role</h5>
            <form id="formAccountSettings" action="{{ route('role.store')}}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="rolename" class="form-label">Role Name</label>
                    <input
                        class="form-control"
                        type="text"
                        id="rolename"
                        name="name"
                        placeholder="Admin"
                        autofocus
                        required />
                </div>
                <div class="form-group">
                    <label for="roleType" class="form-label">Role Type</label>
                    <select id="roleType" name="role_type" class="form-select" required>
                        <option value="" selected disabled>Select Role Type</option>
                        <option value="Admin">Admin</option>
                        <option value="Staff">Staff</option>
                        <option value="Cashier">Cashier</option>
                        <option value="Seller">Seller</option>
                    </select>
                </div>
                <div class="form-group mt-2">
                    <label for="roledescription" class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="roledescription" placeholder="System administrator" rows="3" required></textarea>
                </div>
                <div class="mt-6">
                    <button type="submit" class="btn btn-primary me-3">Save</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection