<?php

namespace App\Libraries;

class Parse
{
    public static function array_group_by(array $array, $key)
	{
		if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key)) {
			trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
			return null;
		}

		$func = (!is_string($key) && is_callable($key) ? $key : null);
		$_key = $key;

		// Load the new array, splitting by the target key
		$grouped = [];
		foreach ($array as $value) {
			$key = null;

			if (is_callable($func)) {
				$key = call_user_func($func, $value);
			} elseif (is_object($value) && property_exists($value, $_key)) {
				$key = $value->{$_key};
			} elseif (isset($value[$_key])) {
				$key = $value[$_key];
			}

			if ($key === null) {
				continue;
			}

			$grouped[$key][] = $value;
		}

		// Recursively build a nested grouping if more parameters are supplied
		// Each grouped array value is grouped according to the next sequential key
		if (func_num_args() > 2) {
			$args = func_get_args();

			foreach ($grouped as $key => $value) {
				$params = array_merge([$value], array_slice($args, 2, func_num_args()));
				$grouped[$key] = call_user_func_array('array_group_by', $params);
			}
		}

		return $grouped;
	}
    
	public static function array_group_by2(array $array, $key)
	{
		if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key)) {
			trigger_error('array_group_by_self(): The key should be a string, an integer, a float, or a function', E_USER_ERROR);
		}

		$isFunction = !is_string($key) && is_callable($key);

		// Load the new array, splitting by the target key
		$grouped = [];
		foreach ($array as $value) {
			$groupKey = null;

			if ($isFunction) {
				$groupKey = $key($value);
			} else if (is_object($value)) {
				$groupKey = $value->{$key};
			} else {
				$groupKey = $value[$key];
			}

			$grouped[$groupKey][] = $value;
		}

		// Recursively build a nested grouping if more parameters are supplied
		// Each grouped array value is grouped according to the next sequential key
		if (func_num_args() > 2) {
			$args = func_get_args();

			foreach ($grouped as $groupKey => $value) {
				$params = array_merge([$value], array_slice($args, 2, func_num_args()));
				$grouped[$groupKey] = call_user_func_array([self::class, 'array_group_by'], $params);
			}
		}

		return $grouped;
	}

    public static function getPrevPeriode($periode)
    {
        $blnkemarin = $periode - 1;
        $bulanK = substr($blnkemarin, 4, 6);
        $tahunK = substr($blnkemarin, 0, 4);
        $bulanInt = (int)$bulanK;
        $tahunInt = (int)$tahunK;
        if ($bulanInt == 0) {
            $bulanK = "12";
            $tahunInt = $tahunInt - 1;
            $tahunK = $tahunInt + "";
        }
        $blnkemarin = $tahunK . $bulanK;
        return $blnkemarin;
    }


    public static function rupiah($angka)
    {
        $jadi = number_format($angka, 2, ',', '.');
        return $jadi;
    }

    public static function minusRupiah($angka)
    {
        if ($angka < 0) {
            $nilai = str_replace("-", "", $angka);
            $jadi = number_format($nilai, 2, ',', '.');
            $result = "(" . $jadi . ")";
            return $result;
        } else {
            $result = number_format($angka, 2, ',', '.');
            return $result;
        }
    }

    public static function deformatRupiah($string)
    {
        if ($string == "") {
            return $string;
        } else {

            $string1 = implode('', explode('Rp', $string));
            $string2 = implode('', explode('.', $string1));
            $string3 = implode('.', explode(',', $string2));
            return $string3;
        }
    }

    public static function deFormatRupiahRam($valString)
    {
        if ($valString == "") {
            return $valString;
        } else {
            $string = $valString;

            // Hapus kata "Rp"
            $string = str_replace("Rp", "", $string);

            // Hapus tanda titik
            $string = str_replace(".", "", $string);

            // Ganti koma dengan titik
            $string = str_replace(",", ".", $string);

            return $string;
        }
    }

    function generateRandomstring($length)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz()';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function numToBulan($bulan)
    {
        if ($bulan == 1) $bulan = "Januari";
        else if ($bulan == 2) $bulan = "Februari";
        else if ($bulan == 3) $bulan = "Maret";
        else if ($bulan == 4) $bulan = "April";
        else if ($bulan == 5) $bulan = "Mei";
        else if ($bulan == 6) $bulan = "Juni";
        else if ($bulan == 7) $bulan = "Juli";
        else if ($bulan == 8) $bulan = "Agustus";
        else if ($bulan == 9) $bulan = "September";
        else if ($bulan == 10) $bulan = "Oktober";
        else if ($bulan == 11) $bulan = "November";
        else if ($bulan == 12) $bulan = "Desember";
        return $bulan;
    }
    public static function dateUSA($tanggal)
    {
        $date = date_create($tanggal);
        return date_format($date, 'Y-m-d');
    }

    public static function dateINA($tanggal)
    {
        $date = date_create($tanggal);
        return date_format($date, 'd-m-Y');
    }

    public static function DateIndo($tanggal)
    {
        $date = date_create($tanggal);
        return date_format($date, 'd-m-Y');
        // return date_format($date, 'd-'.numToBulan('m').'-Y');
    }
    public static function DateExcel($tanggal)
    {
        $date = date_create($tanggal);
        return date_format($date, 'd/m/Y');
        // return date_format($date, 'd-'.numToBulan('m').'-Y');
    }
    public static function batasKar($nilai, $batas)
    {
        $string = substr($nilai, 0, $batas);
        return $string;
    }
    public static function right($string, $chars)
    {
        $vright = substr($string, strlen($string) - $chars, $chars);
        return $vright;
    }

    public static function deFormatAkun($string)
    {
        $string = str_replace(' ', '', $string);
        if ($string == "") {
            return $string;
        } else {
            $string1 = explode(".", $string);
            $string1 = join("", $string1);
            // var string2 = string1.split('.').join('');
            // var string3 = string2.split(',').join('.');
            return $string1;
        }
    }

    public static function TglShort($date)
    {
        if ($date) {

            $BulanIndo = array("Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des");

            $tahun = substr($date, 0, 4);
            $bulan = substr($date, 5, 2);
            $tgl   = substr($date, 8, 2);

            $result = $tgl . " " . $BulanIndo[(int)$bulan - 1];
            return ($result);
        } else {
            return "";
        }
    }
    public static function format_date_ind($tgl, $param = 'long')
    {
        if (trim($tgl) != '' and $tgl != '0000-00-00') {
            $d = substr($tgl, 8, 2);
            $m = substr($tgl, 5, 2);
            $y = substr($tgl, 0, 4);
            $getbulan = array();
            $getbulan[1] = (($param == 'short') ? 'Jan' : 'Januari');
            $getbulan[2] = (($param == 'short') ? 'Feb' : 'Februari');
            $getbulan[3] = (($param == 'short') ? 'Mart' : 'Maret');
            $getbulan[4] = (($param == 'short') ? 'Apr' : 'April');
            $getbulan[5] = (($param == 'short') ? 'Mei' : 'Mei');
            $getbulan[6] = (($param == 'short') ? 'Jun' : 'Juni');
            $getbulan[7] = (($param == 'short') ? 'Jul' : 'Juli');
            $getbulan[8] = (($param == 'short') ? 'Agst' : 'Agustus');
            $getbulan[9] = (($param == 'short') ? 'Sept' : 'September');
            $getbulan[10] = (($param == 'short') ? 'Okt' : 'Oktober');
            $getbulan[11] = (($param == 'short') ? 'Nov' : 'November');
            $getbulan[12] = (($param == 'short') ? 'Des' : 'Desember');
            $tanggal = $d . " " . $getbulan[(int)$m] . " " . $y;
            return $tanggal;
        }
    }
    function terbilang($satuan)
    {
        $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        if ($satuan < 12)
            return " " . $huruf[$satuan];
        elseif ($satuan < 20)
            return Parse::terbilang($satuan - 10) . " Belas ";
        elseif ($satuan < 100)
            return Parse::terbilang($satuan / 10) . " Puluh " . Parse::terbilang($satuan % 10);
        elseif ($satuan < 200)
            return "Seratus" . Parse::terbilang($satuan - 100);
        elseif ($satuan < 1000)
            return Parse::terbilang($satuan / 100) . " Ratus " . Parse::terbilang($satuan % 100);
        elseif ($satuan < 2000)
            return "Seribu" . Parse::terbilang($satuan - 1000);
        elseif ($satuan < 1000000)
            return Parse::terbilang($satuan / 1000) . " Ribu " . Parse::terbilang($satuan % 1000);
        elseif ($satuan < 1000000000)
            return Parse::terbilang($satuan / 1000000) . " Juta " . Parse::terbilang($satuan % 1000000);
        elseif ($satuan < 1000000000000)
            return Parse::terbilang($satuan / 1000000000) . " Miliar " . Parse::terbilang($satuan % 1000000000);
        //elseif ($satuan >= 1000000000)   
    }

    function indonesianDate($date, $type = 1)
    {
        $array_month      = array("01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember");
        $array_month_lite = array("01" => "Jan", "02" => "Feb", "03" => "Mar", "04" => "Apr", "05" => "Mei", "06" => "Jun", "07" => "Jul", "08" => "Ags", "09" => "Sep", "10" => "Okt", "11" => "Nov", "12" => "Des");
        $array_day        = array("1" => "Senin", "2" => "Selasa", "3" => "Rabu", "4" => "Kamis", "5" => "Jumat", "6" => "Sabtu", "7" => "Minggu");
        $array_day_lite   = array("1" => "Sen", "2" => "Sel", "3" => "Rab", "4" => "Kam", "5" => "Jum", "6" => "Sab", "7" => "Min");
        $date = date_create($date);
        switch ($type) {
            case 1:
                return date_format($date, "d") . " " . $array_month[date_format($date, "m")] . " " . date_format($date, "Y");
                break;
            case 2:
                return date_format($date, "d") . " " . $array_month[date_format($date, "m")] . " " . date_format($date, "Y");
                break;
            case 3:
                return $array_day[date_format($date, "N")] . ", " . date_format($date, "d") . " " . $array_month_lite[date_format($date, "m")] . " " . date_format($date, "y");
                break;
            case 4:
                return $array_day[date_format($date, "N")] . ", " . date_format($date, "d") . " " . $array_month[date_format($date, "m")] . " " . date_format($date, "Y");
                break;
            case 5:
                return $array_day_lite[date_format($date, "N")] . ", " . date_format($date, "d/m/Y");
                break;
            case 6:
                return date_format($date, "d-m-Y");
                break;
            case 7:
                return date_format($date, "d") . " " . $array_month_lite[date_format($date, "m")] . " " . date_format($date, "y") . ", " . date_format($date, "H:i");
                break;
            case 8:
                return date_format($date, "d") . " " . $array_month[date_format($date, "m")] . " " . date_format($date, "y") . ", " . date_format($date, "H:i");
                break;
            case 9:
                return date_format($date, "d") . "-" . date_format($date, "m") . "-" . date_format($date, "y") . ", " . date_format($date, "H") . "." . date_format($date, "i");
                break;
            default:
                return "-";
                break;
        };
    }

    public static function numToDay($hari)
    {

        $array_day    = array("1" => "Senin", "2" => "Selasa", "3" => "Rabu", "4" => "Kamis", "5" => "Jumat", "6" => "Sabtu", "7" => "Minggu");
        $result = $array_day[$hari];

        return $result;
    }

    public static function penyebut($angka)
    {
        $angka = abs($angka); //nilai absolut angka
        $bilangan = array(
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas'
        );
        $result = "";
        if ($angka < 12) {
            $result = $bilangan[$angka];
        } elseif ($angka < 20) //belas
        {
            $result = self::penyebut($angka - 10) . " belas";
        } elseif ($angka < 100) //puluh
        {
            $result = self::penyebut(floor($angka / 10)) . " puluh " . self::penyebut($angka % 10);
        } else if ($angka < 200) {
            $result = " seratus " . self::penyebut($angka - 100);
        } else if ($angka < 1000) {
            $result = self::penyebut($angka / 100) . " ratus" . self::penyebut($angka % 100);
        } else if ($angka < 2000) {
            $result = " seribu" . self::penyebut($angka - 1000);
        } else if ($angka < 1000000) {
            $result = self::penyebut($angka / 1000) . " ribu" . self::penyebut($angka % 1000);
        } else if ($angka < 1000000000) {
            $result = self::penyebut($angka / 1000000) . " juta" . self::penyebut($angka % 1000000);
        } else if ($angka < 1000000000000) {
            $result = self::penyebut($angka / 1000000000) . " milyar" . self::penyebut(fmod($angka, 1000000000));
        } else if ($angka < 1000000000000000) {
            $result = self::penyebut($angka / 1000000000000) . " trilyun" . self::penyebut(fmod($angka, 1000000000000));
        }
        return $result;
    }
}
