<div x-data="announcementBar()" x-show="visible" x-transition.opacity.duration.200ms class="border-b border-ivory-200 bg-ivory-100 text-center text-xs sm:text-sm text-chocolate-800">
    <div class="max-w-7xl mx-auto px-4 h-9 flex items-center justify-center gap-4">
        <p class="truncate">Free shipping above ₹2,000 · Handcrafted in Udaipur</p>
        <button type="button" class="shrink-0 rounded-full px-2 py-0.5 text-chocolate-600 hover:text-chocolate-900 hover:bg-ivory-200/80" @click="dismiss" aria-label="Dismiss announcement">Dismiss</button>
    </div>
</div>
