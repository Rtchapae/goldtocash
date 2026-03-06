<?php

namespace App\Domain\Orders\Enums;

enum OrderStatus: int
{
    case KIT_REQUESTED = 0;
    case IN_TRANSIT = 1;
    case ITEMS_RECEIVED = 2;
    case APPRAISAL = 3;
    case OFFER_SENT = 4;
    case OFFER_ACCEPTED = 5;
    case PAID = 6;
    case OFFER_DENIED = 7;
    case ITEMS_SENT_BACK = 8;
    case NO_SALE = 9;
    case ITEMS_SENT_BACK_ALT = 10;

    public function label(): string
    {
        return match ($this) {
            self::KIT_REQUESTED => 'Kit Requested',
            self::IN_TRANSIT => 'In Transit',
            self::ITEMS_RECEIVED => 'Items Received',
            self::APPRAISAL => 'Appraisal',
            self::OFFER_SENT => 'Offer Sent',
            self::OFFER_ACCEPTED => 'Offer Accepted',
            self::PAID => 'Paid',
            self::OFFER_DENIED => 'Offer Denied',
            self::ITEMS_SENT_BACK => 'Items Sent Back',
            self::NO_SALE => 'No Sale',
            self::ITEMS_SENT_BACK_ALT => 'Items Sent Back',
        };
    }

    public function value(): string
    {
        return match ($this) {
            self::KIT_REQUESTED => 'kit_requested',
            self::IN_TRANSIT => 'in_transit',
            self::ITEMS_RECEIVED => 'items_received',
            self::APPRAISAL => 'appraisal',
            self::OFFER_SENT => 'offer_sent',
            self::OFFER_ACCEPTED => 'offer_accepted',
            self::PAID => 'paid',
            self::OFFER_DENIED => 'offer_denied',
            self::ITEMS_SENT_BACK => 'items_sent_back',
            self::NO_SALE => 'no_sale',
            self::ITEMS_SENT_BACK_ALT => 'items_sent_back',
        };
    }
}



