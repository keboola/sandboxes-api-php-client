<?php

declare(strict_types=1);

namespace Keboola\Sandboxes\Api;

class SandboxCredentials
{
    private string $type;
    private string $projectId;
    private string $privateKeyId;
    private string $clientEmail;
    private string $clientId;
    private string $authUri;
    private string $tokenUri;
    private string $authProviderCertUrl;
    private string $clientCertUrl;
    private string $privateKey;

    public static function create(): self
    {
        return new self();
    }

    public static function fromArray(array $data): self
    {
        $instance = new self();
        $instance->type = $data['type'];
        $instance->projectId = $data['project_id'];
        $instance->privateKeyId = $data['private_key_id'];
        $instance->clientEmail = $data['client_email'];
        $instance->clientId = $data['client_id'];
        $instance->authUri = $data['auth_uri'];
        $instance->tokenUri = $data['token_uri'];
        $instance->authProviderCertUrl = $data['auth_provider_x509_cert_url'];
        $instance->clientCertUrl = $data['client_x509_cert_url'];
        $instance->privateKey = $data['private_key'];

        return $instance;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'project_id' => $this->projectId,
            'private_key_id' => $this->privateKeyId,
            'client_email' => $this->clientEmail,
            'client_id' => $this->clientId,
            'auth_uri' => $this->authUri,
            'token_uri' => $this->tokenUri,
            'auth_provider_x509_cert_url' => $this->authProviderCertUrl,
            'client_x509_cert_url' => $this->clientCertUrl,
            'private_key' => $this->privateKey,
        ];
    }
}
