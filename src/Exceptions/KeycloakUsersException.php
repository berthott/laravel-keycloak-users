<?php

namespace berthott\KeycloakUsers\Exceptions;

use berthott\KeycloakUsers\Facades\KeycloakLog;
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
     * 
     * @param RequestException|null $guzzleException The recommended response to send to the client.
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
        $message = '';
        if ($error = json_decode($this->guzzleException->getResponse()->getBody()->getContents())) {
            $message = $error->errorMessage;
        }

        KeycloakLog::log("Keycloak Exception: {$message}");
        return response()->json(['errors' => $message], 422);
    }
}
