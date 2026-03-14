@component('mail::message')
# Order Payment Failed

Hello {{ $order->user->name ?? 'Customer' }},

We noticed that the payment for your order **#{{ $order->order_number }}** has failed.

**Order Details:**
- **Order Number:** {{ $order->order_number }}
- **Date:** {{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}
- **Total Amount:** {{ number_format($order->total_price, 2) }}

If you have already paid, please contact our support team immediately. Otherwise, you can try placing the order again.

@component('mail::button', ['url' => route('user.orders')])
View Orders
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent