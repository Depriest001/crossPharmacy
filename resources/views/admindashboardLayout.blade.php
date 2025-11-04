<!doctype html>

<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title','Admin Dashboard | The Cross Pharmacy')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.jpg')}}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css')}}" />

    <!-- Core CSS -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css')}}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />

    <!-- endbuild -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.bootstrap5.min.css">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/4.0.1/css/fixedHeader.bootstrap5.min.css">

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js')}}"></script>
    <script src="{{ asset('assets/vendor/js/helpers.js')}}"></script>

    <script src="{{ asset('assets/js/config.js')}}"></script>
  </head>
  
  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

	    @include('admin.includes.sidebar')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <div class="navfixed position-fixed">
            <nav
              class="layout-navbar layout-navbar-fixed container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
              id="layout-navbar">
              <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
                <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                  <i class="icon-base bx bx-menu icon-md"></i>
                </a>
              </div>

              <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
                
                <ul class="navbar-nav flex-row align-items-center ms-md-auto">

                  <!-- User -->
                  <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a
                      class="nav-link dropdown-toggle hide-arrow p-0"
                      href="javascript:void(0);"
                      data-bs-toggle="dropdown">
                      <div class="avatar avatar-online">
                        <img src="{{ asset('assets/images/avatar/user.png')}}" alt class="w-px-40 h-auto rounded-circle border" />
                      </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li>
                        <a class="dropdown-item">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar avatar-online">
                                <img src="{{ asset('assets/images/avatar/user.png')}}" alt class="w-px-40 h-auto rounded-circle border" />
                              </div>
                            </div>
                            <div class="flex-grow-1">
                              <h6 class="mb-0">{{ auth('staff')->user()->full_name }}</h6>
                              <small class="text-body-secondary">{{ auth('staff')->user()->role->name }}</small>
                            </div>
                          </div>
                        </a>
                      </li>
                      <li>
                        <div class="dropdown-divider my-1"></div>
                      </li>
                      <li>
                        <a class="dropdown-item" href="{{route ('admin.profile')}}">
                          <i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span>
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#">
                          <i class="icon-base bx bx-cog icon-md me-3"></i><span>Settings</span>
                        </a>
                      </li>
                      <li>
                        <div class="dropdown-divider my-1"></div>
                      </li>
                      <li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                        <a class="dropdown-item" href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                          <i class="icon-base bx bx-power-off icon-md me-3 text-danger"></i><span>Log Out</span>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <!--/ User -->
                </ul>
              </div>
            </nav>
          </div>
          <!-- / Navbar -->
          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
            @yield('content')
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl">
                <div
                  class="footer-container d-flex align-items-center justify-content-center py-4 flex-md-row flex-column">
                  <div class="mb-2 mb-md-0">
                    &#169;
                    <script>
                      document.write(new Date().getFullYear());
                    </script>
                    All Right Reversed @ The Cross Pharmacy.
                  </div>
                  
                </div>
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->

    <script src="{{ asset('assets/vendor/libs/popper/popper.js')}}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js')}}"></script>

    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>

    <script src="{{ asset('assets/vendor/js/menu.js')}}"></script>


    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>

    <!-- Main JS -->

    <script src="{{ asset('assets/js/main.js')}}"></script>

    <!-- Page JS -->
    <script src="{{ asset('assets/js/dashboards-analytics.js')}}"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/4.0.1/js/dataTables.fixedHeader.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const appToast = document.getElementById('appToast');
            if (appToast) {
            const toast = new bootstrap.Toast(appToast);
            toast.show();
            }
        });
    </script>
    <script>
      $(document).ready(function () {
        $('#exampleTable').DataTable({
          pageLength: 10,
          responsive: true,
          scrollY: '60vh',       // make table body scrollable
          scrollCollapse: true,
          paging: true,
          fixedHeader: true,     // keep header fixed when scrolling
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records..."
          }
        });
      });
    </script>
  </body>
</html>
