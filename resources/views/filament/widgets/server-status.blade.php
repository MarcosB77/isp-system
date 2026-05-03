<div style="padding: 12px 16px; border-top: 1px solid rgba(255,255,255,0.08); margin-top: 8px;">
    <p style="font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .06em; margin: 0 0 10px;">Servidor</p>
    <div style="display: flex; flex-direction: column; gap: 8px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 12px; color: #9ca3af;">Uptime</span>
            <span style="font-size: 12px; color: #1D9E75; font-weight: 500;">{{ $uptime }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 12px; color: #9ca3af;">Memoria</span>
            <span style="font-size: 12px; color: #378ADD; font-weight: 500;">{{ $mem }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 12px; color: #9ca3af;">CPU</span>
            <span style="font-size: 12px; color: #EF9F27; font-weight: 500;">{{ $cpu }}%</span>
        </div>
    </div>
</div>