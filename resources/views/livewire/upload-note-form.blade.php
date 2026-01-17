<div class="card p-6">
    <form wire:submit="save" class="space-y-6">
        <!-- Step 1: Select Module -->
        <div class="border-b pb-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                <span class="w-6 h-6 bg-primary text-white rounded-full inline-flex items-center justify-center text-sm mr-2">1</span>
                Select Module
            </h3>

            <div class="grid sm:grid-cols-3 gap-4">
                <!-- School -->
                <div>
                    <label class="form-label">School</label>
                    <select wire:model.live="selectedSchool" class="select" required>
                        <option value="">Select School</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Level -->
                <div>
                    <label class="form-label">Level</label>
                    <select wire:model.live="selectedLevel"
                            @disabled($levels->isEmpty())
                            class="select"
                            required>
                        <option value="">Select Level</option>
                        @foreach($levels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Module -->
                <div>
                    <label class="form-label">Module</label>
                    <select wire:model="selectedModule"
                            @disabled($modules->isEmpty())
                            class="select"
                            required>
                        <option value="">Select Module</option>
                        @foreach($modules as $module)
                            <option value="{{ $module->id }}">{{ $module->title }}</option>
                        @endforeach
                    </select>
                    @error('selectedModule')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 2: Note Details -->
        <div class="border-b pb-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                <span class="w-6 h-6 bg-primary text-white rounded-full inline-flex items-center justify-center text-sm mr-2">2</span>
                Note Details
            </h3>

            <div class="space-y-4">
                <div>
                    <label for="title" class="form-label">Note Title</label>
                    <input type="text"
                           id="title"
                           wire:model="title"
                           placeholder="e.g., Complete Biology Chapter 5 Notes"
                           class="input"
                           required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description"
                              wire:model="description"
                              rows="4"
                              placeholder="Describe what's included in your notes, topics covered, etc."
                              class="input"
                              required></textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="form-label">Price (LKR)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">LKR</span>
                        <input type="number"
                               id="price"
                               wire:model="price"
                               step="0.01"
                               min="0"
                               placeholder="100.00"
                               class="input pl-12"
                               required>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Set a fair price. Free notes can have price 0.</p>
                    @error('price')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Step 3: Files -->
        <div>
            <h3 class="font-semibold text-gray-900 mb-4">
                <span class="w-6 h-6 bg-primary text-white rounded-full inline-flex items-center justify-center text-sm mr-2">3</span>
                Upload Files
            </h3>

            <div class="space-y-4">
                <!-- Note File -->
                <div>
                    <label class="form-label">Note File (PDF, DOC, etc.) <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed rounded-lg p-6 text-center transition-colors
                                {{ $noteFile ? 'border-green-400 bg-green-50' : 'border-gray-200 hover:border-primary' }}">
                        <input type="file"
                               wire:model="noteFile"
                               id="note_file"
                               class="hidden"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt">
                        <label for="note_file" class="cursor-pointer block">
                            @if(!$noteFile)
                                <div>
                                    <i class="fas fa-file-upload text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-600">Click to upload your note file</p>
                                    <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, PPT, etc. (Max 50MB)</p>
                                </div>
                            @else
                                <div>
                                    <i class="fas fa-file-check text-3xl text-green-500 mb-2"></i>
                                    <p class="text-green-700 font-medium">{{ $noteFile->getClientOriginalName() }}</p>
                                    <p class="text-xs text-green-600 mt-1">Click to change file</p>
                                </div>
                            @endif
                        </label>

                        <!-- Upload Progress -->
                        <div wire:loading wire:target="noteFile" class="mt-3">
                            <div class="flex items-center justify-center gap-2 text-primary">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm">Uploading...</span>
                            </div>
                        </div>
                    </div>
                    @error('noteFile')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Preview Images -->
                <div>
                    <label class="form-label">Preview Images (1-3 required) <span class="text-red-500">*</span></label>
                    <div class="border-2 border-dashed rounded-lg p-6 text-center transition-colors
                                {{ $errors->has('previews') || $errors->has('previews.*') ? 'border-red-400 bg-red-50' : (count($previews) > 0 ? 'border-green-400 bg-green-50' : 'border-gray-200 hover:border-primary') }}">
                        <input type="file"
                               wire:model="previews"
                               id="previews"
                               multiple
                               accept="image/*"
                               class="hidden">
                        <label for="previews" class="cursor-pointer block">
                            @if(count($previews) === 0)
                                <div>
                                    <i class="fas fa-images text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-600">Click to upload preview images</p>
                                    <p class="text-xs text-gray-400 mt-1">JPG, PNG - Select 1 to 3 images</p>
                                </div>
                            @else
                                <div>
                                    <i class="fas fa-images text-3xl text-green-500 mb-2"></i>
                                    <p class="text-green-700 font-medium">{{ count($previews) }} image(s) selected</p>
                                    <p class="text-xs text-green-600 mt-2">Click to change images</p>
                                </div>
                            @endif
                        </label>

                        <!-- Upload Progress -->
                        <div wire:loading wire:target="previews" class="mt-3">
                            <div class="flex items-center justify-center gap-2 text-primary">
                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm">Uploading...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Thumbnails -->
                    @if(count($previews) > 0)
                        <div class="mt-3 grid grid-cols-3 gap-3">
                            @foreach($previews as $index => $preview)
                                <div class="relative group">
                                    <img src="{{ $preview->temporaryUrl() }}"
                                         alt="Preview {{ $index + 1 }}"
                                         class="w-full h-24 object-cover rounded-lg border border-gray-200">
                                    <button type="button"
                                            wire:click="removePreview({{ $index }})"
                                            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                    <p class="text-xs text-gray-500 mt-1 truncate">{{ $preview->getClientOriginalName() }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @error('previews')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('previews.*')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Preview images help buyers see what they're getting. Add screenshots of your notes.
                    </p>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('notes.mine') }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-primary btn-lg" wire:loading.attr="disabled" wire:target="save,noteFile,previews">
                <span wire:loading.remove wire:target="save">
                    <i class="fas fa-upload mr-2"></i>Upload Note
                </span>
                <span wire:loading wire:target="save" class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Uploading...
                </span>
            </button>
        </div>
    </form>
</div>
