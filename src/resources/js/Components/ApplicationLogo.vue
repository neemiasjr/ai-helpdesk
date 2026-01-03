<template>
    <svg
        viewBox="0 0 200 200"
        xmlns="http://www.w3.org/2000/svg"
        class="h-full w-full"
        preserveAspectRatio="xMidYMid meet"
        style="max-width: 100%; max-height: 100%;"
    >
        <!-- Background Circle -->
        <circle cx="100" cy="100" r="95" fill="url(#gradient)" />
        
        <!-- AI Brain/Head Icon -->
        <g transform="translate(100, 100)">
            <!-- Head shape -->
            <path
                d="M -35 -20 Q -40 -35 -30 -45 Q -20 -50 -10 -45 Q 0 -48 10 -45 Q 20 -50 30 -45 Q 40 -35 35 -20 Q 38 -5 35 10 Q 30 25 20 30 Q 10 35 0 32 Q -10 35 -20 30 Q -30 25 -35 10 Q -38 -5 -35 -20 Z"
                fill="white"
                opacity="0.95"
            />
            <!-- Neural network nodes -->
            <circle cx="-15" cy="-10" r="4" fill="#6366f1" />
            <circle cx="0" cy="-15" r="4" fill="#6366f1" />
            <circle cx="15" cy="-10" r="4" fill="#6366f1" />
            <circle cx="-10" cy="5" r="4" fill="#6366f1" />
            <circle cx="10" cy="5" r="4" fill="#6366f1" />
            <!-- Connections -->
            <line x1="-15" y1="-10" x2="0" y2="-15" stroke="#6366f1" stroke-width="1.5" opacity="0.6" />
            <line x1="0" y1="-15" x2="15" y2="-10" stroke="#6366f1" stroke-width="1.5" opacity="0.6" />
            <line x1="-15" y1="-10" x2="-10" y2="5" stroke="#6366f1" stroke-width="1.5" opacity="0.6" />
            <line x1="15" y1="-10" x2="10" y2="5" stroke="#6366f1" stroke-width="1.5" opacity="0.6" />
            <line x1="-10" y1="5" x2="10" y2="5" stroke="#6366f1" stroke-width="1.5" opacity="0.6" />
        </g>
        
        <!-- Gradient definition -->
        <defs>
            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#6366f1;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:1" />
            </linearGradient>
        </defs>
    </svg>
</template>
