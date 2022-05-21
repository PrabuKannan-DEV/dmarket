<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesTest extends TestCase
{
    use RefreshDatabase;
    public function test_a_customer_can_buy_products()
    {
        $customer = Customer::create([
           'name'=> 'Kandhasamy',
           'phone'=>'8888888888'
        ]);

        $all_products = Product::insert([
            ['name'=>'Rice', 'price'=>5],
            ['name'=>'Soap', 'price'=>5],
            ['name'=>'Marker', 'price'=>5],
            ['name'=>'Shampoo', 'price'=>5],
            ['name'=>'Laptop', 'price'=>5],
            ['name'=>'Keychain', 'price'=>5],
        ]);

        $buying_products = Product::whereIn('name', ['Rice', 'Soap', 'Shampoo'])->get();

        $cart = $customer->carts()->create();

        $cart->addItems($buying_products);

        $bill = $cart->checkout();

        $this->assertEquals(15, $bill->total);
    }

    public function test_a_customer_can_buy_products_multiple_times()
    {
        $customer = Customer::create([
           'name'=> 'Kandhasamy',
           'phone'=>'8888888888'
        ]);

        $all_products = Product::insert([
            ['name'=>'Rice', 'price'=>5],
            ['name'=>'Soap', 'price'=>5],
            ['name'=>'Marker', 'price'=>5],
            ['name'=>'Shampoo', 'price'=>5],
            ['name'=>'Laptop', 'price'=>5],
            ['name'=>'Keychain', 'price'=>15],
        ]);

        $buying_products_1 = Product::whereIn('name', ['Rice', 'Soap', 'Shampoo'])->get();

        $cart_1 = $customer->carts()->create();

        $cart_1->addItems($buying_products_1);

        $bill_1 = $cart_1->checkout();

        $this->assertEquals(15, $bill_1->total);

        $buying_products_2 = Product::whereIn('name', ['Marker', 'Soap', 'Keychain'])->get();

        $cart_2 = $customer->carts()->create();

        $cart_2->addItems($buying_products_2);

        $bill_2 = $cart_2->checkout();

        $this->assertEquals(25, $customer->carts->sortByDesc('id')->first()->total);
        $this->assertEquals(15, $customer->carts->first()->total);
    }
}
