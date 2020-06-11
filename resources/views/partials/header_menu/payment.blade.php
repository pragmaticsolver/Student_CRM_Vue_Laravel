@can('st-payments')
    <li class="{{ app()->request->route()->getName() == 'payments.index' ? 'active' : '' }}">
        <a aria-expanded="false" role="button" href="{{ route('payments.index') }}" class="nav-link">{{ __('messages.payments') }}</a>
    </li>
@endcan