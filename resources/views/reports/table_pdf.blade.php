<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #333; }
        .header { text-align: center; margin-bottom: 12px; }
        .header h1 { margin: 0; font-size: 16px; color: #1a56db; }
        .header h2 { margin: 4px 0 0; font-size: 12px; color: #111; }
        .header p { margin: 2px 0; color: #666; }
        .summary { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .summary td { border: 1px solid #e5e7eb; padding: 5px 6px; width: 25%; }
        .summary .label { color: #6b7280; font-size: 8px; text-transform: uppercase; }
        .summary .value { font-size: 11px; font-weight: bold; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #ddd; padding: 4px 5px; text-align: left; }
        table.data th { background-color: #f3f4f6; font-weight: bold; }
        table.data tr:nth-child(even) td { background: #fafafa; }
        .num { text-align: right; }
        .footer { margin-top: 16px; text-align: center; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $shop['name'] ?? config('app.name') }}</h1>
        @if (!empty($shop['address']))<p>{{ $shop['address'] }}</p>@endif
        @if (!empty($shop['phone']))<p>{{ $shop['phone'] }}</p>@endif
        <h2>{{ $title }}</h2>
        @foreach ($meta as $label => $value)
            <p>{{ $label }}: {{ $value }}</p>
        @endforeach
    </div>

    @if (!empty($summary))
        <table class="summary">
            @foreach (array_chunk($summary, 4, true) as $chunk)
                <tr>
                    @foreach ($chunk as $label => $value)
                        <td><div class="label">{{ $label }}</div><div class="value">{{ $value }}</div></td>
                    @endforeach
                    @for ($i = count($chunk); $i < 4; $i++)<td></td>@endfor
                </tr>
            @endforeach
        </table>
    @endif

    <table class="data">
        <thead>
            <tr>
                @foreach ($headings as $h)<th>{{ $h }}</th>@endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td class="{{ is_numeric($cell) ? 'num' : '' }}">{{ is_float($cell) ? number_format($cell, 2) : $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($headings) }}" style="text-align:center">No data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Generated on {{ now()->format('d M Y h:i A') }}</div>
</body>
</html>
