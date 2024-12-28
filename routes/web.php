<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssistantController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\DepositWithdrawalController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\CashController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MasterSettlingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\OwnerProfitController;
use App\Http\Controllers\BankUserController;
use App\Http\Controllers\BankEntryController;
use App\Http\Controllers\VenderPaymentController;
use App\Http\Controllers\OpenCloseBalanceController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\DatabaseExportController;
use App\Http\Controllers\ExcelFileController;
use App\Http\Controllers\BankBalanceController;

Route::get('/', [LoginController::class, 'index'])->name('auth.login');
Route::post('/auth/login/post', [LoginController::class, 'login'])->name('login.post');
Route::get('/auth/logout', [LoginController::class, 'logout'])->name('login.logout');

//admin export
Route::get('/export-bank', [BankController::class, 'bankExportExcel'])->name('export.bank');
Route::post('/export-deposit', [DepositController::class, 'depositExportExcel'])->name('export.deposit');
Route::post('/export-withdrawal', [WithdrawalController::class, 'withdrawalExportExcel'])->name('export.withdrawal');
Route::get('/export-expense', [ExpenseController::class, 'expenseExportExcel'])->name('export.expense');
Route::get('/export-masterSettlingWeekly', [MasterSettlingController::class, 'masterSettlingListWeeklyExportExcel'])->name('export.masterSettlingListWeekly');
Route::get('/export-masterSettlingMonthly', [MasterSettlingController::class, 'masterSettlingListMonthlyExportExcel'])->name('export.masterSettlingListMonthly');
Route::get('/export-bankBalance', [BankBalanceController::class, 'bankBalanceListExportExcel'])->name('export.bankBalanceList');
Route::get('/export-ownerProfit', [OwnerProfitController::class, 'ownerProfitListExportExcel'])->name('export.ownerProfitList');
Route::get('/export-venderPayment', [VenderPaymentController::class, 'venderPaymentExportExcel'])->name('export.venderPayment');
Route::get('/export-openCloseBalance', [OpenCloseBalanceController::class, 'openCloseBalanceExportExcel'])->name('export.openCloseBalance');
Route::get('/export-customer', [CustomerController::class, 'customerExportExcel'])->name('export.customer');

