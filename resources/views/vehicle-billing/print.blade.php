<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Bill - {{ $vehicleBill->bill_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .bill-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .bill-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .bill-info-left, .bill-info-right {
            width: 48%;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        .info-value {
            flex: 1;
        }
        .summary-section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .summary-table th,
        .summary-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .summary-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .summary-table .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .entries-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .entries-table th,
        .entries-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 11px;
        }
        .entries-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .amount {
            font-weight: bold;
        }
        .positive {
            color: #28a745;
        }
        .negative {
            color: #dc3545;
        }
        .footer {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            height: 40px;
            margin-bottom: 5px;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Print Bill
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            Close
        </button>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="company-name">Transport Fleet Management</div>
        <div class="bill-title">VEHICLE BILL</div>
        <div>Bill No: {{ $vehicleBill->bill_number }}</div>
    </div>

    <!-- Bill Information -->
    <div class="bill-info">
        <div class="bill-info-left">
            <div class="info-row">
                <div class="info-label">Vehicle:</div>
                <div class="info-value">{{ $vehicleBill->vehicle->vrn }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Driver:</div>
                <div class="info-value">{{ $vehicleBill->vehicle->driver_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Owner:</div>
                <div class="info-value">{{ $vehicleBill->vehicle->owner->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Contact:</div>
                <div class="info-value">{{ $vehicleBill->vehicle->driver_contact }}</div>
            </div>
        </div>
        <div class="bill-info-right">
            <div class="info-row">
                <div class="info-label">Billing Month:</div>
                <div class="info-value">{{ $vehicleBill->billing_month }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Bill Date:</div>
                <div class="info-value">{{ $vehicleBill->created_at->format('d M Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    @if($vehicleBill->is_finalized)
                        <span class="positive">FINALIZED</span>
                    @else
                        <span class="negative">DRAFT</span>
                    @endif
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Created By:</div>
                <div class="info-value">{{ $vehicleBill->creator->name }}</div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="summary-section">
        <div class="section-title">Financial Summary</div>
        <table class="summary-table">
            <tr>
                <th>Description</th>
                <th class="text-right">Amount (₹)</th>
            </tr>
            <tr>
                <td>Previous Bill Balance</td>
                <td class="text-right amount">{{ number_format($vehicleBill->previous_bill_balance, 2) }}</td>
            </tr>
            <tr>
                <td>Total Freight Income</td>
                <td class="text-right amount positive">{{ number_format($vehicleBill->total_freight, 2) }}</td>
            </tr>
            <tr>
                <td>Total Advance Given</td>
                <td class="text-right amount negative">-{{ number_format($vehicleBill->total_advance, 2) }}</td>
            </tr>
            <tr>
                <td>Total Expenses</td>
                <td class="text-right amount negative">-{{ number_format($vehicleBill->total_expense, 2) }}</td>
            </tr>
            <tr>
                <td>Total Shortage</td>
                <td class="text-right amount negative">-{{ number_format($vehicleBill->total_shortage, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>Gross Profit</strong></td>
                <td class="text-right amount {{ $vehicleBill->gross_profit >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($vehicleBill->gross_profit, 2) }}
                </td>
            </tr>
            <tr class="total-row">
                <td><strong>Net Profit</strong></td>
                <td class="text-right amount {{ $vehicleBill->net_profit >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($vehicleBill->net_profit, 2) }}
                </td>
            </tr>
            <tr class="total-row">
                <td><strong>Total Vehicle Balance</strong></td>
                <td class="text-right amount {{ $vehicleBill->total_vehicle_balance >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($vehicleBill->total_vehicle_balance, 2) }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Freight Entries -->
    @if($freightEntries->count() > 0)
    <div class="summary-section">
        <div class="section-title">Freight Entries ({{ $freightEntries->count() }} entries)</div>
        <table class="entries-table">
            <thead>
                <tr>
                    <th>Journey #</th>
                    <th>Date</th>
                    <th>Route</th>
                    <th>Company</th>
                    <th class="text-right">Freight Amount</th>
                    <th class="text-right">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($freightEntries as $entry)
                <tr>
                    <td>{{ $entry->journey_number }}</td>
                    <td>{{ $entry->journey_date->format('d M Y') }}</td>
                    <td>{{ $entry->loading_point }} → {{ $entry->destination }}</td>
                    <td>{{ $entry->company }}</td>
                    <td class="text-right">{{ number_format($entry->freight_amount, 2) }}</td>
                    <td class="text-right amount">{{ number_format($entry->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Advance Entries -->
    @if($advanceEntries->count() > 0)
    <div class="summary-section">
        <div class="section-title">Advance Entries ({{ $advanceEntries->count() }} entries)</div>
        <table class="entries-table">
            <thead>
                <tr>
                    <th>Entry #</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($advanceEntries as $entry)
                <tr>
                    <td>{{ $entry->cash_book_number }}</td>
                    <td>{{ $entry->entry_date->format('d M Y') }}</td>
                    <td>{{ $entry->description }}</td>
                    <td class="text-right amount negative">{{ number_format($entry->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Expense Entries -->
    @if($expenseEntries->count() > 0)
    <div class="summary-section">
        <div class="section-title">Expense Entries ({{ $expenseEntries->count() }} entries)</div>
        <table class="entries-table">
            <thead>
                <tr>
                    <th>Entry #</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenseEntries as $entry)
                <tr>
                    <td>{{ $entry->cash_book_number }}</td>
                    <td>{{ $entry->entry_date->format('d M Y') }}</td>
                    <td>{{ $entry->description }}</td>
                    <td class="text-right amount negative">{{ number_format($entry->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div>Vehicle Owner Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div>Company Representative</div>
            </div>
        </div>
        
        <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
            <p>This is a computer generated bill. No signature required.</p>
            <p>Generated on: {{ now()->format('d M Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
