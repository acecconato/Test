<?php

namespace App\Message;

final class WebhookMessage
{
    public function __construct(
        private readonly ?int $clientId = null
    ) {
    }

    public function getClientId(): ?int
    {
        return $this->clientId;
    }
}
