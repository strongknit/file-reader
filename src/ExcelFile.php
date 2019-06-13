<?php

namespace Strongknit\FileReader;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Settings;

/**
 * Class ExcelFile
 * @package ShiptorRussiaBundle\DataImporter\File
 */
class ExcelFile extends AbstractFile
{
    /**
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    protected function getRawData(): array
    {
        Settings::setLibXmlLoaderOptions(LIBXML_BIGLINES | LIBXML_COMPACT | LIBXML_NONET | LIBXML_NOBLANKS | LIBXML_PARSEHUGE);
        $reader = IOFactory::createReaderForFile($this->getFullPath());
        $excel = $reader->load($this->getFullPath());
        $sheetCount = $excel->getSheetCount();
        $data = [];
        for ($k = 0; $k < $sheetCount; $k++) {
            $sheet = $excel->getSheet($k);
            foreach ($sheet->getRowIterator() as $row) {
                $rowData = [
                    '__sheet' => $k,
                ];
                foreach ($row->getCellIterator() as $col) {
                    $rowData[] = $col->getFormattedValue();
                }
                $data[] = $rowData;
            }
        }

        return $data;
    }
}
