<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            {{ config('app.name', 'Lorekeeper') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                @if(Auth::check())
                    <li class="nav-item dropdown">
                        <a id="inventoryDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            Home
                        </a>

                        <div class="dropdown-menu" aria-labelledby="inventoryDropdown">
                            <a class="dropdown-item" href="#">
                                My Characters
                            </a>
                            <a class="dropdown-item" href="{{ url('inventory') }}">
                                Inventory
                            </a>
                            <a class="dropdown-item" href="{{ url('bank') }}">
                                Bank
                            </a>
                            <a class="dropdown-item" href="#">
                                Trades
                            </a>
                        </div>
                    </li>
                @endif
                <li class="nav-item dropdown">
                    <a id="browseDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        Browse
                    </a>

                    <div class="dropdown-menu" aria-labelledby="browseDropdown">
                        <a class="dropdown-item" href="{{ url('users') }}">
                            Users
                        </a>
                        <a class="dropdown-item" href="{{ url('characters') }}">
                            Characters
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ url('raffles') }}">
                            Raffles
                        </a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a id="loreDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        World
                    </a>

                    <div class="dropdown-menu" aria-labelledby="loreDropdown">
                        <a class="dropdown-item" href="{{ url('world') }}">
                            Encyclopedia
                        </a>
                        <a class="dropdown-item" href="{{ url('world/prompts') }}">
                            Prompts
                        </a>
                    </div>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    @if(Auth::user()->isStaff)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('admin') }}"><i class="fas fa-crown"></i></a>
                        </li>
                    @endif
                    @if(Auth::user()->notifications_unread)
                        <li class="nav-item">
                            <a class="nav-link btn btn-secondary btn-sm" href="{{ url('notifications') }}"><span class="fas fa-envelope"></span> {{ Auth::user()->notifications_unread }}</a>
                        </li>
                    @endif
                    
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="{{ Auth::user()->url }}" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ Auth::user()->url }}">
                                Profile
                            </a>
                            <a class="dropdown-item" href="{{ url('notifications') }}">
                                Notifications
                            </a>
                            <a class="dropdown-item" href="{{ url('account/settings') }}">
                                Settings
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>