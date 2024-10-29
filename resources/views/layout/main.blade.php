<!DOCTYPE html>
<html lang="en">
  @include("layout.header")
<body class="g-sidenav-show  bg-gray-200">
    @include("layout.sideNavBar")
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @include("layout.topNavBar")        
        <div class="container-fluid py-4 d-flex flex-column justify-content-between" style="min-height:95vh">
            @yield("content")
            @include("layout.footer")
        </div>
    </main>

  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="../assets/js/material-dashboard.min.js?v=3.1.0"></script>
</body>
</html>
