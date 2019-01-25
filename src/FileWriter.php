<?php
declare(strict_types=1);

namespace HttpUpload;

use Exception;
use Psr\Http\Message\StreamInterface;

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
     * @param string $filepath
     * @param StreamInterface $data
     * @throws Exception
     * @return void
     */
    public function write(string $filepath, StreamInterface $data)
    {
        $this->createDirectories($filepath);

        $result = file_put_contents($this->rootDir . $filepath, $data);

        if ($result === false) {
            throw new Exception('Failed to write ' . $filepath);
        }
    }


    /**
     * @param string $filepath
     * @return void
     */
    private function createDirectories(string $filepath)
    {
        $dirname = pathinfo($filepath)['dirname'];
        $dirs = array_filter(explode('/', $dirname));
        $currentDir = $this->rootDir;

        while (!empty($dirs)) {
            $currentDir .= array_shift($dirs);

            if (is_dir($currentDir)) {
                continue;
            }

            mkdir($this->rootDir . $dirname, 0744, true);
            break;
        }
    }
}
