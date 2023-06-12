<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Models\User;
use App\Models\Product;

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
    return view('welcome');
});

Route::get('/create/{userId}', function ($userId) {
    $user = User::find($userId);
    $product = $user->products()->create([
        'name' => 'Product Title',
        'description' => 'Product Description',
        'price' => 99.99,
        'category' => 'Electronics',
    ]);

});

Route::get('/get/{userId}', function ($userId){
    $user = User::find($userId);
    $products = $user->products()->get();
    return $products;

});

Route::get('/update/{productId}', function ($productId){
    $product = Product::find($productId);
    $product->name = 'New Title';
    $product->save();
    
});

Route::get('/delete/{productId}', function ($productId){
    $product = Product::find($productId);
    $product->delete();

});



