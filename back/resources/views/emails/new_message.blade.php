<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>New Message From Gold To Cash!</title>

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
                            <p>Hi {{ $user->first_name ?? $user->name ?? 'User' }},</p>
                            <p>You have received a new message, <a href="{{ env('FRONTEND_URL', config('app.url')) }}/account/messages">visit your account</a> to read it.</p>
                        </td>
                    </tr>
                    <tr style="background: #f9f9f9;">
                        <td style="text-align: justify;padding: 20px 30px;">
                            <p>Feel free to send us an email at <a href="mailto:hello@goldtocash.us" target="_blank" rel="noopener noreferrer">hello@goldtocash.us</a><br>
                                Or call our toll-free number <a href="tel:5642377332" target="_blank" rel="noopener noreferrer"><span class="js-phone-number">564-237-7332</span></a> ({{ config('app.working_hours', 'Mon-Fri 9AM-5PM PST') }})</p>
                            <p>Yours Respectfully,<br>
                                <strong>Gold To Cash Team</strong>
                            </p>
                            <p class="memo-replace_mr_css_attr"></p>
                        </td>
                    </tr>
                    <tr style="background: #808080;">
                    </tr>
                </tbody>
            </table>
        </center>
    </div>
</body>
</html>

