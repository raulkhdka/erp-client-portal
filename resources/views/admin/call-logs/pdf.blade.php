<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Call Log List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #10b981;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #10b981;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
    </style>
</head>
<body>
    <h1>Call Log List</h1>
    <table>
        <thead>
            <tr>
                <th>SN</th>
                <th>Date/Time</th>
                <th>Subject</th>
                <th>Caller</th>
                <th>Type</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Employee</th>
                <th>Company</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($callLogs as $callLog)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {!! $callLog->call_date_formatted !!}
                        @if ($callLog->call_time)
                            <br>{{ \Carbon\Carbon::parse($callLog->call_time)->format('h:i A') }}
                        @endif
                    </td>
                    <td>{{ $callLog->subject }}</td>
                    <td>
                        @if ($callLog->caller_name)
                            {{ $callLog->caller_name }}
                            @if ($callLog->caller_phone)
                                <br>{{ $callLog->caller_phone }}
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ ucfirst($callLog->call_type) }}</td>
                    <td>{{ ucfirst($callLog->priority) }}</td>
                    <td>{{ $callLog->status_label }}</td>
                    <td>{{ $callLog->employee->name ?? 'N/A' }}</td>
                    <td>{{ $callLog->client ? $callLog->client->company_name : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>