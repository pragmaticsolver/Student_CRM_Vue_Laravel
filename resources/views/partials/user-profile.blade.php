<ul class="nav navbar-nav navbar-right">
    <li class="dropdown">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            @if( Session::has('orig_user') )
                {{ __('messages.hi') }}, {{ @\App\User::find(Session::get('orig_user'))->name }}
                ( {{ __('messages.viewing-as') }} {{ \Auth::user()->name }} )
            @else
                {{ __('messages.hi') }}, {{ \Auth::user()->name }}
            @endif
        </a>
        <ul role="menu" class="dropdown-menu dropdown-menu-right">
            @if( Session::has('orig_user') )
                <li>
                    <a href="" id="btn_stop_impersonate" >
                        <i class="fa fa-undo"></i> {{ __('messages.stop_impersonate') }}
                    </a>
                    <form id="stop_imporsonate-form" action="{{ route('stop_impersonate') }}" method="GET" style="display: none;">
                    </form>
                </li>
            @endif
            <li>
                <a href="" id="btn_logout">
                    <i class="fa fa-sign-out"></i> {{ __('messages.logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </li>
</ul>
