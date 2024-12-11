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
            ['Today Margin', $totalBalanceDaily, 'bg-gradient-warning', 'attach_money'],
            ['Today Deposit', $totalDepositDaily, 'bg-gradient-warning', 'arrow_upward'],
            ['Today Withdrawal', $totalWithdrawalDaily, 'bg-gradient-warning', 'arrow_downward'],
            ['Today Expense', $totalExpenseDaily, 'bg-gradient-warning', 'money_off'],
            ['Today Bonus', $totalBonusDaily, 'bg-gradient-warning', 'star'],
            ['Today Users', $userCount, 'bg-gradient-warning', 'people'],
            ['Customers', $customerCountDaily, 'bg-gradient-warning', 'person'],
            ['Today Profit', $totalOwnerProfitDaily, 'bg-gradient-warning', 'attach_money'],
            ['Today New Customers', $totalNewCustomerDaily, 'bg-gradient-warning', 'group_add'],
            ['Today Open Close Balance', $totalOpenCloseBalanceDaily, 'bg-gradient-warning', 'account_balance'],
        ] as $card)
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="test1 card-header p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-lg icon-shape {{ $card[2] }} shadow-{{ strtolower($card[2]) }} text-center border-radius-xl position-relative">
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

        @foreach ([ 
            ['Total Deposit', $totalDepositMonthly, 'bg-gradient-warning', 'arrow_upward'],
            ['Total Withdrawal', $totalWithdrawalMonthly, 'bg-gradient-warning', 'arrow_downward'],
            ['Total Expense', $totalExpenseMonthly, 'bg-gradient-warning', 'money_off'],
            ['Total Bonus', $totalBonusMonthly, 'bg-gradient-warning', 'star'],
            ['Total Users', $userCount, 'bg-gradient-warning', 'people'],
            ['Customers', $customerCountMonthly, 'bg-gradient-warning', 'person'],
            ['Monthly Profit', $totalOwnerProfitMonthly, 'bg-gradient-warning', 'attach_money'],
            ['Total New Customers', $totalNewCustomerMonthly, 'bg-gradient-warning', 'group_add'],
            ['Total Settling Points', $totalMasterSettlingMonthly, 'bg-gradient-warning', 'point_of_sale'],
        ] as $card)
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="test1 card-header p-3">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-lg icon-shape {{ $card[2] }} shadow-{{ strtolower($card[2]) }} text-center border-radius-xl position-relative">
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
</div>
@endsection
