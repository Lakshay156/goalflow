<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" fill="none" {{ $attributes }}>
    <defs>
        <linearGradient id="appLogoGrad" x1="10" y1="90" x2="90" y2="10" gradientUnits="userSpaceOnUse">
            <stop offset="0%"   stop-color="#7C3AED"/>
            <stop offset="55%"  stop-color="#6366F1"/>
            <stop offset="100%" stop-color="#22D3EE"/>
        </linearGradient>
        <linearGradient id="appSparkleGrad" x1="0" y1="0" x2="1" y2="1" gradientUnits="objectBoundingBox">
            <stop offset="0%"   stop-color="#67E8F9"/>
            <stop offset="100%" stop-color="#22D3EE"/>
        </linearGradient>
    </defs>
    <!-- G/C arc — opens at top-right (~1 o'clock), sweeps clockwise long-arc -->
    <path d="M 84 50 A 34 34 0 1 1 67 21"
          stroke="url(#appLogoGrad)" stroke-width="15" stroke-linecap="round"/>
    <!-- Arrow crossbar: horizontal then diagonal NE -->
    <path d="M 51 58 L 79 58 L 89 44"
          stroke="url(#appLogoGrad)" stroke-width="13" stroke-linecap="round" stroke-linejoin="round"/>
    <!-- 4-pointed sparkle -->
    <path d="M 82 17 L 83.8 23.2 L 90 25 L 83.8 26.8 L 82 33 L 80.2 26.8 L 74 25 L 80.2 23.2 Z"
          fill="url(#appSparkleGrad)"/>
</svg>
