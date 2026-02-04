<?php

namespace App\Libraries;

use App\Libraries\exFPDF;
use App\Libraries\easyTables;

class TandatanganMulti
{
    private $pdf;
    private $listTtd;

    public function __construct($pdf, $listTtd)
    {
        $this->pdf = $pdf;
        $this->listTtd = $listTtd;
    }

    public function renderSemuaTtd()
    {
        if (empty($this->listTtd) || count($this->listTtd) == 0) {
            return;
        }

        foreach ($this->listTtd as $index => $ttdGroup) {
            // Setiap blok tanda tangan (bisa untuk halaman berbeda)
            $this->renderTtdGroup($ttdGroup);

            // Tambah jarak antar blok tanda tangan
            if ($index < count($this->listTtd) - 1) {
                $this->pdf->Ln(1.0);
            }
        }
    }

    private function renderTtdGroup($ttd)
    {
        if (empty($ttd) || count($ttd) == 0) {
            return;
        }

        $jumlah = count($ttd);

        // ≤3 → 1 baris
        if ($jumlah <= 3) {
            $this->renderRow($ttd);
        }
        // 4–5 → 2 baris
        else {
            $baris1 = array_slice($ttd, 0, 3);
            $baris2 = array_slice($ttd, 3);
            $this->renderRow($baris1);
            $this->pdf->Ln(0.5);
            $this->renderRow($baris2);
        }
    }

    private function renderRow($rows)
    {
        $jumlah = count($rows);
        $colWidth = $this->getColWidth($jumlah);

        // --- Keterangan ---
        $tblKet = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9; font-style:R;');
        foreach ($rows as $v) {
            $tblKet->easyCell($v['keterangan'] ?? '', 'align:C;');
        }
        $tblKet->printRow();
        $tblKet->endTable(0);

        // --- Jabatan ---
        $tblJab = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9; font-style:R;');
        foreach ($rows as $v) {
            $tblJab->easyCell($v['jabatan'] ?? '', 'align:C;');
        }
        $tblJab->printRow();
        $tblJab->endTable(1.5);

        // --- Garis tanda tangan ---
        $tblLine = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9; font-style:R;');
        foreach ($rows as $v) {
            $tblLine->easyCell("______________________", 'align:C;');
        }
        $tblLine->printRow();
        $tblLine->endTable(0);

        // --- Nama ---
        $tblNama = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9; font-style:R;');
        foreach ($rows as $v) {
            $tblNama->easyCell($v['nama'] ?? '', 'align:C;');
        }
        $tblNama->printRow();
        $tblNama->endTable(0);

        // --- NUP ---
        $tblNup = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9; font-style:R;');
        foreach ($rows as $v) {
            $tblNup->easyCell($v['nup'] ?? '', 'align:C;');
        }
        $tblNup->printRow();
        $tblNup->endTable(0.5);
    }

    private function getColWidth($jumlah)
    {
        switch ($jumlah) {
            case 1: return 1;
            case 2: return '{50,50}';
            case 3: return '{33,33,33}';
            default: return '{33,33,33}'; // untuk baris atas dari 4/5
        }
    }
}
