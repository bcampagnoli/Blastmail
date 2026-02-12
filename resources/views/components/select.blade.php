<select 
    {{ $attributes->class(['w-full appearance-none rounded-md border border-neutral-300 bg-neutral-50 px-4 py-2 text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black disabled:cursor-not-allowed disabled:opacity-75 dark:border-neutral-900 dark:bg-neutral-900 dark:focus-visible:outline-white'])}}>
    {{ $slot }}
</select>