Route::group(['middleware' => ['admin']], function () {

    //download databsae
    Route::get('/admin/download', [DatabaseExportController::class, 'downloadDatabase'])->name('admin.confirm.download');


    //admin dashboard
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    //update password
    Route::post('/passwordUpdate', [LoginController::class, 'update'])->name('password.update');

    //logout all
    Route::get('/post', [LoginController::class, 'logoutAll'])->name('logout.all');

    //Report
    Route::get('/admin/report', [ReportController::class, 'index'])->name('admin.report.list');
    Route::post('/admin/report/post', [ReportController::class, 'report'])->name('admin.report.generate');

    // exchange user
    Route::get('/admin/user', [UserController::class, 'index'])->name('admin.user.list');
    Route::post('/admin/user/post', [UserController::class, 'store'])->name('admin.user.post');
    Route::post('/admin/user/update', [UserController::class, 'update'])->name('admin.user.update');
    Route::post('/admin/user/destroy', [UserController::class, 'destroy'])->name('admin.user.destroy');
    Route::post('/admin/user/status', [UserController::class, 'userStatus'])->name('admin.user.status');

    // exchange
    Route::get('/admin/exchange', [ExchangeController::class, 'exchangeList'])->name('admin.exchange.list');
    Route::post('/admin/exchange/post', [ExchangeController::class, 'store'])->name('admin.exchange.store');
    Route::post('/admin/exchange/destroy', [ExchangeController::class, 'destroy'])->name('admin.exchange.destroy');

    //bank
    Route::get('/admin/bank', [BankController::class, 'index'])->name('admin.bank.list');
    Route::post('/admin/bank/post', [BankController::class, 'store'])->name('admin.bank.store');
    Route::post('/admin/bank/destroy', [BankController::class, 'destroy'])->name('admin.bank.destroy');

    //Freez Bank
    Route::get('/admin/bank/freez', [BankController::class, 'freezBankIndex'])->name('admin.bank_freez.list');
    Route::post('/admin/bank/delete', [BankController::class, 'delete'])->name('admin.bank.delete');

    // bank user
    Route::get('/admin/bankUser', [BankUserController::class, 'index'])->name('admin.bank_user.list');
    Route::post('/admin/bankUser/post', [BankUserController::class, 'store'])->name('admin.bank_user.store');
    Route::post('/admin/bankUser/destroy', [BankUserController::class, 'destroy'])->name('admin.bank_user.destroy');

    // deposit withdrawal
    Route::get('/admin/deposit-withdrawal', [DepositWithdrawalController::class, 'index'])->name('admin.deposit_withdrawal.list');
    Route::post('/admin/deposit-withdrawal/destroy', [DepositWithdrawalController::class, 'destroy'])->name('admin.deposit_withdrawal.destroy');

    //expense
    Route::get('/admin/expense', [ExpenseController::class, 'index'])->name('admin.expense.list');
    Route::post('/admin/expense/destroy', [ExpenseController::class, 'destroy'])->name('admin.expense.destroy');

    //bank Balance
    Route::get('/admin/bankBalance', [BankBalanceController::class, 'index'])->name('admin.bank_balance.list');
    Route::post('/admin/bankBalance/destroy', [BankBalanceController::class, 'destroy'])->name('admin.bank_balance.destroy');
    //customer
    Route::get('/admin/customer', [CustomerController::class, 'index'])->name('admin.customer.list');
    Route::post('/admin/customer/destroy', [CustomerController::class, 'destroy'])->name('admin.customer.destroy');

    //Master Settling
    Route::get('/admin/masterSettling', [MasterSettlingController::class, 'index'])->name('admin.master_settling.list');
    Route::post('/admin/masterSettling/destroy', [MasterSettlingController::class, 'destroy'])->name('admin.master_settling.destroy');
    Route::post('/admin/masterSettling/update', [MasterSettlingController::class, 'update'])->name('admin.master_settling.update');

    //Owner Profit
    Route::get('/admin/ownerProfit', [OwnerProfitController::class, 'index'])->name('admin.owner_profit.list');
    Route::post('/admin/ownerProfit/destroy', [OwnerProfitController::class, 'destroy'])->name('admin.owner_profit.destroy');


    //Vender Payment
    Route::get('/admin/vender_payment', [VenderPaymentController::class, 'index'])->name('admin.vender_payment.list');
    Route::post('/admin/venderPayment/post', [VenderPaymentController::class, 'store'])->name('admin.vender_payment.store');
    Route::post('/admin/venderPayment/delete', [VenderPaymentController::class, 'destroy'])->name('admin.vender_payment.destroy');

    //Vender Payment
    Route::get('/admin/openCloseBalance', [OpenCloseBalanceController::class, 'index'])->name('admin.open_close_balance.list');
    Route::post('/admin/openCloseBalance/destroy', [OpenCloseBalanceController::class, 'destroy'])->name('admin.open_close_balance.destroy');

    //File Operations
    Route::get('/admin/file', [ExcelFileController::class, 'index'])->name('admin.file.list');
    Route::post('/admin/file/post', [ExcelFileController::class, 'store'])->name('admin.file.post');
    Route::post('/admin/file/destroy', [ExcelFileController::class, 'destroy'])->name('admin.file.destroy');
});


