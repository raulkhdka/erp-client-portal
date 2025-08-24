<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Service List</title>
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
    <h1>Service List</h1>
    <table>
        <thead>
            <tr>
                <th>SN</th>
                <th>Name</th>
                <th>Service Type</th>
                <th>Clients</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($services as $service)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $service->name }}</td>
                    <td>{{ $service->type }}</td>
                    <td>{{ $service->clients_count }} clients</td>
                    <td>{{ $service->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>{!! $service->created_at_formatted !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>