@php
    $position = $entry['queue_position'] ?? 0;
    $ahead = max(0, $position - 1);
    $waitMins = $entry['estimated_wait_mins'] ?? 0;
    $ticketCode = $entry['ticket_code'] ?? '';
    $entryId = $entry['id'] ?? 0;
    
    // The business model isn't passed directly as an object, so we load it once using the passed ID
    $business = \App\Models\Tenant\Business::find($businessId);
@endphp

<div id="thermal-ticket-{{ $entryId }}" class="thermal-ticket-content hidden">
    <div style="width: 80mm; padding: 5mm; font-family: 'Inter', sans-serif; color: black; background: white; text-align: center;">
        <!-- Header -->
        <div style="font-size: 14pt; font-weight: bold; margin-bottom: 2mm; border-bottom: 1px dashed #ccc; padding-bottom: 2mm;">
            {{ $business?->name ?? 'Queue System' }}
        </div>
        
        <div style="font-size: 10pt; margin-bottom: 4mm; color: #666;">
            {{ now()->timezone($business?->timezone ?? config('app.timezone', 'UTC'))->format('d M Y, H:i') }}
        </div>

        <!-- Ticket Code -->
        <div style="margin: 5mm 0;">
            <div style="font-size: 12pt; text-transform: uppercase; letter-spacing: 2px; color: #888;">YOUR TICKET</div>
            <div style="font-size: 52pt; font-weight: 900; line-height: 1; margin: 2mm 0; font-variant-numeric: tabular-nums;">
                {{ $ticketCode }}
            </div>
        </div>

        <!-- Position -->
        <div style="margin-bottom: 5mm; border-bottom: 1px dashed #ccc; padding-bottom: 5mm;">
            <div style="font-size: 16pt; font-weight: bold;">Position: #{{ $position }}</div>
            <div style="font-size: 10pt; color: #666; margin-top: 1mm;">{{ $ahead }} customers ahead of you</div>
        </div>

        <!-- Wait Time -->
        <div style="margin-bottom: 5mm;">
            <div style="font-size: 10pt; text-transform: uppercase; color: #888; letter-spacing: 1px;">Est. Wait Time</div>
            <div style="font-size: 20pt; font-weight: bold; margin-top: 1mm;">
                ~{{ $waitMins }} <span style="font-size: 12pt;">mins</span>
            </div>
        </div>

        <!-- Footer -->
        <div style="border-top: 1px dashed #ccc; padding-top: 4mm; margin-top: 4mm;">
            <div style="font-size: 9pt; color: #666;">
                Please wait for your number to be called.<br>
                Thank you for your patience!
            </div>
            
            <div style="margin-top: 5mm; font-size: 8pt; font-weight: bold; opacity: 0.5; text-transform: uppercase; letter-spacing: 1px;">
                Powered by Qline
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            margin: 0;
            size: 80mm auto;
        }
        body * {
            visibility: hidden !important;
        }
        .thermal-ticket-content.is-printing, 
        .thermal-ticket-content.is-printing * {
            visibility: visible !important;
        }
        .thermal-ticket-content.is-printing {
            position: fixed;
            left: 0;
            top: 0;
            width: 80mm;
            display: block !important;
        }
    }
</style>
