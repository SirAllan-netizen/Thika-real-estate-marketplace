<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Thika Real Estate Marketplace</title>

  <!-- Google Fonts: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- Tailwind CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      scroll-behavior: smooth;
    }

    /* Glassmorphism for hero */
    .glass-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border-radius: 1rem;
      padding: 2rem;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Navbar hover underline */
    .nav-link {
      position: relative;
    }
    .nav-link::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0%;
      height: 2px;
      background-color: #1D4ED8;
      transition: width 0.3s ease-in-out;
    }
    .nav-link:hover::after {
      width: 100%;
    }

    /* Card hover effect */
    .hover-card:hover {
      transform: scale(1.03);
      transition: 0.3s ease-in-out;
    }

    /* Hero Background */
    .hero {
      background: url('Thika.jpg') center center / cover no-repeat fixed;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      color: white;
    }
    .hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.65);
      z-index: 1;
    }

    .hero-content {
      position: relative;
      z-index: 2;
      text-align: center;
      max-width: 700px;
    }

    /* Smooth button */
    .btn {
      transition: all 0.3s ease;
    }
    .btn:hover {
      transform: scale(1.05);
    }

    /* Floating CTA button */
    .floating-btn {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background-color: #10b981;
      color: white;
      padding: 14px 18px;
      border-radius: 9999px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      transition: transform 0.3s ease;
    }
    .floating-btn:hover {
      transform: translateY(-5px);
    }
  </style>
</head>

<body class="bg-gray-100">

  <!-- Navbar -->
  <nav class="bg-white shadow-md py-4 sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center px-6">
      <h1 class="text-2xl font-bold text-blue-600">Thika Real Estate</h1>
      <ul class="flex space-x-6 text-gray-800 font-medium">
        <li><a href="index.php" class="nav-link text-blue-600 font-bold">Home</a></li>
        <li><a href="properties.html" class="nav-link">Properties</a></li>
        <li><a href="about.html" class="nav-link">About</a></li>
        <li><a href="contact.html" class="nav-link">Contact</a></li>

        <!-- Dropdown -->
        <li class="relative">
          <button class="register-btn bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Register ▼</button>
          <div class="register-dropdown absolute hidden bg-white text-sm mt-2 rounded shadow-md overflow-hidden z-50">
              <a href="register.html" class="block px-4 py-2 hover:bg-gray-100">As Tenant</a>
              <a href="register.html" class="block px-4 py-2 hover:bg-gray-100">As Landlord</a>
              <a href="agent_register.php" class="block px-4 py-2 hover:bg-gray-100">As Agent</a>
          </div>
        </li>

        <li><a href="login.html" class="text-white bg-blue-600 px-4 py-2 rounded hover:bg-blue-700">Login</a></li>
        <li><a href="add_listing.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Post Listing</a></li>
      </ul>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content glass-card text-white">
      <h2 class="text-4xl md:text-5xl font-bold mb-4">Find Your Perfect Home in Thika</h2>
      <p class="text-lg mb-6">Discover trusted rentals and properties in the heart of Thika with confidence and ease.</p>
      <a href="properties.html" class="btn bg-blue-600 px-6 py-3 rounded text-white text-lg hover:bg-blue-700">Browse Listings</a>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="bg-white py-16 text-center">
    <h3 class="text-3xl font-bold mb-6">Let's Connect</h3>
    <p class="text-gray-700 text-lg mb-4">Have a question or need help? We're just a call or email away.</p>
    <p class="text-gray-700"><i class="fas fa-phone-alt"></i> +254 700 123 456</p>
    <p class="text-gray-700"><i class="fas fa-envelope"></i> info@thikarealestate.com</p>
  </section>

  <!-- Floating CTA -->
  <a href="add_listing.php" class="floating-btn text-sm font-medium">
    <i class="fas fa-plus mr-2"></i> Post a Listing
  </a>

  <!-- JavaScript for Register Dropdown -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const registerBtn = document.querySelector(".register-btn");
      const dropdownMenu = document.querySelector(".register-dropdown");

      registerBtn.addEventListener("click", function (event) {
        event.preventDefault();
        dropdownMenu.classList.toggle("hidden");
      });

      document.addEventListener("click", function (event) {
        if (!registerBtn.contains(event.target) && !dropdownMenu.contains(event.target)) {
          dropdownMenu.classList.add("hidden");
        }
      });
    });
  </script>

</body>
</html>
