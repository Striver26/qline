<div class="space-y-6">
    <div class="page-header">
        <div>
            <span class="page-kicker">Customer Entry Point</span>
            <h1 class="page-title mt-4">QR Code Standee</h1>
            <p class="page-description mt-3">
                Print this standee and place it where walk-ins arrive so customers can join the queue without asking staff what to do.
            </p>
        </div>

        <flux:button onclick="window.print()" variant="primary" class="mesh-accent rounded-full px-5 py-2.5 font-semibold text-white">
            <flux:icon.printer class="mr-2 h-4 w-4" />
            Print Standee
        </flux:button>
    </div>

    <div class="grid gap-6 xl:grid-cols-[0.85fr_1.15fr]">
        <div class="glass-card">
            <span class="page-kicker">How it works</span>
            <div class="mt-6 space-y-4">
                <div class="soft-card">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">1. Customers scan the QR code.</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">It opens the queue page directly on their phone.</p>
                </div>
                <div class="soft-card">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">2. They join in seconds.</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">They can use WhatsApp for updates or join as a simple web walk-in.</p>
                </div>
                <div class="soft-card">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">3. They track their turn live.</p>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Less crowding at the counter, less confusion for your team.</p>
                </div>
            </div>
        </div>

        <div class="glass-card flex justify-center" id="printable-area">
            <div class="w-full max-w-md overflow-hidden rounded-[2rem] border border-brand-200 bg-white shadow-[0_34px_90px_-44px_rgba(15,23,42,0.35)]">
                <div class="mesh-accent px-8 py-10 text-center text-white">
                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.28em] text-white/70">Join the line</p>
                    <h2 class="mt-4 text-4xl font-bold tracking-[-0.06em]">Queue Here</h2>
                    <p class="mt-3 text-sm text-white/80">Scan to join instantly and wait with live updates.</p>
                </div>

                <div class="px-8 py-10 text-center">
                    <div class="mx-auto inline-flex h-64 w-64 items-center justify-center rounded-[1.6rem] border border-slate-100 bg-white p-5 shadow-[0_24px_60px_-34px_rgba(15,23,42,0.24)] [&>svg]:h-full [&>svg]:w-full">
                        {!! $qrCode !!}
                    </div>

                    <p class="mt-8 text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-slate-400">Or message this keyword</p>
                    <p class="mt-3 text-4xl font-bold tracking-[0.14em] text-brand-700">JOIN {{ $joinCode }}</p>

                    <div class="mt-8 border-t border-slate-100 pt-6">
                        <x-app-logo class="mx-auto" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }

    #printable-area,
    #printable-area * {
        visibility: visible;
    }

    #printable-area {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        max-width: 520px;
        box-shadow: none;
    }
}
</style>
