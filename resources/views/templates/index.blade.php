<x-layouts.app>
    <x-slot name="header">
        <x-h2> {{ __('Templates') }} </x-h2>
    </x-slot>

    <x-card class="space-y-4">
        <div class="flex justify-between">
            <x-button.link :href="route('templates.create')">
                {{ __('Create new template') }}
            </x-button.link>

            <x-form :action="route('templates.index')" class="w-3/5 flex space-x-4 items-center" flat x-data x-ref="form">
                <x-input.checkbox value="1" name="withTrashed" :label="__('Show deleted records')" @click="$refs.form.submit()" :checked="$withTrashed" />
               
                <x-input.text name="search" :placeholder="__('Search')" :value="$search" class="w-full"/>
            </x-form>
        </div>
        <x-table :headers="[
            '#',
            __('Name'),
            __('Actions'),
        ]">

            <x-slot name="body">
                @foreach ($templates as $template)
                    <tr>
                        <x-table.td class="w-1">{{ $template->id }}</x-table.td>
                        <x-table.td>{{ $template->name }}</x-table.td>
                        <x-table.td class="flex items-center space-x-4 w-1">
                            <x-button.link secondary :href="route('templates.show', $template)">{{ __('Preview') }}</x-button.link >
                            <x-button.link secondary :href="route('templates.edit', $template)">{{ __('Edit') }}</x-button.link >
                            @unless($template->trashed())
                                <x-form :action="route('templates.destroy', $template)" delete flat onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    <x-button.secondary type="submit">Delete</x-button.secondary>
                                </x-form>
                            @else
                                <x-badge danger>{{ __('Deleted') }}</x-badge>
                                

                            @endunless
                        </x-table.td>
                    </tr>
                @endforeach
            </x-slot>
        </x-table>

        {{ $templates->links() }}

    </x-card>
</x-layouts.app>
