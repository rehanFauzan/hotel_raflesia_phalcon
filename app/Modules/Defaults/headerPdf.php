<?php

namespace App\Modules\Defaults;

use App\Libraries\exFPDF;

class HeaderPdf extends exFPDF
{
    protected $B;
    protected $I;
    protected $U;
    protected $HREF;
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


    function Header()
    {
        // Logo - gunakan logo yang ada
        $logoPath = 'external_img/logo-pdam-13.png';
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 10, 5, 22);
        }

        // Text sebelah logo
        $this->SetY(5);
        $this->SetX(35);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, 'Hotel Raflesia Bandung', 0, 1, 'L');

        $this->SetX(35);
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Jl. Raya Bandung No.123, Bandung', 0, 1, 'L');

        $this->SetX(35);
        $this->Cell(0, 6, 'Kota Bandung, Jawa Barat 40254', 0, 1, 'L');

        $this->SetX(35);
        $this->Cell(0, 6, 'Telp: +62 22-1234-5678', 0, 1, 'L');
        $this->Ln(2);

        // Garis horizontal double line
        $pageWidth = $this->GetPageWidth();
        $margin = 10;
        $lineStart = $margin;
        $lineEnd = $pageWidth - $margin;

        $this->SetLineWidth(0.1);
        $this->Line($lineStart, $this->GetY(), $lineEnd, $this->GetY());

        $this->SetLineWidth(0.5);
        $this->Line($lineStart, $this->GetY() + 1, $lineEnd, $this->GetY() + 1);
        $this->Ln(5);
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
                    $this->Write(5, $this->txtentities($e));
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
                $this->Ln(5);
                break;
            case 'P':
                $this->Ln(10);
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
        $this->SetFont('', $style);
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

    // Optional: Tambahkan footer jika dibutuhkan
    // function Footer() {
    //     $this->SetY(-15);
    //     $this->SetFont('Arial', 'I', 8);
    //     $this->Cell(0, 10, 'Halaman ' . $this->PageNo(), 0, 0, 'C');
    // }
}
