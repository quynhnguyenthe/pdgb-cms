<?php

use App\Models\Info;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

if (!function_exists('upload_file_S3')) {
    function upload_file_S3($pathSuffix, $file)
    {
        $originName = $file->getClientOriginalName();
        $nameFile = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $pathSuffix . '/' . $nameFile;
        try {
            Storage::disk('s3')
                ->put($pathSuffix . '/' . $nameFile, file_get_contents($file));
        } catch (Exception $exception) {
            throw new \App\Exceptions\S3Exception();
        }
        return [
            'name' => $originName,
            'path' => $path
        ];
    }
}

if (!function_exists('get_size_file')) {
    function get_size_file($file)
    {
        return number_format($file->getSize() / 1048576, 2);
    }
}

if (!function_exists('format_currency')) {
    function format_currency($number)
    {
        return number_format($number, 0) . '円';
    }
}

if (!function_exists('format_date_homepage')) {
    function format_date_homepage($date)
    {
        return date('Y/m/d', strtotime($date));
    }
}


if (!function_exists('get_extension_file')) {
    function get_extension_file($name)
    {
        $extension = explode('.', $name);
        return mb_strtoupper(end($extension));
    }
}

if (!function_exists('format_date_cms')) {
    function format_date_cms($date)
    {
        return date('Y-m-d', strtotime($date));
    }
}

if (!function_exists('format_date_mail')) {
    function format_date_mail($date)
    {
        return date('Y年m月d日', strtotime($date));
    }
}
