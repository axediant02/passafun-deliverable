<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta property="og:title" content="{{ $result['quizName'] }}">
    <meta property="og:description" content="{{ $result['sub_header'] }}">
    <meta property="og:image" content="{{ $result['image'] }}">
    <meta property="og:url" content="{{ getEnvironmentUrl() }}/{{ $result['quizId'] }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Passafund">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $result['quizName'] }}">
    <meta name="twitter:description" content="{{ $result['sub_header'] }}">
    <meta name="twitter:image" content="{{ $result['image'] }}">
    <meta name="twitter:url" content="{{ getEnvironmentUrl() }}/{{ $result['quizId'] }}">
    <meta name="twitter:site" content="@your_twitter_handle">

    <title>{{ $result['quizName'] }}</title>
</head>
<body>

</body>
<script>
    const quizId = @json($result['quizId']);
    window.location.href = `{{ getEnvironmentUrl() }}/${quizId}`;
</script>
</html>
