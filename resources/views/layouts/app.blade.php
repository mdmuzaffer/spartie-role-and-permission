

<!doctype html>
<html lang="en">
  <head>
    <title>Role & permision</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
        
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="{{ asset('asset/css/style.css')}}">
            @vite(['resources/sass/app.scss', 'resources/js/app.js'])
  </head>
  <body>
        
        <div class="wrapper d-flex align-items-stretch">
            <nav id="sidebar">
                <div class="p-4 pt-5">
                <a href="#" class="img logo rounded-circle mb-5" style="background-image: url('{{ asset('asset/images/logo.jpg') }}');"></a>


       
            <ul class="list-unstyled components mb-5">
            @guest

            @if(Route::has('login'))
                <li >
                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
            @endif

            @if(Route::has('register'))
                <li>
                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
            @endif

            @else


            @can('user-list')
            <li><a  href="{{ route('users.index') }}">Manage Users</a></li>
            @endcan

            @if(auth()->user()->can('role-list') || auth()->user()->can('role-create') || auth()->user()->can('role-edit'))

            <li><a  href="{{ route('roles.index') }}">Manage Role</a></li>
            @endif
            <li><a  href="{{ route('products.index') }}">Manage Product</a></li>


              <li>
              <a href="#pageSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Pages</a>
              <ul class="collapse list-unstyled" id="pageSubmenu">
                <li>
                    <a href="#">Page 1</a>
                </li>
                <li>
                    <a href="#">Page 2</a>
                </li>
                <li>
                    <a href="#">Page 3</a>
                </li>
              </ul>
              </li>


            <li>
              <a href="#pageSubmenuLogout" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">{{ Auth::user()->name }}</a>
              <ul class="collapse list-unstyled" id="pageSubmenuLogout">
                <li>
                    
                        <a class="dropdown-item" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    
                </li>

              </ul>
            </li>


        @endguest


            </ul>

          </div>
        </nav>

        <!-- Page Content  -->
      <div id="content" class="p-4 p-md-5">

        <nav class="navbar navbar-expand-lg navbar-light bg-light">
          <div class="container-fluid">

            <button type="button" id="sidebarCollapse" class="btn btn-primary">
              <i class="fa fa-bars"></i>
              <span class="sr-only">Toggle Menu</span>
            </button>
            <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="nav navbar-nav ml-auto">



              </ul>
            </div>
          </div>
        </nav>


        @yield('content')
        
      
      
      </div>
      
            
        </div>
        
        <div class="footer">
                <p class="text-dark text-center">
                 Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="icon-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib.com</a>
                          </p>
            </div>

    <script src="{{ asset('asset/js/jquery.min.js')}}"></script>
    <script src="{{ asset('asset/js/popper.js')}}"></script>
    <script src="{{ asset('asset/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('asset/js/main.js')}}"></script>
  </body>
</html>