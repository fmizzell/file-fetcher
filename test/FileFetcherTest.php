<?php

namespace FileFetcherTests;

use Contracts\Mock\Storage\Memory;
use FileFetcher\FileFetcher;
use FileFetcher\Processor\LastResort;
use FileFetcher\Processor\Local;
use FileFetcher\Processor\Remote;
use PHPUnit\Framework\TestCase;
use Procrastinator\Result;

class FileFetcherTest extends TestCase
{

    public function testCopyALocalFile()
    {
        // [Basic Usage]

        $fetcher = FileFetcher::get(
            "1",
            new Memory(),
            [
              "filePath" => __DIR__ . '/files/tiny.csv'
            ]
        );

        $fetcher->run();

        // [Basic Usage]

        $state = $fetcher->getState();

        $this->assertEquals(
            file_get_contents($state['source']),
            file_get_contents($state['destination'])
        );
    }

    public function testKeepOriginalFilename()
    {
        $fetcher = FileFetcher::get(
            "2",
            new Memory(),
            [
                "filePath" => __DIR__ . '/files/tiny.csv',
                "keep_original_filename" => true,
                "processors" => [Local::class],
            ]
        );

        $fetcher->run();
        $state = $fetcher->getState();

        $this->assertEquals(
            basename($state['source']),
            basename($state['destination'])
        );
    }

    public function testConfigValidationErrorConfigurationMissing()
    {
        $this->expectExceptionMessage('Constructor missing expected config filePath.');
        FileFetcher::get(
            "2",
            new Memory()
        );
    }

    public function testConfigValidationErrorMissingFilePath()
    {
        $this->expectExceptionMessage('Constructor missing expected config filePath.');
        FileFetcher::get(
            "2",
            new Memory(),
            []
        );
    }

    public function testCustomProcessorsValidationIsNotAnArray()
    {
        $fetcher = FileFetcher::get(
            "2",
            new Memory(),
            [
                "filePath" => __DIR__ . '/files/tiny.csv',
                "processors" => "hello"
            ]
        );
        // Not sure what to assert.
        $this->assertTrue(true);
    }

    public function testCustomProcessorsValidationNotAClass()
    {
        $fetcher = FileFetcher::get(
            "2",
            new Memory(),
            [
                "filePath" => __DIR__ . '/files/tiny.csv',
                "processors" => ["hello"]
            ]
        );
        // Not sure what to assert.
        $this->assertTrue(true);
    }

    public function testCustomProcessorsValidationImproperClass()
    {
        $fetcher = FileFetcher::get(
            "2",
            new Memory(),
            [
                "filePath" => __DIR__ . '/files/tiny.csv',
                "processors" => [\SplFileInfo::class]
            ]
        );
        // Not sure what to assert.
        $this->assertTrue(true);
    }
}
