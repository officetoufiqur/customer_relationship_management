<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            page-break-inside: avoid;
        }

        .header, .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .header h1 { margin: 0; font-size: 22px; }
        .invoice-title { text-align: right; }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-paid { background: #28a745; }
        .badge-partial { background: #ffc107; color: #000; }
        .badge-draft { background: #6c757d; }

        .section { margin-top: 15px; }
        .section h3 {
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            border-bottom: 1px dashed #ddd;
            font-size: 12px;
        }

        .item:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #555; }
        .value { text-align: right; }

        .summary { margin-top: 10px; text-align: right; font-weight: bold; }
        .summary p { margin: 3px 0; }

        .footer p {
            text-align: center;
            color: #888;
            margin-top: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="invoice-container">

    <!-- Header -->
    <div class="header">
        <div>
            <h1>Company Name</h1>
            <p>123 Street, City, Country</p>
            <p>Email: info@company.com</p>
        </div>
        <div class="invoice-title">
            <h2>Invoice</h2>
            <p>Invoice #: {{ $invoice->invoice_number }}</p>
            <p>Date: {{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y') }}</p>
        </div>
    </div>

    <!-- Client Info -->
    <div class="section">
        <h3>Bill To</h3>
        <p><span class="label">Name:</span> {{ $invoice->client->name }}</p>
        <p><span class="label">Phone:</span> {{ $invoice->client->phone }}</p>
        <p><span class="label">Email:</span> {{ $invoice->client->email }}</p>
        <p><span class="label">WhatsApp:</span> {{ $invoice->client->whatsapp }}</p>
        <p><span class="label">Service Type:</span> {{ $invoice->client->service_type }}</p>
    </div>

    <!-- Invoice Items -->
    <div class="section">
    <h3>Invoice Details</h3>

    <div class="item">
        <div class="label">Service Name:</div>
        <div class="value">{{ $invoice->service_name }}</div>
    </div>

    <div class="item">
        <div class="label">Description:</div>
        <div class="value">{{ $invoice->service_description }}</div>
    </div>

    <div class="item">
        <div class="label">Quantity:</div>
        <div class="value">{{ $invoice->quantity }}</div>
    </div>

    <div class="item">
        <div class="label">Unit Price:</div>
        <div class="value">${{ number_format($invoice->unit_price, 2) }}</div>
    </div>

     <div class="item">
        <div class="label">Sub Total:</div>
        <div class="value">${{ number_format($invoice->unit_price * $invoice->quantity, 2) }}</div>
    </div>

    <div class="item">
        <div class="label">Tax:</div>
        <div class="value">{{ number_format($invoice->tax_value, 2) }}%</div>
    </div>

    <div class="item">
        <div class="label">Total:</div>
        <div class="value">
            ${{ number_format($invoice->quantity * $invoice->unit_price * (1 + $invoice->tax_value / 100), 2) }}
        </div>
    </div>
</div>


    <!-- Payment Summary -->
    <div class="section summary">
        <p>Amount Paid: ${{ number_format($invoice->amount_paid, 2) }}</p>
        <p>Remaining Balance: ${{ number_format($invoice->remaining_balance, 2) }}</p>
        <p>Total: ${{ number_format($invoice->total, 2) }}</p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business! | www.yourcompany.com</p>
    </div>

</div>

</body>
</html>
