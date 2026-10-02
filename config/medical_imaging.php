<?php
return [
    'python' => env('MEDICAL_IMAGING_PYTHON', '/opt/fit-imaging/bin/python'),
    'max_file_kb' => 20480,
    'max_files' => 100,
    'max_batch_kb' => 102400,
    'pacs_url' => env('MEDICAL_PACS_DICOMWEB_URL'),
    'pacs_token' => env('MEDICAL_PACS_TOKEN'),
    'modalities' => ['MR'=>'IRM','CT'=>'Scanner','CR'=>'Radiographie','DX'=>'Radiographie numérique','US'=>'Échographie','NM'=>'Scintigraphie','OT'=>'Autre image'],
];
