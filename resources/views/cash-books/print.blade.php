<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Voucher - {{ $cashBook->transaction_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .voucher {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .voucher-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .voucher-subtitle {
            font-size: 14px;
            color: #666;
        }
        .voucher-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .detail-group {
            border: 1px solid #ddd;
            padding: 15px;
        }
        .detail-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .detail-value {
            margin-bottom: 10px;
        }
        .amount-section {
            text-align: center;
            border: 2px solid #000;
            padding: 20px;
            margin: 20px 0;
        }
        .amount-label {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .amount-value {
            font-size: 24px;
            font-weight: bold;
            color: #000;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
            border-top: 1px solid #000;
            padding-top: 20px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            height: 40px;
            margin-bottom: 10px;
        }
        .signature-label {
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        @media print {
            body { margin: 0; }
            .voucher { border: none; }
        }
    </style>
</head>
<body>
    <div class="voucher">
        <!-- Header -->
        <div class="header">
            <div class="company-name">TIRAH ENTERPRISES</div>
            <div class="voucher-title">CASH VOUCHER</div>
            <div class="voucher-subtitle">Transport Fleet Management System</div>
        </div>

        <!-- Voucher Details -->
        <div class="voucher-details">
            <div class="detail-group">
                <div class="detail-label">Transaction Number:</div>
                <div class="detail-value">{{ $cashBook->transaction_number }}</div>
                
                <div class="detail-label">Date:</div>
                <div class="detail-value">{{ $cashBook->entry_date->format('M d, Y') }}</div>
                
                <div class="detail-label">Type:</div>
                <div class="detail-value">
                    @if($cashBook->transaction_type == 'receive')
                        <strong>RECEIVE</strong>
                    @else
                        <strong>PAYMENT</strong>
                    @endif
                </div>
            </div>
            
            <div class="detail-group">
                <div class="detail-label">Account:</div>
                <div class="detail-value">{{ $cashBook->account->account_name }}</div>
                
                @if($cashBook->vehicle)
                <div class="detail-label">Vehicle:</div>
                <div class="detail-value">
                    {{ $cashBook->vehicle->vrn }}<br>
                    <small>{{ $cashBook->vehicle->owner->name }}</small>
                </div>
                @endif
                
                @if($cashBook->payment_type)
                <div class="detail-label">Payment Type:</div>
                <div class="detail-value">{{ $cashBook->payment_type }}</div>
                @endif
            </div>
        </div>

        <!-- Description -->
        <div class="detail-group">
            <div class="detail-label">Description:</div>
            <div class="detail-value">{{ $cashBook->description }}</div>
        </div>

        <!-- Amount Section -->
        <div class="amount-section">
            <div class="amount-label">Amount</div>
            <div class="amount-value">Rs. {{ number_format($cashBook->amount, 2) }}</div>
        </div>

        <!-- Cash Balance Information -->
        <div class="voucher-details">
            <div class="detail-group">
                <div class="detail-label">Previous Day Balance:</div>
                <div class="detail-value">Rs. {{ number_format($cashBook->previous_day_balance, 2) }}</div>
            </div>
            
            <div class="detail-group">
                <div class="detail-label">Total Cash in Hand:</div>
                <div class="detail-value">Rs. {{ number_format($cashBook->total_cash_in_hand, 2) }}</div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Prepared By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Approved By</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Received By</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Generated on: {{ now()->format('M d, Y H:i:s') }}</p>
            <p>Generated by: {{ $cashBook->creator->name }}</p>
            <p>This is a computer generated voucher.</p>
        </div>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
