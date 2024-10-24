<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Prompt;
use Illuminate\Http\Request;

class CartController extends Controller {

    public function addToCart(Request $request) {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'You must be logged in to add to cart'], 401);
        }

        $promptId = $request->input('prompt_id');

        $userId = auth()->id();

        $cartItem = Cart::where('user_id', $userId)
            ->where('prompt_id', $promptId)
            ->first();

        if ($cartItem) {
            return response()->json(['success' => false, 'message' => 'Item is already in the cart']);
        }

        $prompt = Prompt::find($promptId);

        if ($userId == $prompt->user_id) {
            return response()->json(['success' => false, 'message' => 'You cannot add to cart own prompt'], 400);
        }

        $cart = new Cart;
        $cart->user_id = $userId;
        $cart->prompt_id = $promptId;
        $cart->price = $prompt->price;
        $cart->save();

        return response()->json(['success' => true, 'message' => 'Added to cart successfully']);
    }

    public function cartCount() {

        $userId = auth()->id();
        $cartCount = Cart::where('user_id', $userId)->count();

        return response()->json(['success' => true, 'cart_count' => $cartCount]);
    }

    public function viewCart() {
        if (!auth()->check()) {
            return redirect()->route('user.login');
        }
        $pageTitle = 'Cart';
        $userId = auth()->id();

        $cartItems = Cart::where('user_id', $userId)
            ->with('prompt')
            ->get();

        $subtotal = $cartItems->sum('price');

        $total = $subtotal;

        return view('Template::prompt.cart', compact('cartItems', 'subtotal', 'total', 'pageTitle'));
    }

    public function deleteCart($id) {
        $userId = auth()->id();
        $cartItem = Cart::where('id', $id)->where('user_id', $userId)->first();

        if (!$cartItem) {
            return redirect()->back()->with('error', 'Item not found or unauthorized action');
        }

        $cartItem->delete();

        session()->forget('coupon');

        $notify[] = ['success', 'Item removed from cart successfully'];
        return back()->withNotify($notify);
    }

    public function clearCart() {
        $userId = auth()->id();

        $cartItems = Cart::where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            $notify[] = ['error', 'No items in the cart'];
            return back()->withNotify($notify);
        }

        Cart::where('user_id', $userId)->delete();

        session()->forget('coupon');

        $notify[] = ['success', 'Cart cleared successfully'];
        return back()->withNotify($notify);
    }
}
