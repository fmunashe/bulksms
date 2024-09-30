<x-filament-panels::page>
{{--    {{ $this->form }}--}}
{{--    <button type="uploadData" class="success">Upload</button>--}}
            <form wire:submit.prevent="uploadData">
                <input type="file" wire:model="file" required>
                <button type="submit">Upload</button>
            </form>

            @if (session()->has('message'))
                <div>{{ session('message') }}</div>
            @endif

</x-filament-panels::page>
