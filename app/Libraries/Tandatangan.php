<?php

namespace App\Libraries;

use App\Libraries\exFPDF;
use App\Libraries\easyTables;

class Tandatangan
{
    private $pdf;
    private $data_ttd;

    public function __construct($pdf, $ttd_data)
    {
        $this->pdf = $pdf;
        $this->data_ttd = $ttd_data;
    }

    public function setTtd()
    {
        $ttd = $this->data_ttd;
        if (empty($ttd) || count($ttd) == 0) {
            return;
        }

        $jumlah = count($ttd);

        // Jika tanda tangan ≤ 3 → satu baris
        if ($jumlah <= 3) {
            $this->renderRow($ttd);
        }
        // Jika 4 atau 5 → dua baris (3 di atas, sisanya di bawah)
        else {
            $baris1 = array_slice($ttd, 0, 3);
            $baris2 = array_slice($ttd, 3);

            $this->renderRow($baris1); // baris pertama
            $this->pdf->Ln(0.5);             // jarak antar baris tanda tangan
            $this->renderRow($baris2); // baris kedua
        }
    }

    private function renderRow($rows)
    {
        $jumlah = count($rows);
        $colWidth = 1;
        if ($jumlah == 2) {
            $colWidth = '{50,50}';
        } elseif ($jumlah == 3) {
            $colWidth = 3;
        } elseif ($jumlah == 1) {
            $colWidth = 1;
        } else {
            $colWidth = '{33,33,33}';
        }

        // --- Baris 1: keterangan (misal: Mengetahui / Disetujui oleh) ---
        $tblKet = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9; font-style:R;');
        foreach ($rows as $v) {
            $tblKet->easyCell($v['keterangan'], 'align:C;');
        }
        $tblKet->printRow();
        $tblKet->endTable(0);

        // --- Baris 2: jabatan ---
        $tblJab = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9; font-style:R;');
        foreach ($rows as $v) {
            $tblJab->easyCell($v['jabatan'], 'align:C;');
        }
        $tblJab->printRow();
        $tblJab->endTable(1.5);

        // --- Baris 3: garis tanda tangan ---
        $tblLine = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9; font-style:R;');
        foreach ($rows as $v) {
            $tblLine->easyCell("______________________", 'align:C;');
        }
        $tblLine->printRow();
        $tblLine->endTable(0);

        // --- Baris 4: nama ---
        $tblNama = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9; font-style:R;');
        foreach ($rows as $v) {
            $tblNama->easyCell($v['nama'], 'align:C;');
        }
        $tblNama->printRow();
        $tblNama->endTable(0);

        // --- Baris 5: NUP ---
        $tblNup = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9; font-style:R;');
        foreach ($rows as $v) {
            $tblNup->easyCell($v['nup'], 'align:C;');
        }
        $tblNup->printRow();
        $tblNup->endTable(0.5);
    }
}