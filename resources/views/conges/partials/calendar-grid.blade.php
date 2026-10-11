@php
    $dayNames = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    $firstDay = $debut->copy();
    $offset = $firstDay->dayOfWeekIso - 1;
    $daysInMonth = $fin->day;
    $totalCells = (int) (ceil(($offset + $daysInMonth) / 7) * 7);
    $today = now()->format('Y-m-d');
    $compact = $compact ?? false;
@endphp
<table style="width:100%;border-collapse:collapse;table-layout:fixed">
    <thead>
        <tr>
            @foreach($dayNames as $dn)
                <th style="padding:6px 2px;font-size:11px;font-weight:600;color:#475569;border-bottom:2px solid #cbd5e1;text-align:center;text-transform:uppercase;letter-spacing:0.05em">{{ $dn }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @for($cell = 0; $cell < $totalCells; $cell++)
            @if($cell % 7 === 0)<tr>@endif
            @php
                $dayNum = $cell - $offset + 1;
                $isValid = $dayNum >= 1 && $dayNum <= $daysInMonth;
                $date = $isValid ? $debut->copy()->addDays($dayNum - 1) : null;
                $isWeekend = $date && $date->dayOfWeekIso >= 6;
                $dateStr = $date?->format('Y-m-d');
                $isToday = $dateStr === $today;
                $bg = !$isValid ? '#f8fafc' : ($isWeekend ? '#f8fafc' : '#ffffff');
                $border = $isToday ? '2px solid #4f46e5' : '1px solid #e2e8f0';
            @endphp
            <td style="border:{{ $border }};background:{{ $bg }};vertical-align:top;height:{{ $compact ? '38px' : '84px' }};padding:4px">
                @if($isValid)
                <div style="font-size:11px;font-weight:700;color:{{ $isWeekend ? '#94a3b8' : '#334155' }};margin-bottom:2px">{{ $dayNum }}</div>
                <div style="display:flex;flex-direction:column;gap:2px">
                    @foreach($eventsByDay[$dateStr] ?? [] as $conge)
                        @php
                            $couleur = match($conge->statut) {
                                'valide' => ['#d1fae5', '#065f46'],
                                'refuse' => ['#ffe4e6', '#9f1239'],
                                default => ['#fef3c7', '#92400e'],
                            };
                        @endphp
                        <div style="background:{{ $couleur[0] }};color:{{ $couleur[1] }};border-radius:4px;padding:1px 4px;font-size:9px;line-height:1.3;overflow:hidden;white-space:nowrap;text-overflow:ellipsis" title="{{ $conge->employee?->nomComplet }} · {{ $conge->libelleType() }} · {{ $conge->jours }} jour(s)">
                            {{ $conge->employee?->initiales }} · {{ $conge->jours }}j
                        </div>
                    @endforeach
                </div>
                @endif
            </td>
            @if($cell % 7 === 6)</tr>@endif
        @endfor
    </tbody>
</table>