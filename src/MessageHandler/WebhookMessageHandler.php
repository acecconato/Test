<?php

namespace App\MessageHandler;

use App\Entity\Client;
use App\Message\WebhookMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Exception\EnvNotFoundException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class WebhookMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $manager,
        private readonly HttpClientInterface $httpClient,
        private readonly NormalizerInterface $normalizer,
        private readonly ?string $webhookUrl = null
    ) {
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function __invoke(WebhookMessage $message): void
    {
        $client = $this->manager->find(Client::class, $message->getClientId());

        if (null === $client) {
            return;
        }

        if (null === $this->webhookUrl) {
            throw new EnvNotFoundException('Unable to get the WEBHOOK_URL parameter');
        }

        $this->httpClient->request(
            'POST',
            $this->webhookUrl,
            [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => $this->normalizer->normalize($client, 'json', ['groups' => 'client.read']),
            ]
        );
    }
}
