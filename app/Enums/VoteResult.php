<?php

namespace App\Enums;

enum VoteResult
{
    case Cast;
    case AlreadyVoted;
    case Ineligible;
}
