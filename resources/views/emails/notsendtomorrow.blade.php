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
<p><strong>Dears,</strong> <br>Kindly find following Messages that Won't sent tomorrow with reason</p>
 <table cellpadding="10" >
    <thead>
    <tr>
        <th>Service</th>
        <th>Reason</th>
    </tr>
    </thead>
    @foreach($messages as $message)
        <tr>
        <td>{{ $ArrayServices[$message->service_id] }}</td>
        <td>
            @if($message->reason == 'notappvd')
                <strong>Not Approved</strong>
            @else
                <strong>No Messages for this service Tomorrow</strong>
            @endif

        </td>
        </tr>
    @endforeach
</table>

</body>
</html>