<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Client List</title>
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
    <h1>Client List</h1>
    <table>
        <thead>
            <tr>
                <th>SN</th>
                <th>Client Name</th>
                <th>Company Info</th>
                <th>Employee Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $client->name }}</td>
                    <td>
                        {{ $client->company_name }}
                        @if ($client->services && $client->services->count() > 0)
                            <br>
                            @foreach ($client->services->take(2) as $service)
                                <span>{{ $service->name }}</span>@if (!$loop->last), @endif
                            @endforeach
                            @if ($client->services->count() > 2)
                                <span>+{{ $client->services->count() - 2 }} more</span>
                            @endif
                        @endif
                    </td>
                    <td>
                        @if ($client->assignedEmployees->first())
                            {{ $client->assignedEmployees->first()->name ?? 'Unassigned' }}
                        @else
                            Unassigned
                        @endif
                    </td>
                    <td>
                        {{ $client->user->email }}
                        @if ($client->emails->count() > 0)
                            <br>+{{ $client->emails->count() }} additional email(s)
                        @endif
                    </td>
                    <td>
                        @if ($client->phones->count() > 0)
                            {{ $client->phones->first()->phone }}
                            @if ($client->phones->count() > 1)
                                <br>+{{ $client->phones->count() - 1 }} more
                            @endif
                        @else
                            No phone
                        @endif
                    </td>
                    <td>{{ ucfirst($client->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>