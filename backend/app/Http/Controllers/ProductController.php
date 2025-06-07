<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products (public endpoint)
     */
    public function index()
    {
        $products = Product::all();
        
        // Transform the product data to include full image URLs
        $products = $products->map(function ($product) {
            if ($product->image) {
                $product->image = asset('storage/' . $product->image);
            }
            return $product;
        });
        
        return response()->json($products);
    }

    /**
     * Display a specific product (public endpoint)
     */
    public function show(Product $product) // Route model binding
    {
        // In a real application, add authorization checks
        
        // Return the product with the full image URL
        if ($product->image) {
            $product->image = asset('storage/' . $product->image);
        }
        
        return response()->json($product);
    }

    /**
     * Display a listing of products for admin (authenticated endpoint)
     */
    public function adminIndex(): JsonResponse
    {
        try {
            $products = Product::all();
            
            return response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch products',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
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
            $imagePath = $request->file('image')->store('admin/products', 'public');
            // Store the relative path (e.g., admin/products/imagename.jpg) in the database
            $data['image'] = $imagePath;
        }

        // Log the data before creating the product
        Log::info('Data for product creation:', $data);

        // Create the product using the combined data
        $product = Product::create($data);

        // Return the product with the full image URL
        if ($product->image) {
            $product->image = asset('storage/' . $product->image);
        }

        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product) // Route model binding
    {
        // In a real application, add authorization checks

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Handle image upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            // Store new image - FIXED: use consistent path 'admin/products'
            $imagePath = $request->file('image')->store('admin/products', 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);

        // Return the product with the full image URL
        if ($product->image) {
            $product->image = asset('storage/' . $product->image);
        }

        return response()->json(['message' => 'Product updated successfully', 'product' => $product]);
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product) // Route model binding
    {
        // In a real application, add authorization checks

        // Delete the image file if it exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
