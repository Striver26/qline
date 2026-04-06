@props([
    'sidebar' => false,
])

<div {{ $attributes->merge(['class' => 'flex items-center']) }}>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Syne:wght@800&display=swap');

        .qline-custom-logo {
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            /* Default dark mode color from your snippet */
            color: #ededea; 
            text-decoration: none;
            letter-spacing: -0.02em;
        }

        /* Adjust color automatically for light mode backgrounds */
        html:not(.dark) .qline-custom-logo {
            color: #111827; 
        }

        .qline-custom-logo .qline-q {
            color: white;
            -webkit-text-stroke: 1.5px currentColor;
        }

        html:not(.dark) .qline-custom-logo .qline-q {
            color: #111827;
        }

        .qline-custom-logo em {
            font-style: normal;
            color: #14B8A6;
        }
    </style>
    
    <h1 class="qline-custom-logo"><span class="qline-q">Q</span><em>line</em></h1>
</div>
