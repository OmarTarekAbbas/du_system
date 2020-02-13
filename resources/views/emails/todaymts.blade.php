<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
    </head>
    <style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}


</style>
    <body>
        <p><strong>Dears,</strong> <br>Kindly find Today Messages Status</p>
        <table cellpadding="10" >
            <thead>
                <tr>
                    <th>Message Body</th>
                    <th>Shorten URL</th>
                    <th>Service</th>
                    <th>Sent</th>
                </tr>
            </thead>
            @foreach($messages as $message)
            <tr>
                <td>{{ $message->MTBody }}</td>
                <td><a href="{{ $message->ShortnedURL }}"> {{ $message->ShortnedURL }}</a></td>
                <td>{{ $message->service->title }} | {{ $message->service->operator->title }} - {{ $message->service->operator->country->name }}</td>
                <td>  
                    @if(  $message->IsysResponse == 'OK'	)
                    Yes
                    @else
                    No
                    @endif
                </td>
            </tr>

            @endforeach
        </table>

    </body>
</html>