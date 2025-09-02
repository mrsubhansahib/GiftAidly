<!-- FIRST element inside <body> -->
<div id="global-loader" aria-hidden="true"
    style="position:fixed;inset:0;z-index:99999;background:#fff;display:grid;place-items:center;">
    <div class="loader-canvas" role="status" aria-label="Loading">
        <svg viewBox="0 0 220 220" class="loader-svg" aria-hidden="true" style="width:220px;height:220px;">
            <defs>
                <clipPath id="slotClip">
                    <rect x="60" y="80" width="80" height="30" />
                </clipPath>
            </defs>
            <!-- Paper before shredder -->
            <g id="paperBefore">
                <rect x="80" y="-80" width="40" height="50" class="icon" rx="2" ry="2" />
                <line x1="85" y1="-65" x2="115" y2="-65" class="icon" />
                <line x1="85" y1="-55" x2="110" y2="-55" class="icon" />
            </g>
            <!-- Paper inside slot -->
            <g id="paperGroup" clip-path="url(#slotClip)">
                <rect x="80" y="30" width="40" height="50" class="icon" rx="2" ry="2" />
                <line x1="85" y1="45" x2="115" y2="45" class="icon" />
                <line x1="85" y1="55" x2="110" y2="55" class="icon" />
            </g>
            <!-- Shredder -->
            <g id="shredder" class="icon">
                <rect x="60" y="80" width="80" height="30" />
                <line x1="60" y1="95" x2="140" y2="95" />
            </g>
            <!-- Strips -->
            <line x1="80" y1="110" x2="80" y2="140" class="icon strip delay-0" />
            <line x1="100" y1="110" x2="100" y2="140" class="icon strip delay-1" />
            <line x1="120" y1="110" x2="120" y2="140" class="icon strip delay-2" />
            <!-- Particles -->
            <circle class="particle delay-0" cx="90" cy="150" r="3" />
            <circle class="particle delay-1" cx="110" cy="150" r="3" />
            <circle class="particle delay-2" cx="100" cy="160" r="3" />
            <circle class="particle delay-3" cx="95" cy="155" r="3" />
            <!-- Laptop -->
            <g id="laptop" class="icon">
                <rect x="60" y="40" width="100" height="70" rx="4" ry="4" />
                <rect x="70" y="50" width="80" height="50" />
                <rect x="50" y="110" width="120" height="15" rx="2" ry="2" />
                <line x1="50" y1="118" x2="170" y2="118" />
            </g>
        </svg>
    </div>
</div>