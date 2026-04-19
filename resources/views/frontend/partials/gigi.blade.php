<div class="fixed bottom-6 right-6 z-40" x-data="gigiWidget()">
    <button type="button" @click="open = !open" class="min-h-[44px] rounded-full bg-sienna-500 px-5 py-3 text-sm font-medium text-white shadow-warm hover:bg-sienna-600">Gigi</button>
    <div x-show="open" x-cloak x-transition class="absolute bottom-16 right-0 flex max-h-96 w-80 flex-col overflow-hidden rounded-2xl border border-ivory-200 bg-white shadow-warm">
        <div class="border-b border-ivory-200 px-3 py-2 font-display text-sm font-medium text-chocolate-900">Ask Gigi</div>
        <div class="max-h-48 space-y-2 overflow-y-auto px-3 py-2 text-xs text-chocolate-800">
            <template x-for="(m, i) in messages" :key="i">
                <p class="whitespace-pre-wrap leading-relaxed" x-text="m"></p>
            </template>
        </div>
        <form class="flex gap-2 border-t border-ivory-200 p-2" @submit.prevent="send">
            <input x-model="input" type="text" class="min-h-[44px] flex-1 rounded-lg border border-ivory-200 px-2 text-sm" placeholder="Message…">
            <button type="submit" class="min-h-[44px] rounded-lg bg-sienna-500 px-3 text-xs font-medium text-white hover:bg-sienna-600">Send</button>
        </form>
    </div>
</div>
