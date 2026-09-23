<div>
    @php
        $actionIcons = [
            'created'  => ['icon' => 'fa-plus-circle',  'color' => 'var(--success)'],
            'updated'  => ['icon' => 'fa-pen',           'color' => '#3b82f6'],
            'deleted'  => ['icon' => 'fa-trash',         'color' => 'var(--danger)'],
            'viewed'   => ['icon' => 'fa-eye',           'color' => 'var(--text-muted)'],
            'archived' => ['icon' => 'fa-archive',       'color' => '#f59e0b'],
            'restored' => ['icon' => 'fa-undo',          'color' => '#10b981'],
        ];
    @endphp

    @if(empty($grouped))
        <div class="empty-state">
            <i class="fas fa-history"></i>
            <p>No hay registros en el historial todavía.</p>
        </div>
    @else
        <div style="display:flex; flex-direction:column; gap:4px;">
            @foreach($grouped as $i => $group)
                @php
                    $icon = $actionIcons[$group['action']] ?? ['icon' => 'fa-circle', 'color' => 'var(--text-muted)'];
                    $hasChildren = count($group['children']) > 0;
                    $userName = $group['user']->name ?? 'Sistema';
                    $totalCount = 1 + count($group['children']);
                @endphp

                <div x-data="{ open: false }"
                     style="border-bottom: 1px solid rgba(255,255,255,0.04); padding: 10px 0;">

                    <div style="display:flex; align-items:center; gap:12px; cursor: {{ $hasChildren ? 'pointer' : 'default' }};"
                         @if($hasChildren) @click="open = !open" @endif>
                        {{-- Icon --}}
                        <div style="width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,0.05); flex-shrink:0;">
                            <i class="fas {{ $icon['icon'] }}" style="font-size:12px; color:{{ $icon['color'] }};"></i>
                        </div>

                        {{-- Content --}}
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:13px; line-height:1.4;">
                                <strong style="color:var(--accent-primary);">{{ $userName }}</strong>
                                <span style="color:var(--text-secondary); margin-left:4px;">{{ $group['description'] }}</span>
                            </div>
                            <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                                {{ $group['created_at']->format('d/m/Y') }}
                                <span style="margin-left:6px;">{{ $group['created_at']->format('h:i A') }}</span>
                            </div>
                        </div>

                        {{-- Expand arrow if has children --}}
                        @if($hasChildren)
                            <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                                <span style="font-size:11px; background:rgba(99,102,241,0.15); color:#818cf8; padding:2px 8px; border-radius:12px; font-weight:600;">
                                    ×{{ $totalCount }}
                                </span>
                                <i class="fas fa-chevron-down" style="font-size:11px; color:var(--text-muted); transition: transform 0.2s;"
                                   :style="open ? 'transform: rotate(180deg)' : ''"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Expandable Children --}}
                    @if($hasChildren)
                        <div x-show="open" x-collapse style="margin-top:8px; padding-left:44px; border-left:2px solid rgba(255,255,255,0.06);">
                            {{-- Show main entry as first child --}}
                            <div style="font-size:12px; color:var(--text-muted); padding:4px 0;">
                                <i class="fas fa-clock" style="font-size:10px;"></i>
                                {{ $group['created_at']->format('h:i A') }}
                                <span style="margin-left:6px; color:var(--text-secondary);">{{ $group['description'] }}</span>
                            </div>
                            @foreach($group['children'] as $child)
                                <div style="font-size:12px; color:var(--text-muted); padding:4px 0;">
                                    <i class="fas fa-clock" style="font-size:10px;"></i>
                                    {{ $child->created_at->format('h:i A') }}
                                    <span style="margin-left:6px; color:var(--text-secondary);">{{ $child->description }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
