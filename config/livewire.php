<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Temporary File Uploads (subset — merged with Livewire defaults)
    |--------------------------------------------------------------------------
    |
    | Raised above Livewire default (~12 MB) for larger phone photos.
    |
    | If uploads fail even on small (~2 MB) images, PHP defaults are usually the
    | cause: upload_max_filesize/post_max_size of 2M leave no room once multipart
    | encoding is counted. Bump both (and web server limits) above your max file size.
    |
    */

    'temporary_file_upload' => [
        'disk' => null,
        'rules' => ['required', 'file', 'max:51200'],
        'directory' => null,
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
            'heic', 'heif',
        ],
        'max_upload_time' => 10,
        'cleanup' => true,
    ],
];
