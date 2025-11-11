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

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Web Register Route
|--------------------------------------------------------------------------
*/
Route::resource('user', RegisteredUserController::class);
Route::post('user/assign/role', [UserController::class, 'assignRole']);

Route::get('/', [HotelioController::class, 'index']);

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Room Management Routes
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/create', [RoomController::class, 'create'])->name('create');
        Route::post('/', [RoomController::class, 'store'])->name('store');
        Route::get('/{id}', [RoomController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::put('/{id}', [RoomController::class, 'update'])->name('update');
        Route::delete('/{id}', [RoomController::class, 'destroy'])->name('destroy');
        Route::delete('/', [RoomController::class, 'deleteAll'])->name('deleteAll');

        // DataTables Route
        Route::get('/data/get', [RoomController::class, 'getRooms'])->name('data.get');

        // Trash Routes
        Route::get('/trash/index', [RoomController::class, 'trash'])->name('trash.index');
        Route::post('/trash/{id}/restore', [RoomController::class, 'restore'])->name('trash.restore');
        Route::delete('/trash/{id}/force', [RoomController::class, 'forceDelete'])->name('trash.forceDelete');
    });

    // Booking Management Routes
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::get('/create', [BookingController::class, 'create'])->name('create');
        Route::post('/', [BookingController::class, 'store'])->name('store');
        Route::get('/{id}', [BookingController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BookingController::class, 'update'])->name('update');
        Route::delete('/{id}', [BookingController::class, 'destroy'])->name('destroy');
        Route::delete('/', [BookingController::class, 'deleteAll'])->name('deleteAll');

        // Additional booking routes
        Route::post('/{id}/checkin', [BookingController::class, 'checkIn'])->name('checkin');
        Route::post('/{id}/checkout', [BookingController::class, 'checkOut'])->name('checkout');
        Route::post('/{id}/cancel', [BookingController::class, 'cancel'])->name('cancel');
        Route::post('/{id}/payment', [BookingController::class, 'addPayment'])->name('payment');

        // DataTables Route
        Route::get('/data/get', [BookingController::class, 'getBookings'])->name('data.get');

        // Check room availability
        Route::post('/check-availability', [BookingController::class, 'checkAvailability'])->name('check-availability');

        // Trash Routes
        Route::get('/trash/index', [BookingController::class, 'trash'])->name('trash.index');
        Route::post('/trash/{id}/restore', [BookingController::class, 'restore'])->name('trash.restore');
        Route::delete('/trash/{id}/force', [BookingController::class, 'forceDelete'])->name('trash.forceDelete');
    });

    // Transaction Routes
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::get('/{id}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('/{id}', [TransactionController::class, 'update'])->name('transactions.update');
        Route::delete('/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
        Route::get('/trash/list', [TransactionController::class, 'trash'])->name('transactions.trash');
        Route::post('/{id}/restore', [TransactionController::class, 'restore'])->name('transactions.restore');
        Route::delete('/{id}/force-delete', [TransactionController::class, 'forceDelete'])->name('transactions.forceDelete');
        Route::delete('/trash/empty', [TransactionController::class, 'emptyTrash'])->name('transactions.emptyTrash');
        Route::get('/income-report', [TransactionController::class, 'incomeReport'])->name('transactions.incomeReport');
        Route::get('/expense-report', [TransactionController::class, 'expenseReport'])->name('transactions.expenseReport');
        Route::get('/financial-summary', [TransactionController::class, 'financialSummary'])->name('transactions.financialSummary');
    });


    // Inventory Management Routes
    // Inventory Management Routes
    Route::get('/inventory/data', [InventoryController::class, 'getDataTable'])->name('inventory.data');
    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    Route::get('/inventory/trash', [InventoryController::class, 'trash'])->name('inventory.trash');
    Route::post('/inventory/{id}/restore', [InventoryController::class, 'restore'])->name('inventory.restore');
    Route::post('/inventory/{id}/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');
    Route::delete('/inventory/{id}/force-delete', [InventoryController::class, 'forceDelete'])->name('inventory.force-delete');
    Route::delete('/inventory/delete-all', [InventoryController::class, 'deleteAll'])->name('inventory.delete-all');

    // This should be LAST to avoid route conflicts
    Route::resource('inventory', InventoryController::class);
    /*
    |--------------------------------------------------------------------------
    | Web RoomTansfer Route
    |--------------------------------------------------------------------------
    */
    // Route::get('roomTransfer/trash', [RoomTransferController::class, 'trash']);
    // Route::get('/roomTransfer/delete', [RoomTransferController::class, 'destroyAll']);
    // Route::get('roomTransfer/{id}/restore', [RoomTransferController::class, 'restore']);
    // Route::get('roomTransfer/restoreAll', [RoomTransferController::class, 'restoreAll']);
    // Route::get('/roomTransfer/{id}/parmanently/delete', [RoomTransferController::class, 'forceDeleted']);
    // Route::get('/roomTransfer/emptyTrash', [RoomTransferController::class, 'emptyTrash']);
    // Route::get('/roomTransfer/delete/{id}', [RoomTransferController::class, 'destroy']);
    // Route::resource('roomTransfer', RoomTransferController::class);



    // Trash management routes
    Route::get('/trash/list', [BookingController::class, 'trash'])->name('bookings.trash');
    Route::get('/{id}/restore', [BookingController::class, 'restore'])->name('bookings.restore');
    Route::delete('/{id}/force-delete', [BookingController::class, 'forceDeleted'])->name('bookings.forceDeleted');
    Route::delete('/trash/empty', [BookingController::class, 'emptyTrash'])->name('bookings.emptyTrash');
    Route::delete('/delete/all', [BookingController::class, 'destroyAll'])->name('bookings.destroyAll');
    Route::get('/restore/all', [BookingController::class, 'restoreAll'])->name('bookings.restoreAll');

    // AJAX routes
    Route::get('/check/availability', [BookingController::class, 'checkAvailability'])->name('bookings.checkAvailability');
});


