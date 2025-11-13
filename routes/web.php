<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\HotelioController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTransferController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\Admin\RolePermissionController;
use Ramsey\Uuid\Guid\Guid;

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
use App\Http\Controllers\PaymentController;

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::resource('user', RegisteredUserController::class);
Route::post('user/assign/role', [UserController::class, 'assignRole']);

Route::get('/', [HotelioController::class, 'index']);


Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    // Users & Roles
    Route::get('users', [RolePermissionController::class, 'index'])->name('roles.index');
    Route::get('users/{id}/edit', [RolePermissionController::class, 'editUserRoles'])->name('roles.edit');
    Route::post('users/{id}/update', [RolePermissionController::class, 'updateUserRoles'])->name('roles.update');

    // Roles & Permissions
    Route::get('roles', [RolePermissionController::class, 'roles'])->name('roles.roles');
    Route::post('roles/{id}/permissions', [RolePermissionController::class, 'updateRolePermissions'])->name('roles.permissions.update');
});


Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    /*
    |--------------------------------------------------------------------------
    | Room Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('rooms', RoomController::class);
    Route::delete('rooms/delete-all', [RoomController::class, 'destroyAll'])->name('rooms.destroy.all');

    Route::prefix('rooms/trash')->group(function () {
        Route::get('/', [RoomController::class, 'trashIndex'])->name('rooms.trash.index');
        Route::post('/{id}/restore', [RoomController::class, 'trashRestore'])->name('rooms.trash.restore');
        Route::delete('/{id}/destroy', [RoomController::class, 'trashDestroy'])->name('rooms.trash.destroy');
        Route::delete('/empty', [RoomController::class, 'trashEmpty'])->name('rooms.trash.empty');
    });

    /*
    |--------------------------------------------------------------------------
    | Booking Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{id}', [BookingController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BookingController::class, 'update'])->name('update');
        Route::delete('/{id}', [BookingController::class, 'destroy'])->name('destroy');
        Route::delete('/', [BookingController::class, 'deleteAll'])->name('deleteAll');

        Route::post('/{id}/checkin', [BookingController::class, 'checkIn'])->name('checkin');
        Route::post('/{id}/checkout', [BookingController::class, 'checkOut'])->name('checkout');
        Route::post('/{id}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        Route::post('/{id}/payment', [BookingController::class, 'addPayment'])->name('payment');

        Route::get('/data/get', [BookingController::class, 'getBookings'])->name('data.get');
        Route::post('/check-availability', [BookingController::class, 'checkAvailability'])->name('check-availability');

        Route::get('/trash/index', [BookingController::class, 'trash'])->name('trash.index');
        Route::post('/trash/{id}/restore', [BookingController::class, 'restore'])->name('trash.restore');
        Route::delete('/trash/{id}/force', [BookingController::class, 'forceDelete'])->name('trash.forceDelete');
    });

    /*
    |--------------------------------------------------------------------------
    | Transaction Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/create', [TransactionController::class, 'create'])->name('create');
        Route::post('/', [TransactionController::class, 'store'])->name('store');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::get('/{id}/edit', [TransactionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TransactionController::class, 'update'])->name('update');
        Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('destroy');

        Route::get('/trash/list', [TransactionController::class, 'trash'])->name('trash');
        Route::post('/{id}/restore', [TransactionController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [TransactionController::class, 'forceDelete'])->name('forceDelete');
        Route::delete('/trash/empty', [TransactionController::class, 'emptyTrash'])->name('emptyTrash');

        Route::get('/income-report', [TransactionController::class, 'incomeReport'])->name('incomeReport');
        Route::get('/expense-report', [TransactionController::class, 'expenseReport'])->name('expenseReport');
        Route::get('/financial-summary', [TransactionController::class, 'financialSummary'])->name('financialSummary');
    });

    /*
    |--------------------------------------------------------------------------
    | Inventory Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/inventory/data', [InventoryController::class, 'getDataTable'])->name('inventory.data');
    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    Route::get('/inventory/trash', [InventoryController::class, 'trash'])->name('inventory.trash');
    Route::post('/inventory/{id}/restore', [InventoryController::class, 'restore'])->name('inventory.restore');
    Route::post('/inventory/{id}/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');
    Route::delete('/inventory/{id}/force-delete', [InventoryController::class, 'forceDelete'])->name('inventory.force-delete');
    Route::delete('/inventory/delete-all', [InventoryController::class, 'deleteAll'])->name('inventory.delete-all');
    Route::resource('inventory', InventoryController::class);

    /*
    |--------------------------------------------------------------------------
    | Employee Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{id}', [EmployeeController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EmployeeController::class, 'update'])->name('update');
        Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::delete('/', [EmployeeController::class, 'deleteAll'])->name('deleteAll');

        Route::post('/{id}/status', [EmployeeController::class, 'updateStatus'])->name('status');
        Route::get('/data/get', [EmployeeController::class, 'getEmployees'])->name('data.get');

        Route::get('/trash/index', [EmployeeController::class, 'trash'])->name('trash.index');
        Route::post('/trash/{id}/restore', [EmployeeController::class, 'restore'])->name('trash.restore');
        Route::delete('/trash/{id}/force', [EmployeeController::class, 'forceDelete'])->name('trash.forceDelete');
    });

    /*
    |--------------------------------------------------------------------------
    | Housekeeping Routes
    |--------------------------------------------------------------------------
    */
    // Housekeeping Routes
    Route::prefix('housekeeping')->group(function () {
        // Main CRUD routes
        Route::get('/', [HousekeepingController::class, 'index'])->name('housekeeping.index');
        Route::get('/create', [HousekeepingController::class, 'create'])->name('housekeeping.create');
        Route::post('/', [HousekeepingController::class, 'store'])->name('housekeeping.store');
        Route::get('/{housekeeping}/edit', [HousekeepingController::class, 'edit'])->name('housekeeping.edit');
        Route::put('/{housekeeping}', [HousekeepingController::class, 'update'])->name('housekeeping.update');
        Route::get('/{housekeeping}', [HousekeepingController::class, 'show'])->name('housekeeping.show');

        // Delete routes
        Route::delete('/{housekeeping}', [HousekeepingController::class, 'destroy'])->name('housekeeping.destroy');

        // Status update routes
        Route::post('/{housekeeping}/in-progress', [HousekeepingController::class, 'markInProgress'])->name('housekeeping.in-progress');
        Route::post('/{housekeeping}/complete', [HousekeepingController::class, 'markCompleted'])->name('housekeeping.complete');
        Route::post('/{housekeeping}/cancel', [HousekeepingController::class, 'cancel'])->name('housekeeping.cancel');

        // Dashboard and reports
        Route::get('/dashboard', [HousekeepingController::class, 'dashboard'])->name('housekeeping.dashboard');
        Route::get('/tasks/{date}', [HousekeepingController::class, 'getTasksByDate'])->name('housekeeping.tasks-by-date');
        // Trash management routes
        Route::get('/trash/index', [HousekeepingController::class, 'trashIndex'])->name('housekeeping.trash.index');
        Route::post('/trash/{housekeeping}/restore', [HousekeepingController::class, 'trashRestore'])->name('housekeeping.trash.restore');
        Route::delete('/trash/{housekeeping}/destroy', [HousekeepingController::class, 'trashDestroy'])->name('housekeeping.trash.destroy');
        Route::delete('/trash/empty', [HousekeepingController::class, 'trashEmpty'])->name('housekeeping.trash.empty');
    });

    /*
    |--------------------------------------------------------------------------
    | Payment Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/booking/{booking}/create', [PaymentController::class, 'create'])->name('create');
        Route::post('/booking/{booking}', [PaymentController::class, 'store'])->name('store');
        Route::get('/{payment}', [PaymentController::class, 'show'])->name('show');
        Route::delete('/{payment}', [PaymentController::class, 'destroy'])->name('destroy');
        Route::get('/{payment}/download-proof', [PaymentController::class, 'downloadProof'])->name('download-proof');
    });

    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/user/delete/{id}', [UserController::class, 'destroy']);
    Route::get('/user/delete', [UserController::class, 'destroyAll']);
    Route::resource('user', UserController::class);

    /*
    |--------------------------------------------------------------------------
    | Profile Route
    |--------------------------------------------------------------------------
    */
    Route::get('profile/show', [ProfileController::class, 'index']);
}); // <-- ✅ this closes the main auth group properly
