<div class="space-y-8">

    {{-- ═══════════ Header ═══════════ --}}
    <div>
        <flux:subheading class="text-sm font-bold uppercase tracking-widest mb-1" style="color: #14B8A6;">Printable</flux:subheading>
        <flux:heading size="xl" class="text-4xl font-black tracking-tight text-gray-900 dark:text-white leading-none">QR Code Standee</flux:heading>
    </div>

    <div class="flex flex-col lg:flex-row gap-8 items-start">

        {{-- ▸ Instructions Panel --}}
        <div class="w-full lg:w-1/3 space-y-6">
            <flux:card class="p-0 overflow-hidden border-gray-100 dark:border-zinc-800">
                <div class="px-7 py-5 border-b border-gray-100 dark:border-zinc-800 flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/20">
                        <flux:icon.information-circle class="w-4 h-4 text-[#14B8A6]" />
                    </div>
                    <flux:heading class="text-base font-extrabold text-gray-900 dark:text-white">How it works</flux:heading>
                </div>
                <div class="p-6">
                    <ol class="space-y-5 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-7 h-7 rounded-xl flex items-center justify-center font-black text-xs text-white mr-3.5 mt-0.5" style="background: linear-gradient(135deg, #14B8A6, #0d9488);">1</span>
                            <flux:text class="font-medium leading-relaxed">Customers scan the QR code with their phone camera.</flux:text>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-7 h-7 rounded-xl flex items-center justify-center font-black text-xs text-white mr-3.5 mt-0.5" style="background: linear-gradient(135deg, #14B8A6, #0d9488);">2</span>
                            <flux:text class="font-medium leading-relaxed">WhatsApp opens with the text <strong class="font-black text-[#14B8A6]">"JOIN {{ $joinCode }}"</strong> pre-filled.</flux:text>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-7 h-7 rounded-xl flex items-center justify-center font-black text-xs text-white mr-3.5 mt-0.5" style="background: linear-gradient(135deg, #14B8A6, #0d9488);">3</span>
                            <flux:text class="font-medium leading-relaxed">They hit <strong>Send</strong> and are instantly added to your queue!</flux:text>
                        </li>
                    </ol>
                </div>
            </flux:card>

            <flux:button onclick="window.print()" variant="primary" class="w-full py-4 h-auto rounded-2xl font-bold shadow-lg hover:shadow-xl hover:scale-[1.01] transition-all" style="background: #14B8A6; border-color: #14B8A6;">
                <flux:icon.printer class="h-5 w-5 mr-2.5" />
                Print Standee
            </flux:button>
        </div>

        {{-- ▸ Printable Poster --}}
        <div class="w-full lg:w-2/3 flex justify-center" id="printable-area">
            <div class="bg-white rounded-[2rem] shadow-2xl overflow-hidden w-full max-w-sm border-2" style="border-color: #14B8A6;">
                {{-- Top Banner --}}
                <div class="py-8 px-6 text-center relative overflow-hidden" style="background: linear-gradient(135deg, #14B8A6, #0d9488);">
                    <div class="absolute -left-10 -top-10 w-32 h-32 rounded-full opacity-20 bg-white blur-2xl"></div>
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 rounded-full opacity-20 bg-white blur-2xl"></div>
                    <h2 class="relative text-3xl font-black text-white tracking-tight uppercase drop-shadow-sm">Queue Here</h2>
                    <p class="relative text-sm font-bold text-white/70 mt-1">Scan to join instantly</p>
                </div>

                {{-- QR Code --}}
                <div class="px-8 py-10 flex flex-col items-center">
                    <div class="bg-white p-5 rounded-2xl shadow-lg border border-gray-100 mb-8 inline-block">
                        {!! $svg !!}
                    </div>

                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Or text this to join</div>
                    <div class="text-3xl font-black tracking-wider" style="color: #0d9488;">JOIN {{ $joinCode }}</div>

                    <div class="mt-8 pt-6 border-t border-gray-100 w-full text-center">
                        <span class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">Powered by</span>
                        <span class="ml-1"><x-app-logo /></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #printable-area, #printable-area * { visibility: visible; }
    #printable-area {
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        border: none;
        box-shadow: none;
    }
}
</style>