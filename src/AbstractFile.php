<?php

namespace Strongknit\FileReader;

use Iterator;

/**
 * Class AbstractFile
 * @package ShiptorRussiaBundle\DataImporter\File
 */
abstract class AbstractFile implements Iterator, \Countable
{
    /** @var array $data */
    protected $data;

    /** @var array $metaData */
    protected $metaData;

    /** @var string $uniqid */
    protected $uniqid;

    /** @var bool $parsed */
    protected $parsed;

    /** @var int $position */
    protected $position;

    /** @var string|null $fullPath */
    protected $fullPath;

    /**
     * AbstractFile constructor.
     * @param null $fullPath
     */
    public function __construct($fullPath = null)
    {
        $this->data = [];
        $this->uniqid = uniqid('file', true);
        $this->parsed = false;
        $this->position = 0;
        $this->fullPath = $fullPath;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getUniqId()
    {
        return $this->uniqid;
    }

    /**
     * @return bool
     */
    public function isParsed()
    {
        return $this->parsed;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        return $this->data[$this->position];
    }

    /**
     *
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @return int|mixed
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->data[$this->position]);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @param string $fullPath
     * @return $this
     */
    public function setFullPath($fullPath)
    {
        $this->fullPath = $fullPath;
        $this->data = [];
        $this->parsed = false;
        $this->rewind();

        return $this;
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        return $this->fullPath;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metaData;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function parse()
    {
        if (!$this->getFullPath()) {
            throw new \Exception("File path not set");
        }
        if (!is_readable($this->getFullPath())) {
            throw new \Exception("File not found");
        }
        $this->data = [];
        $rawData = $this->getRawData();
        $rowNumber = 0;
        while (\count($rawData)) {
            $rawRow = array_shift($rawData);
            $this->collectMetadata($rawRow, $rowNumber);
            $parsed = $this->parseRow($rawRow, $rowNumber);
            if (null !== $parsed) {
                $this->data[] = $parsed;
            }
            ++$rowNumber;
        }
        $this->parsed = true;
        $this->rewind();

        return $this;
    }

    /**
     * @param array $rawRowData
     * @param int   $rowNumber
     * @return array|null
     */
    protected function parseRow(array $rawRowData, int $rowNumber)
    {
        return $rawRowData;
    }

    /**
     * @param array $rawRowData
     * @param int   $rowNumber
     */
    protected function collectMetadata(array $rawRowData, int $rowNumber)
    {
        return;
    }

    /**
     * @return array
     */
    abstract protected function getRawData(): array;
}
