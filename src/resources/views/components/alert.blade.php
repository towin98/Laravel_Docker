@props(['id', 'color'])

<div role="alert" id="{{ $id }}"
    {{ $attributes->merge(['class' => 'mb-4 opacity-95 relative flex w-full p-3 text-sm text-white rounded-md']) }}>
    {{ $slot }}
    <button
        class="flex items-center justify-center transition-all w-8 h-8 rounded-md text-white hover:bg-white/10 active:bg-white/10 absolute top-1.5 right-1.5"
        onclick="closeAlert('{{ $id }}')" type="button">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
</div>

<script>
    function closeAlert(id) {
        const alertElement = document.getElementById(id);
        alertElement.style.display = 'none';
    }
</script>
