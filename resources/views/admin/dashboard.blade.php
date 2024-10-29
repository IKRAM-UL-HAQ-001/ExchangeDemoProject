@extends("layout.main")
@section('content')
<div class="container-fluid">
    <!-- Daily Bases Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-header p-0 position-relative mb-3">
                <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                    <p style="color: white;"><strong>Daily Bases</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        @php
            $dailyColorClasses = [
                'bg-gradient-success',
                'bg-gradient-info',
                'bg-gradient-warning',
                'bg-gradient-danger',
                'bg-gradient-dark',
                'bg-gradient-primary',
                'bg-gradient-secondary',
            ];
        @endphp

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl position-relative">
                            <i class="material-icons opacity-10">account_balance_wallet</i>
                        </div>
                        <div class="text-end ms-3 text-center flex-grow-1"> <!-- Center alignment -->
                            <p class="text-sm mb-0 text-capitalize">Total Bank Balance</p>
                            <h4 class="mb-0">{{ $totalBankBalance }}</h4>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
            </div>
        </div>

        @foreach ([ 
            ['Today Margin', $totalBalanceDaily, 'trending_up'],
            ['Total Deposit', $totalDepositDaily, 'arrow_circle_up'],
            ['Total Withdrawal', $totalWithdrawalDaily, 'arrow_circle_down'],
            ['Total Expense', $totalExpenseDaily, 'payment'],
            ['Total Bonus', $totalBonusDaily, 'star_border'],
            ['Total Exchanges', $totalExchanges, 'swap_vert'],
            ['Total Users', $totalUsers, 'group'],
            ['Customers', $totalOldCustomersDaily, 'person_outline'],
            ['Today Profit', $totalOwnerProfitDaily, 'attach_money'],
            ['Total New Customer', $totalCustomersDaily, 'person_add'],
            ['Total Open Close Balance', $totalOpenCloseBalanceDaily, 'monetization_on'],
            ['Total Paid Vendor Amount', $totalPaidAmountDaily, 'shopping_cart'],
        ] as $index => $card)
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-header p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-lg icon-shape {{ $dailyColorClasses[$index % count($dailyColorClasses)] }} shadow-{{ strtolower($dailyColorClasses[$index % count($dailyColorClasses)]) }} text-center border-radius-xl position-relative">
                                <i class="material-icons opacity-10">{{ $card[2] }}</i>
                            </div>
                            <div class="text-end ms-3 text-center flex-grow-1"> <!-- Center alignment -->
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
                    <p style="color: white;"><strong>Monthly Bases</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        @php
            $monthlyColorClasses = [
                'bg-gradient-success',
                'bg-gradient-info',
                'bg-gradient-warning',
                'bg-gradient-danger',
                'bg-gradient-dark',
                'bg-gradient-primary',
                'bg-gradient-secondary',
            ];
        @endphp

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl position-relative">
                            <i class="material-icons opacity-10">account_balance_wallet</i> <!-- Monthly Profit -->
                        </div>
                        <div class="text-end ms-3 text-center flex-grow-1"> <!-- Center alignment -->
                            <p class="text-sm mb-0 text-capitalize">Monthly Margin</p>
                            <h4 class="mb-0">{{ $totalBalanceMonthly }}</h4>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
            </div>
        </div>

        @foreach ([ 
            ['Total Deposit', $totalDepositMonthly, 'arrow_circle_up'],
            ['Total Withdrawal', $totalWithdrawalMonthly, 'arrow_circle_down'],
            ['Total Expense', $totalExpenseMonthly, 'money_off'],
            ['Total Bonus', $totalBonusMonthly, 'star_border'],
            ['Total Exchanges', $totalExchanges, 'swap_vert'],
            ['Total Users', $totalUsers, 'group'],
            ['Customers', $totalOldCustomersMonthly, 'person_outline'],
            ['Monthly Profit', $totalOwnerProfitMonthly, 'attach_money'],
            ['Total New Customer', $totalCustomersMonthly, 'person_add'],
            ['Total Settling Points', $totalMasterSettlingMonthly, 'account_balance'],
            ['Total Open Close Balance', $totalOpenCloseBalanceMonthly, 'monetization_on'],
            ['Total Paid Vendor Amount', $totalPaidAmountMonthly, 'shopping_cart'],
        ] as $index => $card)
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-header p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-lg icon-shape {{ $monthlyColorClasses[$index % count($monthlyColorClasses)] }} shadow-{{ strtolower($monthlyColorClasses[$index % count($monthlyColorClasses)]) }} text-center border-radius-xl position-relative">
                                <i class="material-icons opacity-10">{{ $card[2] }}</i>
                            </div>
                            <div class="text-end ms-3 text-center flex-grow-1"> <!-- Center alignment -->
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
