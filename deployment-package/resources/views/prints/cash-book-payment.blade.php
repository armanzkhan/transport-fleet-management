<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Voucher - Payment Side</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .print-info {
            background: #f5f5f5;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        .print-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .print-info td {
            padding: 2px 5px;
            border: none;
        }
        .cash-book-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .cash-book-table th,
        .cash-book-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .cash-book-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .totals {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            padding: 5px;
            border: none;
        }
        .totals .amount {
            text-align: right;
            font-weight: bold;
        }
        .signatures {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            height: 40px;
            margin-bottom: 5px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Cash Voucher - Payment Side</h1>
        <h2>Date: {{ $data->entry_date->format('d-m-Y') }}</h2>
        <h2>Cash Book No: {{ $data->cash_book_number ?? 'Auto-Generated' }}</h2>
    </div>

    <div class="print-info">
        <table>
            <tr>
                <td><strong>Printed by:</strong> {{ $printed_by }} (ID: {{ $printed_by_id }})</td>
                <td><strong>Print Date:</strong> {{ $print_date }}</td>
            </tr>
            <tr>
                <td><strong>Created by:</strong> {{ $created_by }}</td>
                <td><strong>Created Date:</strong> {{ $created_date }}</td>
            </tr>
        </table>
    </div>

    <div class="cash-details">
        <table class="cash-book-table">
            <tr>
                <td><strong>Total Cash in Hand Before Payments:</strong></td>
                <td class="amount">₨{{ number_format($data->total_cash_in_hand_before ?? 0, 2) }}</td>
            </tr>
        </table>
    </div>

    <table class="cash-book-table">
        <thead>
            <tr>
                <th>S.No</th>
                <th>TRX Number</th>
                <th>Type</th>
                <th>Account Selection</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data->transactions ?? [] as $index => $transaction)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $transaction->transaction_number ?? 'Auto' }}</td>
                <td>{{ ucfirst($transaction->payment_type ?? 'N/A') }}</td>
                <td>{{ $transaction->account->account_name ?? 'N/A' }}</td>
                <td>{{ $transaction->description }}</td>
                <td class="amount">₨{{ number_format($transaction->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td><strong>Total Cash in Hand Before Payments:</strong></td>
                <td class="amount">₨{{ number_format($data->total_cash_in_hand_before ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Less: Current Day Payments:</strong></td>
                <td class="amount">-₨{{ number_format($data->total_payments ?? 0, 2) }}</td>
            </tr>
            <tr style="border-top: 2px solid #000;">
                <td><strong>Today's Remaining Cash in Hand:</strong></td>
                <td class="amount">₨{{ number_format($data->remaining_cash_in_hand ?? 0, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Prepared by</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Approved by</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div>Receiver</div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
