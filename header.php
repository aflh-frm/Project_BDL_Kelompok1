<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Rental Mobil - Dashboard</title>
<style>
     /* Reset and basics */
    *, *::before, *::after {
        box-sizing: border-box;
    }
    body {
        font-family: 'Poppins', sans-serif;
        background: #ffffff;
        color: #6b7280;
        margin: 0;
        padding: 0;
        line-height: 1.6;
        font-size: 16px;
    }
    a {
        text-decoration: none;
        color: #2563eb;
        transition: color 0.3s ease;
    }
    a:hover, a:focus {
        color: #1e40af;
        outline: none;
    }
    h1, h2 {
        color: #111827;
        font-weight: 700;
        margin-bottom: 1rem;
    }
    h1 {
        font-size: 2.5rem;
        font-weight: 800;
    }
    h2 {
        font-size: 1.75rem;
    }

    /* Container */
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 1rem 2rem 4rem;
    }

    /* Sticky header */
    header {
        position: sticky;
        top: 0;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 2rem;
        z-index: 100;
        box-shadow: 0 1px 3px rgb(0 0 0 / 0.05);
    }
    header .logo {
        font-size: 1.5rem;
        font-weight: 800;
        color: #111827;
    }
    header nav a {
        margin-left: 1.5rem;
        font-weight: 600;
        font-size: 1rem;
    }
    header nav a.cta {
        background: #111827;
        color: #fff;
        padding: 0.5rem 1.2rem;
        border-radius: 0.5rem;
        font-weight: 700;
        transition: background-color 0.3s ease;
    }
    header nav a.cta:hover {
        background: #1e40af;
    }

    /* Section spacing */
    section {
        padding-top: 4rem;
        padding-bottom: 4rem;
    }
    section + section {
        border-top: 1px solid #e5e7eb;
    }

    /* Cards */
    .card {
        background: #f9fafb;
        border-radius: 0.75rem;
        padding: 2rem;
        box-shadow: 0 1px 4px rgb(0 0 0 / 0.05);
        margin-bottom: 3rem;
    }

    /* Tables */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }
    th, td {
        text-align: left;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        color: #374151;
    }
    th {
        font-weight: 700;
        background: #f3f4f6;
    }
    tr:hover {
        background: #efefef;
    }
    td.actions a {
        margin-right: 1rem;
        font-weight: 600;
        color: #2563eb;
    }
    td.actions a:hover {
        color: #1e40af;
    }

    /* Forms */
    form label {
        display: block;
        font-weight: 600;
        margin-top: 0.75rem;
        margin-bottom: 0.25rem;
        color: #374151;
    }
    form input[type="text"],
    form input[type="number"],
    form input[type="email"],
    form input[type="date"],
    form select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 1rem;
        font-family: 'Poppins', sans-serif;
        transition: border-color 0.3s ease;
        color: #374151;
    }
    form input[type="text"]:focus,
    form input[type="number"]:focus,
    form input[type="email"]:focus,
    form input[type="date"]:focus,
    form select:focus {
        border-color: #2563eb;
        outline: none;
    }
    form button {
        margin-top: 1.5rem;
        background-color: #111827;
        color: white;
        padding: 0.75rem 1.5rem;
        font-weight: 700;
        font-size: 1rem;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    form button:hover,
    form button:focus {
        background-color: #1e40af;
        outline: none;
    }

    /* Messages */
    .message {
        background-color: #dcfce7;
        border: 1px solid #4ade80;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        color: #166534;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    .error {
        background-color: #fee2e2;
        border: 1px solid #f87171;
        color: #991b1b;
    }

    /* Responsive */
    @media (max-width: 768px) {
        header {
            flex-direction: column;
            align-items: flex-start;
        }
        header nav {
            margin-top: 0.5rem;
        }
        table, thead, tbody, th, td, tr {
            display: block;
        }
        thead tr {
            float: left;
            width: 100%;
        }
        tbody tr {
            margin-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }
        tr:hover {
            background: transparent;
        }
        td.actions a {
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        td {
            padding-left: 50%;
            position: relative;
        }
        td:before {
            content: attr(data-label);
            position: absolute;
            left: 1rem;
            font-weight: 700;
            color: #6b7280;
            width: 45%;
            white-space: nowrap;
        }
    }

</style>
</head>
<body>
<header>
    <div class="logo">RentalMobil</div>
    <nav>
        <a href="index.php">Beranda</a>
        <a href="kendaraan.php">Kendaraan</a>
        <a href="penyewa.php">Penyewa</a>
        <a href="rental.php">Rental</a>
    </nav>
</header>
 