<?php

namespace App\Libraries;

// use Exception;
use Fpdf\Fpdf;

class McTablePdfOld extends Fpdf
{
    var $widths;
    var $aligns;
    var $height;

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function SetBorders($a)
    {
        //Set the array of borders alignments
        $this->borders = $a;
    }

    function getHeight()
    {
        return $this->height;
    }

    function SetDash($black=null, $white=null)
    {
        if($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }

    function VCell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false)
    {
        //Output a cell
        $k = $this->k;
        if ($this->y + $h > $this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak()) {
            //Automatic page break
            $x = $this->x;
            $ws = $this->ws;
            if ($ws > 0) {
                $this->ws = 0;
                $this->_out('0 Tw');
            }
            $this->AddPage($this->CurOrientation, $this->CurPageSize);
            $this->x = $x;
            if ($ws > 0) {
                $this->ws = $ws;
                $this->_out(sprintf('%.3F Tw', $ws * $k));
            }
        }
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $s = '';
        // begin change Cell function 
        if ($fill || $border > 0) {
            if ($fill)
                $op = ($border > 0) ? 'B' : 'f';
            else
                $op = 'S';
            if ($border > 1) {
                $s = sprintf(
                    'q %.2F w %.2F %.2F %.2F %.2F re %s Q ',
                    $border,
                    $this->x * $k,
                    ($this->h - $this->y) * $k,
                    $w * $k,
                    -$h * $k,
                    $op
                );
            } else
                $s = sprintf('%.2F %.2F %.2F %.2F re %s ', $this->x * $k, ($this->h - $this->y) * $k, $w * $k, -$h * $k, $op);
        }
        if (is_string($border)) {
            $x = $this->x;
            $y = $this->y;
            if (is_int(strpos($border, 'L')))
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x * $k, ($this->h - $y) * $k, $x * $k, ($this->h - ($y + $h)) * $k);
            else if (is_int(strpos($border, 'l')))
                $s .= sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ', $x * $k, ($this->h - $y) * $k, $x * $k, ($this->h - ($y + $h)) * $k);

            if (is_int(strpos($border, 'T')))
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - $y) * $k);
            else if (is_int(strpos($border, 't')))
                $s .= sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ', $x * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - $y) * $k);

            if (is_int(strpos($border, 'R')))
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', ($x + $w) * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
            else if (is_int(strpos($border, 'r')))
                $s .= sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ', ($x + $w) * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);

            if (is_int(strpos($border, 'B')))
                $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x * $k, ($this->h - ($y + $h)) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
            else if (is_int(strpos($border, 'b')))
                $s .= sprintf('q 2 w %.2F %.2F m %.2F %.2F l S Q ', $x * $k, ($this->h - ($y + $h)) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
        }
        if (trim($txt) != '') {
            $cr = substr_count($txt, "\n");
            if ($cr > 0) { // Multi line
                $txts = explode("\n", $txt);
                $lines = count($txts);
                for ($l = 0; $l < $lines; $l++) {
                    $txt = $txts[$l];
                    $w_txt = $this->GetStringWidth($txt);
                    if ($align == 'U')
                        $dy = $this->cMargin + $w_txt;
                    elseif ($align == 'D')
                        $dy = $h - $this->cMargin;
                    else
                        $dy = ($h + $w_txt) / 2;
                    $txt = str_replace(')', '\\)', str_replace('(', '\\(', str_replace('\\', '\\\\', $txt)));
                    if ($this->ColorFlag)
                        $s .= 'q ' . $this->TextColor . ' ';
                    $s .= sprintf(
                        'BT 0 1 -1 0 %.2F %.2F Tm (%s) Tj ET ',
                        ($this->x + .5 * $w + (.7 + $l - $lines / 2) * $this->FontSize) * $k,
                        ($this->h - ($this->y + $dy)) * $k,
                        $txt
                    );
                    if ($this->ColorFlag)
                        $s .= ' Q ';
                }
            } else { // Single line
                $w_txt = $this->GetStringWidth($txt);
                $Tz = 100;
                if ($w_txt > $h - 2 * $this->cMargin) {
                    $Tz = ($h - 2 * $this->cMargin) / $w_txt * 100;
                    $w_txt = $h - 2 * $this->cMargin;
                }
                if ($align == 'U')
                    $dy = $this->cMargin + $w_txt;
                elseif ($align == 'D')
                    $dy = $h - $this->cMargin;
                else
                    $dy = ($h + $w_txt) / 2;
                $txt = str_replace(')', '\\)', str_replace('(', '\\(', str_replace('\\', '\\\\', $txt)));
                if ($this->ColorFlag)
                    $s .= 'q ' . $this->TextColor . ' ';
                $s .= sprintf(
                    'q BT 0 1 -1 0 %.2F %.2F Tm %.2F Tz (%s) Tj ET Q ',
                    ($this->x + .5 * $w + .3 * $this->FontSize) * $k,
                    ($this->h - ($this->y + $dy)) * $k,
                    $Tz,
                    $txt
                );
                if ($this->ColorFlag)
                    $s .= ' Q ';
            }
        }
        // end change Cell function 
        if ($s)
            $this->_out($s);
        $this->lasth = $h;
        if ($ln > 0) {
            //Go to next line
            $this->y += $h;
            if ($ln == 1)
                $this->x = $this->lMargin;
        } else
            $this->x += $w;
    }

    function WordWrap(&$text, $maxwidth)
    {
        $text = trim($text);
        if ($text === '')
            return 0;
        $space = $this->GetStringWidth(' ');
        $lines = explode("\n", $text);
        $text = '';
        $count = 0;

        foreach ($lines as $line) {
            $words = preg_split('/ +/', $line);
            $width = 0;

            foreach ($words as $word) {
                $wordwidth = $this->GetStringWidth($word);
                if ($wordwidth > $maxwidth) {
                    // Word is too long, we cut it
                    for ($i = 0; $i < strlen($word); $i++) {
                        $wordwidth = $this->GetStringWidth(substr($word, $i, 1));
                        if ($width + $wordwidth <= $maxwidth) {
                            $width += $wordwidth;
                            $text .= substr($word, $i, 1);
                        } else {
                            $width = $wordwidth;
                            $text = rtrim($text) . "\n" . substr($word, $i, 1);
                            $count++;
                        }
                    }
                } elseif ($width + $wordwidth <= $maxwidth) {
                    $width += $wordwidth + $space;
                    $text .= $word . ' ';
                } else {
                    $width = $wordwidth + $space;
                    $text = rtrim($text) . "\n" . $word . ' ';
                    $count++;
                }
            }
            $text = rtrim($text) . "\n";
            $count++;
        }
        $text = rtrim($text);
        return $count;
    }



    function MultiCellRow($cells, $width, $height, $data)
    {
        $x = $this->GetX();
        $y = $this->GetY();
        $maxheight = 0;

        for ($i = 0; $i < $cells; $i++) {
            $this->MultiCell($width, $height, $data[$i]);
            if ($this->GetY() - $y > $maxheight) $maxheight = $this->GetY() - $y;
            $this->SetXY($x + ($width * ($i + 1)), $y);
        }

        for ($i = 0; $i < $cells + 1; $i++) {
            $this->Line($x + $width * $i, $y, $x + $width * $i, $y + $maxheight);
        }

        $this->Line($x, $y, $x + $width * $cells, $y);
        $this->Line($x, $y + $maxheight, $x + $width * $cells, $y + $maxheight);
    }

    function RowNlDynamicHegiht($height, $data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 7 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $border = isset($this->borders[$i]) ? $this->borders[$i] : 'LTBR';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border

            // Get Height
            // $height = $this->GetMultiCellHeight($w, $data[$i]);

            //Print the text
            $this->MultiCell($w, $height, $data[$i], $border, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        // $this->Ln($h);

        $this->height = $h;
    }

    function RowNl($data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 7 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $border = isset($this->borders[$i]) ? $this->borders[$i] : 'LTBR';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border

            // Get Height
            $height = $this->GetMultiCellHeight($w, $data[$i]);

            //Print the text
            $this->MultiCell($w, 6, $data[$i], $border, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        // $this->Ln($h);

        $this->height = $h;
    }

    function RowDynamicHeightNl($height, $data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 7 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $border = isset($this->borders[$i]) ? $this->borders[$i] : 'LTBR';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border

            // Get Height
            // $height = $this->GetMultiCellHeight($w, $data[$i]);

            //Print the text
            $this->MultiCell($w, $height, $data[$i], $border, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        // //Go to the next line
        // $this->Ln($h);
    }

    function RowDynamicHeight($height, $data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = $height * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $border = isset($this->borders[$i]) ? $this->borders[$i] : 'LTBR';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border

            // Get Height
            // $height = $this->GetMultiCellHeight($w, $data[$i]);

            //Print the text
            $this->MultiCell($w, $height, $data[$i], $border, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function RowFlexibleHeight($height, $data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = $height * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $border = isset($this->borders[$i]) ? $this->borders[$i] : 'LTBR';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border

            // Get Height
            $heights = $this->GetMultiCellHeight($w, $data[$i]);
            $strlenvar = strlen($data[$i]);

            if ($strlenvar > 40) {
                //Print the text
                $this->MultiCell($w, $height, $data[$i], $border, $a);
            } else {
                //Print the text
                $this->MultiCell($w, $h, $data[$i], $border, $a);
            }
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function RowFlexibleHeightAssumse($assumLength, $height, $data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = $height * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $border = isset($this->borders[$i]) ? $this->borders[$i] : 'LTBR';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border

            // Get Height
            $heights = $this->GetMultiCellHeight($w, $data[$i]);
            $strlenvar = strlen($data[$i]);

            if ($strlenvar > $assumLength) {
                //Print the text
                $this->MultiCell($w, $height, $data[$i], $border, $a);
            } else {
                //Print the text
                $this->MultiCell($w, $h, $data[$i], $border, $a);
            }
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $border = isset($this->borders[$i]) ? $this->borders[$i] : 'LTBR';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border

            // Get Height
            $height = $this->GetMultiCellHeight($w, $data[$i]);

            //Print the text
            $this->MultiCell($w, 5, $data[$i], $border, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function RowDynamicColor($arrColor, $data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $border = isset($this->borders[$i]) ? $this->borders[$i] : 'LTBR';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border

            // Get Height
            $height = $this->GetMultiCellHeight($w, $data[$i]);

            //Print the text
            $this->SetTextColor($arrColor[$i][0], $arrColor[$i][1], $arrColor[$i][2]);
            $this->MultiCell($w, 5, $data[$i], $border, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        $this->SetTextColor(0,0,0);
        //Go to the next line
        $this->Ln($h);
    }

    function RowDynamicFont($arrFont, $data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $border = isset($this->borders[$i]) ? $this->borders[$i] : 'LTBR';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border

            // Get Height
            $height = $this->GetMultiCellHeight($w, $data[$i]);

            //Print the text
            $this->SetFont($arrFont[$i][0], $arrFont[$i][1], $arrFont[$i][2]);
            $this->MultiCell($w, 5, $data[$i], $border, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        $this->SetTextColor(0,0,0);
        //Go to the next line
        $this->Ln($h);
    }

    function RowHeader($data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row

        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $border = isset($this->borders[$i]) ? $this->borders[$i] : 'LTBR';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border

            //Print the text
            $this->MultiCell($w, 5, $data[$i], $border, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function RowDynamicALL($height,$arrFont,$arrColor, $data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = $height * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $border = isset($this->borders[$i]) ? $this->borders[$i] : 'LTBR';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border

            // Get Height
            // $height = $this->GetMultiCellHeight($w, $data[$i]);

            //Print the text
            $this->SetFont($arrFont[$i][0], $arrFont[$i][1], $arrFont[$i][2]);
            $this->SetTextColor($arrColor[$i][0], $arrColor[$i][1], $arrColor[$i][2]);
            $this->MultiCell($w, $height, $data[$i], $border, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        $this->SetTextColor(0,0,0);
        //Go to the next line
        $this->Ln($h);
    }

    function RowDynamicALLS($height,$arrFont,$arrColor, $data ,$fill = false )
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = $height * $nb ;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            $border = isset($this->borders[$i]) ? $this->borders[$i] : 'LTBR';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            // $this->Rect($x,$y,$w,$h);
            // Get Height
            $heights = $this->GetMultiCellHeight($w, $data[$i]);
            $strlenvar = strlen($data[$i]);
            //Print the text
            $this->SetFont($arrFont[$i][0], $arrFont[$i][1], $arrFont[$i][2]);
            $this->SetTextColor($arrColor[$i][0], $arrColor[$i][1], $arrColor[$i][2]);
           
            if ($strlenvar > 40){
                $this->MultiCell($w, $height, $data[$i] , $border, $a ,$fill);
                // $ht++;
            } else{
                $this->MultiCell($w, $h, $data[$i] , $border, $a ,$fill);
            }
           
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        $this->SetTextColor(0,0,0);
        //Go to the next line
        $this->Ln($h);
    }

    function MultiCellRows($cells, $width, $height, $data , $border, $align)
    {
        $x = $this->GetX();
        $y = $this->GetY();
        $maxheight = 0;

        for ($i = 0; $i < $cells; $i++) {
            $this->MultiCell($width, $height, $data[$i], $border, $align);
            // $this->MultiCell($width, $height, $data[$i]);
            if ($this->GetY() - $y > $maxheight) $maxheight = $this->GetY() - $y;
            $this->SetXY($x + ($width * ($i + 1)), $y);
        }

        for ($i = 0; $i < $cells + 1; $i++) {
            $this->Line($x + $width * $i, $y, $x + $width * $i, $y + $maxheight);
        }

        $this->Line($x, $y, $x + $width * $cells, $y);
        $this->Line($x, $y + $maxheight, $x + $width * $cells, $y + $maxheight);
    }

    function GetMultiCellHeight($w, $txt)
    {
        $height = 0;
        $strlen = strlen($txt);
        $wdth = 0;
        for ($i = 0; $i <= $strlen; $i++) {
            $char = substr($txt, $i, 1);
            $wdth += $this->GetStringWidth($char);
            if ($char == "\n") {
                $height++;
                $wdth = 0;
            }
            if ($wdth >= $w) {
                $height++;
                $wdth = 0;
            }
        }
        return $height;
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    private $padding_top = 1, $padding_bottom = 1, $line_space = 1, $paragraph_space = 1;

    private function topBottom($type, array $args){
        if(!isset($args[0], $args[1], $args[2])) {
            // throw new Exception("Parameter error!");
        }

        $height = $args[1];
        $width = $args[0];

        $y_old = $this->GetY();
        $x_old = $this->GetX();

        if($type == "T"){
            $this->SetXY($x_old, $y_old + $this->padding_top);
            $this->WriteLimit($width, $args[2], isset($args[5])? $args[5] : "L");
        }else{
            $text_height = $this->WriteLimit($width, $args[2], isset($args[5])? $args[5] : "L", true);
            $this->SetXY($x_old, $y_old + $height - $text_height - $this->padding_bottom + 2);
            $this->WriteLimit($width, $args[2], isset($args[5])? $args[5] : "L");
        }
        //var_dump($height);
        $this->SetXY($x_old, $y_old);

        $args[2] = "";//empty the text already written
        call_user_func_array([$this, 'Cell'], $args);
    }

    //the parameters are the same used by Cell
    public function TCell(){
        call_user_func_array([$this, 'topBottom'], ['T', func_get_args()]);
    }

    //the parameters are the same used by Cell
    public function BCell(){
        call_user_func_array([$this, 'topBottom'], ['B', func_get_args()]);
    }

    private function WriteLimit($width, $text, $align, $simulacao = false){
        $texts = explode(chr(13).chr(10), $text);
        $height = 0;

        $x_old = 0;
        $y_old = 0;
        for ($i = 0; $i < count($texts); $i++) { 

            $words = explode(' ', $texts[$i]);
            $line = "";
            $qtd_words = count($words);

            for ($j = 0; $j < $qtd_words; $j++) { 
                $new_word = $words[$j].' ';
                $line .= $new_word;

                if( ($line_width = $this->getStringWidth($line)) > $width ){
                    $y_old = $this->GetY();
                    $x_old = $this->GetX();
                    $write_line = substr($line, 0, (-1)*strlen($new_word));
                    $line = $words[$j].' ';

                    if(!$simulacao)
                        $this->AlignWrite($write_line, $width, $align);

                    $this->SetXY($x_old, $y_old + $this->FontSize + $this->line_space);//reset to the cell point
                    $height += $this->FontSize + $this->line_space;
                }

                if($j == $qtd_words-1){
                    if(!$simulacao)
                        $this->AlignWrite($line, $width, $align);
                }
            }

            $y_old = $this->GetY();
            $this->SetXY($x_old, $y_old + $this->FontSize + $this->paragraph_space);//reset to the cell point
            $height += $this->FontSize + $this->paragraph_space;

        }

        return $height;
    }

    private function AlignWrite($write_line, $width, $align){
        $x_old = $this->GetX();

        if($align == "C" || $align == "R"){
            $width_write_line = $this->getStringWidth($write_line);
            if($align == "C")
                $this->SetX($x_old + ($width - $width_write_line)/2 - 0.75);
            else
                $this->SetX($x_old + $width - $width_write_line - 1.5);
        }

        $this->Write( $this->FontSize, $write_line);
    }

    public function SetLineSpace($line_space){
        $this->line_space = (float)$line_space;
        return $this;
    }

    public function SetParagraphSpace($paragraph_space){
        $this->paragraph_space = (float)$paragraph_space;
        return $this;
    }

    public function SetPaddingTop($padding_top){
        $this->padding_top = (float)$padding_top;
        return $this;
    }

    public function SetPaddingBottom($padding_bottom){
        $this->padding_bottom = $padding_bottom;
        return $this;
    }
}
