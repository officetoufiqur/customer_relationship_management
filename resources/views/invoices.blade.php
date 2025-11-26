<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        .text-end { text-align: right; }
        .badge-success { background: #28a745; color: #fff; padding: 4px 8px; border-radius: 4px; }
        .badge-warning { background: #ffc107; color: #000; padding: 4px 8px; border-radius: 4px; }
        .invoice-box { padding: 20px; border: 1px solid #ddd; border-radius: 6px; background: #fff; }
    </style>
</head>
<body>

<div class="invoice-box">

    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <div>
            <h2>{{ $invoice->expense->company_name }}</h2>
        </div>

        <div class="text-end">
            <h3>Invoice</h3>
            <p>
                Invoice #: 00{{ $invoice->id }} <br>
                Date: {{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y') }} <br>
                Status:
                @if($invoice->expense->status === 'approved')
                    <span class="badge-success">Approved</span>
                @else
                    <span class="badge-warning">{{ ucfirst($invoice->expense->status) }}</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Expense Name / Reason</th>
                <th>Recipient</th>
                <th>Payment Method</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>{{ $invoice->id }}</td>
                <td>{{ $invoice->expense->reason }}</td>
                <td>{{ $invoice->expense->recipient_name }}</td>
                <td>{{ ucfirst($invoice->expense->payment_method) }}</td>
                <td>${{ number_format($invoice->expense->amount, 2) }}</td>
                <td>
                    @if($invoice->expense->status === 'approved')
                        <span class="badge-success">Approved</span>
                    @else
                        <span class="badge-warning">{{ ucfirst($invoice->expense->status) }}</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Total --}}
    <div class="text-end" style="margin-top: 15px;">
        <h4>Total: ${{ number_format($invoice->expense->amount, 2) }}</h4>
    </div>

    {{-- Footer --}}
    <div style="text-align: center; margin-top: 40px;">
        <p>Thank you for your business!</p>
    </div>

</div>

</body>
</html>
