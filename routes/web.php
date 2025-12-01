<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\ClientProfileController;
use App\Http\Controllers\CommercialAddressController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\VelzonRoutesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    
    Route::controller(EmployeeController::class)->group(function () {
        Route::get('/employees/list', 'index')->name('employees.list');
        Route::post('/employees/store', 'store')->name('employees.store')->middleware('permission:create_users');
        Route::put('/employees/update/{id}', 'update')->name('employees.update')->middleware('permission:update_users');
        Route::get('/employee/profile/{id}', 'employeeProfile')->name('employee.profile');
        Route::get('/profile/setting/{id}', 'pages_profile_setting')->name('profile.setting');
    });

    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::controller(LeaveController::class)->group(function () {
        Route::get('/leave/list', 'index')->name('leave.list');
        Route::post('/leave/store', 'store')->name('leave.store')->middleware('permission:create_leaves');
        Route::post('/leave/update/{id}', 'update')->name('leave.update')->middleware('permission:update_leaves');
        Route::put('/leave/update/status/{id}', 'updateStatus')->name('leave.update.status');
    });

    Route::controller(TaskController::class)->group(function () {
        Route::get('/tasks-list', 'tasksList')->name('tasks.list');
        Route::post('/tasks/store', 'tasksStore')->name('tasks.store')->middleware('permission:create_tasks');
        Route::post('/task/update/{id}', 'tasksUpdate')->name('tasks.update')->middleware('permission:update_tasks');
        Route::get('/task/view/{id}', 'tasksView')->name('tasks.view');
        Route::delete('/tasks/destroy/{id}', 'tasksDestroy')->name('tasks.destroy')->middleware('permission:delete_tasks');
        Route::post('/task/reassign/update/{id}', 'reassign')->name('task.reassign');
    });

    Route::controller(CompanyController::class)->group(function () {
        Route::get('/companies/list', 'companyList')->name('company.list');
        Route::post('/companys/store', 'companysStore')->name('companys.store')->middleware('permission:create_companies');
        Route::post('/company/update/{id}', 'companysUpdate')->name('companys.update')->middleware('permission:update_companies');
        Route::delete('/company/destroy/{id}', 'companysDestroy')->name('companys.destroy')->middleware('permission:delete_companies');
    });

    Route::controller(ClientProfileController::class)->group(function () {
        Route::get('/client/list', 'clientList')->name('client.list');
        Route::post('/clients/store', 'clientsStore')->name('clients.store')->middleware('permission:create_clients');
        Route::post('/client/update/{id}', 'clientUpdate')->name('client.update')->middleware('permission:update_clients');
        Route::delete('/client/destroy/{id}', 'clientDestroy')->name('client.destroy')->middleware('permission:delete_clients');

        Route::get('/client/intractions/list', 'clientIntractionList')->name('client.intraction.list');
        Route::post('/client/quotation/{id}', 'clientQuotation')->name('client.quotation')->middleware('permission:send_quotation');
    });

    Route::controller(CommercialAddressController::class)->group(function () {
        Route::get('/commercial/address', 'commercialAddress')->name('commercial.address');
        Route::post('/commercial/address/store', 'commercialAddressStore')->name('commercial.address.store')->middleware('permission:address_create');
        Route::post('/commercial/address/update/{id}', 'commercialAddressUpdate')->name('commercial.address.update')->middleware('permission:address_update');
        Route::delete('/commercial/address/destroy/{id}', 'commercialAddressDestroy')->name('commercial.address.destroy')->middleware('permission:address_delete');
        Route::get('commercial/address/range', 'rangeReport')->name('commercial.address.range');
    });

    Route::controller(ExpenseController::class)->group(function () {
        Route::get('/expenses', 'index')->name('expense');
        Route::post('/expense/store', 'store')->name('expense.store')->middleware('permission:expense_create');
        Route::post('/expense/update/{id}', 'update')->name('expense.update')->middleware('permission:expense_update');
        Route::post('/expense/approved/{id}', 'expenseStatus')->name('expense.status');

        Route::get('/expense/request', 'expenseRequest')->name('expense.request');
        Route::delete('/expense/destroy/{id}', 'expenseDestroy')->name('expense.destroy')->middleware('permission:expense_delete');
        Route::get('/expense/reports/monthly', 'monthlyReport')->name('reports.monthly');
        Route::get('/financial/logs', 'financiaLogs')->name('financial.logs');
    });

    Route::controller(PermissionController::class)->group(function () {
        Route::get('/permission', 'index')->name('permission');
        Route::post('/permission/store', 'store')->name('permission.store')->middleware('permission:permission_create');
        Route::post('/permission/update/{id}', 'update')->name('permission.update')->middleware('permission:permission_update');
        Route::delete('/permission/destroy/{id}', 'destroy')->name('permission.destroy')->middleware('permission:permission_delete');
    });

    Route::controller(RoleController::class)->group(function () {
        Route::get('/roles', 'index')->name('roles');
        Route::post('/roles/store', 'store')->name('roles.store')->middleware('permission:create_roles');
        Route::post('/roles/update/{id}', 'update')->name('roles.update')->middleware('permission:update_roles');
        Route::delete('/roles/destroy/{id}', 'destroy')->name('roles.destroy')->middleware('permission:delete_roles');
    });

    Route::controller(BalanceController::class)->group(function () {
        Route::get('/balance', 'index')->name('balance');
        Route::post('/balance/store', 'store')->name('balance.store');
        Route::post('/balance/transfer', 'transfer')->name('balance.transfer');
    });

     Route::controller(QuotationController::class)->group(function () {
        Route::get('/quotation', 'index')->name('quotation');
        Route::post('/quotation/store', 'store')->name('quotation.store');
        Route::post('/quotation/update/{id}', 'update')->name('quotation.update');
        Route::delete('/quotation/destroy/{id}', 'destroy')->name('quotation.destroy');
    });

    Route::get('/invoice/download/{id}', [InvoiceController::class, 'download'])->name('invoice.download');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::controller(VelzonRoutesController::class)->group(function () {

        // dashboards
        // Route::get('/', 'dashboard');

        // icons route
        Route::get('/icons/boxicons', 'icons_boxicons');
        Route::get('/icons/materialdesign', 'icons_materialdesign');
        Route::get('/icons/feather', 'icons_feather');
        Route::get('/icons/lineawesome', 'icons_lineawesome');
        Route::get('/icons/remix', 'icons_remix');
        Route::get('/icons/crypto', 'icons_crypto');

        // apps project routes
        Route::get('/apps/projects-list', 'projects_list');
        Route::get('/apps/projects-overview', 'projects_overview');
        Route::get('/apps/projects-create', 'projects_create');

        // apps task routes
        Route::get('/apps/tasks-details', 'tasks_details');
        Route::get('/apps/tasks-kanban', 'tasks_kanban');

        // apps tickets routes
        Route::get('/apps/tickets-details', 'tickets_details');
        Route::get('/apps/tickets-list', 'tickets_list');

        // apps other routes
        Route::get('/apps/crm-contacts', 'crm_contacts');
        Route::get('/apps/crm-companies', 'crm_companies');
        Route::get('/apps/crm-deals', 'crm_deals');
        Route::get('/apps/crm-leads', 'crm_leads');

        // apps crypto routes
        Route::get('/crypto/buy-sell', 'crypto_buy_sell');
        Route::get('/crypto/kyc', 'crypto_kyc');
        Route::get('/crypto/ico', 'crypto_ico');
        Route::get('/crypto/orders', 'crypto_orders');
        Route::get('/crypto/wallet', 'crypto_wallet');
        Route::get('/crypto/transactions', 'crypto_transactions');

        // apps invoice routes
        Route::get('/invoices/detail', 'invoices_detail');
        Route::get('/invoices/list', 'invoices_list');
        Route::get('/invoices/create', 'invoices_create');

        // apps jobs routes
        Route::get('/jobs/application', 'jobs_application');
        Route::get('/jobs/candidate-grid', 'jobs_candidate_grid');
        Route::get('/jobs/candidate-lists', 'jobs_candidate_lists');
        Route::get('/jobs/categories', 'jobs_categories');
        Route::get('/jobs/companies-list', 'jobs_companies_list');
        Route::get('/jobs/details', 'jobs_details');
        Route::get('/jobs/grid-lists', 'jobs_grid');
        Route::get('/jobs/lists', 'jobs_lists');
        Route::get('/jobs/new', 'jobs_new');
        Route::get('/jobs/statistics', 'jobs_statistics');

        // apps api key route
        Route::get('/apps-api-key', 'apps_api_key');

        Route::get('/tables/basic', 'tables_basic');
        Route::get('/tables/gridjs', 'tables_gridjs');

        // landing routes
        Route::get('/landing', 'landing');
        Route::get('/nft-landing', 'nft_landing');
        Route::get('/job-landing', 'job_landing');

        // auth sample page routes
        Route::get('/auth/signin-basic', 'auth_signin_basic');
        Route::get('/auth/signin-cover', 'auth_signin_cover');
        Route::get('/auth/signup-basic', 'auth_signup_basic');
        Route::get('/auth/signup-cover', 'auth_signup_cover');
        Route::get('/auth/reset-pwd-basic', 'auth_reset_pwd_basic');
        Route::get('/auth/reset-pwd-cover', 'auth_reset_pwd_cover');
        Route::get('/auth/create-pwd-basic', 'auth_create_pwd_basic');
        Route::get('/auth/create-pwd-cover', 'auth_create_pwd_cover');
        Route::get('/auth/lockscreen-basic', 'auth_lockscreen_basic');
        Route::get('/auth/lockscreen-cover', 'auth_lockscreen_cover');
        Route::get('/auth/twostep-basic', 'auth_twostep_basic');
        Route::get('/auth/twostep-cover', 'auth_twostep_cover');
        Route::get('/auth/404', 'auth_404');
        Route::get('/auth/500', 'auth_500');
        Route::get('/auth/404-basic', 'auth_404_basic');
        Route::get('/auth/404-cover', 'auth_404_cover');
        Route::get('/auth/ofline', 'auth_ofline');
        Route::get('/auth/logout-basic', 'auth_logout_basic');
        Route::get('/auth/logout-cover', 'auth_logout_cover');
        Route::get('/auth/success-msg-basic', 'auth_success_msg_basic');
        Route::get('/auth/success-msg-cover', 'auth_success_msg_cover');

        // maps routes
        Route::get('/maps/amcharts', 'maps_amcharts');
        Route::get('/maps/google', 'maps_google');
        Route::get('/maps/leaflet', 'maps_leaflet');
    });
});
