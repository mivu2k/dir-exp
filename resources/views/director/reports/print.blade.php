<!DOCTYPE html>
<html>
<head>
    <title>Voucher: {{ $report->voucher_no }}</title>
    <style>
        /* High-Density Print Optimization */
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', sans-serif; color: #111; font-size: 8.5pt; line-height: 1.2; margin: 0; padding: 0; }
        .branding { font-size: 7.5pt; color: #0F6CBD; font-weight: bold; border-bottom: 2pt solid #0F6CBD; padding-bottom: 4pt; margin-bottom: 10pt; text-transform: uppercase; letter-spacing: 1pt; }
        
        .voucher-header { width: 100%; margin-bottom: 15pt; border-collapse: collapse; }
        .voucher-header td { vertical-align: top; padding: 2pt 0; }
        .meta-label { font-size: 7pt; color: #888; text-transform: uppercase; font-weight: bold; margin-bottom: 1pt; }
        .meta-value { font-size: 9pt; font-weight: bold; margin-bottom: 6pt; }
        
        .status-badge { display: inline-block; padding: 2pt 6pt; border: 1pt solid #444; font-size: 7pt; font-weight: bold; text-transform: uppercase; border-radius: 2pt; background: #eee; }
        
        .section-title { font-size: 7.5pt; font-weight: black; text-transform: uppercase; color: #000; margin-bottom: 6pt; margin-top: 15pt; border-bottom: 0.5pt solid #ddd; padding-bottom: 2pt; letter-spacing: 0.5pt; }
        
        .line-table { width: 100%; border-collapse: collapse; margin-bottom: 10pt; table-layout: fixed; }
        .line-table th, .line-table td { padding: 4pt 6pt; border: 0.5pt solid #eee; text-align: left; word-wrap: break-word; }
        .line-table th { font-size: 7pt; text-transform: uppercase; color: #0F6CBD; background-color: #fcfcfc; font-weight: bold; }
        .line-table td { border-bottom: 0.5pt solid #f0f0f0; }
        
        .summary-box { width: 180pt; margin-left: auto; margin-top: 10pt; border-top: 1.5pt solid #0F6CBD; padding-top: 6pt; }
        .grand-total { font-size: 14pt; font-weight: bold; color: #000; text-align: right; letter-spacing: -0.5pt; }
        
        .footer { position: fixed; bottom: -20pt; width: 100%; text-align: center; font-size: 7pt; color: #bbb; padding-top: 6pt; border-top: 0.5pt solid #eee; }
        
        /* Zebra striping for better readability at small scales */
        .line-table tr:nth-child(even) { background-color: #fafafa; }
    </style>
</head>
<body>
    <div class="branding">
        DIR-EXPENSE | Official Financial Transcript
    </div>

    <table class="voucher-header">
        <tr>
            <td width="65%">
                <div class="meta-label">Title</div>
                <div class="meta-value">{{ $report->title }}</div>
                
                <table width="100%">
                    <tr>
                        <td width="50%">
                            <div class="meta-label">Director</div>
                            <div class="meta-value" style="font-size: 8.5pt;">{{ $report->director->name }}</div>
                        </td>
                        <td>
                            <div class="meta-label">Reporting Period</div>
                            <div class="meta-value" style="font-size: 8.5pt;">{{ date("F Y", mktime(0, 0, 0, $report->period_month, 1, $report->period_year)) }}</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="35%" style="text-align: right;">
                <div class="meta-label">Voucher Number</div>
                <div class="meta-value" style="font-family: monospace; font-size: 13pt; color: #0F6CBD;">{{ $report->voucher_no }}</div>
                
                <div class="meta-label">Record Status</div>
                <div class="status-badge">{{ $report->status }}</div>
            </td>
        </tr>
    </table>

    <div style="display: table; width: 100%;">
        <div style="display: table-cell; width: 45%; vertical-align: top; padding-right: 20pt;">
            <div class="section-title">Category Summary</div>
            <table class="line-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th style="text-align: right; width: 60pt;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categorySummary as $summary)
                        <tr>
                            <td style="font-weight: bold;">{{ $summary['name'] }}</td>
                            <td style="text-align: right; font-weight: bold;">Rs. {{ number_format($summary['total'], 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div style="display: table-cell; vertical-align: top;">
            <div class="section-title">Validation Protocol</div>
            @if($report->status == 'approved')
                <div style="font-size: 7.5pt; line-height: 1.4; color: #444; background: #f9f9f9; padding: 6pt; border: 0.5pt solid #eee;">
                    <p style="margin: 0;"><strong>Officer:</strong> {{ $report->reviewer->name }}</p>
                    <p style="margin: 2pt 0;"><strong>Timestamp:</strong> {{ $report->reviewed_at->format('d M Y H:i') }}</p>
                    <p style="margin: 0; font-family: monospace; font-size: 6.5pt; color: #888;">Hash: {{ substr(md5($report->id . $report->voucher_no), 0, 16) }}</p>
                </div>
            @else
                <div style="font-size: 7.5pt; font-style: italic; color: #999; padding: 6pt; border: 0.5pt dashed #ddd; text-align: center;">
                    Internal validation pending approval protocol.
                </div>
            @endif
        </div>
    </div>

    <div class="section-title">Transaction Details</div>
    <table class="line-table">
        <thead>
            <tr>
                <th style="width: 50pt;">Date</th>
                <th style="width: 80pt;">Category</th>
                <th>Description</th>
                <th style="text-align: right; width: 70pt;">Amount (PKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->lines as $line)
                <tr>
                    <td>{{ $line->date->format('d/m/Y') }}</td>
                    <td style="font-weight: bold;">{{ $line->category->name }}</td>
                    <td>{{ $line->description }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($line->amount, 0) }}</td>
                </tr>
            @endforeach
            <tr style="background: #f0f0f0;">
                <td colspan="3" style="text-align: right; font-weight: bold; font-size: 7.5pt; border-top: 1pt solid #0F6CBD;">GRAND TOTAL</td>
                <td style="text-align: right; font-weight: bold; font-size: 9pt; border-top: 1pt solid #0F6CBD; color: #0F6CBD;">Rs. {{ number_format($report->lines->sum('amount'), 0) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Automated Export: {{ date('d M Y H:i') }} | Financial Reference v4.2 | Page [1]
    </div>
</body>
</html>
