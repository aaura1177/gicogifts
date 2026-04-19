import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('cartDrawer', (initialCount) => ({
        cartOpen: false,
        mobileNav: false,
        cartCount: initialCount,
        boundCartUpdated: null,
        boundOpenDrawer: null,
        init() {
            this.boundCartUpdated = (e) => {
                if (e.detail && typeof e.detail.count === 'number') {
                    this.cartCount = e.detail.count;
                }
            };
            this.boundOpenDrawer = () => {
                this.cartOpen = true;
            };
            window.addEventListener('cart-updated', this.boundCartUpdated);
            window.addEventListener('open-cart-drawer', this.boundOpenDrawer);
        },
        destroy() {
            window.removeEventListener('cart-updated', this.boundCartUpdated);
            window.removeEventListener('open-cart-drawer', this.boundOpenDrawer);
        },
        openCart() {
            this.cartOpen = true;
        },
        closeCart() {
            this.cartOpen = false;
        },
        toggleMobileNav() {
            this.mobileNav = !this.mobileNav;
        },
        closeMobileNav() {
            this.mobileNav = false;
        },
        closeOverlays() {
            this.cartOpen = false;
            this.mobileNav = false;
        },
    }));

    Alpine.data('announcementBar', () => ({
        visible: true,
        init() {
            try {
                this.visible = localStorage.getItem('gg_announce_dismiss') !== '1';
            } catch {
                this.visible = true;
            }
        },
        dismiss() {
            try {
                localStorage.setItem('gg_announce_dismiss', '1');
            } catch {
                //
            }
            this.visible = false;
        },
    }));

    Alpine.data('addToCartBtn', (productId) => ({
        loading: false,
        message: '',
        async add() {
            this.loading = true;
            this.message = '';
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const url = window.ggRoutes?.cartAdd ?? '/cart/add';
            const body = new URLSearchParams({ product_id: String(productId), quantity: '1', _token: token });
            const res = await fetch(url, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body,
            });
            const data = await res.json().catch(() => ({}));
            this.loading = false;
            if (res.ok && data.ok) {
                this.message = data.message || 'Added to cart.';
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: { count: data.item_count } }));
                window.dispatchEvent(new CustomEvent('open-cart-drawer'));
            } else {
                this.message = 'Could not add to cart.';
            }
        },
    }));

    Alpine.data('productGallery', (images) => ({
        images: Array.isArray(images) ? images : [],
        current: 0,
        zoomOpen: false,
        boundEscape: null,
        get src() {
            return this.images[this.current] ?? '';
        },
        select(i) {
            this.current = i;
        },
        openZoom() {
            this.zoomOpen = true;
        },
        closeZoom() {
            this.zoomOpen = false;
        },
        init() {
            this.boundEscape = (e) => {
                if (e.key !== 'Escape' || !this.zoomOpen) {
                    return;
                }
                e.preventDefault();
                e.stopImmediatePropagation();
                this.closeZoom();
            };
            window.addEventListener('keydown', this.boundEscape, true);
        },
        destroy() {
            window.removeEventListener('keydown', this.boundEscape, true);
        },
    }));

    Alpine.data('productTabs', () => ({
        tab: 'story',
    }));

    const faqAccordion = () => ({
        openId: null,
        toggle(id) {
            this.openId = this.openId === id ? null : id;
        },
    });

    Alpine.data('accordionFaq', faqAccordion);
    Alpine.data('accordion', faqAccordion);

    Alpine.data('checkoutForm', (initialFields) => ({
        loading: false,
        error: '',
        deliveryHint: '',
        payment_gateway: 'razorpay',
        fields: {
            email: initialFields?.email ?? '',
            phone: initialFields?.phone ?? '',
            name: initialFields?.name ?? '',
            line1: initialFields?.line1 ?? '',
            line2: initialFields?.line2 ?? '',
            city: initialFields?.city ?? '',
            state: initialFields?.state ?? '',
            postal_code: initialFields?.postal_code ?? '',
            country: (initialFields?.country ?? 'IN').toString().toUpperCase(),
        },
        init() {
            this.syncGatewayFromCountry();
            this.checkDeliveryPin();
        },
        async checkDeliveryPin() {
            this.deliveryHint = '';
            const c = (this.fields.country || 'IN').toString().trim().toUpperCase() || 'IN';
            const pin = (this.fields.postal_code || '').toString().replace(/\D/g, '');
            if (c !== 'IN' || pin.length < 6) {
                return;
            }
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const url = window.ggRoutes?.checkoutServiceability ?? '/checkout/serviceability';
            const body = new URLSearchParams({
                postal_code: pin,
                country: c,
                _token: token,
            });
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body,
            });
            const data = await res.json().catch(() => ({}));
            if (data.message) {
                this.deliveryHint = data.message;
            }
        },
        syncGatewayFromCountry() {
            const c = (this.fields.country || 'IN').toString().trim().toUpperCase() || 'IN';
            this.fields.country = c;
            if (c !== 'IN') {
                this.payment_gateway = 'stripe';
            } else {
                this.payment_gateway = 'razorpay';
            }
        },
        loadRazorpayScript() {
            return new Promise((resolve, reject) => {
                if (window.Razorpay) {
                    resolve();
                    return;
                }
                const s = document.createElement('script');
                s.src = 'https://checkout.razorpay.com/v1/checkout.js';
                s.onload = () => resolve();
                s.onerror = () => reject(new Error('Could not load Razorpay'));
                document.body.appendChild(s);
            });
        },
        async submit() {
            this.loading = true;
            this.error = '';
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const url = window.ggRoutes?.checkoutPlace ?? '/checkout/place-order';
            const body = new URLSearchParams({
                email: this.fields.email,
                phone: this.fields.phone ?? '',
                name: this.fields.name,
                line1: this.fields.line1,
                line2: this.fields.line2 ?? '',
                city: this.fields.city,
                state: this.fields.state,
                postal_code: this.fields.postal_code,
                country: this.fields.country,
                payment_gateway: this.payment_gateway,
                _token: token,
            });
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body,
            });
            const data = await res.json().catch(() => ({}));
            this.loading = false;
            if (res.status === 422 && data.errors) {
                const first = Object.values(data.errors)[0];
                this.error = Array.isArray(first) ? first[0] : String(first);
                return;
            }
            if (!res.ok || !data.ok) {
                this.error = data.message || 'Checkout failed.';
                return;
            }
            if (data.gateway === 'stripe' && data.stripe_checkout_url) {
                window.location.href = data.stripe_checkout_url;
                return;
            }
            if (data.gateway === 'razorpay') {
                try {
                    await this.loadRazorpayScript();
                } catch (e) {
                    this.error = e.message || 'Payment script error';
                    return;
                }
                const key = data.razorpay_key || window.ggRazorpayKey;
                const options = {
                    key,
                    amount: data.amount,
                    currency: data.currency || 'INR',
                    order_id: data.razorpay_order_id,
                    name: 'GicoGifts',
                    description: `Order #${data.order_id}`,
                    prefill: data.prefill || {},
                    handler: () => {
                        window.location.href = data.success_url;
                    },
                    modal: {
                        ondismiss: () => {
                            //
                        },
                    },
                };
                const rzp = new window.Razorpay(options);
                rzp.open();
            }
        },
    }));

    Alpine.data('gigiWidget', () => ({
        open: false,
        input: '',
        messages: ['Hi! I am Gigi (preview). Ask about shipping or gifts.'],
        async send() {
            if (!this.input.trim()) {
                return;
            }
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const url = window.ggRoutes?.gigiChat ?? '/gigi/chat';
            const body = new URLSearchParams({ message: this.input, _token: token });
            this.messages.push(`You: ${this.input}`);
            this.input = '';
            const res = await fetch(url, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body,
            });
            const data = await res.json().catch(() => ({}));
            this.messages.push(`Gigi: ${data.reply ?? '…'}`);
        },
    }));
});

Alpine.start();
