@extends('layouts.app')

@section('title', $restaurant->name . ' - Video Packages')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Video Packages</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Professional video content for your restaurant</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('restaurant.dashboard', $restaurant->slug) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                    <a href="{{ route('restaurant.video-packages.create', $restaurant->slug) }}" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create Video Package
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-md flex items-center justify-center">
                            <i class="fas fa-video text-purple-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Packages</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $packages->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-md flex items-center justify-center">
                            <i class="fas fa-camera text-blue-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">In Production</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $packages->where('status', 'in_production')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-md flex items-center justify-center">
                            <i class="fas fa-check text-green-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Completed</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $packages->where('status', 'completed')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-md flex items-center justify-center">
                            <i class="fas fa-eye text-yellow-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Views</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($packages->sum('views')) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video Packages List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Your Video Packages</h3>
                    <div class="flex items-center space-x-2">
                        <select id="statusFilter" class="text-sm border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="in_production">In Production</option>
                            <option value="completed">Completed</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <select id="packageTypeFilter" class="text-sm border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">All Types</option>
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                @if($packages->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Package
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Price
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Performance
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Duration
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($packages as $package)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $package->package_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ Str::limit($package->description, 50) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            bg-{{ $package->package_type_color }}-100 text-{{ $package->package_type_color }}-800 dark:bg-{{ $package->package_type_color }}-900 dark:text-{{ $package->package_type_color }}-200 capitalize">
                                            {{ $package->package_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            bg-{{ $package->status_color }}-100 text-{{ $package->status_color }}-800 dark:bg-{{ $package->status_color }}-900 dark:text-{{ $package->status_color }}-200">
                                            {{ ucfirst(str_replace('_', ' ', $package->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $package->formatted_price }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            <div class="flex items-center space-x-4">
                                                <span title="Views">
                                                    <i class="fas fa-eye mr-1"></i>{{ number_format($package->views) }}
                                                </span>
                                                <span title="Shares">
                                                    <i class="fas fa-share mr-1"></i>{{ number_format($package->shares) }}
                                                </span>
                                                <span title="Engagement Rate">
                                                    <i class="fas fa-chart-line mr-1"></i>{{ $package->engagement_rate }}%
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div>
                                            <div>{{ $package->formatted_duration }}</div>
                                            <div class="text-xs">{{ $package->number_of_videos }} video{{ $package->number_of_videos > 1 ? 's' : '' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('restaurant.video-packages.show', [$restaurant->slug, $package->id]) }}" 
                                               class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                View
                                            </a>
                                            <a href="{{ route('restaurant.video-packages.edit', [$restaurant->slug, $package->id]) }}" 
                                               class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                Edit
                                            </a>
                                            <button onclick="deletePackage({{ $package->id }})" 
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-video text-purple-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No video packages yet</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">
                            Create professional video content to showcase your restaurant and attract more customers.
                        </p>
                        <a href="{{ route('restaurant.video-packages.create', $restaurant->slug) }}" 
                           class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-lg transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Create Your First Video Package
                        </a>
                    </div>
                @endif
            </div>

            @if($packages->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $packages->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function deletePackage(packageId) {
    if (!confirm('Are you sure you want to delete this video package? This action cannot be undone.')) {
        return;
    }
    
    fetch(`{{ route('restaurant.video-packages.destroy', [$restaurant->slug, 'PACKAGE_ID']) }}`.replace('PACKAGE_ID', packageId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            window.location.reload();
        } else {
            alert('Error deleting video package');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting video package');
    });
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const packageTypeFilter = document.getElementById('packageTypeFilter');
    
    function applyFilters() {
        const status = statusFilter.value;
        const packageType = packageTypeFilter.value;
        
        let url = new URL(window.location);
        if (status) url.searchParams.set('status', status);
        else url.searchParams.delete('status');
        
        if (packageType) url.searchParams.set('package_type', packageType);
        else url.searchParams.delete('package_type');
        
        window.location = url;
    }
    
    statusFilter.addEventListener('change', applyFilters);
    packageTypeFilter.addEventListener('change', applyFilters);
    
    // Set current filter values
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status')) statusFilter.value = urlParams.get('status');
    if (urlParams.get('package_type')) packageTypeFilter.value = urlParams.get('package_type');
});
</script>
@endsection
