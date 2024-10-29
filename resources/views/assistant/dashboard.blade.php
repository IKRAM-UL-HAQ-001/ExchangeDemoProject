@extends("layout.main")
@section('content')
<div class="container-fluid">
    <!-- Daily Bases Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-header p-0 position-relative mb-3">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                    <h5 class="text-white mb-3"><strong>Daily Bases</strong></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl position-relative">
                            <i class="material-icons opacity-10">attach_money</i>
                        </div>
                        <div class="text-center flex-grow-1 ms-3">
                            <p class="text-sm mb-0 text-capitalize">Total Bank Balance</p>
                            <h4 class="mb-0">{{ $totalBankBalance }}</h4>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
            </div>
        </div>

        @php
            $colorClasses = [
                'bg-gradient-success',
                'bg-gradient-info',
                'bg-gradient-warning',
                'bg-gradient-danger',
                'bg-gradient-dark',
                'bg-gradient-primary',
                'bg-gradient-secondary',
            ];
        @endphp

        @foreach ([
            ['Today Margin', $totalBalanceDaily],
            ['Total Deposit', $totalDepositDaily],
            ['Total Withdrawal', $totalWithdrawalDaily],
            ['Total Expense', $totalExpenseDaily],
            ['Total Bonus', $totalBonusDaily],
            ['Total Exchanges', $totalExchanges],
            ['Total Users', $totalUsers],
            ['Customers', $totalOldCustomersDaily],
            ['Total Profit', $totalOwnerProfitDaily],
            ['Total New Customer', $totalCustomerDaily],
            ['Total Open Close Balance', $totalOpenCloseBalanceDaily],
        ] as $index => $card)
            @php
                $iconMapping = [
                    'Today Margin' => 'attach_money',
                    'Total Deposit' => 'arrow_upward',
                    'Total Withdrawal' => 'arrow_downward',
                    'Total Expense' => 'money_off',
                    'Total Bonus' => 'star',
                    'Total Exchanges' => 'swap_horiz',
                    'Total Users' => 'people',
                    'Customers' => 'person',
                    'Total Profit' => 'money',
                    'Total New Customer' => 'group_add',
                    'Total Open Close Balance' => 'account_balance',
                ];

                $icon = $iconMapping[$card[0]] ?? 'info'; // Default icon if not found
                $colorClass = $colorClasses[$index % count($colorClasses)];
            @endphp
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-header p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-lg icon-shape {{ $colorClass }} shadow-{{ strtolower($colorClass) }} text-center border-radius-xl position-relative">
                                <i class="material-icons opacity-10">{{ $icon }}</i>
                            </div>
                            <div class="text-center flex-grow-1 ms-3">
                                <p class="text-sm mb-0 text-capitalize">{{ $card[0] }}</p>
                                <h4 class="mb-0">{{ $card[1] }}</h4>
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
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                    <h5 class="text-white mb-3"><strong>Monthly Bases</strong></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl position-relative">
                            <i class="material-icons opacity-10">attach_money</i>
                        </div>
                        <div class="text-center flex-grow-1 ms-3">
                            <p class="text-sm mb-0 text-capitalize">Monthly Margin</p>
                            <h4 class="mb-0">{{ $totalBalanceMonthly }}</h4>
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
            ['Total Open Close Balance', $totalOpenCloseBalanceMonthly],
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
                    'Total Open Close Balance' => 'account_balance',
                ];

                $icon = $iconMapping[$card[0]] ?? 'info'; // Default icon if not found
                $colorClass = $colorClasses[$index % count($colorClasses)];
            @endphp
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-header p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-lg icon-shape {{ $colorClass }} shadow-{{ strtolower($colorClass) }} text-center border-radius-xl position-relative">
                                <i class="material-icons opacity-10">{{ $icon }}</i>
                            </div>
                            <div class="text-center flex-grow-1 ms-3">
                                <p class="text-sm mb-0 text-capitalize">{{ $card[0] }}</p>
                                <h4 class="mb-0">{{ $card[1] }}</h4>
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
