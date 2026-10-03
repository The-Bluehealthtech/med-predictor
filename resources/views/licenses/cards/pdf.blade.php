<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Carte de licence {{ $license->license_number ?: 'FIT-'.$license->id }}</title>
@include('licenses.cards._styles')
<style>
    body{margin:0;padding:0;background:#fff}
    .license-pair{display:block}
    .cr80{border:0;border-radius:0;box-shadow:none;page-break-after:always;break-after:page}
    .cr80:last-child{page-break-after:auto}
    .pdf-note{display:none}
</style>
</head>
<body>
@include('licenses.cards._card',['license'=>$license])
</body>
</html>
