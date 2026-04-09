<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $sale->receipt_number }}</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'DM Sans',sans-serif;background:#fff;color:#111;font-size:13px;padding:20px;max-width:380px;margin:0 auto}
        .brand{text-align:center;margin-bottom:12px}
        .brand h1{font-size:22px;font-weight:800;letter-spacing:-1px}
        .brand p{font-size:11px;color:#555;margin-top:2px}
        .divider{border:none;border-top:1px dashed #ccc;margin:10px 0}
        .meta{display:flex;justify-content:space-between;font-size:11px;color:#555;margin-bottom:8px}
        table{width:100%;border-collapse:collapse;margin-bottom:8px}
        th{text-align:left;font-size:10px;text-transform:uppercase;color:#888;padding:4px 0;border-bottom:1px solid #eee}
        td{padding:5px 0;font-size:12px;border-bottom:1px solid #f5f5f5}
        td:last-child{text-align:right}
        .totals{margin-top:8px}
        .totals tr td{border:none;padding:3px 0}
        .totals .grand td{font-weight:800;font-size:15px;border-top:1px solid #111;padding-top:6px}
        .totals .change td{color:#16a34a;font-weight:700}
        .footer{text-align:center;margin-top:16px;font-size:10px;color:#888}
        @media print{
            body{padding:0}
            .no-print{display:none}
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700;800&display=swap" rel="stylesheet">
</head>
<body>
<div class="brand">
    <h1>SOZO POS</h1>
    <p>Sales Receipt</p>
</div>

<hr class="divider">

<div class="meta">
    <span><strong>Receipt:</strong> {{ $sale->receipt_number }}</span>
    <span>{{ $sale->created_at->format('d M Y, H:i') }}</span>
</div>
<div class="meta">
    <span><strong>Cashier:</strong> {{ $sale->user->name }}</span>
    <span><strong>Payment:</strong> {{ str_replace('_',' ', strtoupper($sale->payment_method)) }}</span>
</div>
@if($sale->customer)
<div class="meta">
    <span><strong>Customer:</strong> {{ $sale->customer->name }}</span>
    <span>{{ $sale->customer->phone ?? '' }}</span>
</div>
@endif

<hr class="divider">

<table>
    <thead>
        <tr>
            <th>Item</th>
            <th style="text-align:center">Qty</th>
            <th style="text-align:right">Price</th>
            <th style="text-align:right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sale->items as $item)
        <tr>
            <td>{{ $item->item_name }}</td>
            <td style="text-align:center">{{ $item->quantity }}</td>
            <td style="text-align:right">{{ number_format($item->unit_price, 0) }}</td>
            <td>{{ number_format($item->line_total, 0) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="totals">
    <tr><td>Subtotal</td><td style="text-align:right">{{ number_format($sale->subtotal, 0) }}</td></tr>
    @if($sale->tax_total > 0)
    <tr><td>Tax</td><td style="text-align:right">{{ number_format($sale->tax_total, 0) }}</td></tr>
    @endif
    @if($sale->discount_amount > 0)
    <tr><td>Discount</td><td style="text-align:right">- {{ number_format($sale->discount_amount, 0) }}</td></tr>
    @endif
    <tr class="grand"><td>TOTAL (UGX)</td><td>{{ number_format($sale->total, 0) }}</td></tr>
    <tr><td>Amount Paid</td><td style="text-align:right">{{ number_format($sale->amount_paid, 0) }}</td></tr>
    @if($sale->change_given > 0)
    <tr class="change"><td>Change</td><td style="text-align:right">{{ number_format($sale->change_given, 0) }}</td></tr>
    @endif
</table>

<hr class="divider">

@if($sale->customer)
<p style="text-align:center;font-size:11px;color:#555;margin-bottom:6px">
    Loyalty Points Earned: +{{ (int)($sale->total / 1000) }} pts
    (Total: {{ number_format($sale->customer->loyalty_points) }} pts)
</p>
@endif

<div class="footer">
    <p>Thank you for shopping with us!</p>
    <p style="margin-top:4px">Status: {{ strtoupper($sale->status) }}</p>
    @if($sale->payment_reference)
    <p style="margin-top:2px">Ref: {{ $sale->payment_reference }}</p>
    @endif
</div>

<div style="text-align:center;margin-top:16px" class="no-print">
    <button onclick="window.print()" style="background:#f0c040;border:none;padding:8px 24px;border-radius:6px;font-weight:700;cursor:pointer;margin-right:8px">Print Receipt</button>
    <a href="{{ route('pos.terminal') }}" style="background:#1e2230;color:#fff;border:none;padding:8px 24px;border-radius:6px;font-weight:600;text-decoration:none">New Sale</a>
</div>
</body>
</html>
