<?php
declare(strict_types=1);

namespace HttpUpload;

use Slim\Http\UploadedFile;
use ZipArchive;

class Extractor
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
     * @param string $dir
     * @param UploadedFile $file
     * @return void
     */
    public function extract(string $dir, UploadedFile $file)
    {
        $dir = ltrim($dir, '/');
        $absoluteDir = $this->rootDir . $dir;
        $zipPath = $absoluteDir . '/' . $file->getClientFilename();

        $this->createDirectories(pathinfo($dir, PATHINFO_DIRNAME));
        $file->moveTo($zipPath);
        $this->extractZip($zipPath, $absoluteDir);

        unlink($zipPath);
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

    /**
     * @param $zipPath
     * @param $absoluteDir
     * @return void
     */
    private function extractZip($zipPath, $absoluteDir)
    {
        $archive = new ZipArchive();
        $archive->open($zipPath);
        $archive->extractTo($absoluteDir);
        $archive->close();
    }
}
