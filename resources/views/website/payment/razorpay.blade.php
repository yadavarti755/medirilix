<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay with Razorpay</title>
</head>

<body>
    <button id="rzp-button1" style="display:none;">Pay</button>
    <script @cspNonce src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script @cspNonce>
        var options = {
            "key": "{{ $key }}",
            "amount": "{{ $amount }}",
            "currency": "{{ $currency }}",
            "name": "{{ config('app.name') }}",
            "description": "Payment for Order {{ $order_number }}",
            "image": "{{ asset('logo.png') }}", // Optional
            "order_id": "{{ $order_id }}",
            "callback_url": "{{ $callback_url }}",
            "prefill": {
                "name": "{{ $user_name }}",
                "email": "{{ $user_email }}",
                "contact": "{{ $user_contact }}"
            },
            "theme": {
                "color": "#3399cc"
            },
            "modal": {
                "ondismiss": function() {
                    window.location.href = "{{ $cancel_url }}";
                }
            }
        };
        var rzp1 = new Razorpay(options);
        window.onload = function() {
            rzp1.open();
        };
        document.getElementById('rzp-button1').onclick = function(e) {
            rzp1.open();
            e.preventDefault();
        }
    </script>
</body>

</html>