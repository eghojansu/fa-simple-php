<?php

/**
 * Helper
 */
class Helper
{
    public static $days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    public static $months = [1=>'Januari','Februari','Maret','April','Mei',
        'Juni','Juli','Agustus','September','Oktober','November','Desember'
    ];
    public static $roman = [1=>'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];

    /**
     * Number format wrapper
     * @param  number $no
     * @return string
     */
    public static function number($no)
    {
        return number_format($no, 2, ',', '.');
    }

    /**
     * Normalize number to save in db
     * @param  number $no
     * @return string
     */
    public static function normalizeNumber($no)
    {
        return false === strpos($no, ',') ? $no :
            str_replace(['.',','], ['','.'], $no);
    }

    /**
     * Get month name
     * @param  int $no
     * @return string
     */
    public static function monthName($no)
    {
        $no *= 1;

        return isset(self::$months[$no])?self::$months[$no]:null;
    }

    /**
     * Get day name
     * @param  int $no
     * @return string
     */
    public static function dayName($no)
    {
        $no *= 1;

        return isset(self::$days[$no])?self::$days[$no]:null;
    }

    /**
     * Fix slashes
     * @param  string  $str
     * @param  boolean $append
     */
    public static function fixSlashes($str, $append = true)
    {
        return rtrim(strtr($str, '\\', '/'), '/').($append?'/':'');
    }

    /**
     * Dump variable
     * @param  mixed  $data
     * @param  boolean $halt
     */
    public static function dump($data, $halt = false)
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';

        if ($halt) {
            die;
        }
    }

    /**
     * Read date in indonesian
     * @param  string $tanggal mysql format string
     * @return string
     */
    public static function tanggal($tanggal)
    {
        $date = new DateTime($tanggal);

        return $date->format('d').' '.self::$months[$date->format('n')].' '.$date->format('Y');
    }

    /**
     * Read date and day in indonesian
     * @param  string $tanggal mysql format string
     * @return string
     */
    public static function hariTanggal($tanggal)
    {
        $date = new DateTime($tanggal);

        return self::$days[$date->format('w')].', '.$date->format('d')
            .' '.self::$months[$date->format('n')].' '.$date->format('Y');
    }

    /**
     * Read day from two date(format)
     * @param  string $tanggal_a mysql format string
     * @param  string $tanggal_b mysql format string
     * @return string
     */
    public static function hariKeHari($tanggal_a, $tanggal_b)
    {
        $date_a = new DateTime($tanggal_a);
        $date_b = new DateTime($tanggal_b);

        return self::$days[$date_a->format('w')].' - '.self::$days[$date_b->format('w')];
    }

    /**
     * Read date from two date(format)
     * @param  string $tanggal_a mysql format string
     * @param  string $tanggal_b mysql format string
     * @return string
     */
    public static function tanggalKeTanggal($tanggal_a, $tanggal_b)
    {
        $date_a = new DateTime($tanggal_a);
        $date_b = new DateTime($tanggal_b);

        return $date_a->format('d').' - '.$date_b->format('d').' '.self::$months[$date_a->format('n')].' '.$date_a->format('Y');
    }

    /**
     * Get roman text
     * @param  string $tanggal
     * @return string
     */
    public static function roman($tanggal)
    {
        $date = $tanggal instanceof DateTime ?$tanggal:new DateTime($tanggal);

        return self::$roman[$date->format('n')];
    }

    /**
     * Handle file upload, cannot handle multiple files
     * @param  string $key          $_FILES[$key]
     * @param  string &$filename
     * @param  array  $allowedTypes
     * @return bool
     */
    public static function handleFileUpload($key, &$filename, $allowedTypes = [])
    {
        $result = false;
        $isArray = isset($_FILES[$key]) && is_array($_FILES[$key]['error']);

        if ($isArray) {
            return $result;
        }

        if (isset($_FILES[$key]) &&
            UPLOAD_ERR_OK === $_FILES[$key]['error'] &&
            ($allowedTypes && in_array($_FILES[$key]['type'], $allowedTypes))) {
            $ext = strtolower(strrchr($_FILES[$key]['name'], '.'));
            $filename .= $ext;
            $result = move_uploaded_file($_FILES[$key]['tmp_name'], $filename);
        }

        return $result;
    }

    /**
     * Send CSV
     * @param  string $filename
     * @param  array  $headers
     * @param  array  $data
     */
    public static function sendCSV($filename, array $headers, array $data)
    {
        //headers
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Description: File Transfer');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$filename.'.csv";');
        header('Content-Transfer-Encoding: binary');

        //open file pointer to standard output
        $fp = fopen('php://output', 'w');
        $delimiter = ';';

        if ($fp) {
            //add BOM to fix UTF-8 in Excel
            fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

            fputcsv($fp, $headers, $delimiter);
            foreach ($data as $datum) {
                fputcsv($fp, $datum, $delimiter);
            }
        }

        fclose($fp);
        exit(0);
    }
}