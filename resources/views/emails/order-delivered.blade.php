@component('mail::message')
# Delivered!

Your item **{{ $orderDetails->product_name }}** from Order #{{ $orderNumber }} has been delivered.

We hope you enjoy your purchase!

@component('mail::button', ['url' => route('user.orders.view', encrypt($orderDetails->order_id))])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent