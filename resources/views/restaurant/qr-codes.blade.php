@extends('layouts.app')

@section('title', 'QR Codes - ' . $restaurant->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">QR Codes</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Generate QR codes for your restaurant tables</p>
                </div>
                <a href="{{ route('restaurant.dashboard', $restaurant->slug) }}" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Generate New QR Code -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Generate New QR Code</h3>
            <form action="{{ route('restaurant.generate-qr', $restaurant->slug) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                <div>
                    <label for="table_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Table Number *
                    </label>
                    <input type="text" id="table_number" name="table_number" required
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        placeholder="Table 1">
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <input type="text" id="description" name="description"
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                        placeholder="Window seat, Outdoor, etc.">
                </div>
                <div class="flex items-end">
                    <button type="submit" 
                        class="w-full bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold py-3 px-6 rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all duration-200">
                        <i class="fas fa-qrcode mr-2"></i>
                        Generate QR Code
                    </button>
                </div>
            </form>
        </div>

        <!-- QR Codes List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Your QR Codes</h3>
            </div>
            
            @if($qrCodes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                    @foreach($qrCodes as $qrCode)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <div class="text-center">
                                <!-- QR Code Display -->
                                <div class="bg-white p-4 rounded-lg mb-4 inline-block">
                                    <div class="w-32 h-32 bg-gray-100 rounded-lg flex items-center justify-center">
                                        @if($qrCode->qr_image && Storage::disk('public')->exists($qrCode->qr_image))
                                            <img src="{{ Storage::url($qrCode->qr_image) }}" 
                                                 alt="QR Code for {{ $qrCode->table_number }}" 
                                                 class="w-full h-full object-contain">
                                        @else
                                            <div class="text-center">
                                                <i class="fas fa-qrcode text-4xl text-gray-400 mb-2"></i>
                                                <p class="text-xs text-gray-500">{{ $qrCode->qr_code }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- QR Code Info -->
                                <div class="mb-4">
                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $qrCode->table_number }}</h4>
                                    @if($qrCode->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $qrCode->description }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-2">QR Code: {{ $qrCode->qr_code }}</p>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex space-x-2">
                                    <a href="{{ url('/qr/' . $qrCode->qr_code) }}" target="_blank"
                                        class="flex-1 bg-blue-500 text-white text-sm font-medium py-2 px-3 rounded hover:bg-blue-600 transition-colors">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        Test
                                    </a>
                                    <button onclick="downloadQR('{{ $qrCode->qr_code }}')"
                                        class="flex-1 bg-green-500 text-white text-sm font-medium py-2 px-3 rounded hover:bg-green-600 transition-colors">
                                        <i class="fas fa-download mr-1"></i>
                                        Download
                                    </button>
                                </div>
                                
                                <!-- Status -->
                                <div class="mt-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($qrCode->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                        @if($qrCode->is_active) Active @else Inactive @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-qrcode text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No QR Codes Yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Generate your first QR code to get started</p>
                </div>
            @endif
        </div>

        <!-- Instructions -->
        <div class="mt-8 bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
            <h3 class="text-lg font-medium text-blue-900 dark:text-blue-100 mb-4">
                <i class="fas fa-info-circle mr-2"></i>
                How to Use QR Codes
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-800 dark:text-blue-200">
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold mt-0.5">1</div>
                    <div>
                        <p class="font-medium">Generate QR Codes</p>
                        <p class="text-blue-700 dark:text-blue-300">Create unique QR codes for each table in your restaurant</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold mt-0.5">2</div>
                    <div>
                        <p class="font-medium">Print & Display</p>
                        <p class="text-blue-700 dark:text-blue-300">Print the QR codes and place them on your tables</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold mt-0.5">3</div>
                    <div>
                        <p class="font-medium">Customers Scan</p>
                        <p class="text-blue-700 dark:text-blue-300">Customers scan to view your digital menu and place orders</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Restaurant Menu URL -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Your Restaurant Menu URL</h3>
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <input type="text" value="{{ $restaurant->getMenuUrl() }}" readonly
                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <button onclick="copyToClipboard('{{ $restaurant->getMenuUrl() }}')"
                    class="px-4 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-copy mr-2"></i>
                    Copy
                </button>
                <a href="{{ $restaurant->getMenuUrl() }}" target="_blank"
                    class="px-4 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    <i class="fas fa-external-link-alt mr-2"></i>
                    View
                </a>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                This is your restaurant's public menu URL. Customers can access this directly or through QR codes.
            </p>
        </div>
    </div>
</div>

<script>
function downloadQR(qrCode) {
    // This would generate and download the actual QR code
    alert('QR Code download functionality would be implemented here for: ' + qrCode);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
        button.classList.remove('bg-gray-500', 'hover:bg-gray-600');
        button.classList.add('bg-green-500', 'hover:bg-green-600');
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('bg-green-500', 'hover:bg-green-600');
            button.classList.add('bg-gray-500', 'hover:bg-gray-600');
        }, 2000);
    });
}
</script>
@endsection 