Route::group(['middleware' => ['assistant']], function () {

    Route::post('/assistant/passwordUpdate', [LoginController::class, 'update'])->name('password.update');

    Route::get('/assistant', [assistantController::class, 'index'])->name('assistant.dashboard');

    //Report
    Route::get('/assistant/report', [ReportController::class, 'assistantIndex'])->name('assistant.report.list');
    Route::post('/assistant/report/post', [ReportController::class, 'report'])->name('assistant.report.generate');

    // exchange user
    Route::get('/assistant/user', [UserController::class, 'assistantindex'])->name('assistant.user.list');
    Route::post('/assistant/user/post', [UserController::class, 'store'])->name('assistant.user.post');
    Route::post('/assistant/user/update', [UserController::class, 'update'])->name('assistant.user.update');
    Route::post('/assistant/user/destroy', [UserController::class, 'destroy'])->name('assistant.user.destroy');
    Route::post('/assistant/user/status', [UserController::class, 'userStatus'])->name('assistant.user.status');


    // exchange
    Route::get('/assistant/exchange', [ExchangeController::class, 'assistantExchangeList'])->name('assistant.exchange.list');
    Route::post('/assistant/exchange/post', [ExchangeController::class, 'store'])->name('assistant.exchange.store');
    Route::post('/assistant/exchange/destroy', [ExchangeController::class, 'destroy'])->name('assistant.exchange.destroy');

    //bank
    Route::get('/assistant/bank', [BankController::class, 'assistantIndex'])->name('assistant.bank.list');
    Route::post('/assistant/bank/post', [BankController::class, 'store'])->name('assistant.bank.store');
    Route::post('/assistant/bank/destroy', [BankController::class, 'destroy'])->name('assistant.bank.destroy');

    //Freez Bank
    Route::get('/assistant/bank/freez', [BankController::class, 'assistantFreezBankIndex'])->name('assistant.bank_freez.list');
    Route::post('/assistant/bank/delete', [BankController::class, 'delete'])->name('assistant.bank.delete');


    // bank user
    Route::get('/assistant/bankUser', [BankUserController::class, 'assistantindex'])->name('assistant.bank_user.list');
    Route::post('/assistant/bankUser/post', [BankUserController::class, 'store'])->name('assistant.bank_user.store');
    Route::post('/assistant/bankUser/destroy', [BankUserController::class, 'destroy'])->name('assistant.bank_user.destroy');

    // deposit withdrawal
    Route::get('/assistant/deposit-withdrawal', [DepositWithdrawalController::class, 'assistantIndex'])->name('assistant.deposit_withdrawal.list');
    Route::post('/assistant/deposit-withdrawal/destroy', [DepositWithdrawalController::class, 'destroy'])->name('assistant.deposit_withdrawal.destroy');

    //expense
    Route::get('/assistant/expense', [ExpenseController::class, 'assistantIndex'])->name('assistant.expense.list');
    Route::post('/assistant/expense/destroy', [ExpenseController::class, 'destroy'])->name('assistant.expense.destroy');

    //bank Balance
    Route::get('/assistant/bankBalance', [BankBalanceController::class, 'assistantIndex'])->name('assistant.bank_balance.list');
    Route::post('/assistant/bankBalance/destroy', [BankBalanceController::class, 'destroy'])->name('assistant.bank_balance.destroy');


    //customer
    Route::get('/assistant/customer', [CustomerController::class, 'assistantIndex'])->name('assistant.customer.list');
    Route::post('/assistant/customer/destroy', [CustomerController::class, 'destroy'])->name('assistant.customer.destroy');

    //Master Settling
    Route::get('/assistant/masterSettling', [MasterSettlingController::class, 'assistantIndex'])->name('assistant.master_settling.list');
    Route::post('/assistant/masterSettling/destroy', [MasterSettlingController::class, 'destroy'])->name('assistant.master_settling.destroy');
    Route::post('/assistant/masterSettling/update', [MasterSettlingController::class, 'update'])->name('assistant.master_settling.update');

    //Owner Profit
    Route::get('/assistant/ownerProfit', [OwnerProfitController::class, 'assistantIndex'])->name('assistant.owner_profit.list');
    Route::post('/assistant/ownerProfit/destroy', [OwnerProfitController::class, 'destroy'])->name('assistant.owner_profit.destroy');


    //Vender Payment
    Route::get('/assistant/vender_payment', [VenderPaymentController::class, 'assistantIndex'])->name('assistant.vender_payment.list');
    Route::post('/assistant/venderPayment/post', [VenderPaymentController::class, 'store'])->name('assistant.vender_payment.store');
    Route::post('/assistant/venderPayment/delete', [VenderPaymentController::class, 'destroy'])->name('assistant.vender_payment.destroy');

    //Vender Payment
    Route::get('/assistant/openCloseBalance', [OpenCloseBalanceController::class, 'index'])->name('assistant.open_close_balance.list');
    Route::post('/assistant/openCloseBalance/destroy', [OpenCloseBalanceController::class, 'destroy'])->name('assistant.open_close_balance.destroy');

    //File Operations
    Route::get('/assistant/file', [ExcelFileController::class, 'assistantIndex'])->name('assistant.file.list');
    Route::post('/assistant/file/post', [ExcelFileController::class, 'store'])->name('assistant.file.post');
    Route::post('/assistant/file/destroy', [ExcelFileController::class, 'destroy'])->name('assistant.file.destroy');

});




