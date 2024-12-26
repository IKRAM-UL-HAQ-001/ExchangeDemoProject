@extends("layout.main")
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-header p-0 position-relative mb-3">
                <div class="bg-gradient-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                    <p style="color: black;"><strong>Daily Bases</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        @php
            $dailyColorClasses = [
                'bg-gradient-primary',
            ];
        @endphp

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="test1 card-header p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon icon-lg icon-shape bg-gradient-primary shadow-dark text-center border-radius-xl position-relative"
                        style="width: 60px; height: 60px;">
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
            ['Today Freez Amount', $totalFreezAmountDaily, 'arrow_downward'],
            ['Today Deposit', $totalDepositDaily, 'arrow_circle_up'],
            ['Today Withdrawal', $totalWithdrawalDaily, 'arrow_circle_down'],
            ['Today Expense', $totalExpenseDaily, 'payment'],
            ['Today Bonus', $totalBonusDaily, 'star_border'],
            ['Total Exchanges', $totalExchanges, 'swap_vert'],
            ['Total Users', $totalUsers, 'group'],
            ['Customers', $totalOldCustomersDaily, 'person_outline'],
            ['Today Profit', $totalOwnerProfitDaily, 'attach_money'],
            ['Today New Customer', $totalCustomersDaily, 'person_add'],
            ['Today Settling Points', $totalMasterSettlingDaily, 'attach_money'],
            ['Today Open Close Balance', $totalOpenCloseBalanceDaily, 'monetization_on'],
            ['Today Paid Vendor Amount', $totalPaidAmountDaily, 'attach_money'],
        ] as $index => $card)
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="test1 card-header p-3">
                        <div class="d-flex align-items-center">
                            <div class=" icon icon-lg icon-shape {{ e( $dailyColorClasses[$index % count($dailyColorClasses)]) }} shadow-{{ e( strtolower($dailyColorClasses[$index % count($dailyColorClasses)])) }} text-center )border-radius-xl position-relative"
                            style="width: 60px; height: 60px;">
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
                <div class="bg-gradient-primary   border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                   <p style="color: black;"><strong>Weekly Bases</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card">
                <div class="test1 card-header p-3">
                    <div class="d-flex align-items-center">
                        <!-- Icon Section -->
                        <div class="icon icon-lg icon-shape bg-gradient-primary shadow-dark text-center border-radius-xl position-relative" 
                            style="width: 60px; height: 60px;">
                            <i class="material-icons opacity-10" style="color: white;">account_balance_wallet</i>
                        </div>
                        <!-- Text Section -->
                        <div class="text-center flex-grow-1 ms-3">
                            <p class="text-sm mb-0 text-capitalize">Weekly Margin</p>
                            <h4 class="mb-0" style="color:white;">{{ $totalBalanceWeekly }}</h4>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">
            </div>
        </div>

        @foreach ([ 
            ['Weekly Freez Amount', $totalFreezAmountWeekly, 'arrow_downward'],
            ['Total Deposit', $totalDepositWeekly, 'arrow_upward'],
            ['Total Withdrawal', $totalWithdrawalWeekly, 'arrow_downward'],
            ['Total Expense', $totalExpenseWeekly, 'money_off'],
            ['Total Bonus', $totalBonusWeekly, 'star'],
            ['Total Users', $totalUsers, 'group'],
            ['Customers', $totalOldCustomersWeekly, 'person'],
            ['Weekly Profit', $totalOwnerProfitWeekly, 'attach_money'],
            ['Total New Customers', $totalCustomersWeekly, 'group_add'],
            ['Total Settling Points', $totalMasterSettlingWeekly, 'point_of_sale'],
        ] as $index => $card)
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="test1 card-header p-3">
                        <div class="d-flex align-items-center">
                            <!-- Icon Section -->
                            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-dark text-center border-radius-xl position-relative" 
                                style="width: 60px; height: 60px;">
                                <i class="material-icons opacity-10" style="color: white;">{{ $card[2] }}</i>
                            </div>
                            <div class="text-center flex-grow-1 ms-3">
                                <p class="text-sm mb-0 text-capitalize">{{ $card[0] }}</p>
                                <h4 class="mb-0" style="color:white;">{{ $card[1] }}</h4>
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
                <div class="bg-gradient-primary   border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
                    <p style="color: black;"><strong>Monthly Bases</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        @php
            $monthlyColorClasses = [
                'bg-gradient-primary',
            ];
        @endphp

<div class="col-xl-3 col-sm-6 mb-4">
    <div class="card">
        <div class="test1 card-header p-3">
            <div class="d-flex align-items-center">
                <!-- Icon Section -->
                <div class="icon icon-lg icon-shape bg-gradient-primary shadow-dark text-center border-radius-xl position-relative" 
                     style="width: 60px; height: 60px;">
                    <i class="material-icons" style="color: white;">account_balance_wallet</i> <!-- Monthly Profit Icon -->
                </div>
                <!-- Text Section -->
                <div class="text-center ms-3 flex-grow-1">
                    <p class="text-sm mb-0 text-capitalize" style="color: #ffffff;">Monthly Margin</p>
                    <h4 class="mb-0" style="color: white;">{{ e($totalBalanceMonthly) }}</h4>
                </div>
            </div>
        </div>
        <hr class="dark horizontal my-0">
    </div>
</div>


        @foreach ([ 
            ['Total Deposit', $totalDepositMonthly, 'arrow_circle_up'],
            ['Total Freez Amount', $totalFreezAmountMonthly, 'arrow_downward'],
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
                            <div class=" icon icon-lg icon-shape {{ e( $monthlyColorClasses[$index % count($monthlyColorClasses)]) }} shadow-{{ e( strtolower($monthlyColorClasses[$index % count($monthlyColorClasses)])) }} text-center border-radius-xl position-relative"  style="width: 60px; height: 60px;">
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
