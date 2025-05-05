<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:renter')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $listings = Listing::with('user')->latest()->paginate(10);
        return view('listings.index', compact('listings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('listings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'formatted_address' => 'required|string|max:255',
            'banner_image' => 'required|image|max:2048',
            'additional_images.*' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('banner_image')) {
            $bannerName = time() . '_' . uniqid() . '.' . $request->file('banner_image')->getClientOriginalExtension();
            $bannerPath = $request->file('banner_image')->storeAs('listings/banners', $bannerName, 'public');
            $validated['banner_image'] = $bannerPath;
        }

        $listing = auth()->user()->listings()->create($validated);

        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $index => $image) {
                $imageName = time() . '_' . uniqid() . '_' . $index . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('listings/additional', $imageName, 'public');
                $listing->images()->create([
                    'image_path' => $path,
                    'order' => $index,
                ]);
            }
        }

        $routePrefix = auth()->user()->role;
        return redirect()->route($routePrefix . '.listings.show', $listing)
            ->with('success', 'Listing created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        return view('listings.show', compact('listing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Listing $listing)
    {
        $this->authorize('update', $listing);
        return view('listings.edit', compact('listing'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'formatted_address' => 'required|string|max:255',
            'banner_image' => 'nullable|image|max:2048',
            'additional_images.*' => 'nullable|image|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:listing_images,id',
        ]);

        // Handle banner image
        if ($request->hasFile('banner_image')) {
            // Delete old banner image if it exists
            if ($listing->banner_image) {
                Storage::disk('public')->delete($listing->banner_image);
            }
            $bannerImage = $request->file('banner_image');
            $bannerName = time() . '_' . uniqid() . '.' . $bannerImage->getClientOriginalExtension();
            $bannerPath = $bannerImage->storeAs('listings/banners', $bannerName, 'public');
            $validated['banner_image'] = $bannerPath;
        } elseif ($request->has('remove_banner_image')) {
            // Delete old banner image if it exists
            if ($listing->banner_image) {
                Storage::disk('public')->delete($listing->banner_image);
            }
            $validated['banner_image'] = null;
        }

        // Update the listing first
        $listing->update($validated);

        // Handle additional images deletion
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = $listing->images()->where('id', $imageId)->first();
                if ($image) {
                    // Delete the file from storage
                    if (Storage::disk('public')->exists($image->image_path)) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                    // Delete from database
                    $image->delete();
                }
            }
        }

        // Handle new additional images
        if ($request->hasFile('additional_images')) {
            $currentMaxOrder = $listing->images()->max('order') ?? -1;
            foreach ($request->file('additional_images') as $index => $image) {
                $imageName = time() . '_' . uniqid() . '_' . $index . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('listings/additional', $imageName, 'public');
                $listing->images()->create([
                    'image_path' => $path,
                    'order' => ++$currentMaxOrder,
                ]);
            }
        }

        $routePrefix = auth()->user()->role;
        return redirect()->route($routePrefix . '.listings.show', $listing)
            ->with('success', 'Listing updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        $this->authorize('delete', $listing);
        
        // Delete banner image if exists
        if ($listing->banner_image && Storage::disk('public')->exists($listing->banner_image)) {
            Storage::disk('public')->delete($listing->banner_image);
        }

        // Delete all additional images
        foreach ($listing->images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }
        
        $listing->delete();

        $routePrefix = auth()->user()->role;
        return redirect()->route($routePrefix . '.listings.index')
            ->with('success', 'Listing deleted successfully.');
    }
}
