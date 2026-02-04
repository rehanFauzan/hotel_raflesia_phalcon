<?php


namespace App\Libraries;

use App\Libraries\formatedString;
use Fpdf\Fpdf;

class exFPDF extends Fpdf
{
    protected $outlines = array();
    protected $outlineRoot;

    protected $B;
    protected $I;
    protected $U;
    protected $HREF;
    protected $ALIGN = '';
    protected $fontlist;
    protected $issetfont;
    protected $issetcolor;
    
    function hex2dec($couleur = "#000000")
    {
        $R = substr($couleur, 1, 2);
        $rouge = hexdec($R);
        $V = substr($couleur, 3, 2);
        $vert = hexdec($V);
        $B = substr($couleur, 5, 2);
        $bleu = hexdec($B);
        $tbl_couleur = array();
        $tbl_couleur['R'] = $rouge;
        $tbl_couleur['V'] = $vert;
        $tbl_couleur['B'] = $bleu;
        return $tbl_couleur;
    }

    //conversion pixel -> millimeter at 72 dpi
    function px2mm($px)
    {
        return $px * 25.4 / 72;
    }

    function txtentities($html)
    {
        $trans = get_html_translation_table(HTML_ENTITIES);
        $trans = array_flip($trans);
        return strtr($html, $trans);
    }


    function WriteHTML($html)
    {
        //HTML parser
        $html = strip_tags($html, "<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
        $html = str_replace("\n", ' ', $html); //remplace retour à la ligne par un espace
        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
        foreach ($a as $i => $e) {
            if ($i % 2 == 0) {
                //Text
                if ($this->HREF)
                    $this->PutLink($this->HREF, $e);
                else
                    $this->Write($this->FontSizePt / $this->k, $this->txtentities($e));
            } else {
                //Tag
                if ($e[0] == '/')
                    $this->CloseTag(strtoupper(substr($e, 1)));
                else {
                    //Extract attributes
                    $a2 = explode(' ', $e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach ($a2 as $v) {
                        if (preg_match('/([^=]*)=["\']?([^"\']*)/', $v, $a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag, $attr);
                }
            }
        }
    }

    function OpenTag($tag, $attr)
    {
        //Opening tag
        switch ($tag) {
            case 'STRONG':
                $this->SetStyle('B', true);
                break;
            case 'EM':
                $this->SetStyle('I', true);
                break;
            case 'B':
            case 'I':
            case 'U':
                $this->SetStyle($tag, true);
                break;
            case 'A':
                $this->HREF = $attr['HREF'];
                break;
            case 'IMG':
                if (isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                    if (!isset($attr['WIDTH']))
                        $attr['WIDTH'] = 0;
                    if (!isset($attr['HEIGHT']))
                        $attr['HEIGHT'] = 0;
                    $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), $this->px2mm($attr['WIDTH']), $this->px2mm($attr['HEIGHT']));
                }
                break;
            case 'TR':
            case 'BLOCKQUOTE':
            case 'BR':
                $this->Ln(2);
                break;
            case 'P':
                $this->Ln(1);
                break;
            case 'FONT':
                if (isset($attr['COLOR']) && $attr['COLOR'] != '') {
                    $coul = $this->hex2dec($attr['COLOR']);
                    $this->SetTextColor($coul['R'], $coul['V'], $coul['B']);
                    $this->issetcolor = true;
                }
                if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                    $this->SetFont(strtolower($attr['FACE']));
                    $this->issetfont = true;
                }
                break;
        }
    }

    function CloseTag($tag)
    {
        //Closing tag
        if ($tag == 'STRONG')
            $tag = 'B';
        if ($tag == 'EM')
            $tag = 'I';
        if ($tag == 'B' || $tag == 'I' || $tag == 'U')
            $this->SetStyle($tag, false);
        if ($tag == 'A')
            $this->HREF = '';
        if ($tag == 'FONT') {
            if ($this->issetcolor == true) {
                $this->SetTextColor(0);
            }
            if ($this->issetfont) {
                $this->SetFont('arial');
                $this->issetfont = false;
            }
        }
    }

    function SetStyle($tag, $enable)
    {
        //Modify style and select corresponding font
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach (array('B', 'I', 'U') as $s) {
            if ($this->$s > 0)
                $style .= $s;
        }
        $this->SetFont($this->FontFamily, $style, $this->FontSizePt);
    }

    function PutLink($URL, $txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0, 0, 255);
        $this->SetStyle('U', true);
        $this->Write(5, $txt, $URL);
        $this->SetStyle('U', false);
        $this->SetTextColor(0);
    }

