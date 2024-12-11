<!DOCTYPE html>
<html lang="en">
  @include("layout.header")
<body class="g-sidenav-show bg-gray-200 p-1" style="position: relative; overflow: auto;">
  <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100vh; z-index: 0;">
      <div style="
          width: 100%; 
          height: 100%; 
          background-image: url('../assets/img/background.jpg'); 
          background-size: cover; 
          background-position: center; 
          opacity: 0.6;">
      </div>
  </div>

  @include("layout.sideNavBar")
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
      @include("layout.topNavBar")        
      <div class="container-fluid py-4 d-flex flex-column justify-content-between" style="min-height: 95vh; padding-top: 20px;">
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
