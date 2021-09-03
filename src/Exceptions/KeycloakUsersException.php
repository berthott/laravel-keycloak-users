<?php

namespace berthott\KeycloakUsers\Exceptions;

use Exception;
use GuzzleHttp\Exception\ClientException;

class KeycloakUsersException extends Exception
{
    /**
     * The recommended response to send to the client.
     *
     * @var ClientException
     */
    private $guzzleException;

    /**
     * Create a new exception instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Response|null  $response
     * @return void
     */
    public function __construct(ClientException $guzzleException = null)
    {
        parent::__construct('The request to Keycloak failed.');

        $this->guzzleException = $guzzleException;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        $r = $this->guzzleException->getResponse();
        return response()->json(json_decode($r->getBody()->getContents()), $r->getStatusCode());
    }
}