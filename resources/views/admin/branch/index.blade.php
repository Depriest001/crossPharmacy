@extends('admindashboardLayout')
@section('title','Manage Branches | The Cross Pharmacy')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between">
                <h5>Branch Table</h5>
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
                        <th>S/N</th>
                        <th>Branch</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($branches as $branch)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $branch->name }}</td>
                                <td>{{ $branch->address }}</td>
                                <td>
                                    @if ($branch->status === 'active')
                                        <span class="badge bg-label-success">Active</span>
                                    @else
                                        <span class="badge bg-label-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Edit Button -->
                                    <a href="{{ route('branch.edit', $branch->id) }}" class="btn btn-sm p-1 btn-primary me-1">
                                        <i class="icon-base bx bx-edit-alt"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <form action="{{ route('branch.destroy', $branch->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this branch?');">
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
    <!-- Add Branch  Modal -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBackdrop" aria-labelledby="offcanvasBackdropLabel">
        <div class="offcanvas-header">
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body my-auto mx-0 flex-grow-0">
            <h5 id="offcanvasBackdropLabel" class="offcanvas-title mb-5">Add Branch</h5>
            <form id="formAccountSettings" action="{{ route('branch.store') }}" method="POST">
                @csrf
                <div class="row g-6">
                    <div class="form-group">
                        <label for="branchName" class="form-label">Branch Name</label>
                        <input
                            class="form-control"
                            type="text"
                            id="branchName"
                            name="name"
                            placeholder="New York Branch"
                            autofocus
                            required />
                    </div>
                    <div class="form-group">
                        <label for="address" class="form-label">Location</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Address" required />
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="btn btn-primary me-3">Save</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection