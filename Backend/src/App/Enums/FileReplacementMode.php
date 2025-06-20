<?php

namespace App\Enums;

enum FileReplacementMode: int
{
    case Replace = 0;
    case Keep = 1;
}