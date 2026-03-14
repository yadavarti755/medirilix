@component('mail::message')
# Great News!

Your item **{{ $orderDetails->product_name }}** from Order #{{ $orderNumber }} has been shipped.

@if($orderDetails->shippingDetail)
**Shipping Details:**
{{ $orderDetails->shippingDetail->shipping_details }}
@endif

@component('mail::button', ['url' => route('user.orders.view', encrypt($orderDetails->order_id))])
Track Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent