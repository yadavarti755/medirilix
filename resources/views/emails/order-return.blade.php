@component('mail::message')
# Return Request Received

We have received your return request for items in Order #{{ $orderNumber }}.

Our team will review your request and process it shortly.

@component('mail::button', ['url' => route('user.orders.view', encrypt($order->id))])
View Order
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent