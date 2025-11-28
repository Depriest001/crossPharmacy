@extends('admindashboardLayout')
@section('title','Admin Dashboard | The Cross Pharmacy')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="mb-6">
                <div class="card">
                <div class="d-flex align-items-start row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">Welcome {{ auth('staff')->user()->full_name }}! 🎉</h5>
                            
                            @php
                                use Carbon\Carbon;
                                use App\Models\Sale;

                                $branchId = auth('staff')->user()->branch_id;

                                // Today's sales for this branch
                                $todaySales = Sale::where('branch_id', $branchId)
                                    ->where('status', 'completed')
                                    ->whereDate('created_at', Carbon::today())
                                    ->sum('grand_total');

                                // Yesterday's sales for this branch
                                $yesterdaySales = Sale::where('branch_id', $branchId)
                                    ->where('status', 'completed')
                                    ->whereDate('created_at', Carbon::yesterday())
                                    ->sum('grand_total');

                                // Growth percentage
                                $growth = $yesterdaySales > 0 
                                    ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100 
                                    : 0;
                            @endphp

                            <p class="mb-6">
                                You’ve made <strong>₦{{ number_format($todaySales, 2) }}</strong> in sales today.
                                @if($growth > 0)
                                    That’s <span class="text-success fw-bold">↑ {{ number_format($growth, 1) }}%</span> higher than yesterday. Keep up the great work!
                                @elseif($growth < 0)
                                    That’s <span class="text-danger fw-bold">↓ {{ number_format(abs($growth), 1) }}%</span> less than yesterday. Let’s aim higher tomorrow!
                                @else
                                    Your sales are steady compared to yesterday. Stay consistent!
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-6">
                            <img
                            src="{{ asset('assets/images/illustrations/man-with-laptop.png')}}"
                            height="175"
                            alt="View Badge User" />
                        </div>
                    </div>
                </div>
                </div>
            </div>
            @php
                $role = auth('staff')->user()->role->role_type ?? '';
            @endphp

            @if(in_array($role, ['Admin', 'Staff']))
            <div class="col-12">
                <div class="row">
                    <div class="col-md-3 col-12 mb-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                    <div class="avatar flex-shrink-0">
                                        <i class="icon-base bx bx-bxs-user icon-xl text-success"></i>
                                    </div>
                                </div>
                                <p class="mb-1">Staffs</p>
                                <h4 class="card-title mb-3">{{ number_format($userCount) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-12 mb-6 payments">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                    <div class="avatar flex-shrink-0">
                                        <i class="icon-base bx bx-cube-alt icon-xl text-warning"></i>
                                    </div>
                                </div>
                                <p class="mb-1">Products</p>
                                <h4 class="card-title mb-3">{{ number_format($productCount) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-12 mb-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                <div class="avatar flex-shrink-0">
                                    <i class="icon-base bx bx-bar-chart-alt-2 icon-xl text-info"></i>
                                </div>
                                </div>
                                <p class="mb-1">Sold Products</p>
                                <h4 class="card-title mb-3">{{ number_format($totalProductSales) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-12 mb-6">
                        <div class="card h-100">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between mb-4">
                                <div class="avatar flex-shrink-0">
                                    <i class="icon-base bx bx-bxs-credit-card icon-xl text-primary"></i>
                                </div>
                            </div>
                            <p class="mb-1">Sales</p>
                            <h4 class="card-title mb-3">₦{{ number_format($totalSales, 2) }}</h4>
                        </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            @endif

        </div>
    </div>
@endsection