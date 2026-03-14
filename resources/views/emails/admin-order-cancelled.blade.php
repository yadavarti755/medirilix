@component('mail::message')
# Order Update

We regret to inform you that your item **{{ $orderDetails->product_name }}** from Order #{{ $orderNumber }} has been cancelled by our administration.

**Reason:**
{{ $orderDetails->remarks ?? 'Administrative decision' }}

A refund has been initiated if you have already paid.

@component('mail::button', ['url' => route('user.orders')])
View Orders
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent