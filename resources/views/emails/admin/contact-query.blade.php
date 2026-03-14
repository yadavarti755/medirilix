@component('mail::message')
# New Contact Query

**Name:** {{ $data->name }}
**Email:** {{ $data->email_id }}
**Phone:** {{ $data->phone_number }}

**Message:**
{{ $data->message }}

@component('mail::button', ['url' => url('/admin/contact-queries')])
View Queries
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent