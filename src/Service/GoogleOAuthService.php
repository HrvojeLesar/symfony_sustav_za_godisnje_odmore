<?php

namespace App\Service;

use App\Exceptions\GoogleAuthFailedException;
use App\Exceptions\InvalidStateException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleOAuthService
{
    protected SessionInterface $session;
    protected Request $request;

    protected string $state;

    public function __construct(
        protected string $clientId,
        protected string $clientSecret,
        protected string $authEndpoint,
        protected string $tokenEndpoint,
        protected string $userInfoEndpoint,
        protected string $redirectUri,
        protected RequestStack $requestStack,
        protected UrlGeneratorInterface $urlGenerator,
        protected HttpClientInterface $httpClient,
    ) {
        $this->session = $this->requestStack->getSession();
        $this->request = $this->requestStack->getCurrentRequest();
        $this->state = $this->getSessionState() ?? $this->generateState();
    }

    public function getLoginUrl(): string
    {
        $this->setSessionState();
        $params = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'scope' => $this->getScopes(),
            'redirect_uri' => $this->redirectUri,
            'state' => $this->state
        ];
        $url = $this->authEndpoint.'?'.http_build_query($params);
        return $url;
    }

    protected function generateState(): string
    {
        $state = bin2hex(random_bytes(32));
        return $state;
    }

    protected function getScopes(): string
    {
        $scopes = [
            'email',
        ];
        return implode(' ', $scopes);
    }

    protected function setSessionState(): void
    {
        $this->session->set('state', $this->state);
    }

    protected function getSessionState(): string|null
    {
        return $this->session->get('state');
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function handleCallback(): void
    {
        $this->validateState();
        $userInfo = $this->exchangeCode();
        if (! isset($userInfo['email'])) {
            throw new GoogleAuthFailedException();
        }
        $this->session->set('googleOAuthUserEmail', $userInfo['email']);
    }

    protected function validateState(): void
    {
        if ($this->request->get('state') !== $this->getState()) {
            throw new InvalidStateException();
        }
    }

    protected function exchangeCode(): mixed
    {
        $code = $this->request->get('code');
        $response = $this->httpClient->request(
            'POST',
            $this->tokenEndpoint,
            [
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'body' => [
                    'code' => $code,
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'redirect_uri' => $this->redirectUri,
                    'grant_type' => 'authorization_code'
                ]
            ]
        );

        $accessInfo = json_decode($response->getContent(), true);
        $response = $this->httpClient->request(
            'GET',
            $this->userInfoEndpoint,
            [
                'headers' => ['Authorization' => 'Bearer '.$accessInfo['access_token']],
            ]
        );

        $userInfo = json_decode($response->getContent(), true);

        return $userInfo;
    }

    /**
    * @throws GoogleAuthFailedException
    */
    public function getEmail(): string
    {
        $email = $this->session->get('googleOAuthUserEmail');
        if (is_null($email)) {
            throw new GoogleAuthFailedException();
        }
        return $email;
    }
}
