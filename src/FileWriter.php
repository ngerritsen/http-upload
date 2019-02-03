<?php
declare(strict_types=1);

namespace HttpUpload;

use Slim\Http\UploadedFile;

class FileWriter
{
    /** @var string */
    private $rootDir;

    /**
     * @param string $rootDir
     */
    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $basePath
     * @param string $dir
     * @param UploadedFile $file
     * @return void
     */
    public function write(string $filepath, UploadedFile $file)
    {
        $filepath = ltrim($filepath, '/');
        $this->createDirectories(pathinfo($filepath)['dirname']);
        $file->moveTo($this->rootDir . $filepath);
    }

    /**
     * @param string $dir
     * @return void
     */
    private function createDirectories(string $dir)
    {
        $dirs = explode('/', $dir);
        $currentDir = $this->rootDir;

        while (!empty($dirs)) {
            $currentDir = $currentDir . array_shift($dirs) . '/';

            if (is_dir($currentDir)) {
                continue;
            }

            mkdir($this->rootDir . $dir, 0744, true);
            break;
        }
    }
}
