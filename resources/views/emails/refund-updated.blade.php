@component('mail::message')
# Refund Status Update

Hello,

The status of your refund for order **#{{ $refund->orderProductList->order->order_number ?? 'N/A' }}** has been updated.

**Refund Details:**
- **Product:** {{ $refund->orderProductList->product->name ?? 'N/A' }}
- **Refund Amount:** {{ number_format($refund->refund_amount, 2) }}
- **New Status:** {{ $status }}

@if($refund->remarks)
**Remarks:**
{{ $refund->remarks }}
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent