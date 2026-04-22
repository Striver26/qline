<div id="print-area" class="justify-center">

    <div class="bg-white rounded-[2rem] overflow-hidden border border-brand-200">

        {{-- HEADER --}}
        <div class="mesh-accent text-center py-4 px-6 border border-brand-200">
            <p class="text-xs text-slate-400 tracking-wide text-white">
                Join the line at
            </p>

            <h2 class="mt-2 text-3xl uppercase font-black font-bold leading-tight">
                {{ $business->name }}
            </h2>

            @if ($business->address)
                <p class="mt-2 text-sm text-slate-500 text-white">
                    {{ $business->address }}
                </p>
            @endif

            @if ($business->postcode && $business->city)
                <p class="text-sm text-slate-500 text-white">
                    {{ $business->postcode }}, {{ $business->city }}
                </p>
            @endif
        </div>

        {{-- QR SECTION --}}
        <div class="text-center px-6 py-6 items-center">

            <p class="text-xs font-bold text-brand-600 uppercase tracking-wide mb-4">
                Skip waiting in line • Get your turn instantly
            </p>

            <div class="qr-frame">
                <div class="qr-corner tl"></div>
                <div class="qr-corner tr"></div>
                <div class="qr-corner bl"></div>
                <div class="qr-corner br"></div>

                <div class="qr-inner">
                    {!! $qrCode !!}
                </div>
            </div>

        </div>

        {{-- DIVIDER --}}
        <div class="h-px bg-gradient-to-r from-transparent via-brand-200 to-transparent mx-6"></div>

        {{-- INSTRUCTIONS --}}
        <div class="px-6 py-6">

            <p class="text-[10px] uppercase tracking-[0.3em] text-brand-600 font-bold mb-4">
                How it works
            </p>

            <div class="space-y-4">

                <div class="flex items-start gap-3">
                    <div
                        class="w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs font-bold">
                        1</div>
                    <p class="text-sm text-slate-700">
                        Scan the QR code with your phone
                    </p>
                </div>

                <div class="flex items-start gap-3">
                    <div
                        class="w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs font-bold">
                        2</div>
                    <p class="text-sm text-slate-700">
                        Send <span class="font-bold text-brand-600">JOIN {{ $joinCode }}</span> via WhatsApp
                    </p>
                </div>

                <div class="flex items-start gap-3">
                    <div
                        class="w-6 h-6 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs font-bold">
                        3</div>
                    <p class="text-sm text-slate-700">
                        Get notified when it's your turn
                    </p>
                </div>

            </div>
        </div>

        {{-- FOOTER --}}
        <div class="mesh-accent border-t border-slate-500 px-6 py-4 flex justify-between items-center">
            <p class="text-sm text-white">
                Code: <span class="font-mono font-bold text-white">{{ $joinCode }}</span>
            </p>

            <p class="text-sm font-semibold text-white">
                www.qline.my
            </p>
        </div>

    </div>
</div>

{{-- PRINT --}}
<style>
    .qr-frame {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        border-radius: 16px;
        border: 1.8px solid rgba(20, 184, 166, 0.25);
        position: relative;
    }

    /* 🔥 THIS FIXES EVERYTHING */
    .qr-inner {
        width: 100%;
        height: 100%;
        align-items: center;
        border-radius: 5px;
    }

    .qr-inner svg {
        width: 100%;
        height: 100%;
    }

    .qr-corner {
        position: absolute;
        width: 22px;
        height: 22px;
        border-color: #14B8A6;
        border-style: solid;
    }

    .qr-corner.tl {
        top: 32px;
        left: 32px;
        border-width: 3px 0 0 3px;
        border-radius: 3px 0 0 0;
    }

    .qr-corner.tr {
        top: 32px;
        right: 32px;
        border-width: 3px 3px 0 0;
        border-radius: 0 3px 0 0;
    }

    .qr-corner.bl {
        bottom: 32px;
        left: 32px;
        border-width: 0 0 3px 3px;
        border-radius: 0 0 0 3px;
    }

    .qr-corner.br {
        bottom: 32px;
        right: 32px;
        border-width: 0 3px 3px 0;
        border-radius: 0 0 3px 0;
    }

    @media print {

        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            background: white !important;
        }

        .mesh-accent {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* 🔥 ONLY PRINT THIS */
        body * {
            visibility: hidden;
        }

        #print-area,
        #print-area * {
            visibility: visible;
        }

        /* 🔥 FORCE EXACT A4 FIT */
        #print-area {
            position: fixed;
            top: 0;
            left: 0;

            width: 210mm;
            height: 297mm;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* 🔥 CONTROL YOUR CARD SIZE */
        #print-area>div {
            width: 180mm;
            /* safe margin */
            max-height: 280mm;
        }

        /* 🔥 SCALE QR FOR PRINT */
        .qr-inner {
            width: 130mm !important;
            height: 130mm !important;
        }

        /* 🔥 PREVENT PAGE BREAK */
        * {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    }
</style>