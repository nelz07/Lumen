<?php

use Carbon\Carbon;

return [

    /*
    |--------------------------------------------------------------------------
    | Snappy PDF / Image Configuration
    |--------------------------------------------------------------------------
    |
    | This option contains settings for PDF generation.
    |
    | Enabled:
    |    
    |    Whether to load PDF / Image generation.
    |
    | Binary:
    |    
    |    The file path of the wkhtmltopdf / wkhtmltoimage executable.
    |
    | Timout:
    |    
    |    The amount of time to wait (in seconds) before PDF / Image generation is stopped.
    |    Setting this to false disables the timeout (unlimited processing time).
    |
    | Options:
    |
    |    The wkhtmltopdf command options. These are passed directly to wkhtmltopdf.
    |    See https://wkhtmltopdf.org/usage/wkhtmltopdf.txt for all options.
    |
    | Env:
    |
    |    The environment variables to set while running the wkhtmltopdf process.
    |
    */
    
    'pdf' => [
        'enabled' => true,
        'binary'  => env('WKHTML_PDF_BINARY', '/usr/local/bin/wkhtmltopdf'),
        'timeout' => false,
        'options' => [
            'enable-local-file-access' => true,
            'encoding' =>'utf-8',
            'orientation'   => 'landscape',
            'page-size'=>'a4',
            'footer-center'=>'Page [page] of [toPage]',
            'footer-font-size' => 8,
            'footer-left' => 'LIGHT Microfinance Inc © ' . date('Y'),
            'footer-right' => 'Printed at: ' . Carbon::now()->timezone('Asia/Manila')->format('Y-m-d h:i:sA'),
            'margin-top'    => 5,
            'margin-right'  => 5,
            'margin-bottom' => 10,
            'margin-left'   => 5,
        ],
        'env'     => [],
    ],
    
    'image' => [
        'enabled' => true,
        'binary'  => env('WKHTML_IMG_BINARY', '/usr/local/bin/wkhtmltoimage'),
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],

];
