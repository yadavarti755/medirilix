@component('mail::message')
# New Return Request

**Order Number:** {{ $returnRequest->order_number }}
**Return Code:** {{ $returnRequest->return_code }}
**Reason:** {{ $returnRequest->return_reason }}
**Customer Comment:** {{ $returnRequest->comment }}

@component('mail::button', ['url' => url('/admin/return-requests')])
View Return Requests
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent