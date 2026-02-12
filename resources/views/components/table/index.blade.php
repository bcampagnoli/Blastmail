@props([
    'headers',
    'body'
])

<div class="overflow-hidden w-full overflow-x-auto rounded-radius border border-outline dark:border-outline-dark">

    <table class="w-full text-left text-sm text-on-surface dark:text-on-surface-dark">
        <thead class="border-b border-outline bg-surface-alt text-sm text-on-surface-strong dark:border-outline-dark dark:bg-surface-dark-alt dark:text-on-surface-dark-strong">
            <tr>
                @foreach($headers as $header)
                    <th scope="col" class="p-4">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-outline dark:divide-outline-dark">
            {{ $body }}
        </tbody>
    </table>
</div>