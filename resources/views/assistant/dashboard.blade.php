@extends("layout.main")
@section('content')
<div class="container-fluid">
    <!-- Daily Bases Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-header p-0 position-relative mb-3">
                <div class="bg-gradient-warning shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                    <h5 class="text-white mb-3"><strong>Daily Bases</strong></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="test1 card-header p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-lg icon-shape bg-gradient-warning shadow-dark text-center border-radius-xl position-relative">
                            <i class="material-icons opacity-10">attach_money</i>
                        </div>
                        <div class="text-center flex-grow-1 ms-3">
                            <p class="text-sm mb-0 text-capitalize">Total Bank Balance</p>
                            <h4 class="mb-0" style="color:white">{{ $totalBankBalance }}</h4>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
            </div>
        </div>

        @php
            $colorClasses = [
                'bg-gradient-warning',
            ];
        @endphp

        @foreach ([
            ['Today Margin', $totalBalanceDaily],
            ['Today Deposit', $totalDepositDaily],
            ['Today Withdrawal', $totalWithdrawalDaily],
            ['Today Expense', $totalExpenseDaily],
            ['Today Bonus', $totalBonusDaily],
            ['Today Exchanges', $totalExchanges],
            ['Today Users', $totalUsers],
            ['Customers', $totalOldCustomersDaily],
            ['Today Profit', $totalOwnerProfitDaily],
            ['Today New Customer', $totalCustomerDaily],
            ['Today Open Close Balance', $totalOpenCloseBalanceDaily],
        ] as $index => $card)
            @php
                $iconMapping = [
                    'Today Margin' => 'attach_money',
                    'Today Deposit' => 'arrow_upward',
                    'Today Withdrawal' => 'arrow_downward',
                    'Today Expense' => 'money_off',
                    'Today Bonus' => 'star',
                    'Today Exchanges' => 'swap_horiz',
                    'Today Users' => 'people',
                    'Customers' => 'person',
                    'Today Profit' => 'money',
                    'Today New Customer' => 'group_add',
                    'Today Open Close Balance' => 'account_balance',
                ];

                $icon = $iconMapping[$card[0]] ?? 'info'; // Default icon if not found
                $colorClass = $colorClasses[$index % count($colorClasses)];
            @endphp
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="test1 card-header p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-lg icon-shape {{ $colorClass }} shadow-{{ strtolower($colorClass) }} text-center border-radius-xl position-relative">
                                <i class="material-icons opacity-10">{{ $icon }}</i>
                            </div>
                            <div class="text-center flex-grow-1 ms-3">
                                <p class="text-sm mb-0 text-capitalize">{{ $card[0] }}</p>
                                <h4 class="mb-0" style="color:white">{{ $card[1] }}</h4>
                            </div>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                </div>
            </div>
        @endforeach
    </div>

    <!-- Monthly Bases Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-header p-0 position-relative mb-3">
                <div class="bg-gradient-warning shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                    <h5 class="text-white mb-3"><strong>Monthly Bases</strong></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="test1 card-header p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-lg icon-shape bg-gradient-warning shadow-dark text-center border-radius-xl position-relative">
                            <i class="material-icons opacity-10">attach_money</i>
                        </div>
                        <div class="text-center flex-grow-1 ms-3">
                            <p class="text-sm mb-0 text-capitalize">Monthly Margin</p>
                            <h4 class="mb-0" style="color:white">{{ $totalBalanceMonthly }}</h4>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
            </div>
        </div>

        @foreach ([
            ['Total Deposit', $totalDepositMonthly],
            ['Total Withdrawal', $totalWithdrawalMonthly],
            ['Total Expense', $totalExpenseMonthly],
            ['Total Bonus', $totalBonusMonthly],
            ['Total Exchanges', $totalExchanges],
            ['Total Users', $totalUsers],
            ['Customers', $totalOldCustomersMonthly],
            ['Total Profit', $totalOwnerProfitMonthly],
            ['Total New Customer', $totalCustomerMonthly],
            ['Total Settling Points', $totalMasterSettlingMonthly],
        ] as $index => $card)
            @php
                $iconMapping = [
                    'Total Deposit' => 'arrow_upward',
                    'Total Withdrawal' => 'arrow_downward',
                    'Total Expense' => 'money_off',
                    'Total Bonus' => 'star',
                    'Total Exchanges' => 'swap_horiz',
                    'Total Users' => 'people',
                    'Customers' => 'person',
                    'Total Profit' => 'money',
                    'Total New Customer' => 'group_add',
                    'Total Settling Points' => 'point_of_sale',
                ];

                $icon = $iconMapping[$card[0]] ?? 'info'; // Default icon if not found
                $colorClass = $colorClasses[$index % count($colorClasses)];
            @endphp
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="test1 card-header p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-lg icon-shape {{ $colorClass }} shadow-{{ strtolower($colorClass) }} text-center border-radius-xl position-relative">
                                <i class="material-icons opacity-10">{{ $icon }}</i>
                            </div>
                            <div class="text-center flex-grow-1 ms-3">
                                <p class="text-sm mb-0 text-capitalize">{{ $card[0] }}</p>
                                <h4 class="mb-0" style="color:white">{{ $card[1] }}</h4>
                            </div>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
