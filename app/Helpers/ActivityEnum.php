<?php

namespace App\Helpers;

class ActivityEnum
{
    public const RESERVERATION_CANCELLED_VIA_EMAIL = 1;
    public const USER_LOGGEDIN = 2;
    public const CONTACT_CREATED = 3;
    public const RESERVATION_MADE = 4;
    public const RESERVATION_CANCELLED = 5;
    public const PAYMENT_CREATED = 6;
    public const TODO_CREATED = 7;
    public const TODO_COMPLETED = 8;
    public const CLASS_SCHEDULED = 9;
    public const CLASS_CANCELLED = 10;
    public const RESERVATION_DELETED = 11;
    public const PAYMENT_UPDATED = 12;
    public const PAYMENT_DELETED = 13;
}

?>
