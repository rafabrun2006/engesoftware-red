<?php

namespace Engesoftware\Red;

use Engesoftware\Keycloak\Helper\Token;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard as Contract;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Engesoftware\Models\User;

class Red
{
    const VERSION = '1.0';
}