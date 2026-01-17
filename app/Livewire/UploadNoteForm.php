<?php

namespace App\Livewire;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\School;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadNoteForm extends Component
{
    use WithFileUploads;

    // Dropdown data
    public Collection $schools;
    public Collection $levels;
    public Collection $modules;

    // Selected values for cascade dropdowns
    public $selectedSchool = '';
    public $selectedLevel = '';
    public $selectedModule = '';

    // Form fields
    public $title = '';
    public $description = '';
    public $price = '';

    // File uploads
    public $noteFile;
    public $previews = [];

    protected $rules = [
        'selectedModule' => 'required|exists:modules,id',
        'title'          => 'required|string|max:255',
        'description'    => 'required|string',
        'price'          => 'required|numeric|min:0',
        'noteFile'       => 'required|file|max:51200', // 50MB
        'previews'       => 'required|array|min:1|max:3',
        'previews.*'     => 'image|max:20480', // 20MB per image
    ];

    protected $messages = [
        'selectedModule.required' => 'Please select a module.',
        'noteFile.required'       => 'Please upload a note file.',
        'noteFile.max'            => 'Note file must be less than 50MB.',
        'previews.required'       => 'Please upload at least 1 preview image.',
        'previews.min'            => 'Please upload at least 1 preview image.',
        'previews.max'            => 'Maximum 3 preview images allowed.',
        'previews.*.image'        => 'Preview files must be images.',
        'previews.*.max'          => 'Each preview image must be less than 20MB.',
    ];

    public function mount(Collection $schools)
    {
        $this->schools = $schools;
        $this->levels = collect();
        $this->modules = collect();
    }

    public function updatedSelectedSchool($value)
    {
        $this->levels = collect();
        $this->modules = collect();
        $this->selectedLevel = '';
        $this->selectedModule = '';

        if ($value) {
            $this->levels = Level::where('school_id', $value)->get();
        }
    }

    public function updatedSelectedLevel($value)
    {
        $this->modules = collect();
        $this->selectedModule = '';

        if ($value) {
            $this->modules = Module::where('level_id', $value)->where('status', true)->get();
        }
    }

    public function updatedPreviews()
    {
        // Real-time validation for preview images
        $this->validateOnly('previews');
        $this->validateOnly('previews.*');
    }

    public function updatedNoteFile()
    {
        $this->validateOnly('noteFile');
    }

    public function removePreview($index)
    {
        unset($this->previews[$index]);
        $this->previews = array_values($this->previews);
    }

    public function save()
    {
        $this->validate();

        $note = Note::create([
            'user_id'     => auth()->id(),
            'module_id'   => $this->selectedModule,
            'title'       => $this->title,
            'description' => $this->description,
            'price'       => $this->price,
            'status'      => 'approved',
        ]);

        // Store note file
        $note->addMedia($this->noteFile->getRealPath())
            ->usingFileName($this->noteFile->getClientOriginalName())
            ->toMediaCollection('note_file');

        // Store preview images (max 3)
        foreach (array_slice($this->previews, 0, 3) as $preview) {
            $note->addMedia($preview->getRealPath())
                ->usingFileName($preview->getClientOriginalName())
                ->toMediaCollection('previews');
        }

        session()->flash('success', 'Note uploaded successfully!');

        return redirect()->route('notes.mine');
    }

    public function render()
    {
        return view('livewire.upload-note-form');
    }
}