    function Bookmark($txt, $isUTF8 = false, $level = 0, $y = 0)
    {
        if (!$isUTF8)
            $txt = $this->_UTF8encode($txt);
        if ($y == -1)
            $y = $this->GetY();
        $this->outlines[] = array('t' => $txt, 'l' => $level, 'y' => ($this->h - $y) * $this->k, 'p' => $this->PageNo());
    }

    function _putbookmarks()
    {
        $nb = count($this->outlines);
        if ($nb == 0)
            return;
        $lru = array();
        $level = 0;
        foreach ($this->outlines as $i => $o) {
            if ($o['l'] > 0) {
                $parent = $lru[$o['l'] - 1];
                // Set parent and last pointers
                $this->outlines[$i]['parent'] = $parent;
                $this->outlines[$parent]['last'] = $i;
                if ($o['l'] > $level) {
                    // Level increasing: set first pointer
                    $this->outlines[$parent]['first'] = $i;
                }
            } else
                $this->outlines[$i]['parent'] = $nb;
            if ($o['l'] <= $level && $i > 0) {
                // Set prev and next pointers
                $prev = $lru[$o['l']];
                $this->outlines[$prev]['next'] = $i;
                $this->outlines[$i]['prev'] = $prev;
            }
            $lru[$o['l']] = $i;
            $level = $o['l'];
        }
        // Outline items
        $n = $this->n + 1;
        foreach ($this->outlines as $i => $o) {
            $this->_newobj();
            $this->_put('<</Title ' . $this->_textstring($o['t']));
            $this->_put('/Parent ' . ($n + $o['parent']) . ' 0 R');
            if (isset($o['prev']))
                $this->_put('/Prev ' . ($n + $o['prev']) . ' 0 R');
            if (isset($o['next']))
                $this->_put('/Next ' . ($n + $o['next']) . ' 0 R');
            if (isset($o['first']))
                $this->_put('/First ' . ($n + $o['first']) . ' 0 R');
            if (isset($o['last']))
                $this->_put('/Last ' . ($n + $o['last']) . ' 0 R');
            $this->_put(sprintf('/Dest [%d 0 R /XYZ 0 %.2F null]', $this->PageInfo[$o['p']]['n'], $o['y']));
            $this->_put('/Count 0>>');
            $this->_put('endobj');
        }
        // Outline root
        $this->_newobj();
        $this->outlineRoot = $this->n;
        $this->_put('<</Type /Outlines /First ' . $n . ' 0 R');
        $this->_put('/Last ' . ($n + $lru[0]) . ' 0 R>>');
        $this->_put('endobj');
    }

    function _putresources()
    {
        parent::_putresources();
        $this->_putbookmarks();
    }

    function _putcatalog()
    {
        parent::_putcatalog();
        if (count($this->outlines) > 0) {
            $this->_put('/Outlines ' . $this->outlineRoot . ' 0 R');
            $this->_put('/PageMode /UseOutlines');
        }
    }

    public function PageBreak()
    {
        return $this->PageBreakTrigger;
    }

    public function current_font($c)
    {
        if ($c == 'family') {
            return $this->FontFamily;
        } elseif ($c == 'style') {
            return $this->FontStyle;
        } elseif ($c == 'size') {
            return $this->FontSizePt;
        }
    }

    public function get_color($c)
    {
        if ($c == 'fill') {
            return $this->FillColor;
        } elseif ($c == 'text') {
            return $this->TextColor;
        }
    }

