<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarouselSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CarouselSlideController extends Controller
{
    /**
     * Display a listing of the slides and the add form.
     */
    public function index(): View
    {
        $slides = CarouselSlide::latest()->get();
        return view('admin.carousel.index', compact('slides'));
    }

    /**
     * Store a newly created slide.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'cta_text' => ['nullable', 'string', 'max:50'],
            'cta_url' => ['nullable', 'string', 'max:255'],
        ]);

        $imagePath = $request->file('image')->store('carousel', 'public');

        CarouselSlide::create([
            'image_path' => $imagePath,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'cta_text' => $request->input('cta_text') ?? 'Apply Now',
            'cta_url' => $request->input('cta_url') ?? '#',
        ]);

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Hero slide uploaded successfully.');
    }

    /**
     * Remove the specified slide.
     */
    public function destroy(CarouselSlide $carousel): RedirectResponse
    {
        // Delete image file from public storage disk
        if ($carousel->image_path) {
            Storage::disk('public')->delete($carousel->image_path);
        }

        $carousel->delete();

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Hero slide deleted successfully.');
    }
}
