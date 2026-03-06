<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account information - Gold To Cash</title>
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
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .credentials { background: #f5f5f5; padding: 12px; border-radius: 4px; margin: 12px 0; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <center>
            <table style="width: 100%; max-width: 600px;">
                <tbody>
                    <tr style="background: #dbaf3e;">
                        <td style="text-align: center; padding: 40px 30px;">
                            <img src="{{ config('app.url') }}/images/logo_dark.png" alt="Gold To Cash" style="max-width: 200px;">
                        </td>
                    </tr>
                    <tr style="background: #f9f9f9;">
                        <td style="text-align: justify; padding: 20px 30px;">
                            <p>Hi {{ $user->first_name ?? $user->name ?? 'User' }},</p>
                            <p>Here are your account login details. Please keep this email secure.</p>
                            <div class="credentials">
                                <p style="margin: 0 0 6px 0;"><strong>Email (username):</strong> {{ $user->email }}</p>
                                <p style="margin: 0;"><strong>Password:</strong> {{ $password }}</p>
                            </div>
                            <p>You can sign in at <a href="{{ env('FRONTEND_URL', config('app.url')) }}/sign-in">{{ rtrim(env('FRONTEND_URL', config('app.url')), '/') }}/sign-in</a>.</p>
                        </td>
                    </tr>
                    <tr style="background: #f9f9f9;">
                        <td style="text-align: justify; padding: 20px 30px;">
                            <p>If you have any questions, contact us at <a href="mailto:hello@goldtocash.us">hello@goldtocash.us</a> or call <a href="tel:5642377332">564-237-7332</a> ({{ config('app.working_hours', 'Mon-Fri 9AM-5PM PST') }}).</p>
                            <p>Yours respectfully,<br><strong>Gold To Cash Team</strong></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </center>
    </div>
</body>
</html>
