@component('mail::message')
# New Cancellation Request

**Order Number:** {{ $cancellationRequest->orderProductList->order->order_number ?? 'N/A' }}
**Product:** {{ $cancellationRequest->orderProductList->product->name ?? 'N/A' }}
**Reason:** {{ $cancellationRequest->description }}

@component('mail::button', ['url' => url('/admin/order-cancellation-requests')])
View Cancellation Requests
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent