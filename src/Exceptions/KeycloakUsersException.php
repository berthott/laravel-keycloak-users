<?php

namespace berthott\KeycloakUsers\Exceptions;

use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;

class KeycloakUsersException extends Exception
{
    /**
     * The recommended response to send to the client.
     */
    private RequestException $guzzleException;

    /**
     * Create a new exception instance.
     */
    public function __construct(RequestException|null $guzzleException = null)
    {
        parent::__construct('The request to Keycloak failed.');

        $this->guzzleException = $guzzleException;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(/* Request $request */): JsonResponse
    {
        $response = $this->guzzleException->getResponse();

        return response()->json(['errors' => json_decode($response->getBody()->getContents())->errorMessage], 422);
    }
}
