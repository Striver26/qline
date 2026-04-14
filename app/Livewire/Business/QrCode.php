<?php

namespace App\Livewire\Business;

use Livewire\Component;

class QrCode extends Component
{
    public function render()
    {
        $business = auth()->user()->business;
        $url = route('public.join', ['slug' => $business->slug]);

        // Define options explicitly
        $options = new \chillerlan\QRCode\QROptions([
            'outputInterface' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
            'outputBase64' => false, // Raw SVG
            'svgAddXmlHeader' => false,
            'connectPaths' => true,
        ]);

        $qrCode = (new \chillerlan\QRCode\QRCode($options))->render($url);

        return view('livewire.business.qr-code', [
            'business' => $business,
            'qrCode' => $qrCode,
            'url' => $url,
            'joinCode' => $business->join_code ?? 'SETUP_REQUIRED'
        ])->layout('layouts.app');
    }
}
