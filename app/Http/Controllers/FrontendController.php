<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function category(int $id): View
    {
        
        $category = Category::findOrFail($id);

        return view('category', [
            'category'   => $category,
            'categories' => Category::all(),
        ]);
    }
}