<?php

namespace BinSoul\Test\Net\Http\Message;

use BinSoul\Net\Http\Message\UploadedFile;
use Psr\Http\Message\StreamInterface;

class UploadedFileTest extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    protected $tempFile;

    public function tearDown()
    {
        if (file_exists($this->tempFile)) {
            @unlink($this->tempFile);
        }

        $this->tempFile = null;
    }

    public function test_constructor()
    {
        /** @var StreamInterface $stream */
        $stream = $this->getMock(StreamInterface::class);

        $file = new UploadedFile($stream, 'filename', UPLOAD_ERR_OK, 12345, 'clientFilename', 'clientMediaType');

        $this->assertSame($stream, $file->getStream());
        $this->assertEquals(UPLOAD_ERR_OK, $file->getError());
        $this->assertEquals(12345, $file->getSize());
        $this->assertEquals('clientFilename', $file->getClientFilename());
        $this->assertEquals('clientMediaType', $file->getClientMediaType());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_getStream_raises_exception_for_upload_error()
    {
        /** @var StreamInterface $stream */
        $stream = $this->getMock(StreamInterface::class);
        (new UploadedFile($stream, '', UPLOAD_ERR_CANT_WRITE))->getStream();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_moveTo_raises_exception_for_upload_error()
    {
        /** @var StreamInterface $stream */
        $stream = $this->getMock(StreamInterface::class);
        (new UploadedFile($stream, '', UPLOAD_ERR_CANT_WRITE))->moveTo('php://memory');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_moveTo_raises_exception_for_invalid_target()
    {
        /** @var StreamInterface $stream */
        $stream = $this->getMock(StreamInterface::class);
        (new UploadedFile($stream, '', UPLOAD_ERR_OK))->moveTo('');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_moveTo_uses_move_uploaded_file()
    {
        /** @var StreamInterface $stream */
        $stream = $this->getMock(StreamInterface::class);
        $this->tempFile = tempnam(sys_get_temp_dir(), 'BinSoul');

        $uploadedFile = new UploadedFile($stream, 'abc://abc', UPLOAD_ERR_OK);
        $uploadedFile->moveTo($this->tempFile);
    }

    /**
     * @return UploadedFile
     */
    private function buildMovableFile()
    {
        $stream = $this->getMock(StreamInterface::class);

        $count = 0;
        $stream->expects($this->any())
            ->method('eof')
            ->willReturnCallback(function () use (&$count) {
                ++$count;

                return $count > 1;
            });

        $stream->expects($this->once())
            ->method('read')
            ->willReturn('abc');

        $this->tempFile = tempnam(sys_get_temp_dir(), 'BinSoul');

        /** @var StreamInterface $stream */
        $uploadedFile = new UploadedFile($stream, '', UPLOAD_ERR_OK);

        return $uploadedFile;
    }

    public function test_moveTo_uses_stream()
    {
        $uploadedFile = $this->buildMovableFile();
        $uploadedFile->moveTo($this->tempFile);
        $this->assertEquals('abc', file_get_contents($this->tempFile));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_moveTo_raises_exception_for_unwritable_target()
    {
        /** @var StreamInterface $stream */
        $stream = $this->getMock(StreamInterface::class);
        $this->tempFile = sys_get_temp_dir();

        $uploadedFile = new UploadedFile($stream, '', UPLOAD_ERR_OK);
        $uploadedFile->moveTo(sys_get_temp_dir());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_moveTo_raises_exception_if_moved()
    {
        $uploadedFile = $this->buildMovableFile();
        $uploadedFile->moveTo($this->tempFile);
        $uploadedFile->moveTo($this->tempFile);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function test_getStream_raises_exception_if_moved()
    {
        $uploadedFile = $this->buildMovableFile();
        $uploadedFile->moveTo($this->tempFile);
        $uploadedFile->getStream();
    }
}
