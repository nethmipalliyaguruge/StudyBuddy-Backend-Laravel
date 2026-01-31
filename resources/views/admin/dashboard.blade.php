<x-layouts.admin>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="section-title">Admin Dashboard</h1>
            <p class="section-subtitle">Manage schools, levels, modules, users, and materials</p>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <x-stats-card
                title="Schools"
                :value="$stats['schools']"
                icon="fa-school"
                color="primary" />

            <x-stats-card
                title="Modules"
                :value="$stats['modules']"
                icon="fa-book"
                color="info" />

            <x-stats-card
                title="Users"
                :value="$stats['users']"
                icon="fa-users"
                color="success" />

            <x-stats-card
                title="Materials"
                :value="$stats['materials']"
                icon="fa-file-alt"
                color="warning" />
        </div>

        <!-- Tabs Navigation -->
        <div x-data="{ activeTab: 'schools' }" class="space-y-6">
            <div class="flex flex-wrap gap-2 border-b pb-4">
                <button @click="activeTab = 'schools'"
                        :class="activeTab === 'schools' ? 'btn-primary' : 'btn-ghost'"
                        class="btn-sm">
                    <i class="fas fa-school mr-2"></i>Schools
                </button>
                <button @click="activeTab = 'levels'"
                        :class="activeTab === 'levels' ? 'btn-primary' : 'btn-ghost'"
                        class="btn-sm">
                    <i class="fas fa-layer-group mr-2"></i>Levels
                </button>
                <button @click="activeTab = 'modules'"
                        :class="activeTab === 'modules' ? 'btn-primary' : 'btn-ghost'"
                        class="btn-sm">
                    <i class="fas fa-book mr-2"></i>Modules
                </button>
                <button @click="activeTab = 'users'"
                        :class="activeTab === 'users' ? 'btn-primary' : 'btn-ghost'"
                        class="btn-sm">
                    <i class="fas fa-users mr-2"></i>Users
                </button>
                <button @click="activeTab = 'materials'"
                        :class="activeTab === 'materials' ? 'btn-primary' : 'btn-ghost'"
                        class="btn-sm">
                    <i class="fas fa-file-alt mr-2"></i>Materials
                </button>
            </div>

            <!-- Schools Tab -->
            <div x-show="activeTab === 'schools'" x-cloak>
                <livewire:admin.school-manager />
            </div>

            <!-- Levels Tab -->
            <div x-show="activeTab === 'levels'" x-cloak>
                <livewire:admin.level-manager />
            </div>

            <!-- Modules Tab -->
            <div x-show="activeTab === 'modules'" x-cloak>
                <livewire:admin.module-manager />
            </div>

            <!-- Users Tab -->
            <div x-show="activeTab === 'users'" x-cloak>
                <livewire:admin.user-manager />
            </div>

            <!-- Materials Tab -->
            <div x-show="activeTab === 'materials'" x-cloak>
                <livewire:admin.material-manager />
            </div>
        </div>
    </div>

    <!-- Global Notifications -->
    <livewire:admin.notification />
</x-layouts.admin>
