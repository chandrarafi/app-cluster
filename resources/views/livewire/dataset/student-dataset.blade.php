@push('styles')
<style>
    .drop-zone {
        position: relative;
        overflow: hidden;
        background-image: 
            repeating-linear-gradient(
                -45deg, 
                rgba(0, 0, 0, 0.03), 
                rgba(0, 0, 0, 0.03) 10px, 
                transparent 10px, 
                transparent 20px
            );
        transition: all 0.3s ease-in-out;
    }

    .drop-zone.active {
        background-image: 
            repeating-linear-gradient(
                -45deg, 
                rgba(var(--primary-rgb), 0.1), 
                rgba(var(--primary-rgb), 0.1) 10px, 
                transparent 10px, 
                transparent 20px
            );
    }

    .file-preview {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .import-button {
        position: relative;
        overflow: hidden;
    }

    .import-button::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(
            90deg, 
            transparent, 
            rgba(255, 255, 255, 0.2), 
            transparent
        );
        transition: none;
    }

    .import-button:hover::after {
        left: 100%;
        transition: 0.7s linear;
    }
</style>
@endpush

<x-slot:title>
    {{ __('Dataset Siswa') }}
</x-slot:title>

<div class="py-6">
    <div class="container mx-auto">
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-zinc-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Dataset Siswa') }}</h3>
                    <div class="flex space-x-2">
                        <button wire:click="downloadTemplate" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md import-button" wire:loading.attr="disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor" wire:loading.remove wire:target="downloadTemplate">
                                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd" />
                            </svg>
                            <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" wire:loading wire:target="downloadTemplate">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="downloadTemplate">{{ __('Download Template') }}</span>
                            <span wire:loading wire:target="downloadTemplate">{{ __('Mengunduh...') }}</span>
                        </button>
                        <button wire:click="exportToExcel" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md import-button" wire:loading.attr="disabled">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor" wire:loading.remove wire:target="exportToExcel">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            <svg class="animate-spin h-5 w-5 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" wire:loading wire:target="exportToExcel">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="exportToExcel">{{ __('Export Excel') }}</span>
                            <span wire:loading wire:target="exportToExcel">{{ __('Mengekspor...') }}</span>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-4">
                    @if (session()->has('message'))
                        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if (session()->has('warning'))
                        <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700">
                            {{ session('warning') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">{{ __('Import Data Siswa') }}</h3>
                      
                        <form wire:submit="importExcel" enctype="multipart/form-data" class="w-full">
                            <div 
                                x-data="{ 
                                    isDropping: false,
                                    isUploading: false,
                                    progress: 0,
                                }"
                                x-on:livewire-upload-start="isUploading = true"
                                x-on:livewire-upload-finish="isUploading = false"
                                x-on:livewire-upload-error="isUploading = false"
                                x-on:livewire-upload-progress="progress = $event.detail.progress"
                                class="bg-gray-50 p-6 rounded-lg border-2 border-dashed border-gray-300 transition-colors duration-300 ease-in-out drop-zone"
                                x-bind:class="{ 'border-primary-400 bg-primary-50 active': isDropping }"
                                x-on:dragover.prevent="isDropping = true"
                                x-on:dragleave.prevent="isDropping = false"
                                x-on:drop.prevent="isDropping = false"
                            >
                                <div class="space-y-4">
                                    <div class="flex justify-center items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 transition-transform duration-300 ease-in-out" x-bind:class="{ 'scale-110': isDropping }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </div>
                                    
                                    <div class="text-center">
                                        <div class="relative">
                                            <input type="file" wire:model="file" id="file" class="hidden" accept=".csv, .xlsx, .xls" />
                                            <label for="file" class="cursor-pointer">
                                                <span class="text-sm font-medium text-primary-600 hover:text-primary-500 focus:outline-none focus:underline transition duration-150 ease-in-out">{{ __('Pilih file') }}</span>
                                                <span class="text-sm text-gray-500"> atau seret dan lepas</span>
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">
                                            Format CSV, Excel (.xlsx, .xls) hingga 5MB
                                        </p>
                                    </div>
                                    
                                    <div x-show="isUploading" x-transition class="relative pt-1">
                                        <div class="flex mb-2 items-center justify-between">
<div>
                                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-primary-600 bg-primary-200">
                                                    Mengunggah
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-xs font-semibold inline-block text-primary-600" x-text="progress + '%'"></span>
                                            </div>
                                        </div>
                                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-primary-200">
                                            <div x-bind:style="'width: ' + progress + '%'" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-primary-500 transition-all duration-300 ease-out"></div>
                                        </div>
                                    </div>
                                    
                                    @if($file)
                                    <div class="mt-4 flex items-center justify-center">
                                        <div class="bg-white rounded-md shadow p-4 w-full transition-all duration-300 transform file-preview">
                                            <div class="flex items-center space-x-2">
                                                <svg class="h-8 w-8 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <div class="flex-1 truncate">
                                                    <div class="flex items-center space-x-1">
                                                        <h3 class="text-sm font-medium text-gray-900 truncate">{{ $file->getClientOriginalName() }}</h3>
                                                    </div>
                                                    <p class="text-xs text-gray-500">
                                                        {{ round($file->getSize() / 1024, 2) }} KB
                                                    </p>
                                                </div>
                                                <button type="button" wire:click="resetFile" class="text-gray-400 hover:text-red-500 transition-colors duration-200">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="flex justify-center">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors duration-200 ease-in-out import-button" wire:loading.attr="disabled" @if(!$file) disabled @endif x-bind:class="{'opacity-50 cursor-not-allowed': !$wire.file}">
                                            <span wire:loading.remove wire:target="importExcel">
                                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                                {{ __('Import Data') }}
                                            </span>
                                            <span wire:loading wire:target="importExcel" class="inline-flex items-center">
                                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                {{ __('Mengimpor...') }}
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="mb-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="search" wire:model.live.debounce.500ms="search" class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500" placeholder="Cari berdasarkan nama atau kelas">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('No') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nama') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Kelas') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('UTS') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('UAS') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Sikap') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Pramuka') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('PMR') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Kehadiran') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($students as $index => $student)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $students->firstItem() + $index }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->nama }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->kelas }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->uts }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->uas }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->penilaian_sikap }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->pramuka }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->pmr }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->kehadiran }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            {{ __('Tidak ada data siswa') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.addEventListener('livewire:initialized', function () {
            const dropZone = document.querySelector('.border-dashed');
            const fileInput = document.getElementById('file');
            
            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (e.dataTransfer.files.length) {
                    // Update Livewire model
                    const files = e.dataTransfer.files;
                    fileInput.files = files;
                    
                    // Create and dispatch a new change event
                    const event = new Event('change', { bubbles: true });
                    fileInput.dispatchEvent(event);
                }
            });
        });
    });
</script>
@endpush
