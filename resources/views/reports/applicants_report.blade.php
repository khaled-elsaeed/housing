<!DOCTYPE html>
<html>
<head>
    <title>Applicants Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
            color: #333333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px; /* Adjust logo size */
        }
        .title {
            color: #263a5b; /* Dark Blue Color */
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        .report-name {
            color: #8C2F39; /* Crimson Red Color */
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0;
        }
        .date {
            text-align: right;
            font-size: 14px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #8C2F39; /* Crimson Red Color for Header */
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('path/to/university-logo.png') }}" alt="University Logo"> <!-- Update path -->
        <div class="title">Housing System</div>
        <div class="report-name">Report of Applicants</div>
    </div>
    
    <div class="date">Generated on: {{ date('Y-m-d') }}</div> <!-- Label for date of generation -->

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username (AR)</th>
                <th>Email</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($applicants as $applicant)
                <tr>
                    <td>{{ $applicant->id }}</td>
                    <td>{{ $applicant->username_ar }}</td>
                    <td>{{ $applicant->email }}</td>
                    <td>{{ $applicant->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
