<?php

namespace App\Models;

use App\Models\CartItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function checkout()
    {
        $cart_items = $this->cartItems;
        $total = $cart_items->pluck('cost')->sum();
        $this->update([
            'total'=> $total,
            'status'=>'CLOSED'
        ]);

        return $this;
    }

    public function addItems($products)
    {
        $quantity = 1;
        foreach ($products as $product) {
            $this->cartItems()->create([
                'product_id'=> $product->id,
                'quantity' => $quantity,
                'cost' => $product->price * $quantity
            ]);
        }
    }
}
