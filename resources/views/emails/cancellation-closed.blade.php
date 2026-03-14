@component('mail::message')
# Request Closed

Your cancellation request for **{{ $productName }}** (Order #{{ $orderNumber }}). has been **Closed**.

If valid, your refund will be processed according to our policy.

@component('mail::button', ['url' => route('user.orders.view', encrypt($request->orderProductList->order_id))])
View Details
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent