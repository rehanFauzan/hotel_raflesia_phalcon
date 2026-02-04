<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExcelEasyTable
{
    private $spreadsheet;
    private $worksheet;
    private $currentRow = 1;
    private $currentCol = 0;
    private $tableStartCol = 0;
    private $defaultStyles;
    private $currentStyle;
    private $colWidths = [];
    private $headerRows = [];
    private $rowHeights = [];
    private $grid = []; // Untuk melacak cell yang digunakan
    private $tableCounter = 0;
    private $isInTable = false;
    private $documentWidth = 100;
    private $numCols = 0;

    public function __construct(Spreadsheet $spreadsheet, $worksheetName = 'Sheet1')
    {
        $this->spreadsheet = $spreadsheet;
        $this->worksheet = $spreadsheet->getSheetByName($worksheetName) ?? $spreadsheet->getActiveSheet();
        
        $this->initDefaultStyles();
        $this->resetStyle();
    }

    private function initDefaultStyles()
    {
        $this->defaultStyles = [
            'font' => [
                'name' => 'Arial',
                'size' => 10,
                'bold' => false,
                'italic' => false,
                'underline' => Font::UNDERLINE_NONE,
                'color' => ['rgb' => '000000']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
                'indent' => 1
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFFFF']
            ],
            'colspan' => 0,
            'rowspan' => 0,
            'paddingX' => 1,
            'paddingY' => 1,
            'lineHeight' => 15
        ];
    }

    private function resetStyle()
    {
        $this->currentStyle = $this->defaultStyles;
    }

    public function startTable($numCols, $style = '', $startPosition = 'A')
    {
        if ($this->isInTable) {
            $this->endTable();
        }

        $this->isInTable = true;
        $this->tableCounter++;
        $this->tableStartCol = Coordinate::columnIndexFromString($startPosition) - 1;
        $this->currentCol = $this->tableStartCol;
        $this->headerRows = [];
        $this->colWidths = [];
        $this->rowHeights = [];
        $this->grid = [];

        $this->parseStyle($style);
        $this->calculateColumnWidths($numCols);
        $this->numCols = count($this->colWidths);

        foreach ($this->colWidths as $col => $width) {
            $colLetter = Coordinate::stringFromColumnIndex($col + $this->tableStartCol + 1);
            $this->worksheet->getColumnDimension($colLetter)->setWidth($width);
        }
    }

    private function calculateColumnWidths($numCols)
    {
        if (is_int($numCols)) {
            $colWidth = $this->documentWidth / $numCols;
            $this->colWidths = array_fill(0, $numCols, $colWidth);
        } elseif (is_string($numCols) && preg_match('/^([%]?)\{([0-9., ]+)\}$/', $numCols, $matches)) {
            $isPercent = ($matches[1] === '%');
            $widths = array_map('floatval', explode(',', str_replace(' ', '', $matches[2])));
            
            if ($isPercent) {
                $totalPercent = array_sum($widths);
                if ($totalPercent > 100) {
                    throw new \Exception("Total persentase kolom melebihi 100%");
                }
                
                foreach ($widths as $width) {
                    $this->colWidths[] = ($width / 100) * $this->documentWidth;
                }
            } else {
                $totalWidth = array_sum($widths);
                $this->colWidths = $widths;
                
                if ($totalWidth > $this->documentWidth) {
                    $ratio = $this->documentWidth / $totalWidth;
                    foreach ($this->colWidths as &$width) {
                        $width *= $ratio;
                    }
                }
            }
        } else {
            throw new \Exception("Format kolom tidak valid");
        }
    }

    public function rowStyle($styleString)
    {
        $this->parseStyle($styleString);
    }

    public function easyCell($data, $styleString = '')
    {
        if (!$this->isInTable) {
            throw new \Exception("Tabel belum dimulai, gunakan startTable() terlebih dahulu");
        }

        // Parse cell style
        $cellStyle = $this->parseStyle($styleString, true);
        $colspan = $cellStyle['colspan'];
        $rowspan = $cellStyle['rowspan'];

        // Cari posisi yang tersedia untuk cell ini
        while (isset($this->grid[$this->currentRow][$this->currentCol])) {
            $this->currentCol++;
            // Jika melebihi jumlah kolom, kembali ke aritnya
            if ($this->currentCol >= $this->tableStartCol + $this->numCols) {
                $this->currentRow++;
                $this->currentCol = $this->tableStartCol;
            }
        }

        $startCol = $this->currentCol;
        // Pastikan colspan tidak melebihi batas kolom tabel
        $maxColspan = $this->tableStartCol + $this->numCols - $startCol;
        if ($colspan > $maxColspan) {
            $colspan = $maxColspan;
            $cellStyle['colspan'] = $colspan;
        }

        $endCol = $startCol + $colspan;
        $startRow = $this->currentRow;
        $endRow = $startRow + $rowspan;

        // Tandai cell yang digunakan di grid
        for ($r = $startRow; $r <= $endRow; $r++) {
            for ($c = $startCol; $c <= $endCol; $c++) {
                $this->grid[$r][$c] = true;
            }
        }

        $cellCoordinate = Coordinate::stringFromColumnIndex($startCol + 1) . $startRow;
        $this->worksheet->setCellValueExplicit($cellCoordinate, $data, DataType::TYPE_STRING);
        
        // Merge dan apply style
        if ($colspan > 0 || $rowspan > 0) {
            $endCoordinate = Coordinate::stringFromColumnIndex($endCol + 1) . $endRow;
            $this->worksheet->mergeCells($cellCoordinate . ':' . $endCoordinate);
        }
        
        $this->applyCellStyle($cellCoordinate, $cellStyle);

        // Update currentCol for next cell
        $this->currentCol = $endCol + 1;
        
        // Jika sudah di akhir baris, reset kembali ke awal baris
        if ($this->currentCol >= $this->tableStartCol + $this->numCols) {
            $this->currentCol = $this->tableStartCol;
        }
    }

    public function printRow($setAsHeader = false)
    {
        if ($setAsHeader) {
            $this->headerRows[] = $this->currentRow;
        }

        $this->worksheet->getRowDimension($this->currentRow)->setRowHeight(
            $this->currentStyle['lineHeight']
        );

        $this->currentRow++;
        $this->currentCol = $this->tableStartCol;
        $this->resetStyle();
    }

    public function endTable()
    {
        if (!$this->isInTable) return;

        // Apply header style
        $startColLetter = Coordinate::stringFromColumnIndex($this->tableStartCol + 1);
        $endColLetter = Coordinate::stringFromColumnIndex($this->tableStartCol + count($this->colWidths));
        
        foreach ($this->headerRows as $row) {
            $range = $startColLetter . $row . ':' . $endColLetter . $row;
            
            $this->worksheet->getStyle($range)->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DDDDDD']
                ]
            ]);
        }

        $this->currentRow += 2;
        $this->isInTable = false;
        $this->resetStyle();
    }

    private function parseStyle($styleString, $isCellStyle = false)
    {
        $style = $isCellStyle ? array_merge([], $this->currentStyle) : $this->defaultStyles;

        if (!empty($styleString)) {
            $styleParts = explode(';', $styleString);
            foreach ($styleParts as $part) {
                $part = trim($part);
                if (strpos($part, ':') !== false) {
                    list($key, $value) = explode(':', $part, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    switch ($key) {
                        case 'font-family':
                            $style['font']['name'] = $value;
                            break;
                        case 'font-size':
                            $style['font']['size'] = (float)$value;
                            break;
                        case 'font-weight':
                            $style['font']['bold'] = strtoupper($value) === 'B';
                            break;
                        case 'font-style':
                            $style['font']['italic'] = strtoupper($value) === 'I';
                            break;
                        case 'font-color':
                            $style['font']['color']['rgb'] = $this->parseColor($value);
                            break;
                        case 'bgcolor':
                            $style['fill']['startColor']['rgb'] = $this->parseColor($value);
                            break;
                        case 'border':
                            $this->parseBorder($style, $value);
                            break;
                        case 'border-color':
                            foreach ($style['borders'] as &$border) {
                                if (is_array($border)) {
                                    $border['color']['rgb'] = $this->parseColor($value);
                                }
                            }
                            break;
                        case 'border-width':
                            $width = (int)$value;
                            $borderStyle = $width <= 1 ? Border::BORDER_THIN : 
                                          ($width <= 2 ? Border::BORDER_MEDIUM : Border::BORDER_THICK);
                            foreach ($style['borders'] as &$border) {
                                if (is_array($border)) {
                                    $border['borderStyle'] = $borderStyle;
                                }
                            }
                            break;
                        case 'align':
                            $style['alignment']['horizontal'] = $this->mapAlignment($value);
                            break;
                        case 'valign':
                            $style['alignment']['vertical'] = $this->mapVerticalAlignment($value);
                            break;
                        case 'paddingX':
                            $style['paddingX'] = (float)$value;
                            $style['alignment']['indent'] = (int)$value;
                            break;
                        case 'paddingY':
                            $style['paddingY'] = (float)$value;
                            break;
                        case 'line-height':
                            $style['lineHeight'] = (float)$value;
                            break;
                        case 'colspan':
                            $style['colspan'] = max(0, (int)$value);
                            break;
                        case 'rowspan':
                            $style['rowspan'] = max(0, (int)$value);
                            break;
                    }
                }
            }
        }

        if ($isCellStyle) {
            return $style;
        } else {
            $this->currentStyle = $style;
        }
    }

    private function parseColor($color)
    {
        $color = ltrim($color, '#');
        if (strlen($color) === 3) {
            $color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
        }
        return strtoupper($color);
    }

    private function parseBorder(&$style, $value)
    {
        if ($value === 'none') {
            $style['borders'] = [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_NONE,
                    'color' => ['rgb' => '000000']
                ]
            ];
        } else {
            $borderParts = explode(',', $value);
            $borders = [];
            
            foreach ($borderParts as $part) {
                $part = strtoupper(trim($part));
                switch ($part) {
                    case 'T': case 'TOP':
                        $borders['top'] = [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => $style['borders']['allBorders']['color']
                        ];
                        break;
                    case 'R': case 'RIGHT':
                        $borders['right'] = [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => $style['borders']['allBorders']['color']
                        ];
                        break;
                    case 'B': case 'BOTTOM':
                        $borders['bottom'] = [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => $style['borders']['allBorders']['color']
                        ];
                        break;
                    case 'L': case 'LEFT':
                        $borders['left'] = [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => $style['borders']['allBorders']['color']
                        ];
                        break;
                    case 'ALL':
                        $borders['allBorders'] = [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => $style['borders']['allBorders']['color']
                        ];
                        break;
                }
            }
            
            if (!empty($borders)) {
                $style['borders'] = $borders;
            }
        }
    }

    private function mapAlignment($value)
    {
        $value = strtoupper($value);
        switch ($value) {
            case 'C': case 'CENTER':
                return Alignment::HORIZONTAL_CENTER;
            case 'R': case 'RIGHT':
                return Alignment::HORIZONTAL_RIGHT;
            case 'J': case 'JUSTIFY':
                return Alignment::HORIZONTAL_JUSTIFY;
            default:
                return Alignment::HORIZONTAL_LEFT;
        }
    }

    private function mapVerticalAlignment($value)
    {
        $value = strtoupper($value);
        switch ($value) {
            case 'T': case 'TOP':
                return Alignment::VERTICAL_TOP;
            case 'B': case 'BOTTOM':
                return Alignment::VERTICAL_BOTTOM;
            default:
                return Alignment::VERTICAL_CENTER;
        }
    }

    private function applyCellStyle($cellCoordinate, $style)
    {
        $spreadsheetStyle = [
            'font' => $style['font'],
            'alignment' => $style['alignment'],
            'borders' => $style['borders'],
            'fill' => $style['fill']
        ];

        $this->worksheet->getStyle($cellCoordinate)->applyFromArray($spreadsheetStyle);
    }

    public function createTable($headers, $data, $options = [])
    {
        $defaultOptions = [
            'columnWidths' => 'auto',
            'headerStyle' => 'font-weight:B; bgcolor:DDDDDD; align:center',
            'rowStyle' => '',
            'tableStyle' => '',
            'autoSize' => false,
            'startPosition' => 'A'
        ];
        $options = array_merge($defaultOptions, $options);

        $numCols = is_array($options['columnWidths']) ? count($options['columnWidths']) : count($headers);
        $this->startTable($options['columnWidths'], $options['tableStyle'], $options['startPosition']);

        // Header
        $this->rowStyle($options['headerStyle']);
        foreach ($headers as $header) {
            $this->easyCell($header);
        }
        $this->printRow(true);

        // Data
        $this->rowStyle($options['rowStyle']);
        foreach ($data as $row) {
            foreach ($row as $cellData) {
                $this->easyCell($cellData);
            }
            $this->printRow();
        }

        if ($options['autoSize']) {
            $startCol = $this->tableStartCol + 1;
            $endCol = $startCol + $numCols - 1;
            
            foreach (range($startCol, $endCol) as $col) {
                $this->worksheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
            }
        }

        $this->endTable();
    }

    public function easyRow($rowData, $style = '')
    {
        if (!empty($style)) {
            $this->rowStyle($style);
        }

        foreach ($rowData as $cellData) {
            $this->easyCell($cellData);
        }
    }
}