<!doctype html>
<html>
@php
  $site_phone = config('fedex.parcel_options.recipient_phone', '564-237-7332');
  $site_email = config('fedex.parcel_options.recipient_email', 'hello@goldtocash.us');
@endphp

<head>
    <meta charset="utf-8">
    <title>Customer Kit Request Notification</title>
    <style>
    .invoice-box {
        max-width: 600px;
        margin: auto;
        padding: 0px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 12px;
        line-height: 18px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #000;
    }
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }
    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }
    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }
        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }
    </style>
</head>

<body>
    <div class="invoice-box">
        <center>
            <table style="width: 100%;max-width: 600px;">
                <tbody>
                    <tr style="background: #dbaf3e;">
                        <td style="text-align: center;padding: 40px 30px;">
                            <img src="{{ env('APP_URL') }}/images/logo_dark.png " style="max-width: 200px;">
                        </td>
                    </tr>
                    <tr style="background: #f9f9f9;">
                        <td style="text-align: justify;padding: 20px 30px;">
                            <p>{{ \Carbon\Carbon::now() }}</p>
                            <p>Order #: {{ $order->id }}</p>
                            <p>Customer: {{ $user->name }} ({{ $user->email }})</p>
                            <p>Address: {{ $user->city }}, {{ $user->address }}, {{ $user->state }}, {{ $user->zip }}</p>
                            <p>Kit Delivery Method: {{ $order->send_label ? 'Print' : 'Mail-in' }}</p>
                        </td>
                    </tr>
                    <tr style="background: #f9f9f9;">
                        <td style="text-align: justify;padding: 20px 30px;">
                            <p>A new customer has requested an appraisal kit.</p>
                            <p>Customer details and shipping label have been processed.</p>
                            <p>Feel free to send us an email at <a href="mailto:{{ $site_email }}" target="_blank">{{ $site_email }}</a></p>
                            <p>Or call our toll-free number <a href="tel:{{ $site_phone }}" target="_blank">{{ $site_phone }}</a></p>
                            <p>Best regards,<br><strong>Gold To Cash System</strong></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </center>
    </div>
</body>
</html>




