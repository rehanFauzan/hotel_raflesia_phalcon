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

    /**
     * Render semua grup tanda tangan sesuai halaman
     */
    public function renderSemuaTtd()
    {
        if (empty($this->listTtd)) {
            return;
        }

        foreach ($this->listTtd as $index => $ttdGroup) {
            // Jika diset new_page true, pindah ke halaman baru
            if (!empty($ttdGroup['new_page'])) {
                $this->pdf->AddPage();
            }

            // Render tanda tangan per grup
            $this->renderTtdGroup($ttdGroup['data'] ?? []);

            // Jarak antar grup tanda tangan (jika tidak new_page)
            if (empty($ttdGroup['new_page']) && $index < count($this->listTtd) - 1) {
                $this->pdf->Ln(0);
            }
        }
    }

    /**
     * Render satu grup tanda tangan (1 voucher)
     */
    private function renderTtdGroup($ttd)
    {
        if (empty($ttd)) {
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
            $this->pdf->Ln(0.7);
            $this->renderRow($baris2);
        }
    }

    /**
     * Render 1 baris tanda tangan (multi kolom)
     */
    private function renderRow($rows)
    {
        $jumlah = count($rows);
        $colWidth = $this->getColWidth($jumlah);

        // --- Keterangan ---
        $tblKet = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9;font-style:R;');
        foreach ($rows as $v) {
            $tblKet->easyCell($v['keterangan'] ?? '', 'align:C;');
        }
        $tblKet->printRow();
        $tblKet->endTable(0);

        // --- Jabatan ---
        $tblJab = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9;font-style:R;');
        foreach ($rows as $v) {
            $tblJab->easyCell($v['jabatan'] ?? '', 'align:C;');
        }
        $tblJab->printRow();
        $tblJab->endTable(1.5);

        // --- Garis tanda tangan ---
        $tblLine = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9;font-style:R;');
        foreach ($rows as $v) {
            $tblLine->easyCell("______________________", 'align:C;');
        }
        $tblLine->printRow();
        $tblLine->endTable(0);

        // --- Nama ---
        $tblNama = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9;font-style:R;');
        foreach ($rows as $v) {
            $tblNama->easyCell($v['nama'] ?? '', 'align:C;');
        }
        $tblNama->printRow();
        $tblNama->endTable(0);

        // --- NUP ---
        $tblNup = new easyTables($this->pdf, $colWidth, 'border:0;font-size:9;font-style:R;');
        foreach ($rows as $v) {
            $tblNup->easyCell($v['nup'] ?? '', 'align:C;');
        }
        $tblNup->printRow();
        $tblNup->endTable(0.5);
    }

    /**
     * Lebar kolom dinamis berdasarkan jumlah tanda tangan
     */
    private function getColWidth($jumlah)
    {
        switch ($jumlah) {
            case 1: return 1;
            case 2: return '{50,50}';
            case 3: return '{33,33,33}';
            case 4: return '{25,25,25,25}';
            case 5: return '{20,20,20,20,20}';
            default: return '{33,33,33}';
        }
    }
}