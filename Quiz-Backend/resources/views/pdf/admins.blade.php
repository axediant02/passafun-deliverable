<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admins List</title>
    <style>

        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Admins List</h1>
    <table>
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Role ID</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>

            @foreach($admins as $admin)
                <tr>
                    <td>{{ $admin['first_name'] }}</td>
                    <td>{{ $admin['last_name'] }}</td>
                    <td>{{ $admin['email'] }}</td>
                    <td>{{ $admin['role_id'] }}</td>
                    <td>{{ $admin['role'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
