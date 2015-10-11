<?php

namespace BinSoul\Net\Http\Message;

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Implements the PSR-7 UploadedFileInterface.
 *
 * {@inheritdoc}
 */
class UploadedFile implements UploadedFileInterface
{
    /** @var StreamInterface */
    private $stream;
    /** @var string */
    private $filename;
    /** @var int */
    private $size;
    /** @var int */
    private $error;
    /** @var string */
    private $clientFilename;
    /** @var string */
    private $clientMediaType;
    /** @var bool */
    private $isMoved = false;

    /**
     * Constructs an instance of this class.
     *
     * @param StreamInterface $stream          stream for the file
     * @param string          $filename        path to the file
     * @param int             $size            size of the file
     * @param int             $errorStatus     UPLOAD_ERR_XXX status code of the file
     * @param string          $clientFilename  name of the file sent by the client
     * @param string          $clientMediaType mime type of the file sent by the client
     */
    public function __construct(
        StreamInterface $stream,
        $filename,
        $errorStatus,
        $size = null,
        $clientFilename = null,
        $clientMediaType = null
    ) {
        $this->stream = $stream;
        $this->filename = $filename;
        $this->error = (int) $errorStatus;
        $this->size = $size;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    public function getStream()
    {
        $this->assertNotMoved();

        return $this->stream;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getClientFilename()
    {
        return $this->clientFilename == '' ? null : $this->clientFilename;
    }

    public function getClientMediaType()
    {
        return $this->clientMediaType == '' ? null : $this->clientMediaType;
    }

    public function moveTo($targetPath)
    {
        $this->assertNotMoved();

        if ($targetPath == '' || !@file_exists($targetPath)) {
            throw new \InvalidArgumentException(sprintf('Invalid path "%s" provided for move operation.', $targetPath));
        }

        if ($this->filename == '') {
            $stream = $this->getStream();

            $handle = @fopen($targetPath, 'wb');
            if ($handle === false) {
                throw new \RuntimeException(
                    sprintf(
                        'Unable to write to target path "%s".',
                        $targetPath
                    )
                );
            }

            $stream->rewind();
            while (!$stream->eof()) {
                @fwrite($handle, $stream->read(1 * 1024 * 1024));
            }

            @fclose($handle);
        } else {
            if (move_uploaded_file($this->filename, $targetPath) === false) {
                throw new \RuntimeException(
                    sprintf(
                        'An error occurred while moving uploaded file to target path "%s".',
                        $targetPath
                    )
                );
            }
        }

        $this->isMoved = true;
    }

    /**
     * Asserts that the file was uploaded without an error and is not moved.
     *
     * @throws \RuntimeException
     */
    private function assertNotMoved()
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('The file is not available because of an upload error.');
        }

        if ($this->isMoved) {
            throw new \RuntimeException('The file has already been moved.');
        }
    }
}
