<?php

namespace App\Traits;


use App\Libraries\easyTables;
use Nggit\PHPTerbilang\Terbilang;
use App\Libraries\Parse;
use App\Libraries\TandatanganMulti;

trait VoucherBanyakJurnalTemplate
{
    public function renderVoucher(array $listVoucher = [], $title = '', array $listTandatangan = [], $namaJudul = 'JURNAL')
    {
        $this->SetMargins(0.5, 0.5, 1);
        $this->AliasNbPages();
        $this->headerVisible = "true";

        $this->SetTitle('Voucher Banyak');

        foreach ($listVoucher as $voucher) {
            $Mjournal = $voucher['Mjournal'];
            $Djournal = $voucher['Djournal'];

            // ambil tanda tangan khusus voucher ini
            $idVoucher = $Mjournal['mjo_id'] ?? null;
            $listTtdVoucher = isset($listTandatangan[$idVoucher]) ? $listTandatangan[$idVoucher] : [];

            $this->AddPage();

            $periode = '-';
            if (!empty($Mjournal['mjo_date'])) {
                $periode = strftime("%B %Y", strtotime($Mjournal['mjo_date']));
                $bulan = [
                    1 => 'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                    'Juli',
                    'Agustus',
                    'September',
                    'Oktober',
                    'November',
                    'Desember'
                ];
                $monthNum = date("n", strtotime($Mjournal['mjo_date']));
                $year     = date("Y", strtotime($Mjournal['mjo_date']));
                $periode  = $bulan[$monthNum] . ' ' . $year;
            }

            // Judul
            $this->SetFont('helvetica', 'UB', 15);
            $this->Cell(0, 1, $namaJudul, 0, 0, 'C');
            $this->Ln();
            $this->SetFont('helvetica', '', 11);
            $this->Cell(0, 0.5, $title, 0, 0, 'C');
            $this->Ln();
            $this->Cell(0, 0.5, "Nomor : " . (!empty($Mjournal['mjo_ceque']) ? $Mjournal['mjo_ceque'] : ($Mjournal['mjo_code'] ?? '')) . (!empty($Mjournal['mjo_paymentdate']) ? ", Tanggal : " . format_date_ind($Mjournal['mjo_paymentdate']) : ", Periode : " . $periode), 0, 0, 'C');
            $this->Ln(1);

            // Header tabel
            $table = new easyTables($this, '{8,3,6,4,4}', 'width:100%; border:1; padding:2; font-size:8');

            // Header baris pertama
            $table->easyCell('NAMA PERKIRAAN', 'rowspan:2; align:C; font-style:B; font-size:9');
            $table->easyCell('KODE PERK.', 'rowspan:2; align:C; font-style:B; font-size:9');
            $table->easyCell('UNIT', 'rowspan:2; align:C; font-style:B; font-size:9');
            $table->easyCell('JUMLAH (Rupiah)', 'colspan:2; align:C; font-style:B; font-size:9');
            $table->printRow();

            // Header baris kedua
            $table->easyCell('DEBET', 'align:C; font-style:B; font-size:9');
            $table->easyCell('KREDIT', 'align:C; font-style:B; font-size:9');
            $table->printRow();

            // Data
            $total_debet = $total_kredit = 0;

            foreach ($Djournal as $datas) {
                $isdebet  = (float) $datas['djo_debit'];
                $iskredit = (float) $datas['djo_credit'];

                // Untuk indentasi jika debet = 0, gunakan spasi di awal nama akun
                $acc_name = ($isdebet == "0") ? '  ' . $datas['acc_name'] : $datas['acc_name'];
                $table->easyCell($acc_name, 'align:L');
                $table->easyCell($datas['acc_code'], 'align:C');
                $table->easyCell('(' . $datas['kode_satker'] . ') ' . $datas['nama_satker'], 'align:C');
                $table->easyCell(number_format($isdebet, 2, ',', '.'), 'align:R');
                $table->easyCell(number_format($iskredit, 2, ',', '.'), 'align:R');
                $table->printRow();

                $total_debet  += $isdebet;
                $total_kredit += $iskredit;
            }

            // Total
            $table->easyCell('JUMLAH', 'align:C; font-style:B; colspan:3');
            $table->easyCell(number_format($total_debet, 2, ',', '.'), 'align:R; font-style:B');
            $table->easyCell(number_format($total_kredit, 2, ',', '.'), 'align:R; font-style:B');
            $table->printRow();

            $table->endTable(0);
            $this->SetFont('helvetica', '', 8);
            // $terbilang = $total_debet > 0 ? ucwords(terbilang($total_debet)) . ' RUPIAH' : '-';

            $t = new Terbilang();
            if (fmod($total_debet, 1) !== 0.00) {
                $t->parse(Parse::rupiah($total_debet));
            } else {
                $t->parse($total_debet);
            }
            $this->MultiCell(19.5, 0.6, '# TERBILANG : ' . ucfirst($t->getResult()) . " rupiah #", 1, 'L');

            // $this->MultiCell(19.5, 0.6, 'TERBILANG : ' . $terbilang, 1, 'L');
            $this->MultiCell(19.5, 0.6, 'KETERANGAN : ' . ($Mjournal['mjo_ket'] ?? '-'), 1, 'L');
            $this->MultiCell(19.5, 0.6, 'NOMOR VOUCHER : ' . ($Mjournal['mjo_code'] ?? '-'), 1, 'L');
            $this->Ln();

            if (!empty($listTtdVoucher)) {
                // Set posisi tanda tangan agak ke bawah
                // $currentY = $pdf->GetY();
                // $pageHeight = $pdf->GetPageHeight();
                // if ($currentY < ($pageHeight - 7)) {
                //     $pdf->SetY($pageHeight - 7);
                // }

                // buat data multi page per voucher
                $multiTtd = [
                    [
                        'new_page' => false,
                        'data' => $listTtdVoucher
                    ]
                ];
                $ttd = new TandatanganMulti($this, $multiTtd);
                $ttd->renderSemuaTtd();
            }

            // TTD
            // $tgl = date("Y-m-d");
            // $this->Ln();
            // $this->SetFont('helvetica', '', 9);
            // $this->SetTextColor(0, 0, 0);
            // $this->Cell(7, 0.5, 'Disetujui Oleh :', 0, 0, 'C');
            // $this->Cell(6, 0.5, 'Diperiksa Oleh :', 0, 0, 'C');
            // $this->Cell(6, 0.5, 'Dibuat Oleh :', 0, 1, 'C');

            // $this->Cell(7, 0.5, 'Manajer Keuangan', 0, 0, 'C');
            // $this->Cell(6, 0.5, 'Asman Akuntansi & Perpajakan', 0, 0, 'C');
            // $this->Cell(6, 0.5, 'Staf. Keuangan', 0, 1, 'C');

            // $this->Ln(2.5);
            // $this->SetFont('helvetica', 'U', 10);
            // $this->Cell(7, 0.5, '______________________', 0, 0, 'C');
            // $this->Cell(6, 0.5, '______________________', 0, 0, 'C');
            // $this->Cell(6, 0.5, '______________________', 0, 1, 'C');

        }
    }
}
