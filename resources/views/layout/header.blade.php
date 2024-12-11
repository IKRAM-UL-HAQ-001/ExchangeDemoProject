<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/logo.png">
    <link rel="icon" type="image/png" href="./assets/img/logo.png">
<meta name="robots" content="index, follow">
    
    <title>
        Your Custom Exchange Platform 
    </title>
    
    <!-- Customized Meta Description -->
    <meta name="description" content="testing this">
    <meta name="google-site-verification" content="dyeHS1jvPA6amUut6GVo-n5SoCdgjTEw4LZzb3-u774" />
    <!-- Fonts and Icons -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script type="application/ld+json">
    {
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "Your Custom Exchange Platform",
    "description": "Effortlessly manage your exchange roles, including Admin, Exchange, and Assistant. Choose from top platforms: JADUGAR, AMAZON, CRICKETSTAR, FASTBET, BETBAZAR."
    }
    </script>
    <style>
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
    <script>
        document.addEventListener('contextmenu', function(event) {
            event.preventDefault();
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'F12' || (event.ctrlKey && event.shiftKey && event.key === 'I') || (event.ctrlKey &&
                    event.key === 'U')) {
                event.preventDefault();
            }
        });

        document.addEventListener('keydown', function(event) {
            if ((event.ctrlKey && event.shiftKey && (event.key === 'J' || event.key === 'C')) || (event.ctrlKey &&
                    event.key === 'S')) {
                event.preventDefault();
            }
        });

        document.addEventListener('selectstart', function(event) {
            event.preventDefault();
        });
        document.addEventListener('copy', function(event) {
            event.preventDefault();
        });
    </script>

</head>
