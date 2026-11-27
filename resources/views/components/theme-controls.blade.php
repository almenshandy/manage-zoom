<button type="button" id="themeToggle" role="switch" aria-label="Mode gelap" aria-checked="false"
    class="flex w-11 h-11 md:w-8 md:h-8 items-center justify-center rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-100">
    <i class="fa-solid fa-moon" aria-hidden="true" data-theme-icon="light"></i>
    <i class="fa-solid fa-sun" aria-hidden="true" data-theme-icon="dark"></i>
</button>
<details class="relative">
    <summary class="flex w-11 h-11 md:w-8 md:h-8 list-none items-center justify-center rounded-sm border border-gray-300 text-gray-700 hover:bg-gray-100" aria-label="Pengaturan tampilan" title="Pengaturan tampilan">
        <i class="fa-solid fa-gear" aria-hidden="true"></i>
    </summary>
    <div class="absolute right-0 top-full z-40 mt-2 w-44 border border-gray-300 bg-white p-3 shadow-sm">
        <label for="themePreference" class="block text-xs font-medium mb-2">Tema tampilan</label>
        <select id="themePreference" class="w-full min-h-9 rounded-sm border border-gray-300 bg-white px-2 text-sm text-gray-800">
            <option value="system">Ikuti Sistem</option>
            <option value="light">Terang</option>
            <option value="dark">Gelap</option>
        </select>
    </div>
</details>
