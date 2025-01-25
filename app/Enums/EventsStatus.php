<?php

namespace App\Enums;
enum EventsStatus: string {
    case INCOMING = 'incoming';
    case ONGOING = 'ongoing';
    case PAST = 'past';

}
