<?php

namespace App\Http\Controllers;

use App\Models\Product; // Assuming you have a Product model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        // In a real application, add authorization checks
        $products = Product::all();
        return response()->json($products);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        // In a real application, add authorization checks

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            // Add validation rules for other product fields like image
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get all validated data
        $data = $validator->validated();

        // Log the validated data before handling image
        Log::info('Validated data before image handling:', $data);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/images');
            // Store the relative path (e.g., images/imagename.jpg) in the database
            $data['image'] = str_replace('public/', '', $imagePath);
        }

        // Log the data before creating the product
        Log::info('Data for product creation:', $data);

        // Create the product using the combined data
        $product = Product::create($data);

        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product) // Route model binding
    {
        // In a real application, add authorization checks
        return response()->json($product);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product) // Route model binding
    {
        // In a real application, add authorization checks

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            // Add validation rules for other product fields like image
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

         $product->update($request->validated()); // Assuming validated() works with Validator

        return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product) // Route model binding
    {
        // In a real application, add authorization checks

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
} 