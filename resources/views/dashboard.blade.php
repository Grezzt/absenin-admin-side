<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Access Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Users Management Card -->
                <a href="{{ route('users.dashboard') }}" class="block">
                    <div
                        class="bg-white overflow-hidden shadow-xl sm:rounded-lg hover:shadow-2xl transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-500 bg-opacity-20">
                                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-gray-700">Kelola Users</h2>
                                    <p class="text-sm text-gray-500">Manage user accounts</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Attendance Card -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg opacity-50 cursor-not-allowed">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-500 bg-opacity-20">
                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-gray-700">Attendance</h2>
                                <p class="text-sm text-gray-500">Coming soon...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports Card -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg opacity-50 cursor-not-allowed">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-500 bg-opacity-20">
                                <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-gray-700">Reports</h2>
                                <p class="text-sm text-gray-500">Coming soon...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Section -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <x-welcome />
            </div>
        </div>
    </div>
</x-app-layout>
