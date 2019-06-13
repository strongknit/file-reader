<?php

namespace Strrongknit\FileReader;

/**
 * Class CsvFile
 * @package ShiptorRussiaBundle\DataImporter\File
 */
class CsvFile extends AbstractFile
{
    protected const DEFAULT_ENCODING = 'UTF-8';
    protected const DEFAULT_SEPARATOR = ';';
    protected const DEFAULT_DELIMITER = '"';

    /** @var array $colNames */
    protected $colNames;

    /** @var string $encoding */
    protected $encoding;

    /** @var string $separator */
    protected $separator;

    /** @var string $delimiter */
    protected $delimiter;

    /**
     * CsvFile constructor.
     * @param null  $fullPath
     * @param array $colNames
     */
    public function __construct($fullPath = null, array $colNames = [])
    {
        parent::__construct($fullPath);

        $this->colNames = $colNames;
        $this->encoding = self::DEFAULT_ENCODING;
        $this->separator = self::DEFAULT_SEPARATOR;
        $this->delimiter = self::DEFAULT_DELIMITER;
    }

    /**
     * @param array $colNames
     */
    public function setColNames(array $colNames = [])
    {
        $this->colNames = $colNames;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding(string $encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * @param string $separator
     */
    public function setSeparator(string $separator)
    {
        $this->separator = $separator;
    }

    /**
     * @param string $delimiter
     */
    public function setDelimiter(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    /**
     * @return array
     */
    protected function getRawData(): array
    {
        $buffer = fopen($this->getFullPath(), 'r');

        $lines = [];
        while (($line = fgetcsv($buffer, null, $this->separator, $this->delimiter)) !== false) {
            if ($this->encoding !== self::DEFAULT_ENCODING) {
                foreach ($line as $key => $field) {
                    $line[$key] = iconv($this->encoding, self::DEFAULT_ENCODING, $field);
                }
            }
            $lines[] = $line;
        }
        fclose($buffer);

        if (!empty($this->colNames)) {
            $headers = array_shift($lines);

            $defColumns = array_combine(array_column($this->colNames, 'name'), array_keys($this->colNames));

            $headers = array_map(function ($item) use ($defColumns) {
                return $defColumns[$item] ?? $item;
            }, $headers);

            $lines = array_map(function ($item) use ($headers) {
                return array_filter(array_combine(array_slice($headers, 0, count($item)), $item));
            }, $lines);
        }

        return $lines;
    }
}
