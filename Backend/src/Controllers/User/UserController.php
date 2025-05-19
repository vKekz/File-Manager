<?php

namespace Controllers\User;

use Attributes\Controllers\HttpGetAttribute;
use Controllers\ApiController;
use Services\User\UserService;
use Services\User\UserServiceInterface;

/**
 * Represents the controller that is used for the user service.
 */
class UserController extends ApiController
{
    private const ROUTE = "api/user";
    private readonly UserServiceInterface $userService;

    function __construct()
    {
        $this->userService = new UserService();
        parent::__construct(self::ROUTE);
    }

    #[HttpGetAttribute("all")]
    public function getUsers(): array
    {
        return $this->userService->getUsers();
    }
}