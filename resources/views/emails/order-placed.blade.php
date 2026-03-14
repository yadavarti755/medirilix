@component('mail::message')
# Order Placed Successfully

Hello {{ $order->user->name ?? 'Customer' }},

Your order **#{{ $order->order_number }}** has been placed successfully.

**Order Details:**
- **Order Number:** {{ $order->order_number }}
- **Date:** {{ \Carbon\Carbon::parse($order->order_date)->format('d M, Y') }}
- **Total Amount:** {{ number_format($order->total_price, 2) }}

@component('mail::button', ['url' => route('user.orders.view', Illuminate\Support\Facades\Crypt::encryptString($order->id))])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent