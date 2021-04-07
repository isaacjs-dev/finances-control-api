<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\UserWalletController;
use App\Http\Controllers\InflowController;
use App\Http\Controllers\OutflowController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/ping', function () {
    return ['pong' => true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);


Route::post('/user', [UserController::class, 'createUser']);
Route::get('/users', [UserController::class, 'readAllUsers']);
Route::get('/user/{id}', [UserController::class, 'readUser']);
Route::put('/user', [UserController::class, 'updateUser']);
Route::delete('/user/{id}', [UserController::class, 'deleteUser']);


Route::post('/wallet', [WalletController::class, 'createWallet']);
Route::get('/wallets', [WalletController::class, 'readAllWallets']);
Route::get('/wallet/{id}', [WalletController::class, 'readWallet']);
Route::put('/wallet/{id}', [WalletController::class, 'updateWallet']);
Route::delete('/wallet/{id}', [WalletController::class, 'deleteWallet']);

//Incompleto
Route::post('/user_wallet/', [UserWalletController::class, 'createUserWallet']);
//Route::put('/user_wallet/{id}', [UserWalletController::class, 'updateUserWallet']); //falta
Route::delete('/user_wallet/{id}', [UserWalletController::class, 'deleteUserWallet']);


//Saidas são relacionadas as Carteiras
Route::post('/inflow', [InflowController::class, 'createInflow']);
Route::get('/inflows', [InflowController::class, 'readAllInflows']); //Lista todos indepedente da carteira
Route::get('/inflows/{idWallet}', [InflowController::class, 'readAllInflowsWallet']); //Lista todos de uma carteira
Route::get('/inflow/{id}', [InflowController::class, 'readInflow']); //lista uma unica entrada
Route::put('/inflow/{id}', [InflowController::class, 'updateInflow']);
Route::delete('/inflow/{id}', [InflowController::class, 'deleteInflow']);


//Entradas são relacionadas as Carteiras
Route::post('/outflow', [OutflowController::class, 'createOutflow']);
Route::get('/outflows', [OutflowController::class, 'readAllOutflows']); //Lista todos indepedente da carteira
Route::get('/outflows/{idWallet}', [OutflowController::class, 'readAllOutflowsWallet']); //Lista todos de uma carteira
Route::get('/outflow/{id}', [OutflowController::class, 'readOutflow']); //lista uma unica entrada
Route::put('/outflow/{id}', [OutflowController::class, 'updateOutflow']);
Route::delete('/outflow/{id}', [OutflowController::class, 'deleteOutflow']);


Route::post('/type-pay', [TypePayController::class, 'createTypePay']);
Route::get('/types-pay', [TypePayController::class, 'readAllTypesPay']);
Route::get('/type-pay/{id}', [TypePayController::class, 'readTypePay']);
Route::put('/type-pay/{id}', [TypePayController::class, 'updateTypePay']);
Route::delete('/type-pay/{id}', [TypePayController::class, 'deleteTypePay']);


Route::post('/card', [CardController::class, 'createCard']);
Route::get('/cards', [CardController::class, 'readAllCards']);
Route::get('/card/{id}', [CardController::class, 'readCard']);
Route::put('/card/{id}', [CardController::class, 'updateCard']);
Route::delete('/card/{id}', [CardController::class, 'deleteCard']);
