<?php

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

Route::get('/', function () {
    return view('auth.login');
});

// Registered and Activated User Routes
Route::group(['middleware' => ['auth']], function () {

    Route::get('/logout', ['uses' => 'App\Http\Controllers\Auth\LoginController@logout'])->name('logout');

});

Route::group(['middleware' => ['auth', 'role:superadmin, admin']], function () {
    //Users
    Route::get('usuarios', '\App\Http\Controllers\UserController@showView')->name('usuarios');
    Route::resource('/users', \App\Http\Controllers\UserController::class);

    //Get Roles
    Route::post('roles', '\App\Http\Controllers\UserController@getRoles')->name('roles');

    //Report
    Route::get('reportes', '\App\Http\Controllers\ReportController@showView')->name('reportes');
    Route::get('excel-report', '\App\Http\Controllers\ReportController@exportExcel');
    Route::get('kardex-report', '\App\Http\Controllers\ReportController@exportKardex');
    Route::get('pdf-report', '\App\Http\Controllers\ReportController@exportPDF');
    Route::get('report-livestock-medicines', '\App\Http\Controllers\ReportController@getLivestockMedicine');
     

});

Route::group(['middleware' => ['auth', 'role:trabajador, admin, superadmin']], function () {

    //Livestock
    Route::get('ganado', '\App\Http\Controllers\LivestockController@showView')->name('ganado');
    Route::get('get-livestocks', '\App\Http\Controllers\LivestockController@getLivestocks');
    Route::resource('/livestocks', \App\Http\Controllers\LivestockController::class);
    Route::post('/livestocks-by-name-id', '\App\Http\Controllers\LivestockController@getLivestock');

    //Medicines
    Route::get('medicinas', '\App\Http\Controllers\MedicineController@showView')->name('medicinas');
    Route::get('get-medicines', '\App\Http\Controllers\MedicineController@getMedicines');
    Route::resource('/medicines', \App\Http\Controllers\MedicineController::class);

    //Marks
    Route::get('marcas', '\App\Http\Controllers\MarkController@showView')->name('marcas');
    Route::get('get-marks', '\App\Http\Controllers\MarkController@getMarks');
    Route::resource('/marks', \App\Http\Controllers\MarkController::class);

    //Categories
    Route::get('categorias', '\App\Http\Controllers\CategoryController@showView')->name('categorias');
    Route::get('get-categories', '\App\Http\Controllers\CategoryController@getCategories');
    Route::resource('/categories', \App\Http\Controllers\CategoryController::class);

    //Breeds
    Route::get('razas', '\App\Http\Controllers\BreedController@showView')->name('razas');
    Route::get('get-breeds', '\App\Http\Controllers\BreedController@getBreeds');
    Route::resource('/breeds', \App\Http\Controllers\BreedController::class);

    //Medicines Livestock
    Route::get('aplicar-medicina', '\App\Http\Controllers\LivestockMedicineController@showView')->name('aplicar-medicina');
    Route::resource('/livestock-medicines', \App\Http\Controllers\LivestockMedicineController::class);

    //Medicines Entries
    Route::get('ingresar-medicina', '\App\Http\Controllers\MedicineEntryController@showView')->name('ingresar-medicina');
    Route::resource('/medicine-entries', \App\Http\Controllers\MedicineEntryController::class);
    Route::get('get-medicines-entries', '\App\Http\Controllers\MedicineEntryController@getMedicineEntries');
    
    //Dashboard
    Route::get('show-dashboard', '\App\Http\Controllers\UserController@showDashboard')->name('show-dashboard');
    Route::get('dose-per-medicine', '\App\Http\Controllers\ReportController@dosePerMedicine')->name('dose-per-medicine');  
    Route::get('top-5-animal', '\App\Http\Controllers\ReportController@topAnimalDose')->name('top-5-animal');

    //Kardex
    Route::get('kardex', '\App\Http\Controllers\KardexController@showView')->name('kardex');
    Route::get('get-kardex', '\App\Http\Controllers\KardexController@getKardex');

    //Users
    Route::get('get-users', '\App\Http\Controllers\UserController@getUsers');

});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
