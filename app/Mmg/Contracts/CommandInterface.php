<?php

namespace App\Mmg\Contracts;

use App\Services\Messages\Contracts\MessageHandlerInterface;

interface CommandInterface extends MessageHandlerInterface, GameOperatorInterface
{ }
