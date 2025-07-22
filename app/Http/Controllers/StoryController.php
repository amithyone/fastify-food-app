<?php

namespace App\Http\Controllers;

use App\Models\Story;
use Illuminate\Http\Request;

class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::ordered()->get();
        return view('admin.stories.index', compact('stories'));
    }

    public function create()
    {
        return view('admin.stories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'emoji' => 'nullable|string|max:10',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'show_button' => 'boolean',
            'button_text' => 'nullable|string|max:100',
            'button_action' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        Story::create($request->all());

        return redirect()->route('admin.stories.index')
            ->with('success', 'Story created successfully!');
    }

    public function edit(Story $story)
    {
        return view('admin.stories.edit', compact('story'));
    }

    public function update(Request $request, Story $story)
    {
        $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'emoji' => 'nullable|string|max:10',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'show_button' => 'boolean',
            'button_text' => 'nullable|string|max:100',
            'button_action' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        $story->update($request->all());

        return redirect()->route('admin.stories.index')
            ->with('success', 'Story updated successfully!');
    }

    public function destroy(Story $story)
    {
        $story->delete();

        return redirect()->route('admin.stories.index')
            ->with('success', 'Story deleted successfully!');
    }

    public function toggleStatus(Story $story)
    {
        $story->update(['is_active' => !$story->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $story->is_active,
            'message' => $story->is_active ? 'Story activated!' : 'Story deactivated!'
        ]);
    }
}
