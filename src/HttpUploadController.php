<?php
declare(strict_types=1);

namespace HttpUpload;

use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class HttpUploadController
{
    /** @var FileWriter */
    private $fileWriter;

    /**
     * @param FileWriter $fileWriter
     */
    public function __construct(FileWriter $fileWriter)
    {
        $this->fileWriter = $fileWriter;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws Exception
     * @return Response
     */
    public function post(Request $request, Response $response): Response
    {
        $filepath = $request->getHeaderLine('X-Filepath');
        $uploadedFiles = $request->getUploadedFiles();

        if (empty($filepath)) {
            $response->getBody()->write('Please provide a filepath in X-FilePath.');

            return $response->withStatus(400);
        }

        if (!isset($uploadedFiles['data'])) {
            $response->getBody()->write('Please provide the file contents under the key "data".');

            return $response->withStatus(400);
        }

        try {
            $this->fileWriter->write($filepath, $uploadedFiles['data']);

            $response->getBody()->write('Wrote ' . $filepath);

            return $response->withStatus(200);
        } catch (\RuntimeException $exception) {
            $response->getBody()->write('Failed writing ' . $filepath);

            return $response->withStatus(500);
        }
    }
}
