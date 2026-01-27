<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MaterialResource;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * List authenticated user's own notes.
     */
    public function myNotes(Request $request): AnonymousResourceCollection
    {
        $query = Note::with(['module.level.school', 'media'])
            ->where('user_id', Auth::id());

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $notes = $query->orderBy('created_at', 'desc')->paginate(15);

        return MaterialResource::collection($notes);
    }

    /**
     * Store a new note (upload).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'module_id' => ['required', 'exists:modules,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'note_file' => ['required', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:51200'], // 50MB
            'previews' => ['nullable', 'array', 'max:3'],
            'previews.*' => ['image', 'mimes:jpg,jpeg,png', 'max:20480'], // 20MB each
        ]);

        $note = Note::create([
            'user_id' => Auth::id(),
            'module_id' => $validated['module_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'status' => 'pending', // Requires admin approval
        ]);

        // Handle note file upload
        if ($request->hasFile('note_file')) {
            $note->addMediaFromRequest('note_file')
                ->toMediaCollection('note_file');
        }

        // Handle preview images
        if ($request->hasFile('previews')) {
            foreach ($request->file('previews') as $preview) {
                $note->addMedia($preview)
                    ->toMediaCollection('previews');
            }
        }

        $note->load(['module.level.school', 'media']);

        return response()->json([
            'message' => 'Note uploaded successfully. It will be available after admin approval.',
            'data' => new MaterialResource($note),
        ], 201);
    }

    /**
     * Update user's own note.
     */
    public function update(Request $request, Note $note): JsonResponse
    {
        // Ensure user owns this note
        if ($note->user_id !== Auth::id()) {
            abort(403, 'You can only update your own notes.');
        }

        // Prevent updating if note has purchases
        if ($note->hasPurchases()) {
            abort(403, 'Cannot update a note that has been purchased.');
        }

        $validated = $request->validate([
            'module_id' => ['sometimes', 'exists:modules,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:2000'],
            'price' => ['sometimes', 'numeric', 'min:0', 'max:99999.99'],
            'note_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:51200'],
            'previews' => ['nullable', 'array', 'max:3'],
            'previews.*' => ['image', 'mimes:jpg,jpeg,png', 'max:20480'],
        ]);

        // Update fields
        $note->fill($validated);

        // Reset status to pending if content changed
        if ($note->isDirty(['title', 'description', 'module_id'])) {
            $note->status = 'pending';
        }

        $note->save();

        // Handle note file update
        if ($request->hasFile('note_file')) {
            $note->clearMediaCollection('note_file');
            $note->addMediaFromRequest('note_file')
                ->toMediaCollection('note_file');
            $note->update(['status' => 'pending']);
        }

        // Handle preview images update
        if ($request->hasFile('previews')) {
            $note->clearMediaCollection('previews');
            foreach ($request->file('previews') as $preview) {
                $note->addMedia($preview)
                    ->toMediaCollection('previews');
            }
        }

        $note->load(['module.level.school', 'media']);

        return response()->json([
            'message' => 'Note updated successfully.',
            'data' => new MaterialResource($note),
        ]);
    }

    /**
     * Delete user's own note.
     */
    public function destroy(Note $note): JsonResponse
    {
        // Ensure user owns this note
        if ($note->user_id !== Auth::id()) {
            abort(403, 'You can only delete your own notes.');
        }

        // Prevent deleting if note has purchases
        if ($note->hasPurchases()) {
            abort(403, 'Cannot delete a note that has been purchased.');
        }

        // Clear media files
        $note->clearMediaCollection('note_file');
        $note->clearMediaCollection('previews');

        $note->delete();

        return response()->json([
            'message' => 'Note deleted successfully.',
        ]);
    }
}
