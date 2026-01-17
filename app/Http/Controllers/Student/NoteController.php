<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\School;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function myNotes()
    {
        $notes = Note::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('student.my-notes', compact('notes'));
    }

    public function create()
    {
        $schools = School::with('levels.modules')->get();
        return view('student.upload-note', compact('schools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'module_id'   => 'required|exists:modules,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'note_file'   => 'required|file|max:51200', // 50MB
            'previews'    => 'required|array|min:1|max:3',
            'previews.*'  => 'image|max:20480', // 20MB per image
        ]);

        $note = Note::create([
            'user_id'     => auth()->id(),
            'module_id'   => $request->module_id,
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
            'status'      => 'approved',
        ]);

        // 🔹 Store NOTE FILE (single)
        $note->addMediaFromRequest('note_file')
            ->toMediaCollection('note_file');

        // 🔹 Store PREVIEW IMAGES (max 3)
        if ($request->hasFile('previews')) {
            foreach (array_slice($request->file('previews'), 0, 3) as $image) {
                $note->addMedia($image)
                    ->toMediaCollection('previews');
            }
        }

        return redirect()
            ->route('notes.mine')
            ->with('success', 'Note uploaded successfully');
    }


    public function update(Request $request, Note $note)
    {
        abort_if($note->user_id !== auth()->id(), 403);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
        ]);

        $note->update([
            'title'       => $request->title,
            'description' => $request->description,
            'price'       => $request->price,
        ]);

        return back()->with('success', 'Note updated successfully');
    }

    public function destroy(Note $note)
    {
        abort_if($note->user_id !== auth()->id(), 403);

        if ($note->purchases()->exists()) {
            // Soft disable (keep buyer history)
            $note->update(['status' => 'pending']);

            return back()->with(
                'success',
                'Note has purchases and was disabled instead of deleted.'
            );
        }

        $note->delete();

        return back()->with('success', 'Note deleted successfully');
    }
}
