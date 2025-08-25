<?php

namespace App\Http\Controllers;

use App\Models\ContentManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContentManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = ContentManagement::with('createdBy');

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $content = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get available types and categories for filters
        $types = ContentManagement::distinct()->pluck('type');
        $categories = ContentManagement::distinct()->pluck('category');

        return view('back-office.content.index', compact('content', 'types', 'categories'));
    }

    public function create()
    {
        return view('back-office.content.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'required|string|in:logo,image,document,banner,icon',
            'category' => 'required|string|in:club,association,competition,system,player,team',
            'name' => 'required|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $file = $request->file('file');
            
            // Validate file type based on content type
            $allowedMimes = $this->getAllowedMimes($request->type);
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return redirect()->back()
                    ->with('error', 'Invalid file type for this content type.')
                    ->withInput();
            }

            $contentData = $request->only([
                'type', 'category', 'name', 'alt_text', 'description',
                'is_active', 'is_featured', 'sort_order'
            ]);

            $content = ContentManagement::uploadFile($file, $contentData);

            return redirect()->route('back-office.content.index')
                ->with('success', 'Content uploaded successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to upload content: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(ContentManagement $content)
    {
        return view('back-office.content.show', compact('content'));
    }

    public function edit(ContentManagement $content)
    {
        return view('back-office.content.edit', compact('content'));
    }

    public function update(Request $request, ContentManagement $content)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $contentData = $request->only([
                'name', 'alt_text', 'description', 'is_active',
                'is_featured', 'sort_order', 'metadata'
            ]);

            $contentData['updated_by'] = auth()->id();

            $content->update($contentData);

            return redirect()->route('back-office.content.index')
                ->with('success', 'Content updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update content: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(ContentManagement $content)
    {
        try {
            // Delete the file from storage
            $content->deleteFile();
            
            // Delete the database record
            $content->delete();

            return redirect()->route('back-office.content.index')
                ->with('success', 'Content deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete content: ' . $e->getMessage());
        }
    }

    public function bulkUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files.*' => 'required|file|max:10240',
            'type' => 'required|string|in:logo,image,document,banner,icon',
            'category' => 'required|string|in:club,association,competition,system,player,team',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $uploadedCount = 0;
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $contentData = [
                    'type' => $request->type,
                    'category' => $request->category,
                    'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'is_active' => $request->is_active ?? true,
                ];

                ContentManagement::uploadFile($file, $contentData);
                $uploadedCount++;

            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        $message = "Successfully uploaded {$uploadedCount} files.";
        if (!empty($errors)) {
            $message .= ' Errors: ' . implode(', ', $errors);
        }

        return redirect()->route('back-office.content.index')
            ->with('success', $message);
    }

    public function toggleStatus(ContentManagement $content)
    {
        try {
            $content->update(['is_active' => !$content->is_active]);
            
            $status = $content->is_active ? 'activated' : 'deactivated';
            return redirect()->route('back-office.content.index')
                ->with('success', "Content {$status} successfully!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to toggle content status: ' . $e->getMessage());
        }
    }

    private function getAllowedMimes($type): array
    {
        return match($type) {
            'logo', 'image', 'banner' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'icon' => ['image/png', 'image/svg+xml'],
            'document' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            default => ['*/*'],
        };
    }
} 