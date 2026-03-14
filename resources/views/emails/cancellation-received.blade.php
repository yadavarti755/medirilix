@component('mail::message')
# Request Received

We have received your cancellation request for **{{ $productName }}** (Order #{{ $orderNumber }}).

**Status:** {{ $request->status }}

Our team will review your request and get back to you shortly.

@component('mail::button', ['url' => route('user.orders.view', encrypt($request->orderProductList->order_id))])
View Request
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent