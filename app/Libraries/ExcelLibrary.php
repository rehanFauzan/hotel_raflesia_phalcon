<?php
namespace App\Libraries;

use Core\Facades\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Shared\Font;

defined('BASEPATH') or exit('No direct script access allowed');

class ExcelLibrary
{
    private $excel = null;

    public function load($excel_path)
    {
        $this->excel = IOFactory::load($excel_path);
        $this->setActiveSheetIndex(0);
        return $this;
    }

    public function newExcel()
    {
        $this->excel = new Spreadsheet();
        $this->setActiveSheetIndex(0);
        Font::setAutoSizeMethod(Font::AUTOSIZE_METHOD_EXACT);
        return $this;
    }

    public function getExcel()
    {
        return $this->excel;
    }

    public function setActiveSheetIndex($index)
    {
        $this->excel->setActiveSheetIndex($index);
        return $this;
    }

    /**
     * @return Worksheet
     */
    public function getActiveSheet()
    {
        return $this->excel->getActiveSheet();
    }

    public function __call($method, $args = [])
    {
        $sheet = $this->getActiveSheet();
        $return = call_user_func_array([$sheet, $method], $args);

        return get_class($return) == Worksheet::class ? $this : $return;
    }

    public function apply_data($data, $options = [])
    {
        $start_row      = $options['start_row'] ?? 1;
        $start_column   = $options['start_column'] ?? 1;
        $with_numbering = $options['with_numbering'] ?? TRUE;
        $with_border    = $options['with_border'] ?? TRUE;

        $properties = $options['properties'] ?? NULL;
        if(!is_array($properties) && isset($data[0]))
        {
            $properties = array_keys($data[0]);
        }
        
        $sheet = $this->getActiveSheet();

        $row = $start_row;
        $column = $start_column;
        foreach($data as $item)
        {
            $item = (array) $item;
            $column = $start_column;
            if($with_numbering)
            {
                $sheet->setCellValueByColumnAndRow($column++, $row, $row - $start_row + 1);
            }

            foreach($properties as $property)
            {
                if(array_key_exists($property, $item))
                {
                    $sheet->setCellValueByColumnAndRow($column, $row, $item[$property]);
                }
                $column++;
            }

            $row++;
        }

        if($with_border && $column > 0 && $row > 0)
        {
            $sheet->getStyleByColumnAndRow($start_column, $start_row, $column - 1, $row - 1)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Style\Border::BORDER_THIN,
                        'color' => ['argb' => '00000000'],
                    ],
                ],
            ]);

            $sheet->getStyleByColumnAndRow(1, 1);
        }
        return $this;
    }

    public function setBorder(
        $column1, $row1, $column2, $row2,
        $style = Style\Border::BORDER_THIN,
        $color = ['argb' => 'FF000000']
    ) {
        $this->getActiveSheet()->getStyleByColumnAndRow($column1, $row1, $column2, $row2)
            ->getBorders()->applyFromArray([
                'allBorders' => [ 'borderStyle' => $style, 'color' => $color ],
            ]);

        return $this;
    }

    public function alignCenter($column1, $row1, $column2, $row2)
    {
        $this->getActiveSheet()->getStyleByColumnAndRow($column1, $row1, $column2, $row2)
            ->getAlignment()->applyFromArray([
                'horizontal' => 'center',
                'vertical'   => 'center',
            ]);
    }

    public function mergeCenter($column1, $row1, $column2, $row2) {
        $this->getActiveSheet()->mergeCellsByColumnAndRow($column1, $row1, $column2, $row2);
        $this->getActiveSheet()->getStyleByColumnAndRow($column1, $row1, $column2, $row2)
            ->getAlignment()->applyFromArray([
                'horizontal' => 'center',
                'vertical'   => 'center',
            ]);
        return $this;
    }

    public function output($filename)
    {
        $temp = tempnam(BASEPATH . '/storage/temp', 'excel_');
        chmod($temp, 0777);
        $writer = new Writer\Xlsx($this->excel);
        $writer->save($temp);
        $output = file_get_contents($temp);
        unlink($temp);

        return Response::setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setHeader('Cache-Control', 'max-age=0')
            ->setContentType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setContent($output);
    }

    public function output_html()
    {
        $objWriter = new Writer\Html($this->excel);
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save('php://output');
    }
}