<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShortcutDictionary;
use Illuminate\Support\Facades\Validator;

class ShortcutDictionaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-shortcuts')->only('create', 'store', 'edit', 'update', 'destroy');
    }

    /**
     * Display a listing of shortcuts
     */
    public function index(Request $request)
    {
        $query = ShortcutDictionary::with('creator');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('shortcut', 'like', "%{$search}%")
                  ->orWhere('full_form', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        $shortcuts = $query->orderBy('shortcut')->paginate(15);
        $categories = ShortcutDictionary::getCategories();
        
        return view('shortcut-dictionary.index', compact('shortcuts', 'categories'));
    }

    /**
     * Show the form for creating a new shortcut
     */
    public function create()
    {
        $categories = ShortcutDictionary::getCategories();
        return view('shortcut-dictionary.create', compact('categories'));
    }

    /**
     * Store a newly created shortcut
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shortcut' => 'required|string|max:50|unique:shortcut_dictionaries,shortcut',
            'full_form' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:50',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            ShortcutDictionary::create([
                'shortcut' => $request->shortcut,
                'full_form' => $request->full_form,
                'description' => $request->description,
                'category' => $request->category,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => auth()->id()
            ]);

            return redirect()->route('shortcut-dictionary.index')
                ->with('success', 'Shortcut created successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create shortcut: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified shortcut
     */
    public function edit(ShortcutDictionary $shortcutDictionary)
    {
        $categories = ShortcutDictionary::getCategories();
        return view('shortcut-dictionary.edit', compact('shortcutDictionary', 'categories'));
    }

    /**
     * Update the specified shortcut
     */
    public function update(Request $request, ShortcutDictionary $shortcutDictionary)
    {
        $validator = Validator::make($request->all(), [
            'shortcut' => 'required|string|max:50|unique:shortcut_dictionaries,shortcut,' . $shortcutDictionary->id,
            'full_form' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:50',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $shortcutDictionary->update([
                'shortcut' => $request->shortcut,
                'full_form' => $request->full_form,
                'description' => $request->description,
                'category' => $request->category,
                'is_active' => $request->boolean('is_active', true)
            ]);

            return redirect()->route('shortcut-dictionary.index')
                ->with('success', 'Shortcut updated successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update shortcut: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified shortcut
     */
    public function destroy(ShortcutDictionary $shortcutDictionary)
    {
        try {
            $shortcutDictionary->delete();
            
            return redirect()->route('shortcut-dictionary.index')
                ->with('success', 'Shortcut deleted successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete shortcut: ' . $e->getMessage()]);
        }
    }

    /**
     * Get shortcuts for JavaScript
     */
    public function getShortcuts()
    {
        $shortcuts = ShortcutDictionary::getShortcutsArray();
        
        return response()->json([
            'shortcuts' => $shortcuts
        ]);
    }

    /**
     * Get shortcuts by category
     */
    public function getShortcutsByCategory($category)
    {
        $shortcuts = ShortcutDictionary::getShortcutsByCategory($category);
        
        return response()->json([
            'shortcuts' => $shortcuts
        ]);
    }
}
