<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description" content="{{ $metaDescription }}">

    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:url" content="{{ getEnvironmentUrl() }}/{{ $uid }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Passafund">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $metaTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">
    <meta name="twitter:url" content="{{ getEnvironmentUrl() }}/{{ $uid }}">
    <meta name="twitter:site" content="@your_twitter_handle">

    <title>{{ $metaTitle }}</title>
</head>
<body>
    <script>
        window.location.href = `{{ getEnvironmentUrl() }}/{{ $uid }}`;
    </script>
</body>
</html>