/*
    |--------------------------------------------------------------------------
    | Web Guests Route
    |--------------------------------------------------------------------------
    */
// Route::get('/guest/trash', [GuestController::class, 'trash']);
// Route::get('/guest/delete', [GuestController::class, 'destroyAll']);
// Route::get('/guest/{id}/restore', [GuestController::class, 'restore']);
// Route::get('/guest/restoreAll', [GuestController::class, 'restoreAll']);
// Route::get('/guest/parmanently/delete/{id}', [GuestController::class, 'forceDelete']);
// Route::get('/guest/emptyTrash', [GuestController::class, 'emptyTrash']);
// Route::get('/guest/delete/{id}',[GuestController::class,'destroy']);
// Route::resource('guest', GuestController::class);

/*
    |--------------------------------------------------------------------------
    | Web Emplooyee Route
    |--------------------------------------------------------------------------
    */
// Employee Management Routes
Route::prefix('employees')->name('employees.')->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('index');
    Route::get('/create', [EmployeeController::class, 'create'])->name('create');
    Route::post('/', [EmployeeController::class, 'store'])->name('store');
    Route::get('/{id}', [EmployeeController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('edit');
    Route::put('/{id}', [EmployeeController::class, 'update'])->name('update');
    Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('destroy');
    Route::delete('/', [EmployeeController::class, 'deleteAll'])->name('deleteAll');

    // Additional employee routes
    Route::post('/{id}/status', [EmployeeController::class, 'updateStatus'])->name('status');

    // DataTables Route
    Route::get('/data/get', [EmployeeController::class, 'getEmployees'])->name('data.get');

    // Trash Routes
    Route::get('/trash/index', [EmployeeController::class, 'trash'])->name('trash.index');
    Route::post('/trash/{id}/restore', [EmployeeController::class, 'restore'])->name('trash.restore');
    Route::delete('/trash/{id}/force', [EmployeeController::class, 'forceDelete'])->name('trash.forceDelete');
});

// Housekeeping Management Routes
Route::prefix('housekeeping')->name('housekeeping.')->group(function () {
    Route::get('/', [HousekeepingController::class, 'index'])->name('index');
    Route::get('/create', [HousekeepingController::class, 'create'])->name('create');
    Route::post('/', [HousekeepingController::class, 'store'])->name('store');

    // Dashboard and Calendar routes - MUST come before parameterized routes
    Route::get('/dashboard', [HousekeepingController::class, 'dashboard'])->name('dashboard');
    Route::get('/calendar', [HousekeepingController::class, 'calendar'])->name('calendar');

    // DataTables Route
    Route::get('/data/get', [HousekeepingController::class, 'getTasks'])->name('data.get');

    // Parameterized routes - MUST come after specific routes
    Route::get('/{id}', [HousekeepingController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [HousekeepingController::class, 'edit'])->name('edit');
    Route::put('/{id}', [HousekeepingController::class, 'update'])->name('update');
    Route::delete('/{id}', [HousekeepingController::class, 'destroy'])->name('destroy');

    // Additional housekeeping routes
    Route::post('/{id}/start', [HousekeepingController::class, 'startTask'])->name('start');
    Route::post('/{id}/complete', [HousekeepingController::class, 'completeTask'])->name('complete');
    Route::post('/{id}/cancel', [HousekeepingController::class, 'cancelTask'])->name('cancel');

    Route::delete('/', [HousekeepingController::class, 'deleteAll'])->name('deleteAll');

    // Trash Routes
    Route::get('/trash/index', [HousekeepingController::class, 'trash'])->name('trash.index');
    Route::post('/trash/{id}/restore', [HousekeepingController::class, 'restore'])->name('trash.restore');
    Route::delete('/trash/{id}/force', [HousekeepingController::class, 'forceDelete'])->name('trash.forceDelete');
});
/*
    |--------------------------------------------------------------------------
    | Web User Route
    |--------------------------------------------------------------------------
    */
Route::get('/user/delete/{id}', [UserController::class, 'destroy']);
Route::get('/user/delete', [UserController::class, 'destroyAll']);
Route::resource('user', UserController::class);


/*
    |--------------------------------------------------------------------------
    | Web profile Route
    |--------------------------------------------------------------------------
    */
Route::get('profile/show', [ProfileController::class, 'index']);
