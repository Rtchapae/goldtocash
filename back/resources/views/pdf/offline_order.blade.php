@extends('layouts.pdf')

@section('content')
<div class="WordSection1">
    <!-- Header: Logo + Royal Element + Information Card (same line) -->
    <table class="print-header-table">
        <tr>
            <td class="print-header-left">
                <img src="{{ public_path('images/logo.jpg') }}" alt="Royal Element" class="print-logo-image">
                <span class="print-logo-text">Royal<br>Element</span>
            </td>
            <td class="print-header-right">
                <span class="print-header-card">Information Card</span>
            </td>
        </tr>
    </table>

    <!-- Seller Information -->
    <p class="MsoNormal print-section-title">
        <span class="print-section-title-text">Seller Information</span>
    </p>

    <!-- Full Name + Date of Birth -->
    <p class="MsoNormal print-field-line">
        <span class="print-label">Full Name: </span>
        <span class="print-underline print-underline-name">{{ $user->name ?? '&nbsp;' }}</span>
        <span class="print-label">Date of Birth: </span>
        <span class="print-underline print-underline-dob">{!! $dateOfBirth !== '' ? e($dateOfBirth) : '&nbsp;' !!}</span>
    </p>

    <!-- Address + City + State + ZIP -->
    <p class="MsoNormal print-field-line">
        <span class="print-label">Address: </span>
        <span class="print-underline print-underline-address">{{ $user->address ?? '&nbsp;' }}</span>
        <span class="print-label">City: </span>
        <span class="print-underline print-underline-city">{{ $user->city ?? '&nbsp;' }}</span>
        <span class="print-label">State: </span>
        <span class="print-underline print-underline-state">{{ $user->state ?? '&nbsp;' }}</span>
        <span class="print-label">ZIP: </span>
        <span class="print-underline print-underline-zip">{{ $user->zip ?? '&nbsp;' }}</span>
    </p>

    <!-- Phone + Gov. ID # + State Issued -->
    <p class="MsoNormal print-field-line-phone">
        <span class="print-label">Phone:</span>
        <span class="print-underline print-underline-phone">{{ $user->phone ?? '&nbsp;' }}</span>
        <span class="print-label">Gov. ID #: </span>
        <span class="print-underline print-underline-govid">{{ $govIdNumber ?: '&nbsp;' }}</span>
        <span class="print-label">State Issued: </span>
        <span class="print-underline print-underline-state-issued">{{ $stateIssued ?: '&nbsp;' }}</span>
    </p>

    <!-- Transaction Details -->
    <h1 class="print-transaction-title">
        <span>Transaction Details</span>
        <span class="print-transaction-no">#: <span class="print-underline-inline">{{ $order->id ?? '&nbsp;' }}</span></span>
    </h1>

    <!-- Spot Price: Au + Ag + Pt -->
    <p class="MsoNormal print-field-line">
        <span class="print-label">Spot Price: Au: </span>
        <span class="print-underline print-underline-au">&nbsp;</span>
        <span class="print-label">Ag: </span>
        <span class="print-underline print-underline-ag">&nbsp;</span>
        <span class="print-label">Pt: </span>
        <span class="print-underline print-underline-pt">&nbsp;</span>
    </p>

    <!-- Time + Date + Location -->
    <p class="MsoNormal print-field-line-time">
        <span class="print-label">Time:</span>
        <span class="print-underline print-underline-time">{{ $order->created_at ? $order->created_at->format('H:i') : '&nbsp;' }}</span>
        <span class="print-label">Date:</span>
        <span class="print-underline print-underline-date">{{ $order->created_at ? $order->created_at->format('m/d/Y') : '&nbsp;' }}</span>
        <span class="print-label">Location:</span>
        <span class="print-underline print-underline-location">{{ $order->branch ? $order->branch->display_name : '&nbsp;' }}</span>
    </p>

    <!-- Items Description -->
    <p class="MsoNormal print-items-title">
        <span>Items Description:</span>
    </p>

    <table class="print-items-table">
        <tbody>
            @for ($row = 0; $row < 5; $row++)
            <tr>
                <td class="print-item-left">
                    <span class="print-item-num">{{ $row * 2 + 1 }}.</span>
                    <span class="print-item-line">{{ $items[$row * 2] ?? '' }}</span>
                </td>
                <td class="print-item-right">
                    <span class="print-item-num">{{ $row * 2 + 2 }}.</span>
                    <span class="print-item-line">{{ $items[$row * 2 + 1] ?? '' }}</span>
                </td>
            </tr>
            @endfor
        </tbody>
    </table>

    <!-- Value + Payment Method -->
    <p class="MsoNormal print-field-line">
        <span class="print-label">Value of the Transaction: $</span>
        <span class="print-underline print-underline-value">{{ $order->amount ? number_format($order->amount, 2) : '&nbsp;' }}</span>
        <span class="print-label">Payment Method: </span>
        <span class="print-underline print-underline-payment">{{ $order->payment_method ? ucfirst($order->payment_method) : '&nbsp;' }}</span>
    </p>

    <!-- Buyer Name + Signature -->
    <p class="MsoNormal print-field-line">
        <span class="print-label">Buyer Name: </span>
        <span class="print-underline print-underline-buyer">{{ $buyerName ?: '&nbsp;' }}</span>
        <span class="print-label">Signature: </span>
        <span class="print-underline print-underline-signature">&nbsp;</span>
    </p>

    <div class="no-page-break">
        <!-- Legal Terms -->
        <p class="MsoNormal print-terms">
            <span>I affirm that I have read and agree to the terms and conditions available at royalelement.com. I am at least 18 years of age and the lawful owner of the items I am selling. I have full legal authority to sell these items. All personal identification information I have provided is true, accurate, and complete. I understand that submitting false or misleading information may result in the civil and/or criminal penalties. I further acknowledge that this transaction will be recorded and reported in accordance with local law and regulations.</span>
        </p>

        <!-- Seller Signature + Date -->
        <p class="MsoNormal print-field-line">
            <span class="print-label">Seller Signature: </span>
            <span class="print-underline print-underline-seller-sig">&nbsp;</span>
            <span class="print-label">Date: </span>
            <span class="print-underline print-underline-seller-date">{{ $order->created_at ? $order->created_at->format('F j, Y') : '&nbsp;' }}</span>
        </p>
    </div>

    <!-- Right Thumbprint -->
    @if(!isset($isPortlandBranch) || !$isPortlandBranch)
    <p class="MsoNormal print-thumbprint">
        <span>Right Thumbprint:</span>
    </p>
    <div class="print-thumbprint-box"></div>
    @endif
</div>
@endsection
