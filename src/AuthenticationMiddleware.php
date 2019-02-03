<?php
declare(strict_types=1);

namespace HttpUpload;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthenticationMiddleware
{
    /** @var string */
    private $key;

    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response {
        $providedKey = $request->getHeaderLine('X-Key');

        if (empty($providedKey) || $this->key !== $providedKey) {
            $response->getBody()->write('Access denied.');

            return $response->withStatus(403);
        };

        return $next($request, $response);
    }
}
