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
        $dir = $request->getHeaderLine('X-ExtactTo');
        $uploadedFiles = $request->getUploadedFiles();

        if (empty($dir)) {
            $response->getBody()->write('Please provide a directory in X-ExtractTo.');

            return $response->withStatus(400);
        }

        if (!isset($uploadedFiles['data'])) {
            $response->getBody()->write('Please provide the file contents of your zip under the key "data".');

            return $response->withStatus(400);
        }

        /** @var UploadedFile $file */
        $file = $uploadedFiles['data'];

        if (pathinfo($file->getClientFilename(), PATHINFO_EXTENSION) !== 'zip') {
            $response->getBody()->write('Please provide a zip file.');

            return $response->withStatus(400);
        }

        try {
            $this->extractor->extract($dir, $file);

            $response->getBody()->write('Successfully deployed');

            return $response->withStatus(200);
        } catch (\RuntimeException $exception) {
            $response->getBody()->write('Deployment failed');

            return $response->withStatus(500);
        }
    }
}
