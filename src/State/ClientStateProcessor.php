<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Client;
use App\Entity\User;
use App\Message\WebhookMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;

class ClientStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly ProcessorInterface $persistProcessor,
        private readonly ProcessorInterface $removeProcessor,
        private readonly MessageBusInterface $messageBus
    ) {
    }

    /**
     * @param array<mixed> $uriVariables
     * @param array<mixed> $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if (!is_object($data) || Client::class !== $data::class) {
            return $data;
        }

        if ($operation instanceof DeleteOperationInterface) {
            return $this->removeProcessor->process($data, $operation, $uriVariables, $context);
        }

        // @phpstan-ignore-next-line
        $data = $this->setOwner($data);
        $data = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        $this->triggerWebhook($data);

        return $data;
    }

    private function setOwner(Client $client): Client
    {
        $user = $this->security->getUser();

        if (null === $user) {
            throw new UserNotFoundException();
        }

        if (User::class !== $user::class) {
            throw new \TypeError($user::class.' is not an instance of '.User::class);
        }

        // @phpstan-ignore-next-line
        $client->setOwner($user);

        return $client;
    }

    private function triggerWebhook(Client $client): void
    {
        $this->messageBus->dispatch(new WebhookMessage($client->getId()));
    }
}
