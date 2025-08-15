<div class="p-4">
    <div class="flex items-center space-x-3 mb-6">
        <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Dependencies Found</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">This leader cannot be deleted due to the following dependencies:</p>
        </div>
    </div>

    <div class="space-y-4">
        @if($equippingCount > 0)
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-amber-600 dark:text-amber-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-amber-800 dark:text-amber-200">Equipping Records</h4>
                        <p class="text-sm text-amber-700 dark:text-amber-300">{{ $equippingCount }} equipping record(s) reference this leader</p>
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Remove or reassign these records before deletion</p>
                    </div>
                </div>
            </div>
        @endif

        @if($cellMembersCount > 0)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-blue-600 dark:text-blue-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Cell Members</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-300">{{ $cellMembersCount }} cell member(s) are assigned to this leader</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Reassign these members to another leader first</p>
                    </div>
                </div>
            </div>
        @endif

        @if($childLeadersCount > 0)
            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-purple-600 dark:text-purple-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-purple-800 dark:text-purple-200">Subordinate Leaders</h4>
                        <p class="text-sm text-purple-700 dark:text-purple-300">{{ $childLeadersCount }} leader(s) report to this leader</p>
                        <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">Reassign these leaders to another hierarchy level first</p>
                    </div>
                </div>
            </div>
        @endif

        @if($cellGroupsCount > 0)
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-green-800 dark:text-green-200">Cell Groups</h4>
                        <p class="text-sm text-green-700 dark:text-green-300">{{ $cellGroupsCount }} cell group(s) are led by this leader</p>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">Assign these groups to another leader first</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <h4 class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2">Recommended Actions:</h4>
        <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
            <li class="flex items-start">
                <span class="inline-block w-4 h-4 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-full text-xs text-center leading-4 mr-2 mt-0.5 flex-shrink-0">1</span>
                <span>Review and reassign all dependent records to other leaders</span>
            </li>
            <li class="flex items-start">
                <span class="inline-block w-4 h-4 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-full text-xs text-center leading-4 mr-2 mt-0.5 flex-shrink-0">2</span>
                <span>Use the "Assign Leader" action to reassign members and subordinates</span>
            </li>
            <li class="flex items-start">
                <span class="inline-block w-4 h-4 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-full text-xs text-center leading-4 mr-2 mt-0.5 flex-shrink-0">3</span>
                <span>Delete or archive the equipping records if no longer needed</span>
            </li>
            <li class="flex items-start">
                <span class="inline-block w-4 h-4 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-full text-xs text-center leading-4 mr-2 mt-0.5 flex-shrink-0">4</span>
                <span>Once all dependencies are resolved, deletion will be allowed</span>
            </li>
        </ol>
    </div>
</div>
