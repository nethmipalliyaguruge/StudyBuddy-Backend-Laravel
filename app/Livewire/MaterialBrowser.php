<?php

namespace App\Livewire;

use App\Models\Level;
use App\Models\Module;
use App\Models\Note;
use App\Models\School;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialBrowser extends Component
{
    use WithPagination;

    // Filter properties
    public $school = '';
    public $level = '';
    public $module = '';
    public $search = '';
    public $minPrice = '';
    public $maxPrice = '';
    public $sort = 'newest';

    // Query string binding for shareable URLs
    protected $queryString = [
        'school' => ['except' => ''],
        'level' => ['except' => ''],
        'module' => ['except' => ''],
        'search' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
        'sort' => ['except' => 'newest'],
    ];

    // Reset pagination when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSchool()
    {
        $this->level = '';
        $this->module = '';
        $this->resetPage();
    }

    public function updatingLevel()
    {
        $this->module = '';
        $this->resetPage();
    }

    public function updatingModule()
    {
        $this->resetPage();
    }

    public function updatingMinPrice()
    {
        $this->resetPage();
    }

    public function updatingMaxPrice()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    // Clear all filters
    public function clearFilters()
    {
        $this->school = '';
        $this->level = '';
        $this->module = '';
        $this->search = '';
        $this->minPrice = '';
        $this->maxPrice = '';
        $this->sort = 'newest';
        $this->resetPage();
    }

    // Get schools for dropdown
    public function getSchoolsProperty()
    {
        return School::orderBy('name')->get();
    }

    // Get levels based on selected school
    public function getLevelsProperty()
    {
        if (!$this->school) {
            return collect();
        }

        return Level::where('school_id', $this->school)
            ->orderBy('name')
            ->get();
    }

    // Get modules based on selected level
    public function getModulesProperty()
    {
        if (!$this->level) {
            return collect();
        }

        return Module::where('level_id', $this->level)
            ->where('status', true)
            ->orderBy('title')
            ->get();
    }

    // Check if any filter is active
    public function getHasFiltersProperty()
    {
        return $this->school || $this->level || $this->module ||
               $this->search || $this->minPrice || $this->maxPrice;
    }

    public function render()
    {
        // Build query
        $query = Note::where('status', 'approved')
            ->with(['user', 'media', 'module.level.school']);

        // Filter by school
        if ($this->school) {
            $query->whereHas('module.level', function ($q) {
                $q->where('school_id', $this->school);
            });
        }

        // Filter by level
        if ($this->level) {
            $query->whereHas('module', function ($q) {
                $q->where('level_id', $this->level);
            });
        }

        // Filter by module
        if ($this->module) {
            $query->where('module_id', $this->module);
        }

        // Search
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by price range
        if ($this->minPrice !== '' || $this->maxPrice !== '') {
            $minPrice = $this->minPrice !== '' ? max(0, (float) $this->minPrice) : 0;
            $maxPrice = $this->maxPrice !== '' ? max(0, (float) $this->maxPrice) : null;

            if ($maxPrice !== null) {
                $query->whereBetween('price', [$minPrice, $maxPrice]);
            } else {
                $query->where('price', '>=', $minPrice);
            }
        }

        // Sort results
        $sortOptions = [
            'newest' => ['created_at', 'desc'],
            'oldest' => ['created_at', 'asc'],
            'price_high' => ['price', 'desc'],
            'price_low' => ['price', 'asc'],
        ];

        if (isset($sortOptions[$this->sort])) {
            [$column, $direction] = $sortOptions[$this->sort];
            $query->orderBy($column, $direction);
        } else {
            $query->latest();
        }

        // Get paginated results
        $notes = $query->paginate(12);

        return view('livewire.material-browser', [
            'notes' => $notes,
            'schools' => $this->schools,
            'levels' => $this->levels,
            'modules' => $this->modules,
        ]);
    }
}
