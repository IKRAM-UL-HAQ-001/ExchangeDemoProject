@extends("layout.main")
@section('content')
</style>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-header p-0 position-relative mb-3">
                <div class="bg-gradient-success shadow-success border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                    <p style="color: black;"><strong>Daily Bases</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        @php
            $dailyColorClasses = [
                'bg-gradient-success',
            ];
        @endphp

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="test1 card-header p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-lg icon-shape bg-gradient-success shadow-dark text-center border-radius-xl position-relative">
                            <i class="material-icons"style="color:white">account_balance_wallet</i>
                        </div>
                        <div class=" text-end ms-3 text-center flex-grow-1"> <!-- Center alignment -->
                            <p class="text-sm mb-0 text-capitalize">Total Bank Balance</p>
                            <h4 class="smb-0"style="color:white">{{ e( $totalBankBalance) }}</h4>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
            </div>
        </div>

        @foreach ([ 
            ['Today Margin', $totalBalanceDaily, 'trending_up'],
            ['Today Deposit', $totalDepositDaily, 'arrow_circle_up'],
            ['Today Withdrawal', $totalWithdrawalDaily, 'arrow_circle_down'],
            ['Today Expense', $totalExpenseDaily, 'payment'],
            ['Today Bonus', $totalBonusDaily, 'star_border'],
            ['Total Exchanges', $totalExchanges, 'swap_vert'],
            ['Total Users', $totalUsers, 'group'],
            ['Customers', $totalOldCustomersDaily, 'person_outline'],
            ['Today Profit', $totalOwnerProfitDaily, 'attach_money'],
            ['Today New Customer', $totalCustomersDaily, 'person_add'],
            ['Today Open Close Balance', $totalOpenCloseBalanceDaily, 'monetization_on'],
            ['Today Paid Vendor Amount', $totalPaidAmountDaily, 'attach_money'],
        ] as $index => $card)
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="test1 card-header p-3">
                        <div class="d-flex align-items-center">
                            <div class=" icon icon-lg icon-shape {{ e( $dailyColorClasses[$index % count($dailyColorClasses)]) }} shadow-{{ e( strtolower($dailyColorClasses[$index % count($dailyColorClasses)])) }} text-center )border-radius-xl position-relative">
                                <i class=" material-icons" style="color:white">{{ e( $card[2]) }}</i>
                            </div>
                            <div class="text-end ms-3 text-center flex-grow-1"> <!-- Center alignment -->
                                <p class=" text-sm mb-0 text-capitalize">{{ e( $card[0] )}}</p>
                                <h4 class=" mb-0" style="color:white">{{ e( $card[1] )}}</h4>
                            </div>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                </div>
            </div>
        @endforeach
    </div>
    <!-- Weekly Bases Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-header p-0 position-relative mb-3">
                <div class="bg-gradient-success shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                    <h5 class="text-white mb-3"><strong>Weekly Bases</strong></h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="test1 card-header p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-lg icon-shape bg-gradient-success shadow-dark text-center border-radius-xl position-relative">
                            <i class="material-icons opacity-10">account_balance_wallet</i>
                        </div>
                        <div class="text-center flex-grow-1 ms-3">
                            <p class="text-sm mb-0 text-capitalize">Weekly Margin</p>
                            <h4 class="mb-0" style="color:white">{{ $totalBalanceWeekly }}</h4>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
            </div>
        </div>

        @foreach ([ 
            ['Weekly Freez Amount', $totalFreezAmountWeekly, 'bg-gradient-success', 'arrow_downward'],
            ['Total Deposit', $totalDepositWeekly, 'bg-gradient-success', 'arrow_upward'],
            ['Total Withdrawal', $totalWithdrawalWeekly, 'bg-gradient-success', 'arrow_downward'],
            ['Total Expense', $totalExpenseWeekly, 'bg-gradient-success', 'money_off'],
            ['Total Bonus', $totalBonusWeekly, 'bg-gradient-success', 'star'],
            ['Total Users', $totalUsers, 'group'],
            ['Customers', $totalOldCustomersWeekly, 'bg-gradient-success', 'person'],
            ['Weekly Profit', $totalOwnerProfitWeekly, 'bg-gradient-success', 'attach_money'],
            ['Total New Customers', $totalCustomersWeekly, 'bg-gradient-success', 'group_add'],
            ['Total Settling Points', $totalMasterSettlingWeekly, 'bg-gradient-success', 'point_of_sale'],
        ] as $card)
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="test1 card-header p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-lg icon-shape {{ $card[2] }} shadow-{{ strtolower($card[2]) }} text-center border-radius-xl position-relative">
                                <i class="material-icons opacity-10">{{ $card[2] }}</i>
                            </div>
                            <div class=" text-center flex-grow-1 ms-3">
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
                <div class="bg-gradient-success shadow-success border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                    <p style="color: black;"><strong>Monthly Bases</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        @php
            $monthlyColorClasses = [
                'bg-gradient-success',
            ];
        @endphp

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="test1 card-header p-3">
                    <div class="d-flex align-items-center">
                        <div class=" icon icon-lg icon-shape bg-gradient-success shadow-dark text-center border-radius-xl position-relative">
                            <i class="material-icons" style="color:white">account_balance_wallet</i> <!-- Monthly Profit -->
                        </div>
                        <div class=" text-end ms-3 text-center flex-grow-1"> <!-- Center alignment -->
                            <p class=" text-sm mb-0 text-capitalize">Monthly Margin</p>
                            <h4 class="mb-0" style="color:white">{{ e( $totalBalanceMonthly )}}</h4>
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
            ['Total Paid Vendor Amount', $totalPaidAmountMonthly, 'attach_money'],
        ] as $index => $card)
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="test1 card-header p-3">
                        <div class="d-flex align-items-center">
                            <div class=" icon icon-lg icon-shape {{ e( $monthlyColorClasses[$index % count($monthlyColorClasses)]) }} shadow-{{ e( strtolower($monthlyColorClasses[$index % count($monthlyColorClasses)])) }} text-center border-radius-xl position-relative">
                                <i class="material-icons" style="color:white">{{ e( $card[2]) }}</i>
                            </div>
                            <div class="text-end ms-3 text-center flex-grow-1"> <!-- Center alignment -->
                                <p class=" text-sm mb-0 text-capitalize">{{ e( $card[0]) }}</p>
                                <h4 class=" mb-0" style="color:white">{{ e( $card[1] )}}</h4>
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
