<!DOCTYPE html>
<html>
<head>
    <title>Ledger Transcript - {{ date('Y-m-d') }}</title>
    <style>
        @page { margin: 0.3in; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Helvetica', Arial, sans-serif; 
            color: #1a1a1a; 
            font-size: 8px; 
            line-height: 1.2; 
            background: #fff; 
        }

        /* Header & Identity */
        .page-header { 
            border-bottom: 3px solid #0F6CBD; 
            padding-bottom: 10px; 
            margin-bottom: 15px; 
        }
        .app-title { font-size: 20px; font-weight: 900; color: #242424; letter-spacing: -0.5px; }
        .protocol-tag { font-size: 8px; font-weight: bold; color: #0F6CBD; text-transform: uppercase; letter-spacing: 2px; }
        
        /* Summary Grid */
        .summary-bar { 
            display: table; 
            width: 100%; 
            background: #fcfcfc; 
            border: 1px solid #eee; 
            margin-bottom: 20px; 
        }
        .summary-item { 
            display: table-cell; 
            padding: 10px 15px; 
            border-right: 1px solid #eee; 
            vertical-align: middle;
        }
        .summary-item:last-child { border-right: none; }
        .stat-label { font-size: 6px; font-weight: bold; color: #888; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 2px; }
        .stat-value { font-size: 13px; font-weight: 800; color: #242424; }
        .val-income { color: #107C10; }
        .val-expense { color: #D1102A; }
        .val-net { color: #0F6CBD; }

        /* Main Table */
        .ledger-table { width: 100%; border-collapse: collapse; }
        .ledger-table thead th { 
            text-align: left; 
            padding: 6px 8px; 
            background: #fafafa; 
            border-bottom: 1.5px solid #0F6CBD; 
            font-size: 7px; 
            font-weight: 900; 
            color: #444;
            text-transform: uppercase;
        }
        .ledger-table tbody td { 
            padding: 5px 8px; 
            border-bottom: 0.5px solid #f0f0f0; 
            vertical-align: top;
            color: #333;
        }
        .ledger-table tbody tr:nth-child(even) { background: #fafafa; }

        /* Status & Type Indicators */
        .type-dot { 
            display: inline-block; 
            width: 5px; 
            height: 5px; 
            border-radius: 5px; 
            margin-right: 4px;
        }
        .dot-income { background: #107C10; }
        .dot-expense { background: #D1102A; }
        
        .status-badge {
            font-size: 6px;
            font-weight: bold;
            padding: 1px 4px;
            border-radius: 2px;
            text-transform: uppercase;
            background: #eee;
            color: #666;
        }
        .status-approved { background: #dff6dd; color: #107c10; }
        .status-submitted { background: #c7e0f4; color: #005a9e; }

        /* Amounts */
        .amt { font-weight: 800; text-align: right; font-family: 'Courier', monospace; font-size: 9px; }
        .amt-pos { color: #107C10; }
        .amt-neg { color: #D1102A; }

        .footer { 
            position: fixed; 
            bottom: -10px; 
            left: 0; 
            right: 0; 
            font-size: 6px; 
            color: #ccc; 
            text-align: center; 
            border-top: 0.5px solid #eee;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="page-header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="protocol-tag">Fiscal Core Protocol</div>
                    <div class="app-title">DIR-EXPENSE <span style="font-weight: 300; opacity: 0.5;">| Ledger Transcript</span></div>
                </td>
                <td style="text-align: right; vertical-align: bottom;">
                    <div style="font-size: 7px; font-weight: bold; color: #666;">
                        GENERATED: {{ now()->format('d M Y, H:i') }}<br>
                        PERIOD: {{ $startDate ? $startDate->format('M Y') : 'FULL HISTORY' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @php
        $totalInc = $lines->where('type', 'income')->sum('amount');
        $totalExp = $lines->where('type', 'expense')->sum('amount');
        $net = $totalInc - $totalExp;
    @endphp

    <div class="summary-bar">
        <div class="summary-item">
            <span class="stat-label">Transactions</span>
            <span class="stat-value">{{ number_format($lines->count()) }}</span>
        </div>
        <div class="summary-item">
            <span class="stat-label">Total Revenue</span>
            <span class="stat-value val-income">Rs. {{ number_format($totalInc, 0) }}</span>
        </div>
        <div class="summary-item">
            <span class="stat-label">Total Expenditure</span>
            <span class="stat-value val-expense">Rs. {{ number_format($totalExp, 0) }}</span>
        </div>
        <div class="summary-item">
            <span class="stat-label">Net Fiscal Position</span>
            <span class="stat-value val-net {{ $net < 0 ? 'val-expense' : 'val-income' }}">
                Rs. {{ number_format($net, 0) }}
            </span>
        </div>
    </div>

    <table class="ledger-table">
        <thead>
            <tr>
                <th width="8%">Date</th>
                <th width="10%">Voucher</th>
                <th width="12%">Director</th>
                <th width="12%">Category</th>
                <th>Description</th>
                <th width="8%">Status</th>
                <th width="12%" style="text-align: right;">Amount (PKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lines as $line)
                @php $isInc = ($line->type === 'income'); @endphp
                <tr>
                    <td>{{ $line->date->format('d/m/Y') }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $line->report->voucher_no ?? '—' }}</td>
                    <td style="font-weight: bold;">{{ $line->report?->director?->name ?? '—' }}</td>
                    <td>{{ $line->category?->name ?? '—' }}</td>
                    <td style="font-style: italic; color: #666;">{{ $line->description }}</td>
                    <td>
                        <span class="status-badge status-{{ $line->report->status ?? 'default' }}">
                            {{ strtoupper($line->report->status ?? 'Draft') }}
                        </span>
                    </td>
                    <td class="amt {{ $isInc ? 'amt-pos' : 'amt-neg' }}">
                        {{ $isInc ? '+' : '−' }} {{ number_format($line->amount, 0) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        DIR-EXPENSE AUDIT TRANSCRIPT v4.2 • SECURED LEDGER RECORD • CONFIDENTIAL
    </div>
</body>
</html>
