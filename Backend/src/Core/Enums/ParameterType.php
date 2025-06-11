<?php

namespace Core\Enums;

enum ParameterType
{
    case Query;
    case Post;
    case Body;
    case Header;
    case File;
}