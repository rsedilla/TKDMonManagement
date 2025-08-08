<div class="space-y-6">
    <div class="bg-white roun                                <div>
                                    <p class="font-medium text-gray-900">{{ $member->name }}</p>
                                    <p class="text-sm text-gray-600">Age {{ $member->age }}</p>
                                    <p class="text-xs text-gray-500">Joined: {{ $member->enrollment_date?->format('M Y') ?? 'N/A' }}</p>
                                </div>g border border-gray-200 p-6">
        <div class="flex items-center space-x-4 mb-6">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900">{{ $leader->name }}</h3>
                <p class="text-sm text-gray-600">{{ $leader->position }} • Level {{ $leader->level }}</p>
                <p class="text-xs text-gray-500">{{ $leader->department }}</p>
            </div>
            <div class="ml-auto">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Network Size: {{ $leader->getNetworkSize() }}
                </span>
            </div>
        </div>

        <!-- Hierarchy Breadcrumb -->
        @if($leader->parentLeader)
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">Reports to:</p>
                <div class="flex items-center text-sm">
                    @foreach($leader->getHierarchyBreadcrumb() as $index => $name)
                        @if($index > 0)
                            <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        @endif
                        <span class="{{ $index === count($leader->getHierarchyBreadcrumb()) - 1 ? 'font-semibold text-blue-600' : 'text-gray-600' }}">
                            {{ $name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Direct Cell Members -->
        @if($leader->cellMembers->count() > 0)
            <div class="mb-6">
                <h4 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Direct Cell Members ({{ $leader->cellMembers->count() }})
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($leader->cellMembers as $member)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $member->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $member->grade }} • Age {{ $member->age }}</p>
                                    <p class="text-xs text-gray-500">Joined: {{ $member->enrollment_date?->format('M Y') ?? 'N/A' }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($member->status)
                                        <span class="inline-block w-3 h-3 bg-green-400 rounded-full"></span>
                                    @else
                                        <span class="inline-block w-3 h-3 bg-gray-400 rounded-full"></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Subordinate Cell Leaders and Their Teams -->
        @if($leader->childLeaders->count() > 0)
            <div>
                <h4 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Subordinate Cell Leaders ({{ $leader->childLeaders->count() }})
                </h4>
                
                <div class="space-y-4">
                    @foreach($leader->childLeaders as $childLeader)
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <!-- Child Cell Leader Info -->
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $childLeader->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $childLeader->position }} • Level {{ $childLeader->level }}</p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $childLeader->cellMembers->count() }} Members
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $childLeader->childLeaders->count() }} Sub-cell-leaders
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Network: {{ $childLeader->getNetworkSize() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Child Cell Leader's Cell Members -->
                            @if($childLeader->cellMembers->count() > 0)
                                <div class="ml-8">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Cell Members:</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach($childLeader->cellMembers as $member)
                                            <div class="bg-white border border-gray-200 rounded-lg p-2">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                                                        <p class="text-xs text-gray-600">Age {{ $member->age }}</p>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        @if($member->status)
                                                            <span class="inline-block w-2 h-2 bg-green-400 rounded-full"></span>
                                                        @else
                                                            <span class="inline-block w-2 h-2 bg-gray-400 rounded-full"></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Show if there are further sub-cell-leaders -->
                            @if($childLeader->childLeaders->count() > 0)
                                <div class="ml-8 mt-3">
                                    <p class="text-sm text-gray-600">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Has {{ $childLeader->childLeaders->count() }} sub-cell-leader(s) with their own teams
                                        <a href="{{ route('filament.admin.resources.cell-leaders.edit', $childLeader) }}" 
                                           class="text-blue-600 hover:text-blue-800 ml-2">
                                            View Details →
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Network Summary -->
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <p class="text-2xl font-bold text-green-600">{{ $leader->cellMembers->count() }}</p>
                    <p class="text-sm text-gray-600">Direct Members</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-blue-600">{{ $leader->childLeaders->count() }}</p>
                    <p class="text-sm text-gray-600">Direct Cell Leaders</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-purple-600">{{ $leader->allChildLeaders()->count() }}</p>
                    <p class="text-sm text-gray-600">All Sub-Cell-Leaders</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-orange-600">{{ $leader->getNetworkSize() }}</p>
                    <p class="text-sm text-gray-600">Total Network</p>
                </div>
            </div>
        </div>
    </div>
</div>
