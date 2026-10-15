<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function home(): View
    {
        $products = Product::getFeaturedProducts(3);

        return view('hompage', compact('products'));
    }

    public function index(): View
    {
        $products = Product::getAllProducts();

        return view('products.index', compact('products'));
    }

    public function adminIndex(): View
    {
        $products = Product::getAllProducts();

        return view('admin.products.index', compact('products'));
    }
}
