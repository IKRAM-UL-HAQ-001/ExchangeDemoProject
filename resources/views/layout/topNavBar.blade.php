<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
          @if(Auth::check())
          @if(Auth::user()->role === "admin")
            <li class="breadcrumb-item text-sm">
              <a class="text-dark" href="javascript:;">
                @if(Auth::user()->role =="admin")
                  Admin Dashboard
                @endif
              </a>
            </li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
          @endif
          @if(Auth::user()->role === "assistant")
            <li class="breadcrumb-item text-sm">
              <a class="text-dark" href="javascript:;">
                @if(Auth::user()->role =="assistant")
                  Assistant Dashboard
                @endif  
              </a>
            </li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
          @endif
          @if(Auth::user()->role === "exchange")
            <li class="breadcrumb-item text-sm">
              <a class="text-dark" href="javascript:;">
                @if(Auth::user()->role =="exchange")
                  {{ Auth::user()->exchange->name ?? 'No Exchange' }} Exchange Dashboard
                @endif
              </a>
            </li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
          @endif
          @endif
          </ol>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex justify-content-end" id="navbar">
          <ul class="navbar-nav  justify-content-end">
            <li class="nav-item dropdown pe-2 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-user cursor-pointer"></i>
                <span class="d-sm-inline">{{Auth::user()->name}}</span>
              </a>
              <ul class="dropdown-menu  dropdown-menu-end  px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                @if(Auth::check())
                  @if(Auth::user()->role === "admin")
                    <li class="mb-2">
                      <a class="dropdown-item border-radius-md" href="javascript:;">
                        <div class="d-flex py-1">
                          <div class="my-auto">
                            <img src="../assets/img/lock.svg" class="avatar-sm  me-3 ">
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="text-sm font-weight-normal mb-1">
                              <span class="font-weight-bold">Update Password</span>
                            </h6>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li class="mb-2">
                      <form action="{{ route('logout.all') }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="dropdown-item border-radius-md" style="border: none; background: none; padding: 0;">
                              <div class="d-flex py-1">
                                  <div class="my-auto">
                                      <img src="../assets/img/logout.svg" class="avatar-sm me-3">
                                  </div>
                                  <div class="d-flex flex-column justify-content-center">
                                      <h6 class="text-sm font-weight-normal mb-1">
                                          <span class="font-weight-bold">Logout All Users</span>
                                      </h6>
                                  </div>
                              </div>
                          </button>
                      </form>
                  </li>
                  @endif
                @endif
                <li class="mb-2">
                  <a class="dropdown-item border-radius-md" href="{{route('login.logout')}}">
                    <div class="d-flex py-1">
                      <div class="my-auto">
                        <img src="../assets/img/logout.svg" class="avatar-sm me-3">
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          <span class="font-weight-bold">Logout</span>
                        </h6>
                      </div>
                    </div>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center"> 
              <a href="javascript:;" class="nav-link p-0 text-body" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner"> 
                  <i class="sidenav-toggler-line"></i> 
                  <i class="sidenav-toggler-line"></i> 
                  <i class="sidenav-toggler-line"></i> 
                </div> 
              </a> 
          </li>
          </ul>
        </div>
      </div>
    </nav>