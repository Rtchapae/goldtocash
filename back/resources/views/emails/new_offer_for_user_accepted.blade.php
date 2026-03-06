<!doctype html>
<html>
@php
  $site_phone = config('fedex.parcel_options.recipient_phone');
  $site_email = config('fedex.parcel_options.recipient_email');
@endphp

<head>
    <meta charset="utf-8">
    <title>Your Free Appraisal Kit From Gold To Cash Is Here!</title>
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
                            <img src="{{ config('app.url') }}/images/logo_dark.png" style="max-width: 200px;">
                        </td>
                    </tr>
                    <tr style="background: #f9f9f9;">
                        <td style="text-align: justify;padding: 20px 30px;">
                            <p>Hi {{ $user->first_name }},</p>
                            <p>Appraisal is complete and we have an offer for you! Visit your account to view our offer and accept it.</p>
                            <p>Please remember, your offer will be automatically accepted if you don't respond or if we don't hear from you within 72 hours.*</p>
                        </td>
                    </tr>
                    <tr style="background: #f9f9f9;">
                        <td style="text-align: justify;padding: 20px 30px;">
                            <p>Feel free to send us an email at <a href="mailto:{{ $site_email }}" target="_blank">{{ $site_email }}</a></p>
                            <p>Or call our toll-free number <a href="tel:{{ $site_phone }}" target="_blank">{{ $site_phone }}</a> ({{ config('app.working_hours', 'Mon-Fri 9AM-5PM PST') }})</p>
                            <p style="color: red;">* Subject to <a href="{{ config('app.url') }}/terms-and-conditions">Terms and Conditions</a>.</p>
                            <p>Yours Respectfully,<br><strong>Gold To Cash Team</strong></p>
                            <p class="memo-replace_mr_css_attr"></p>
                        </td>
                    </tr>
                    <tr style="background: #808080;">
                        <h2><p>Your order #: {{ $order->id }}</p></h2>
                    </tr>
                </tbody>
            </table>
        </center>
    </div>
</body>
</html>
