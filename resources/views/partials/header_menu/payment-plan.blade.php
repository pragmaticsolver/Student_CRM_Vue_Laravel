@if(Auth::user()->hasPermissionTo('payment-plan-list') || 
    Auth::user()->hasPermissionTo('payment-plan-create') || 
    Auth::user()->hasPermissionTo('payment-list') ||
    Auth::user()->hasPermissionTo('accounting-payments') ||
    Auth::user()->hasPermissionTo('manage-monthly-payments')
    )
    <li class="{{ (request()->is('accounting/plan/add') || 
        request()->is('accounting/plan/list') || 
        request()->is('accounting/monthly') ||
        Route::currentRouteName() == 'accounting.payments') ? 'dropdown active' : 'dropdown' }}">
        <a aria-expanded="false" role="button" href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">{{ __('messages.accounting') }}</a>
        <ul role="menu" class="dropdown-menu">
            @can('payment-plan-create')
                <li class="{{ (request()->is('accounting/plan/add')) ? 'active' : '' }}"><a href="{{ url('/accounting/plan/add') }}">{{ __('messages.addplan') }}</a></li>
            @endcan
            @can('payment-plan-list')
                <li class="{{ (request()->is('accounting/plan/list')) ? 'active' : '' }}"><a href="{{ url('/accounting/plan/list') }}">{{ __('messages.planlist') }}</a></li>
            @endcan
            @can('accounting-payments')
                <li class="{{ (Route::currentRouteName() == 'accounting.payments') ? 'active' : '' }}"><a href="{{ route('accounting.payments') }}">Payments</a></li>
            @endcan
            @can('manage-monthly-payments')
                <li class="{{ Route::currentRouteName() == 'manage.monthly.payments.index' ? 'active' : '' }}"><a href="{{ route('manage.monthly.payments.index') }}">Manage Monthly Payments</a></li>
            @endcan
        </ul>
    </li>
@endif
