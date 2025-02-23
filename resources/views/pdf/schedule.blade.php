<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Horario - {{ $group->name }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }

        body {
            font-family: 'Helvetica', sans-serif;
            color: #2d3748;
            line-height: 1.6;
            background-color: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 1px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
        }

        .school-name {
            color: #1a365d;
            font-size: 32px;
            font-weight: 400;
            margin: 0;
            letter-spacing: 2px;
        }

        .group-name {
            color: #4a5568;
            font-size: 18px;
            font-weight: 400;
            margin: 12px 0;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            box-shadow: 0 0 0 1px #e2e8f0;
        }

        th {
            background: #2c5282;
            color: white;
            padding: 16px;
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid #2a4365;
        }

        td {
            border: 1px solid #e2e8f0;
            padding: 12px;
            font-size: 13px;
            text-align: center;
        }

        .time-slot {
            background: #f7fafc;
            font-weight: 500;
            text-align: left;
            color: #2d3748;
        }

        .time-info {
            font-size: 11px;
            color: #718096;
            display: block;
            margin-top: 4px;
            font-weight: normal;
        }

        .break-row td {
            background: #ebf8ff;
        }

        .break-text {
            color: #2b6cb0;
            font-weight: 500;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            font-size: 11px;
            color: #718096;
            position: relative;
            padding-top: 20px;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 40%;
            height: 1px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
        }

        .subject-cell {
            color: #2d3748;
            font-size: 13px;
            padding: 12px;
        }

        .logo-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.03;
            font-size: 150px;
            z-index: -1;
            color: #2c5282;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="school-name">{{ $school->name }}</h1>
        <h2 class="group-name">Horario Académico • {{ $group->name }}</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th style="border-radius: 4px 0 0 0">Hora</th>
                @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $day)
                    <th @if($loop->last) style="border-radius: 0 4px 0 0" @endif>{{ $day }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($timeSlots as $slot)
                <tr class="{{ $slot->is_break ? 'break-row' : '' }}">
                    <td class="time-slot">
                        {{ $slot->name }}
                        <span class="time-info">{{ $slot->start_time->format('H:i') }} - {{ $slot->end_time->format('H:i') }}</span>
                    </td>
                    @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'] as $day)
                        <td class="subject-cell">
                            @if($slot->is_break)
                                <span class="break-text">Descanso</span>
                            @else
                                {{ $scheduleEntries->first(function($entry) use ($slot, $day) {
                                    return $entry->time_slot_id === $slot->id && $entry->day === $day;
                                })?->subject ?? '—' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>{{ $school->name }} • Documento generado el {{ now()->format('d/m/Y') }}</p>
    </div>

    <div class="logo-watermark">
        {{ substr($school->name, 0, 1) }}
    </div>
</body>
</html>
