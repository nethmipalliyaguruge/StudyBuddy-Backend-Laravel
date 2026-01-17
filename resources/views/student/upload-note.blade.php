<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Upload Note</h2>
    </x-slot>

    <div class="p-6 max-w-3xl">

        <form method="POST" action="{{ route('notes.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- SCHOOL --}}
            <select id="schoolSelect" class="border p-2 w-full" required>
                <option value="">Select School</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}">
                        {{ $school->name }}
                    </option>
                @endforeach
            </select>

            {{-- LEVEL --}}
            <select id="levelSelect" class="border p-2 w-full" required disabled>
                <option value="">Select Level</option>
            </select>

            {{-- MODULE --}}
            <select name="module_id" id="moduleSelect"
                    class="border p-2 w-full" required disabled>
                <option value="">Select Module</option>
            </select>

            {{-- NOTE DETAILS --}}
            <input type="text" name="title" placeholder="Note title"
                   required class="border p-2 w-full">

            <textarea name="description" placeholder="Description"
                      required class="border p-2 w-full"></textarea>

            <input type="number" step="0.01" name="price"
                   placeholder="Price"
                   required class="border p-2 w-full">

            {{-- NOTE FILE --}}
            <div>
                <label class="block text-sm font-medium">Note File (Any format)</label>
                <input type="file"
                       name="note_file"
                       required
                       class="border p-2 w-full">
            </div>

            {{-- PREVIEW IMAGES --}}
            <div>
                <label class="block text-sm font-medium">Preview Images (Max 3)</label>
                <input type="file"
                       name="previews[]"
                       accept="image/*"
                       multiple
                       class="border p-2 w-full">
            </div>


            <button class="bg-blue-600 text-white px-6 py-2 rounded">
                Upload Note
            </button>
        </form>

    </div>

    {{-- ================= JAVASCRIPT ================= --}}
    <script>
        const schools = @json($schools);

        const schoolSelect = document.getElementById('schoolSelect');
        const levelSelect = document.getElementById('levelSelect');
        const moduleSelect = document.getElementById('moduleSelect');

        schoolSelect.addEventListener('change', function () {
            levelSelect.innerHTML = '<option value="">Select Level</option>';
            moduleSelect.innerHTML = '<option value="">Select Module</option>';
            moduleSelect.disabled = true;

            const school = schools.find(s => s.id == this.value);
            if (!school) return;

            school.levels.forEach(level => {
                levelSelect.innerHTML +=
                    `<option value="${level.id}">${level.name}</option>`;
            });

            levelSelect.disabled = false;
        });

        levelSelect.addEventListener('change', function () {
            moduleSelect.innerHTML = '<option value="">Select Module</option>';

            const school = schools.find(s => s.id == schoolSelect.value);
            const level = school.levels.find(l => l.id == this.value);
            if (!level) return;

            level.modules.forEach(module => {
                moduleSelect.innerHTML +=
                    `<option value="${module.id}">${module.title}</option>`;
            });

            moduleSelect.disabled = false;
        });
    </script>
</x-app-layout>
