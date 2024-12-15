@extends("layout.main")
@section('content')
<div class="container-fluid">
    <!-- Daily Bases Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-header p-0 position-relative mb-3">
                <div class="bg-gradient-success border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
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
                        <div class="icon icon-lg icon-shape bg-gradient-success shadow-dark text-center border-radius-xl position-relative"
                        style="width: 60px; height: 60px;">
                            <i class="material-icons opacity-10">account_balance_wallet</i>
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

        @foreach ([ 
            ['Today Freez Amount', $totalFreezAmountDaily, 'bg-gradient-success', 'arrow_downward'],
            ['Today Margin', $totalBalanceDaily, 'bg-gradient-success', 'attach_money'],
            ['Today Deposit', $totalDepositDaily, 'bg-gradient-success', 'arrow_upward'],
            ['Today Withdrawal', $totalWithdrawalDaily, 'bg-gradient-success', 'arrow_downward'],
            ['Today Expense', $totalExpenseDaily, 'bg-gradient-success', 'money_off'],
            ['Today Bonus', $totalBonusDaily, 'bg-gradient-success', 'star'],
            ['Today Users', $userCount, 'bg-gradient-success', 'people'],
            ['Customers', $customerCountDaily, 'bg-gradient-success', 'person'],
            ['Today Profit', $totalOwnerProfitDaily, 'bg-gradient-success', 'attach_money'],
            ['Today New Customers', $totalNewCustomerDaily, 'bg-gradient-success', 'group_add'],
            ['Today Open Close Balance', $totalOpenCloseBalanceDaily, 'bg-gradient-success', 'account_balance'],
            ['Today Vendor Payments', $totalVendorPaymentsDaily, 'bg-gradient-success', 'payment'], // Vendor Payment Card

        ] as $card)
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="test1 card-header p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-lg icon-shape {{ $card[2] }} shadow-{{ strtolower($card[2]) }} text-center border-radius-xl position-relative" style="width: 60px; height: 60px;">
                                <i class="material-icons opacity-10">{{ $card[3] }}</i>
                            </div>
                            <div class="text-center flex-grow-1 ms-3">
                                <p class="text-sm mb-0 text-capitalize">{{ $card[0] }}</p>
                                <h4 class="mb-0"style="color:white">{{ $card[1] }}</h4>
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
                            <div class="icon icon-lg icon-shape bg-gradient-success shadow-dark text-center border-radius-xl position-relative"
                            style="width: 60px; height: 60px;">
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
                ['Total Users', $userCount, 'bg-gradient-success', 'people'],
                ['Customers', $customerCountWeekly, 'bg-gradient-success', 'person'],
                ['Weekly Profit', $totalOwnerProfitWeekly, 'bg-gradient-success', 'attach_money'],
                ['Total New Customers', $totalNewCustomerWeekly, 'bg-gradient-success', 'group_add'],
                ['Total Settling Points', $totalMasterSettlingWeekly, 'bg-gradient-success', 'point_of_sale'],
                ['Total Vendor Payments', $totalVendorPaymentsWeekly, 'bg-gradient-success', 'payment'], // Vendor Payment Card
                
                ] as $card)
                <div class="col-xl-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="test1 card-header p-3">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-lg icon-shape {{ $card[2] }} shadow-{{ strtolower($card[2]) }} text-center border-radius-xl position-relative" style="width: 60px; height: 60px;">
                                    <i class="material-icons opacity-10">{{ $card[3] }}</i>
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
                        <div class="bg-gradient-success shadow-primary border-radius-lg pt-4 d-flex justify-content-between align-items-center px-3">
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
                                <div class="icon icon-lg icon-shape bg-gradient-success shadow-dark text-center border-radius-xl position-relative" style="width: 60px; height: 60px;">
                                    <i class="material-icons opacity-10">account_balance_wallet</i>
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
                <!-- Remaining Cards -->
                @foreach ([ 
                    ['Freez Amount', $totalFreezAmountMonthly, 'bg-gradient-success', 'arrow_downward'],
                    ['Total Deposit', $totalDepositMonthly, 'bg-gradient-success', 'arrow_upward'],
                    ['Total Withdrawal', $totalWithdrawalMonthly, 'bg-gradient-success', 'arrow_downward'],
                    ['Total Expense', $totalExpenseMonthly, 'bg-gradient-success', 'money_off'],
                    ['Total Bonus', $totalBonusMonthly, 'bg-gradient-success', 'star'],
                    ['Total Users', $userCount, 'bg-gradient-success', 'people'],
                    ['Customers', $customerCountMonthly, 'bg-gradient-success', 'person'],
                    ['Monthly Profit', $totalOwnerProfitMonthly, 'bg-gradient-success', 'attach_money'],
                    ['Total New Customers', $totalNewCustomerMonthly, 'bg-gradient-success', 'group_add'],
                    ['Total Settling Points', $totalMasterSettlingMonthly, 'bg-gradient-success', 'point_of_sale'],
                    ['Total Vendor Payments', $totalVendorPaymentsMonthly, 'bg-gradient-success', 'payment'],
                ] as $card)
                    <div class="col-xl-3 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="test1 card-header p-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon icon-lg icon-shape {{ $card[2] }} shadow-{{ strtolower($card[2]) }} text-center border-radius-xl" style="width: 60px; height: 60px;">
                                        <i class="material-icons opacity-10">{{ $card[3] }}</i>
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
