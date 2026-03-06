<html>
<head>
    <meta charset="utf-8">
    <title>Order# {{ $order->id }}</title>
    <style>
        .div-table {
            display:block;
            padding: 40px 20px 40px 40px;
            overflow: hidden;
        }
        .div-table-row {
            display:block;
        }
        .div-table-col {
            display:inline-block;
        }
        .div-table-col div {
            word-break:keep-all;
        }
        .font-medium {
            font-style:normal;
            font-weight:normal;
            font-size:0.9em;
            font-family:Helvetica;
            color:#000000;
            line-height: 1.5em;
        }
        .font-small {
            font-style:normal;
            font-weight:normal;
            font-size:0.75em;
            font-family:Helvetica;
            color:#000000;
            line-height: 1.5em;
        }
        .fedex-locations {
            margin-top: 12pt;
            padding-top: 6pt;
            font-size: 0.7em;
            line-height: 1.25;
        }
        .fedex-locations-title {
            font-weight: bold;
            margin-bottom: 4pt;
            font-size: 1em;
        }
        .fedex-location-item {
            margin-bottom: 2pt;
        }
        @page {
            size: 21cm 29.7cm;
            margin: 0;
        }
    </style>
</head>
<body class="font-medium">

<div class="div-table" style="padding-top: 110px;">
    <div class="div-table-row">
        <div class="div-table-col" style="width:45%;">
                @if(isset($logoBase64) && !empty($logoBase64))
                    <img style="width:2.58in;height:0.63in" src="{{ $logoBase64 }}">
                @endif
        </div>
        <div class="div-table-col" style="width:45%;text-align:right;">
            <div style="text-align: right;">Username: {{ $user->email }}</div>
            <div style="text-align: right;">Kit #: {{ $order->id }}</div>
            <div style="text-align: right;">Tracking #: {{ $track_number }}</div>
        </div>
    </div>
    <div class="div-table-row">
        <div class="div-table-col" style="margin-top:40px;">
            <div style="margin-bottom:10px;">
                Dear {{ $user->last_name }} {{ $user->first_name }},
            </div>
            <div>
                Thank you for trusting Gold to Cash. We understand that it may be unsettling to send your gold and jewelry off in the mail,
                so we've done everything we can to make the process as easy and secure as possible.
            </div>
            <div style="margin-top:10px;">
                <ul>
                    <li>We've included your prepaid FedEx shipping mailer.
                        All you need to do is fill out the information card provided and securely package your items for shipping.</li>
                    <li>We've provided a list of authorized FedEx locations nearby for easy drop-off. Take your package to a FedEx store and ship it out.
                        If you'd prefer a pickup, call 564.237.7332 so we can schedule a free FedEx home pickup.</li>
                    <li>We automatically insure your package up to $5,000.
                        If your items exceed that value, call us at 564.237.7332 to increase insurance coverage, up to $100,000 at no additional cost.
                        Please note: Only packages picked up by FedEx or dropped at a staffed FedEx location are eligible for insurance; Must provide shipping receipt for a claim.</li>
                    <li><b>We guarantee your satisfaction.</b> We're confident that we'll give you the best offer for your gold, but the choice is yours.
                        If you're not happy with our offer, we'll send your items back at no charge.</li>
                </ul>
            </div>
            <div>
                Visit your dashboard at any time to track the status of your shipment and offer. Log in at goldtocash.us/sign-in using the
                login credentials included in the welcome email we've sent.
            </div>
            <div style="margin-top:10px;">
                Once we receive your items, you'll have our offer within 24 hours. You can accept or deny it with one click—it's that easy.
            </div>
            <div style="margin-top:10px;">
                Again, thank you for choosing Gold to Cash. We will justify your trust - we promise! If you have questions or concerns, or if
                you didn't receive an email with your login credentials, please email us at hello@goldtocash.us or call 564.237.7332.
            </div>
            <div style="margin:20px 0;">
                Sincerely,<br/>Gold to Cash Team
            </div>

            <div class="fedex-locations">
                <div class="fedex-locations-title">Authorized FedEx Locations:</div>
                @if(isset($locations) && count($locations) > 0)
                    @foreach($locations as $location)
                        <div class="fedex-location-item">{{ $location->getDistanceInMiles() ?? '?' }} miles away - {{ $location->getCompanyName() ?? $location->getDisplayName() }} - {{ $location->getFullAddress() }}</div>
                    @endforeach
                @else
                    <div class="fedex-location-item">Find your nearest FedEx location at fedex.com/locate or call 564.237.7332 for assistance.</div>
                @endif
            </div>
        </div>
    </div>

    @if(isset($ratingBase64) && !empty($ratingBase64))
        <div>
            <img src="{{ $ratingBase64 }}" style="margin-top:40px;width:97%;" />
        </div>
    @endif
    @if(isset($formBase64) && !empty($formBase64))
        <div>
            <img src="{{ $formBase64 }}" style="width:100%;" />
        </div>
    @endif
    @if(isset($label) && !empty($label))
        <div style="page-break-before: always;">
            <img src="data:image/png;base64,{{ $label }}" style="width:100%;" />
        </div>
    @endif
</div>
</body>
</html>


