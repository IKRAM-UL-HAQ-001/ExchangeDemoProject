<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
<meta name="robots" content="index, follow">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>Exchange Management System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />
    <meta name="google-site-verification" content="dyeHS1jvPA6amUut6GVo-n5SoCdgjTEw4LZzb3-u774" />
    <meta name="description" content="testing this">
    <style>
    .card {
        box-shadow: 10px 14px 45px rgba(0, 0, 0, 0.49); /* Adjust shadow intensity */
        border: 1px solid rgba(0, 0, 0, 0.1); /* Light border for visibility */
    }

    .bg-gradient-primary {
    }

    .card-header {
        border-radius: 8px; /* Smooth corners */
    }

    .btn.bg-gradient-primary {
        box-shadow: 0 4px 8px rgba(59, 131, 246, 0.38); /* Add a shadow effect to the button */
        transition: transform 0.3s ease-in-out; /* Smooth transition on hover */
    }

    .btn.bg-gradient-primary:hover {
        transform: scale(1.01); /* Slightly enlarge on hover */
        box-shadow: 0 6px 12px rgba(59, 131, 246, 0.41); /* Enhance shadow on hover */
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

<body class="">
    <main class="main-content mt-0">
        <div class="page-header align-items-start min-vh-100" style="opacity: 1.0; background-image: url('../assets/img/imgs.jpg'); ">
            <span class=""></span>
            <div class="container my-auto">
                <h1 class="text-black font-weight-bolder text-center mt-2 mb-5">Exchange Management System</h1>
                <div class="row mt-3">
                    <div class="col-lg-4 col-md-8 col-12 mx-auto">
                        <div class="card z-index-0 fadeIn3 fadeInBottom">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                                    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Log in</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Display Validation Errors -->
                                @if ($errors->any())
                                    <div class="alert alert-danger text-white">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form id="loginForm" role="form" method="post" action="{{ route('login.post') }}">
                                    @csrf
                                    <div class="input-group input-group-outline my-3">
                                        <select class="form-control" name="role" id="role" onchange="toggleExchangeDropdown()">
                                            <option value="" disabled selected>Select your Role</option>
                                            <option value="admin">Admin</option>
                                            <option value="exchange">Exchange</option>
                                            <option value="assistant">Assistant</option>
                                        </select>
                                    </div>
                                    <div id="userFields">
                                        <div class="input-group input-group-outline my-3">
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter User Name" required>
                                        </div>
                                        <div class="input-group input-group-outline mb-3">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                                        </div>
                                    </div>
                                    <div id="ExchangeDropdown" style="display: none;">
                                        <div class="input-group input-group-outline mb-3">
                                            <select class="form-control" id="exchange" name="exchange"> 
                                              <option value="" disabled selected>Select Your Exchange</option>
                                              @foreach($exchangeRecords as $exchange)
                                                <option value="{{ $exchange->id ?? 'N/A' }}">{{ $exchange->name ?? 'N/A' }}</option>
                                              @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">Sign in</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/material-dashboard.min.js?v=3.1.0"></script>

    <script>
        function toggleExchangeDropdown() {
            const userRole = document.getElementById('role').value;
            const exchangeDropdown = document.getElementById('ExchangeDropdown');
            exchangeDropdown.style.display = (userRole === 'exchange') ? 'block' : 'none';
        }
    </script>
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
</body>
</html>
