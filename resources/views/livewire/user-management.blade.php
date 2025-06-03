<div>
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Manajemen Pengguna') }}</h3>
            <a href="{{ route('users.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors duration-200 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Tambah Pengguna') }}
            </a>
        </div>

        <div class="px-6 py-4">
            @if (session()->has('success'))
            <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
                {{ session('success') }}
            </div>
            @endif

            <div class="mb-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="search" wire:model.live.debounce.500ms="search"
                        class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Cari berdasarkan nama atau email...">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('id')">
                                <div class="flex items-center">
                                    ID
                                    @if($sortField === 'id')
                                    <svg class="h-3 w-3 ml-1 {{ $sortDirection === 'desc' ? 'transform rotate-180' : '' }}"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7" />
                                    </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('name')">
                                <div class="flex items-center">
                                    Nama
                                    @if($sortField === 'name')
                                    <svg class="h-3 w-3 ml-1 {{ $sortDirection === 'desc' ? 'transform rotate-180' : '' }}"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7" />
                                    </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('email')">
                                <div class="flex items-center">
                                    Email
                                    @if($sortField === 'email')
                                    <svg class="h-3 w-3 ml-1 {{ $sortDirection === 'desc' ? 'transform rotate-180' : '' }}"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7" />
                                    </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('created_at')">
                                <div class="flex items-center">
                                    Tanggal Dibuat
                                    @if($sortField === 'created_at')
                                    <svg class="h-3 w-3 ml-1 {{ $sortDirection === 'desc' ? 'transform rotate-180' : '' }}"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 15l7-7 7 7" />
                                    </svg>
                                    @endif
                                </div>
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{
                                $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-2">
                                    <a href="{{ route('users.show', $user) }}"
                                        class="inline-flex items-center text-blue-600 hover:text-blue-900 hover:underline">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Detail
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}"
                                        class="inline-flex items-center text-yellow-600 hover:text-yellow-900 hover:underline">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <button wire:click="deleteUser({{ $user->id }})"
                                        wire:confirm="Apakah Anda yakin ingin menghapus pengguna ini?"
                                        class="inline-flex items-center text-red-600 hover:text-red-900 hover:underline">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                Tidak ada data pengguna
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 pagination-wrapper">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Pager styling */
        .pagination-wrapper nav {
            background-color: white;
            border-radius: 0.5rem;
            padding: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .pagination-wrapper nav div span.relative,
        .pagination-wrapper nav div a {
            background-color: white !important;
            color: #6B7280 !important;
            border-color: #E5E7EB !important;
        }

        .pagination-wrapper nav div span.bg-blue-50 {
            background-color: white !important;
            border-color: rgba(var(--primary-rgb), 0.5) !important;
            color: rgba(var(--primary-rgb), 1) !important;
            font-weight: 600;
        }

        .pagination-wrapper nav div span.text-blue-600,
        .pagination-wrapper nav div a:hover {
            color: rgba(var(--primary-rgb), 1) !important;
        }

        .pagination-wrapper nav div a.border-gray-300:hover {
            border-color: rgba(var(--primary-rgb), 0.5) !important;
            background-color: white !important;
        }
    </style>
    @endpush
</div>