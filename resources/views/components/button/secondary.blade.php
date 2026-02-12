@props([
    'danger' => null    
])

<button 
    {{ $attributes
        ->merge(['type' => 'button'])
        ->class([
            'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150',
            'bg-gray-800 dark:bg-gray-200 border-transparent text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300' => !$danger,
            'bg-white dark:bg-red-800 border-red-300 dark:border-red-500 text-red-700 dark:text-red-300 shadow-sm hover:bg-red-50 dark:hover:bg-red-700 disabled:opacity-25' => $danger
    ]) }}>
    {{ $slot }}
</button>
