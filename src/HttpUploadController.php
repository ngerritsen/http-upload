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
        $filepath = $request->getHeaderLine('X-FilePath');
        $data = $request->getBody();

        if (empty($filepath)) {
            throw new Exception('Please provide a valid file path.');
        }

        $this->fileWriter->write($filepath, $data);

        $response->getBody()->write('Wrote ' . $filepath);

        return $response->withStatus(200);
    }
}
