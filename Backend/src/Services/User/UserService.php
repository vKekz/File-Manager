<?php

namespace Services\User;

class UserService implements UserServiceInterface
{
    function getUsers(): array
    {
        return ["1", "2"];
    }
}