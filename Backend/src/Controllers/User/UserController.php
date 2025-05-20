<?php

namespace Controllers\User;

use Attributes\Http\Methods\HttpGetAttribute;
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
        parent::__construct(self::ROUTE);

        $this->userService = new UserService();
    }

    #[HttpGetAttribute("all")]
    public function getUsers(): void
    {
        var_dump($this->userService->getUsers());
    }
}