    public function get_page_width()
    {
        return $this->w;
    }

    public function get_margin($c)
    {
        if ($c == 'l') {
            return $this->lMargin;
        } elseif ($c == 'r') {
            return $this->rMargin;
        } elseif ($c == 't') {
            return $this->tMargin;
        }
    }

    public function get_linewidth()
    {
        return $this->LineWidth;
    }

    public function get_orientation()
    {
        return $this->CurOrientation;
    }

    public function get_page_size()
    {
        return $this->CurPageSize;
    }

    public function get_rotation()
    {
        return $this->CurRotation;
    }

    public function get_scale_factor()
    {
        return $this->k;
    }

    static private $hex = array(
        '0' => 0,
        '1' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        'A' => 10,
        'B' => 11,
        'C' => 12,
        'D' => 13,
        'E' => 14,
        'F' => 15
    );

    public function is_rgb($str)
    {
        $a = true;
        $tmp = explode(',', trim($str, ','));
        foreach ($tmp as $color) {
            if (!is_numeric($color) || $color < 0 || $color > 255) {
                $a = false;
                break;
            }
        }
        return $a;
    }

    public function is_hex($str)
    {
        $a = true;
        $str = strtoupper($str);
        $n = strlen($str);
        if (($n == 7 || $n == 4) && $str[0] == '#') {
            for ($i = 1; $i < $n; $i++) {
                if (!isset(self::$hex[$str[$i]])) {
                    $a = false;
                    break;
                }
            }
        } else {
            $a = false;
        }
        return $a;
    }

    public function hextodec($str)
    {
        $result = array();
        $str = strtoupper(substr($str, 1));
        $n = strlen($str);
        for ($i = 0; $i < 3; $i++) {
            if ($n == 6) {
                $result[$i] = self::$hex[$str[2 * $i]] * 16 + self::$hex[$str[2 * $i + 1]];
            } else {
                $result[$i] = self::$hex[$str[$i]] * 16 + self::$hex[$str[$i]];
            }
        }
        return $result;
    }
    static private $options = array('F' => '', 'T' => '', 'D' => '');

    public function resetColor($str, $p = 'F')
    {
        if (isset(self::$options[$p]) && self::$options[$p] != $str) {
            self::$options[$p] = $str;
            $array = array();
            if ($this->is_hex($str)) {
                $array = $this->hextodec($str);
            } elseif ($this->is_rgb($str)) {
                $array = explode(',', trim($str, ','));
                for ($i = 0; $i < 3; $i++) {
                    if (!isset($array[$i])) {
                        $array[$i] = 0;
                    }
                }
            } else {
                $array = array(null, null, null);
                $i = 0;
                $tmp = explode(' ', $str);
                foreach ($tmp as $c) {
                    if (is_numeric($c)) {
                        $array[$i] = $c * 256;
                        $i++;
                    }
                }
            }
            if ($p == 'T') {
                $this->SetTextColor($array[0], $array[1], $array[2]);
            } elseif ($p == 'D') {
                $this->SetDrawColor($array[0], $array[1], $array[2]);
            } elseif ($p == 'F') {
                $this->SetFillColor($array[0], $array[1], $array[2]);
            }
        }
    }
    static private $font_def = '';

    public function resetFont($font_family, $font_style, $font_size)
    {
        if (self::$font_def != $font_family . '-' . $font_style . '-' . $font_size) {
            self::$font_def = $font_family . '-' . $font_style . '-' . $font_size;
            $this->SetFont($font_family, $font_style, $font_size);
        }
    }

    public function resetStaticData()
    {
        self::$font_def = '';
        self::$options = array('F' => '', 'T' => '', 'D' => '');
    }

    /***********************************************************************
     *
     * Based on FPDF method SetFont
     *
     ************************************************************************/

