<!DOCTYPE html>
<html>
<head>
    <title>Print Order - Information Card</title>
    <meta charset="utf-8">
    <style>
        @page print-page {
            size: 612pt 1008pt;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .print-page {
            page: print-page;
        }
        html {
            font-size: 10px;
        }
        body.print-page {
            margin: 0;
            padding: 0 28pt 0pt 28pt;
            font-family: "Calibri", sans-serif;
            font-size: 0.9167rem;
            background: white;
            color: black;
            word-wrap: break-word;
        }
        .order-form-print {
            display: block !important;
            visibility: visible !important;
            position: relative !important;
            width: 100% !important;
            height: auto !important;
            background: white !important;
        }
        .WordSection1 {
            max-width: 100%;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            /*transform: scale(0.6);*/
            /*transform-origin: top left;*/
        }
        .MsoNormal {
            margin-top: 0cm;
            margin-right: 0cm;
            margin-bottom: 8pt;
            margin-left: 0cm;
            line-height: 107%;
            font-size: 0.9167rem;
            font-family: "Calibri", sans-serif;
            color: black;
        }
        /* Header */
        .print-header-table {
            width: 100%;
            margin-top: 18pt;
            margin-bottom: 18pt;
            border-collapse: collapse;
        }
        .print-header-left {
            width: 60%;
            vertical-align: middle;
        }
        .print-header-right {
            width: 40%;
            vertical-align: middle;
            text-align: right;
        }
        .print-logo-image {
            width: 64pt;
            height: 64pt;
            display: inline-block;
            vertical-align: middle;
        }
        .print-logo-text {
            font-size: 28pt;
            line-height: 0.95;
            white-space: nowrap;
            display: inline-block;
            vertical-align: middle;
            margin-left: 10pt;
            font-weight: 700;
        }
        .print-header-card {
            font-size: 16pt;
            line-height: 1;
        }
        /* Section Title */
        .print-section-title {
            margin-top: 16pt;
            margin-right: 0cm;
            margin-bottom: 4.8pt;
            margin-left: 0.95pt;
        }
        .print-section-title-text {
            font-size: 15pt;
            font-weight: 600;
            line-height: 1.1;
        }
        /* Field Lines */
        .print-field-line {
            margin-top: 0cm;
            margin-right: 0cm;
            margin-bottom: 1.2pt;
            margin-left: 0.7pt;
            text-indent: 0.45pt;
            line-height: 147%;
            text-align: left;
        }
        .print-field-line-phone {
            margin-top: 0cm;
            margin-right: 0cm;
            margin-bottom: 16pt;
            margin-left: 0.7pt;
            text-indent: 0.45pt;
            line-height: 110%;
            text-align: left;
        }
        .print-label {
            font-size: 11pt;
            line-height: 147%;
            white-space: nowrap;
        }
        .print-value {
            font-size: 12pt;
            font-weight: 600;
            line-height: 1.2;
        }
        .print-underline {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 100pt;
            margin: 0 4pt;
            vertical-align: bottom;
            padding-bottom: 1px;
        }
        .print-underline-inline {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 60pt;
            margin-left: 4pt;
            vertical-align: bottom;
            padding-bottom: 2px;
        }
        /* Specific underline widths based on original HTML (converted to pt) */
        .print-underline-name { min-width: 260pt; }
        .print-underline-dob { min-width: 130pt; }
        .print-underline-address { min-width: 160pt; }
        .print-underline-city { min-width: 80pt; }
        .print-underline-state { min-width: 60pt; }
        .print-underline-zip { min-width: 80pt; }
        .print-underline-phone { min-width: 180pt; }
        .print-underline-govid { min-width: 60pt; }
        .print-underline-state-issued { min-width: 95pt; }
        .print-underline-au { min-width: 120pt; }
        .print-underline-ag { min-width: 120pt; }
        .print-underline-pt { min-width: 120pt; }
        .print-underline-time { min-width: 130pt; }
        .print-underline-date { min-width: 130pt; }
        .print-underline-location { min-width: 160pt; }
        .print-underline-value { min-width: 100pt; }
        .print-underline-payment { min-width: 140pt; }
        .print-underline-buyer { min-width: 180pt; }
        .print-underline-signature { min-width: 180pt; }
        .print-underline-seller-sig { min-width: 180pt; }
        .print-underline-seller-date { min-width: 124pt; }
        /* Transaction Details */
        h1.print-transaction-title {
            margin-top: 8pt;
            margin-right: 0pt;
            margin-bottom: 7.45pt;
            margin-left: 0pt;
            page-break-after: avoid;
            font-size: 15pt;
            font-family: "Calibri", sans-serif;
            color: black;
            font-weight: 600;
            width: 100%;
        }
        .print-transaction-title span:first-child {
            font-size: 15pt;
            font-weight: 600;
        }
        .print-transaction-no {
            font-size: 1.1667rem;
            line-height: 147%;
            white-space: nowrap;
            float: right;
        }
        .print-transaction-title::after {
            content: "";
            display: block;
            clear: both;
        }
        .print-field-line-time {
            margin-bottom: 27.75pt;
            line-height: 110%;
            text-align: left;
        }
        /* Items Description */
        .print-items-title {
            margin-top: 0cm;
            margin-right: 0cm;
            margin-bottom: 7.9pt;
            margin-left: 1.2pt;
            text-indent: -0.5pt;
        }
        .print-items-title span {
            font-size: 13pt;
            font-weight: 400;
            line-height: 1.1;
        }
        .print-items-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 18pt;
        }
        .print-items-table tr td + td {
            padding-left: 12pt;
        }
        .print-items-table td {
            padding: 0 0 6pt 0;
            vertical-align: bottom;
        }
        .print-item-left,
        .print-item-right {
            width: 50%;
        }
        .print-item-row {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }
        .print-item-num {
            font-size: 11pt;
            line-height: 1.2;
            width: 18pt;
            white-space: nowrap;
            vertical-align: bottom;
            padding: 0 6pt 1px 0;
        }
        .print-item-line {
            border-bottom: 1px solid #000;
            vertical-align: bottom;
            padding: 0 0 1px 0;
            width: 100%;
        }
        /* Terms */
        .print-terms {
            margin-top: 16pt;
            margin-right: 2.65pt;
            margin-bottom: 18pt;
            margin-left: 0cm;
            text-align: justify;
            text-justify: inter-ideograph;
            text-indent: 0.7pt;
            line-height: 90%;
        }
        .no-page-break {
            page-break-inside: avoid;
        }
    </style>
</head>
<body class="print-page">
    <div class="order-form-print">
        @yield('content')
    </div>
</body>
</html>
