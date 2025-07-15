<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-dyZtM8Qb1QvQvQb1QvQvQb1QvQvQb1QvQvQb1QvQvQb1QvQvQb1QvQvQb1QvQvQb1QvQvQb1QvQvQb1Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@php
$setting = App\Models\SiteSetting::find(1);
@endphp

<!-- Header Start -->
<header class="custom-header border-bottom bg-white" style="border-bottom:1px solid #e5e7eb;">
  <div class="container px-0">
    <div class="px-0 py-2 border-bottom small text-muted d-flex justify-content-between align-items-center" style="border-bottom:1px solid #f1f3f6;">
    <div>
      <i class="fas fa-phone me-1"></i> {{ $setting->phone ?? '085159080404' }}
      <span class="mx-3">|</span>
      <i class="fas fa-envelope me-1"></i> {{ $setting->email ?? 'nac@gmail.com' }}
    </div>
    <div>
      <!-- Optional: Add dark mode toggle or other icons here -->
    </div>
  </div>
  <div class="container px-0">
    <div class="d-flex align-items-center justify-content-between py-3">
    <!-- Logo -->
    <a href="{{ route('home') }}" class="me-4 d-flex align-items-center">
      <img src="{{ asset($setting->logo ?? 'new_frontend/assets/images/logo.png') }}" alt="logo" style="height:40px; width:auto;">
    </a>
    <!-- Categories Dropdown -->
    <div class="dropdown me-3">
      <a class="nav-link dropdown-toggle fw-bold text-dark" href="#" id="categoriesDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:1.15em;">
        Categories
      </a>
      <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
        <li><a class="dropdown-item" href="#">Programming</a></li>
        <li><a class="dropdown-item" href="#">Design</a></li>
        <li><a class="dropdown-item" href="#">Marketing</a></li>
        <li><a class="dropdown-item" href="#">Business</a></li>
        <li><a class="dropdown-item" href="#">Photography</a></li>
        <li><a class="dropdown-item" href="#">Language</a></li>
      </ul>
    </div>
    <!-- Search Bar -->
    <form class="flex-grow-1 mx-3" style="max-width:400px;">
      <div class="input-group">
        <input type="text" class="form-control" placeholder="Search for anything">
        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
      </div>
    </form>
    <!-- Navigation Links -->
    <nav class="d-flex align-items-center gap-3">
      <a href="{{ route('home') }}" class="nav-link fw-bold text-dark">Home</a>
      <div class="dropdown">
        <a class="nav-link dropdown-toggle fw-bold text-dark" href="#" id="coursesDropdown" data-bs-toggle="dropdown" aria-expanded="false">Courses</a>
        <ul class="dropdown-menu" aria-labelledby="coursesDropdown">
          <li><a class="dropdown-item" href="#">All Courses</a></li>
          <li><a class="dropdown-item" href="#">Popular</a></li>
        </ul>
      </div>
      <a href="{{ route('blog') }}" class="nav-link fw-bold text-dark">Blog</a>
      <!-- Cart Icon -->
      <a href="{{ route('mycart') }}" class="nav-link position-relative">
        <i class="fas fa-shopping-cart"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary" style="font-size:0.8em;">0</span>
      </a>
      <!-- Admission Button -->
      <a href="#" class="btn btn-primary fw-bold ms-2" style="min-width:120px;">
        <i class="fas fa-user-plus me-1"></i> Admission
      </a>
    </nav>
    </div>
  </div>
</header>
<!-- Header End -->

<!-- CSS (can be placed in a <style> tag or your CSS file) -->
<!-- Google Fonts: Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

<style>
.custom-header, .custom-header * {
  font-family: 'Inter', Arial, sans-serif;
}
.custom-header .nav-link {
  color: #223a5f;
  transition: color .2s;
}
.custom-header .nav-link:hover,
.custom-header .dropdown-toggle.show {
  color: #1976d2;
}
.custom-header .btn-primary {
  background: #56a3d9;
  border: none;
}
.custom-header .btn-primary:hover {
  background: #1976d2;
}
.custom-header .dropdown-menu {
  min-width: 180px;
}
.custom-header .badge {
  pointer-events: none;
}
@media (max-width: 991px) {
  .custom-header .container-fluid {
    flex-direction: column !important;
    align-items: stretch !important;
  }
  .custom-header nav {
    flex-wrap: wrap;
    gap: 1rem;
  }
}
</style>