    private function &FontData($family, $style, $size)
    {
        if ($family == '')
            $family = $this->FontFamily;
        else
            $family = strtolower($family);
        $style = strtoupper($style);
        if (strpos($style, 'U') !== false) {
            $this->underline = true;
            $style = str_replace('U', '', $style);
        }
        if ($style == 'IB')
            $style = 'BI';
        $fontkey = $family . $style;
        if (!isset($this->fonts[$fontkey])) {
            if ($family == 'arial')
                $family = 'helvetica';
            if (in_array($family, $this->CoreFonts)) {
                if ($family == 'symbol' || $family == 'zapfdingbats')
                    $style = '';
                $fontkey = $family . $style;
                if (!isset($this->fonts[$fontkey]))
                    $this->AddFont($family, $style);
            } else
                $this->Error('Undefined font: ' . $family . ' ' . $style);
        }
        $result['FontSize'] = $size / $this->k;
        $result['CurrentFont'] = &$this->fonts[$fontkey];
        return $result;
    }


    private function setLines(&$fstring, $p, $q)
    {
        $parced_str = &$fstring->parced_str;
        $lines = &$fstring->lines;
        $linesmap = &$fstring->linesmap;
        $cfty = $fstring->get_current_style($p);
        $ffs = $cfty['font-family'] . $cfty['style'];
        if (!isset($fstring->used_fonts[$ffs])) {
            $fstring->used_fonts[$ffs] = &$this->FontData($cfty['font-family'], $cfty['style'], $cfty['font-size']);
        }
        $cw = &$fstring->used_fonts[$ffs]['CurrentFont']['cw'];
        $wmax = $fstring->width * 1000 * $this->k;
        $j = count($lines) - 1;
        $k = strlen($lines[$j]);
        if (!isset($linesmap[$j][0])) {
            $linesmap[$j] = array($p, $p, 0);
        }
        $sl = $cw[' '] * $cfty['font-size'];
        $x = $a = $linesmap[$j][2];
        if ($k > 0) {
            $x += $sl;
            $lines[$j] .= ' ';
            $linesmap[$j][2] += $sl;
        }
        $u = $p;
        $t = '';
        $l = $p + $q;
        $ftmp = '';
        for ($i = $p; $i < $l; $i++) {
            if ($ftmp != $ffs) {
                $cfty = $fstring->get_current_style($i);
                $ffs = $cfty['font-family'] . $cfty['style'];
                if (!isset($fstring->used_fonts[$ffs])) {
                    $fstring->used_fonts[$ffs] = &$this->FontData($cfty['font-family'], $cfty['style'], $cfty['font-size']);
                }
                $cw = &$fstring->used_fonts[$ffs]['CurrentFont']['cw'];
                $ftmp = $ffs;
            }
            $x += $cw[$parced_str[$i]] * $cfty['font-size'];
            if ($x > $wmax) {
                if ($a > 0) {
                    $t = substr($parced_str, $p, $i - $p);
                    $lines[$j] = substr($lines[$j], 0, $k);
                    $linesmap[$j][1] = $p - 1;
                    $linesmap[$j][2] = $a;
                    $x -= ($a + $sl);
                    $a = 0;
                    $u = $p;
                } else {
                    $x = $cw[$parced_str[$i]] * $cfty['font-size'];
                    $t = '';
                    $u = $i;
                }
                $j++;
                $lines[$j] = $t;
                $linesmap[$j] = array();
                $linesmap[$j][0] = $u;
                $linesmap[$j][2] = 0;
            }
            $lines[$j] .= $parced_str[$i];
            $linesmap[$j][1] = $i;
            $linesmap[$j][2] = $x;
        }
        return;
    }

