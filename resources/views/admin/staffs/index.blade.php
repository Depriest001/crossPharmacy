@extends('admindashboardLayout')
@section('title','Manage Staffs | The Cross Pharmacy')
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

        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between">
                <h5>Staffs Table</h5>
                <div class="dt-buttons btn-group flex-wrap mb-0">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBackdrop" aria-controls="offcanvasBackdrop">
                            <i class="icon-base bx bx-plus icon-md"></i>
                            <span class="d-none d-sm-inline-block">Add</span>
                        </button>
                        <div class="btn-group" id="dropdown-icon-demo">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="icon-base bx bx-export me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Export</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('export.data', ['table' => 'staff', 'type' => 'print']) }}" target="_blank"
                                    class="dropdown-item d-flex align-items-center">
                                        <i class="icon-base bx bx-printer me-1"></i>Print
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('export.data', ['table' => 'staff', 'type' => 'csv']) }}"
                                    class="dropdown-item d-flex align-items-center">
                                        <i class="icon-base bx bx-file me-1"></i>CSV
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('export.data', ['table' => 'staff', 'type' => 'excel']) }}"
                                    class="dropdown-item d-flex align-items-center">
                                        <i class="icon-base bx bxs-file-export me-1"></i>Excel
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('export.data', ['table' => 'staff', 'type' => 'pdf']) }}"
                                    class="dropdown-item d-flex align-items-center">
                                        <i class="icon-base bx bxs-file-pdf me-1"></i>PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-sm table-striped table-hover" id="exampleTable">
                    <thead>
                        <tr>
                        <th>S/N</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Branch</th>
                        <th>Status</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($staffs as $index => $staff)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $staff->full_name }}</td>
                            <td>{{ $staff->email }}</td>
                            <td>{{ $staff->role->name ?? 'N/A' }}</td>
                            <td>{{ \Illuminate\Support\Str::words($staff->branch?->name ?? '—', 5, '...') }}</td>

                            <td>
                                @if ($staff->status === 'active')
                                    <span class="badge bg-label-success">Active</span>
                                @else
                                    <span class="badge bg-label-danger">Inactive</span>
                                @endif
                            </td>

                            <td>
                            <a href="{{ route('staffs.edit', $staff->id) }}" class="btn p-2 btn-primary me-1">
                                <i class="bx bx-edit-alt"></i>
                            </a>
                            <a href="{{ route('staffs.show', $staff->id) }}" class="btn p-2 btn-info me-1">
                                <i class="bx bx-show-alt"></i>
                            </a>
                            <form action="{{ route('staffs.destroy', $staff->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure?')" class="btn p-2 btn-danger">
                                <i class="bx bx-trash"></i>
                                </button>
                            </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No staff members found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Add Staff Modal -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBackdrop" aria-labelledby="offcanvasBackdropLabel">
        <div class="offcanvas-header">
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body my-auto mx-0 flex-grow-0">
            <h5 id="offcanvasBackdropLabel" class="offcanvas-title mb-5">Add New Staff</h5>
            <form id="formAccountSettings" method="POST" action="{{ route('staffs.store') }}">
                @csrf
                <div class="mt-3">
                    <label for="fullName" class="form-label">Full Name</label>
                    <input class="form-control" type="text" placeholder="John Doe" id="fullName" name="full_name" required />
                </div>

                <div class="mt-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input class="form-control" type="email" placeholder="johndoe@example.com" id="email" name="email" disabled />
                </div>

                <div class="mt-3">
                    <label for="phoneNumber" class="form-label">Phone Number</label>
                    <input type="text" id="phoneNumber" placeholder="+234 801 2345 678" name="phone_number" class="form-control" required />
                </div>

                <div class="mt-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" placeholder="Lagos, Nigeria" id="address" name="address" required />
                </div>

                <div class="mt-3">
                    <label for="role" class="form-label">Role</label>
                    <select id="role" name="role_id" class="form-select" required>
                        <option value="" selected disabled>Select Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="branch" class="form-label">Branch</label>
                    <select id="branch" name="branch_id" class="form-select" required>
                        <option value="" selected disabled>Select Branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mt-3">
                    <small class="text-warning">The default password is <strong>123456</strong></small>
                    <input type="hidden" class="form-control" value="123456" name="password" required />
                </div>
                <hr>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection