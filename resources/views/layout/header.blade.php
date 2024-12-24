<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="index, follow">
    <meta name="description" content="testing this">
    <meta name="google-site-verification" content="dyeHS1jvPA6amUut6GVo-n5SoCdgjTEw4LZzb3-u774" />

    <!-- Page Title -->
    <title>Your Custom Exchange Platform</title>

    <!-- Favicon and Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/logo.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo.png') }}">

    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- CSS Files -->
    <link href="{{ asset('assets/css/material-dashboard.css?v=3.1.0') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script type="application/ld+json">
    {
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "Your Custom Exchange Platform",
    "description": "Effortlessly manage your exchange roles, including Admin, Exchange, and Assistant. Choose from top platforms: JADUGAR, AMAZON, CRICKETSTAR, FASTBET, BETBAZAR."
    }
    document.addEventListener('DOMContentLoaded', function () {
        const modals = document.querySelectorAll('.modal');

        modals.forEach(function (modal) {
            modal.addEventListener('hidden.bs.modal', function () {
                // Refresh the page when any modal is closed
                location.reload();
            });
        });
    });
    </script>
    <style>
        #sidenav-collapse-main::-webkit-scrollbar {
            width: 8px;
        }
    
        #sidenav-collapse-main::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 10px;
        }
    
        #sidenav-collapse-main::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }
    
        body::-webkit-scrollbar {
            width: 8px;
        }
    
        body::-webkit-scrollbar-thumb {
            background-color: #80bdff;
            border-radius: 10px;
        }
    
        body::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }
    
        .bg-gradient-to-white {
            background: linear-gradient(to bottom, #f0f0f0, white);
        }

        .test1 {
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.9));
            color: white;
            opacity: 1;
        }

        .form-control {
            color: black;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .d-sm-inline,
        .breadcrumb-item {
            font-weight: bold;
            color: black;
        }

        .form-label {
            color: black;
        }

        .text-capitalize {
            font-weight: bold;
            color: white;
        }

        .nav-link-text {
            font-weight: bold;
        }

        .form-control:focus {
            border-color: #80bdff;
            /* Border color on focus */
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            /* Shadow on focus */
        }

        input::placeholder {
            padding-left: 10px;
            color: #aaa;
        }

        .modal-header {
            background-color: #343a40;
            color: white;
        }

        .td-large {
            width: 45%;
        }

        .pagination {
            display: flex;
            list-style-type: none;
            padding: 0;
            margin: 0;
            justify-content: flex-end !important;

        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination li a,
        .pagination li span {
            display: inline-block;
            padding: 8px 16px;
            background-color: #f1f1f1;
            /* Light background color */
            color: #333;
            /* Text color */
            border-radius: 4px;
            font-size: 16px;
            /* Adjust the font size */
            text-decoration: none;
        }

        .pagination li a:hover,
        .pagination li span:hover {
            background-color: #ddd;
            /* Change background color on hover */
        }

        .pagination .page-link {
            color: black;
            background-color: #fff;
            border: 1px solid #dee2e6;
            text-decoration: none;
            transition: all 0.3s;
        }

        .pagination li.active span {
            background-color: #007bff;
            /* Change background color for the active page */
            color: white;
            /* Text color for active page */
            font-size: 18px;
            /* Increase font size for the active page */
            padding: 10px 18px;
            /* Increase padding to make it bigger */
        }

        .pagination li.disabled span {
            background-color: #e0e0e0;
            /* Gray color for disabled state */
            color: #2a2a2a;
            /* Text color for disabled state */
        }

        .pagination li a,
        .pagination li span {
            font-size: 14px;
            /* Default font size for pagination */
        }

        .td-small {
            width: 10%;
            text-align: center;
        }

        .modal-header {
            background-color: #343a40;
            color: white;
        }

        .table thead tr th {
            color: black !important;
            font-size: 14px !important;
            font-weight: bold !important;
            text-transform: uppercase !important;
        }

        /* DataTable Custom Styling */
        .table tbody tr:nth-child(odd) {
            background-color: black;
            color: white;
        }

        .table tbody tr:nth-child(odd) td {
            color: white;
        }

        .table tbody tr:nth-child(even) {
            background-color: white !important;
            /* Use !important if needed */
            color: black !important;
            /* Use !important if needed */
        }

        .table tbody tr:nth-child(even) td {
            background-color: white !important;
            /* Use !important if needed */
            color: black !important;
            /* Use !important if needed */
        }

        /* Optional: Adjust hover effect to keep text visible */

        .table tbody tr:nth-child(odd):hover {
            background-color: black !important;
            /* Optional: Change background on hover */
            color: white !important;
            /* Change text to black */
        }

        .table tbody tr:nth-child(even):hover {
            background-color: white !important;
            /* Use !important if needed */
            color: black !important;
            /* Use !important if needed */
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-right: 20px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 5px 10px;
            margin: 0 5px;
            font-size: 16px;
            border-radius: 50%;
            border: none;
            transition: background-color 0.3s ease;
            cursor: pointer;
        }

        .dataTables_paginate .paginate_button.current {
            color: orange;
        }


        @import url("https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;700&display=swap");


        input[type="checkbox"] {
            display: none;
        }

        input[type="checkbox"]:checked+.button {
            filter: none;
        }

        input[type="checkbox"]:checked+.button .dot {
            left: calc(100% - 1.7rem);
            /* Adjust this value */
            background-color: #acc301;
        }

        .button {
            position: relative;
            width: 3.5rem;
            height: 1.6rem;
            border-radius: 1rem;
            box-shadow: inset 2px 2px 5px rgba(0, 0, 0, 0.3), inset -2px -2px 5px rgba(255, 255, 255, 0.8);
            cursor: pointer;
        }

        .button .dot {
            position: absolute;
            width: 1.4rem;
            height: 1.4rem;
            left: 0.25rem;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 50%;
            box-shadow: 3px 3px 6px rgba(0, 0, 0, 0.3), -3px -3px 6px rgba(255, 255, 255, 0.8);
            transition: all 0.3s;
            background-color: #f10f0f;
            will-change: left, background-color;
        }

        @keyframes deco-move {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
    </style>


</head>