    public function &extMultiCell($font_family, $font_style, $font_size, $font_color, $w, $txt)
    {
        $result = array();
        if ($w == 0) {
            return $result;
        }
        $current_font = array('font-family' => $font_family, 'style' => $font_style, 'font-size' => $font_size, 'font-color' => $font_color);
        $fstring = new formatedStrings($txt, $w, $current_font);
        $word = '';
        $p = 0;
        $i = 0;
        $n = strlen($fstring->parced_str);
        while ($i < $n) {
            $word .= $fstring->parced_str[$i];
            if ($fstring->parced_str[$i] == "\n" || $fstring->parced_str[$i] == ' ' || $i == $n - 1) {
                $word = trim($word);
                $this->setLines($fstring, $p, strlen($word));
                $p = $i + 1;
                $word = '';
                if ($fstring->parced_str[$i] == "\n" && $i < $n - 1) {
                    $z = 0;
                    $j = count($fstring->lines);
                    $fstring->lines[$j] = '';
                    $fstring->linesmap[$j] = array();
                }
            }
            $i++;
        }
        if ($n == 0) {
            return $result;
        }
        $n = count($fstring->lines);
        for ($i = 0; $i < $n; $i++) {
            $result[$i] = $fstring->break_by_style($i);
        }
        return $result;
    }

    private function GetMixStringWidth($line)
    {
        $w = 0;
        foreach ($line['chunks'] as $i => $chunk) {
            $t = 0;
            $cf = &$this->FontData($line['style'][$i]['font-family'], $line['style'][$i]['style'], $line['style'][$i]['font-size']);
            $cw = &$cf['CurrentFont']['cw'];
            $s = implode('', explode(' ', $chunk));
            $l = strlen($s);
            for ($j = 0; $j < $l; $j++) {
                $t += $cw[$s[$j]];
            }
            $w += $t * $line['style'][$i]['font-size'];
        }
        return $w;
    }

    public function CellBlock($w, $lh, &$lines, $align = 'J')
    {
        if ($w == 0) {
            return;
        }
        $ctmp = '';
        $ftmp = '';
        foreach ($lines as $i => $line) {
            $k = $this->k;
            if ($this->y + $lh * $line['height'] > $this->PageBreakTrigger) {
                break;
            }
            $dx = 0;
            $dw = 0;
            if ($line['width'] != 0) {
                if ($align == 'R') {
                    $dx = $w - $line['width'] / ($this->k * 1000);
                } elseif ($align == 'C') {
                    $dx = ($w - $line['width'] / ($this->k * 1000)) / 2;
                }
                if ($align == 'J') {
                    $tmp = explode(' ', implode('', $line['chunks']));
                    $ns = count($tmp);
                    if ($ns > 1) {
                        $sx = implode('', $tmp);
                        $delta = $this->GetMixStringWidth($line) / ($this->k * 1000);
                        $dw = ($w - $delta) * (1 / ($ns - 1));
                    }
                }
            }
            $xx = $this->x + $dx;
            foreach ($line['chunks'] as $tj => $txt) {
                $this->resetFont($line['style'][$tj]['font-family'], $line['style'][$tj]['style'], $line['style'][$tj]['font-size']);
                $this->resetColor($line['style'][$tj]['font-color'], 'T');
                $y = $this->y + 0.5 * $lh * $line['height'] + 0.3 * $line['height'] / $this->k;
                if ($dw) {
                    $tmp = explode(' ', $txt);
                    foreach ($tmp as $e => $tt) {
                        if ($e > 0) {
                            $xx += $dw;
                            if ($tt == '') {
                                continue;
                            }
                        }
                        $this->Text($xx, $y, $tt);
                        if ($line['style'][$tj]['href']) {
                            $yr = $this->y + 0.5 * ($lh * $line['height'] - $line['height'] / $this->k);
                            $this->Link($xx, $yr, $this->GetStringWidth($txt), $line['height'] / $this->k, $line['style'][$tj]['href']);
                        }
                        $xx += $this->GetStringWidth($tt);
                    }
                } else {
                    $this->Text($xx, $y, $txt);
                    if ($line['style'][$tj]['href']) {
                        $yr = $this->y + 0.5 * ($lh * $line['height'] - $line['height'] / $this->k);
                        $this->Link($xx, $yr, $this->GetStringWidth($txt), $line['height'] / $this->k, $line['style'][$tj]['href']);
                    }
                    $xx += $this->GetStringWidth($txt);
                }
            }
            unset($lines[$i]);
            $this->y += $lh * $line['height'];
        }
    }
}
