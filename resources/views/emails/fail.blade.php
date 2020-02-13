<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <p><strong>Dears,</strong> <br>this message fail to send </p>
    <table border="1">
        <thead>
        <tr>
            <th>Message Id</th>
            <th>Message Body</th>
            <th>Message Date</th>
            <th>Shorten URL</th>
            <th>Service</th>
        </tr>
        </thead>
        @foreach($messages as $message)
            <tbody>
            <td>{{ $Message->id }}</td>
            <td>{{ $Message->MTBody }}</td>
            <td>{{ $Message->date }}</td>
            <td><a href="{{ $Message->ShortnedURL }}"> {{ $Message->ShortnedURL }}</a></td>
            <td>{{ $Message->service->title }} | {{ $Message->service->operator->title }} - {{ $Message->service->operator->country->name }}</td>
            <br />
            </tbody>
        @endforeach
    </table>

</body>
</html>