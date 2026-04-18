<!DOCTYPE html>
<html>
<head>
    <title>Expense Summary Report</title>
    <style>
        @page { margin: 0.5in; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 10px; line-height: 1.5; background: #fff; }

        /* Branding & Header */
        .header-container { border-bottom: 2px solid #0F6CBD; padding-bottom: 15px; margin-bottom: 25px; }
        .layout-table { width: 100%; border-collapse: collapse; }
        .layout-table td { vertical-align: bottom; border: none; }
        h1 { color: #0F6CBD; margin: 0; font-size: 20px; font-weight: bold; }
        .report-type { font-size: 8px; font-weight: bold; color: #888; text-transform: uppercase; letter-spacing: 2px; margin-top: 2px; }
        .period { font-size: 11px; font-weight: bold; color: #242424; text-align: right; }

        /* Summary Box */
        .summary-card { background: #F8F9FA; border: 1px solid #E0E0E0; padding: 15px; border-radius: 4px; margin-bottom: 30px; text-align: center; }
        .summary-label { font-size: 8px; font-weight: bold; color: #888; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 5px; }
        .summary-total { font-size: 18px; font-weight: bold; color: #0F6CBD; }

        /* Data Table */
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .summary-table th { 
            background-color: #0F6CBD; 
            color: #ffffff; 
            font-size: 8px; 
            text-transform: uppercase; 
            padding: 10px; 
            text-align: left; 
            letter-spacing: 0.5px;
            border: 1px solid #0F6CBD;
        }
        .summary-table td { padding: 8px 10px; border-bottom: 1px solid #eee; font-size: 9px; vertical-align: top; }
        .summary-table tr:nth-child(even) { background-color: #fafafa; }
        
        .amount-col { text-align: right; font-weight: bold; white-space: nowrap; color: #242424; }

        .footer { 
            position: fixed; 
            bottom: -0.2in; 
            left: 0; 
            right: 0; 
            text-align: center; 
            font-size: 7.5px; 
            color: #aaa; 
            border-top: 1px solid #eee; 
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header-container">
        <table class="layout-table">
            <tr>
                <td>
                    <h1>Fiscal Intelligence Report</h1>
                    <p class="report-type">DIR-EXPENSE &bull; Expenditure Summary Analyzer</p>
                </td>
                <td class="period">
                    <span class="text-muted" style="font-size: 8px; font-weight: normal; display: block; margin-bottom: 3px;">Report Period</span>
                    {{ $startDate->format('d M Y') }} &mdash; {{ $endDate->format('d M Y') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-card">
        <span class="summary-label">Total Period Output</span>
        <span class="summary-total">Rs. {{ number_format($total, 0) }}</span>
    </div>

    <table class="summary-table">
        <thead>
            <tr>
                <th width="12%">Date</th>
                <th width="18%">Director</th>
                <th width="15%">Category</th>
                <th>Description</th>
                <th width="15%" style="text-align: right;">Amount (PKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lines as $line)
                <tr>
                    <td>{{ $line->date->format('d M Y') }}</td>
                    <td style="font-weight: bold;">{{ $line->report->director->name }}</td>
                    <td>{{ $line->category->name }}</td>
                    <td style="color: #666; font-size: 8.5px;">{{ $line->description }}</td>
                    <td class="amount-col">Rs. {{ number_format($line->amount, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated via DIR-EXPENSE v4.2 &bull; Office Official Document &bull; {{ now()->format('d M Y, H:i') }}
    </div>
</body>
</html>
