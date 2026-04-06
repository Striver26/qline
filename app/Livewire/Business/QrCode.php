<?php

namespace App\Livewire\Business;

use Livewire\Component;
use chillerlan\QRCode\QRCode as QrLib;
use chillerlan\QRCode\QROptions;

class QrCode extends Component
{
    public function render()
    {
        $business = auth()->user()->business;
        $joinCode = $business->join_code ?? 'SETUP_REQUIRED';

        $qlinePhone = config('qline.phone_number', '6012345678'); 
        $whatsappText = urlencode("JOIN {$joinCode}");
        $url = "https://wa.me/{$qlinePhone}?text={$whatsappText}";

        // Simplest safe wrapper for all v4/v5 chillerlan versions
        $svgRaw = (new QrLib())->render($url);
        
        $svg = $svgRaw;
        // if base64 encoded SVG
        if (str_starts_with($svgRaw, 'data:image/svg+xml')) {
            $decoded = base64_decode(substr($svgRaw, strpos($svgRaw, ',') + 1));
            if (strpos($decoded, '<svg') !== false) {
                $svg = substr($decoded, strpos($decoded, '<svg'));
            }
        }

        return view('livewire.business.qr-code', compact('business', 'svg', 'url', 'joinCode'))
            ->layout('layouts.app');
    }
}

