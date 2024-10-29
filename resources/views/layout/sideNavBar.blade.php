
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="" target="_blank">
            <img src="../assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold text-white">Frank Exchange System</span>
        </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            @if(Auth::check())
                @if(Auth::user()->role === "admin")
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active bg-gradient-primary' : '' }}" href="{{route('admin.dashboard')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">dashboard</i>
                            </div>
                            <span class="nav-link-text ms-1">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('admin/user') ? 'active bg-gradient-primary' : '' }}" href="{{route('admin.user.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">people</i>
                            </div>
                            <span class="nav-link-text ms-1">User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('admin/exchange') ? 'active bg-gradient-primary' : '' }}" href="{{route('admin.exchange.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">swap_horiz</i>
                            </div>
                            <span class="nav-link-text ms-1">Exchange</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('admin/vender_payment') ? 'active bg-gradient-primary' : '' }}" href="{{route('admin.vender_payment.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">swap_horiz</i>
                            </div>
                            <span class="nav-link-text ms-1">Vender Payment</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('admin/openCloseBalance') ? 'active bg-gradient-primary' : '' }}" href="{{route('admin.open_close_balance.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">swap_horiz</i>
                            </div>
                            <span class="nav-link-text ms-1">Opening Closing Balance</span>
                        </a>
                    </li>
                    <li class="nav-item">
    <a class="nav-link text-white {{ request()->is('admin/bank*') ? 'active bg-gradient-primary' : '' }}" href="#bankSubMenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="material-icons opacity-10">account_balance</i>
        </div>
        <span class="nav-link-text ms-1">Bank List</span>
    </a>
    <div class="collapse" id="bankSubMenu">
        <ul class="nav flex-column ms-3">
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->is('admin/bank') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.bank.list') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">account_balance</i>
                    </div>
                    <span class="nav-link-text ms-1">Bank</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->is('admin/bankUser') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.bank_user.list') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">person</i>
                    </div>
                    <span class="nav-link-text ms-1">Bank User</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white {{ request()->is('admin/bankBalance') ? 'active bg-gradient-primary' : '' }}" href="{{ route('admin.bank_balance.list') }}">
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10">account_balance_wallet</i>
                    </div>
                    <span class="nav-link-text ms-1">Bank Balance</span>
                </a>
            </li>
        </ul>
    </div>
</li>

                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('admin/deposit-withdrawal') ? 'active bg-gradient-primary' : '' }}" href="{{route('admin.deposit_withdrawal.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">attach_money</i>
                            </div>
                            <span class="nav-link-text ms-1">Deposit - Withdrawal</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('admin/expense') ? 'active bg-gradient-primary' : '' }}" href="{{route('admin.expense.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">monetization_on</i>
                            </div>
                            <span class="nav-link-text ms-1">Expense</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('admin/masterSettling') ? 'active bg-gradient-primary' : '' }}" href="{{route('admin.master_settling.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">settings</i>
                            </div>
                            <span class="nav-link-text ms-1">Master Settling</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('admin/customer') ? 'active bg-gradient-primary' : '' }}" href="{{route('admin.customer.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">person_add</i>
                            </div>
                            <span class="nav-link-text ms-1">New Customer</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('admin/ownerProfit') ? 'active bg-gradient-primary' : '' }}" href="{{route('admin.owner_profit.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">attach_money</i>
                            </div>
                            <span class="nav-link-text ms-1">HK</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('admin/report') ? 'active bg-gradient-primary' : '' }}" href="{{route('admin.report.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">assessment</i>
                            </div>
                            <span class="nav-link-text ms-1">Reports</span>
                        </a>
                    </li>
                @endif
                @if(Auth::user()->role === "exchange")
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('exchange.dashboard') ? 'active bg-gradient-primary' : '' }}" href="{{route('exchange.dashboard')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">dashboard</i>
                            </div>
                            <span class="nav-link-text ms-1">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('exchange/cash') ? 'active bg-gradient-primary' : '' }}" href="{{route('exchange.cash.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">monetization_on</i>
                            </div>
                            <span class="nav-link-text ms-1">Cash</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('exchange/openCloseBalance') ? 'active bg-gradient-primary' : '' }}" href="{{route('exchange.open_close_balance.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">swap_horiz</i>
                            </div>
                            <span class="nav-link-text ms-1">Opening Closing Balance</span>
                        </a>
                    </li>
                    @if(Auth::user()->role == "exchange" && session('bankUser') && session('bankUser')->user_id == Auth::id())
                        <li class="nav-item">
                            <a class="nav-link text-white {{ request()->is('exchange/bank') ? 'active bg-gradient-primary' : '' }}" href="{{route('exchange.bank.list')}}">
                                <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                    <i class="material-icons opacity-10">account_balance</i>
                                </div>
                                <span class="nav-link-text ms-1">Bank</span>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('exchange/customer') ? 'active bg-gradient-primary' : '' }}" href="{{route('exchange.customer.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">account_balance</i>
                            </div>
                            <span class="nav-link-text ms-1">New Customer</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('exchange/owner_profit') ? 'active bg-gradient-primary' : '' }}" href="{{route('exchange.owner_profit.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">account_balance</i>
                            </div>
                            <span class="nav-link-text ms-1">HK</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('exchange/deposit-withdrawal') ? 'active bg-gradient-primary' : '' }}" href="{{route('exchange.deposit_withdrawal.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">attach_money</i>
                            </div>
                            <span class="nav-link-text ms-1">Deposit - Withdrawal</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('exchange/expense') ? 'active bg-gradient-primary' : '' }}" href="{{route('exchange.expense.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">monetization_on</i>
                            </div>
                            <span class="nav-link-text ms-1">Expense</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('exchange/masterSettling') ? 'active bg-gradient-primary' : '' }}" href="{{route('exchange.master_settling.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">monetization_on</i>
                            </div>
                            <span class="nav-link-text ms-1">Master Settling</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('exchange/report') ? 'active bg-gradient-primary' : '' }}" href="{{route('exchange.report.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">monetization_on</i>
                            </div>
                            <span class="nav-link-text ms-1">Report</span>
                        </a>
                    </li>
                @endif
                @if(Auth::user()->role === "assistant")
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->routeIs('assistant.dashboard') ? 'active bg-gradient-primary' : '' }}" href="{{route('assistant.dashboard')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">dashboard</i>
                            </div>
                            <span class="nav-link-text ms-1">Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('assistant/masterSettling') ? 'active bg-gradient-primary' : '' }}" href="{{route('assistant.master_settling.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">settings</i>
                            </div>
                            <span class="nav-link-text ms-1">Master Settling</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('assistant/bankBalance') ? 'active bg-gradient-primary' : '' }}" href="{{route('assistant.bank_balance.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">account_balance_wallet</i>
                            </div>
                            <span class="nav-link-text ms-1">Bank Balance</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('assistant/deposit-withdrawal') ? 'active bg-gradient-primary' : '' }}" href="{{route('assistant.deposit_withdrawal.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">attach_money</i>
                            </div>
                            <span class="nav-link-text ms-1">Deposit - Withdrawal</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('assistant/expense') ? 'active bg-gradient-primary' : '' }}" href="{{route('assistant.expense.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">attach_money</i>
                            </div>
                            <span class="nav-link-text ms-1">Expense</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white {{ request()->is('assistant/openCloseBalance') ? 'active bg-gradient-primary' : '' }}" href="{{route('assistant.open_close_balance.list')}}">
                            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="material-icons opacity-10">swap_horiz</i>
                            </div>
                            <span class="nav-link-text ms-1">Opening Closing Balance</span>
                        </a>
                    </li>
                @endif
            @endif
        </ul>
    </div>
</aside>
