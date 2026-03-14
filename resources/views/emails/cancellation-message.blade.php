@component('mail::message')
# New Message

You have a new message regarding your cancellation request for **{{ $request->orderProductList->product_name ?? 'Item' }}**.

**Message:**
"{{ $messageObj->message }}"

@component('mail::button', ['url' => route('user.orders.view', encrypt($request->orderProductList->order_id))])
Reply Now
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent