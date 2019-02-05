<?php
declare(strict_types=1);

namespace HttpUpload;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\UploadedFile;

class HttpUploadController
{
    /** @var Extractor */
    private $extractor;

    /**
     * @param Extractor $extractor
     */
    public function __construct(Extractor $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws Exception
     * @return Response
     */
    public function post(Request $request, Response $response): Response
    {
        $dir = $request->getHeaderLine('X-ExtractTo');
        $uploadedFiles = $request->getUploadedFiles();

        if (empty($dir)) {
            return $this->createResponse($response, 400, 'Please provide a directory in X-ExtractTo.');
        }

        if (!isset($uploadedFiles['data'])) {
            return $this->createResponse($response, 400, 'Please provide the file contents of your zip under the key "data".');
        }

        /** @var UploadedFile $file */
        $file = $uploadedFiles['data'];

        if (pathinfo($file->getClientFilename(), PATHINFO_EXTENSION) !== 'zip') {
            return $this->createResponse($response, 400, 'Please provide a zip file.');
        }

        try {
            $this->extractor->extract($dir, $file);

            return $this->createResponse($response, 200, 'Successfully deployed');
        } catch (\RuntimeException $exception) {
            return $this->createResponse($response, 500, 'Deployment failed');
        }
    }

    private function createResponse(Response $response, int $status, string $message)
    {
        $response->getBody()->write($message);

        return $response->withStatus($status);
    }
}