Route::group(['middleware' => ['exchange']], function () {

    //Exchange Dashboard
    Route::get('/exchange', [ExchangeController::class, 'index'])->name('exchange.dashboard');

    //Exchange Cash
    Route::get('/exchange/cash', [CashController::class, 'index'])->name('exchange.cash.list');
    Route::post('/exchange/cash/store', [CashController::class, 'store'])->name('exchange.cash.store');
    Route::post('/exchange/cash/destroy', [CashController::class, 'destroy'])->name('exchange.cash.destroy');

    //bank
    Route::get('/exchange/bank', [BankEntryController::class, 'index'])->name('exchange.bank.list');
    Route::get('/exchange/bank/freez', [BankEntryController::class, 'freezBankIndex'])->name('exchange.bank.freezbank');
    Route::post('/exchange/bank/post', [BankEntryController::class, 'store'])->name('exchange.bank.store');
    Route::post('/exchange/bank/balance/post', [BankEntryController::class, 'getBankBalance'])->name('exchange.bank.post');
    Route::post('/exchange/bank/un-freeze', [BankEntryController::class, 'unFreeze'])->name('exchange.bank.un-freeze');

    //customer
    Route::get('/exchange/customer', [CustomerController::class, 'exchangeIndex'])->name('exchange.customer.list');
    Route::post('/exchange/customer/post', [CustomerController::class, 'store'])->name('exchange.customer.store');

    //Owner Profit
    Route::get('/exchange/ownerProfit', [OwnerProfitController::class, 'exchangeIndex'])->name('exchange.owner_profit.list');
    Route::post('/exchange/ownerProfit/post', [OwnerProfitController::class, 'store'])->name('exchange.owner_profit.store');

    // withdrawal withdrawal
    Route::get('/exchange/withdrawal', [WithdrawalController::class, 'index'])->name('exchange.withdrawal.list');
    Route::post('/exchange/withdrawal/approval', [CashController::class, 'approval'])->name('exchange.withdrawal.approval');

    // deposit withdrawal
    Route::get('/exchange/deposit', [DepositController::class, 'index'])->name('exchange.deposit.list');

    //expense
    Route::get('/exchange/expense', [ExpenseController::class, 'exchangeIndex'])->name('exchange.expense.list');

    //master settling
    Route::get('/exchange/masterSettling', [MasterSettlingController::class, 'exchangeIndex'])->name('exchange.master_settling.list');
    Route::post('/exchange/masterSettling/post', [MasterSettlingController::class, 'store'])->name('exchange.master_settling.store');

    //Report
    Route::get('/exchange/report', [ReportController::class, 'exchangeIndex'])->name('exchange.report.list');
    Route::post('/exchange/report/post', [ReportController::class, 'exchangeReport'])->name('exchange.report.generate');

    //open close balance
    Route::get('/exchange/openCloseBalance', [OpenCloseBalanceController::class, 'exchangeIndex'])->name('exchange.open_close_balance.list');
    Route::post('/exchange/openCloseBalance/post', [OpenCloseBalanceController::class, 'store'])->name('exchange.open_close_balance.store');

});
