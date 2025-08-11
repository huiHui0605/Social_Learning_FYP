<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Welcome | Social Learning Platform</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }

    /* Simple fade in animation */
    .fade-in {
      animation: fadeInUp 0.7s ease-out both;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body class="bg-white text-gray-800">

  <!-- NAVIGATION BAR -->
  <header class="w-full bg-white shadow-md fixed top-0 left-0 z-50">
    <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
      <div class="flex items-center gap-4">
        <img src="/image/logo.jpg" class="h-12 w-12 rounded-full shadow-md" alt="Logo">
        <span class="text-xl font-bold text-indigo-700">Social Learning Platform</span>
      </div>
      <div class="flex items-center gap-4">
        <a href="#" id="studentBtn" class="text-sm font-medium px-4 py-1.5 border rounded-full transition hover:bg-green-100 border-green-600 text-green-700">Student</a>
        <a href="#" id="lecturerBtn" class="text-sm font-medium px-4 py-1.5 border rounded-full transition hover:bg-blue-100 border-blue-600 text-blue-700">Lecturer</a>
        @if (Route::has('login'))
          @auth
            <a href="{{ url('/dashboard') }}" class="text-sm font-medium bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">Dashboard</a>
          @else
            <a href="{{ route('login') }}" class="text-sm font-medium text-indigo-700 hover:underline">Login</a>
            @if (Route::has('register'))
              <a href="{{ route('register') }}" class="text-sm font-medium border px-4 py-2 rounded-md border-indigo-500 text-indigo-700 hover:bg-indigo-50 transition">Register</a>
            @endif
          @endauth
        @endif
      </div>
    </div>
  </header>

  <main class="pt-24">

    <!-- HERO SECTION -->
    <section class="bg-gradient-to-br from-indigo-100 to-white">
      <div class="max-w-7xl mx-auto px-6 py-20 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div class="fade-in">
          <h1 id="welcomeTitle" class="text-4xl md:text-5xl font-bold text-green-700 leading-tight">Welcome, Student!</h1>
          <p id="welcomeSubtitle" class="mt-5 text-lg text-gray-700 leading-relaxed">
            Discover new knowledge, collaborate with peers, and track your academic journey. Sign in to access your courses, assignments, and tools.
          </p>
          <a href="{{ route('login') }}" class="mt-6 inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition shadow-lg">Start Now</a>
        </div>
        <div class="fade-in delay-150">
          <img id="welcomeImage" src="/image/student.jpg" alt="Student Illustration" class="rounded-xl shadow-2xl w-full">
        </div>
      </div>
    </section>

    <!-- ABOUT PLATFORM -->
    <section class="py-20 bg-white">
      <div class="max-w-5xl mx-auto text-center px-6">
        <h2 class="text-3xl md:text-4xl font-semibold text-indigo-700 mb-4">About the Social Learning Platform</h2>
        <p class="text-gray-600 text-lg leading-relaxed">
          Our platform empowers students and lecturers through digital education tools. Learn, teach, collaborate, and grow together in one seamless environment.
        </p>
      </div>
    </section>

    <!-- FEATURES SECTION -->
    <section class="bg-indigo-50 py-20">
      <div class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 text-center">
          <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
            <img src="/image/course.png" class="mx-auto h-16 mb-4" alt="Courses">
            <h3 class="font-semibold text-xl mb-2 text-indigo-800">Interactive Courses</h3>
            <p class="text-gray-600">Access rich content, video tutorials, and materials tailored to your learning path.</p>
          </div>
          <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
            <img src="/image/assignment.png" class="mx-auto h-16 mb-4" alt="Assignments">
            <h3 class="font-semibold text-xl mb-2 text-indigo-800">Assignment Tracking</h3>
            <p class="text-gray-600">Submit homework, view grades, and get feedback in real-time from your lecturers.</p>
          </div>
          <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
            <img src="/image/chat.png" class="mx-auto h-16 mb-4" alt="Chat & Community">
            <h3 class="font-semibold text-xl mb-2 text-indigo-800">Collaboration & Support</h3>
            <p class="text-gray-600">Participate in discussions, connect with classmates, and share resources easily.</p>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- FOOTER -->
  <footer class="bg-indigo-700 text-white py-6 mt-12 text-center">
    &copy; 2025 Social Learning Platform. All rights reserved.
  </footer>

  <!-- SWITCH ROLE SCRIPT -->
  <script>
    const welcomeTitle = document.getElementById("welcomeTitle");
    const welcomeSubtitle = document.getElementById("welcomeSubtitle");
    const welcomeImage = document.getElementById("welcomeImage");
    const studentBtn = document.getElementById("studentBtn");
    const lecturerBtn = document.getElementById("lecturerBtn");

    const studentData = {
      title: "Welcome, Student!",
      subtitle: "Discover new knowledge, collaborate with peers, and track your academic journey. Sign in to access your courses, assignments, and tools.",
      image: "/image/student.jpg",
      color: "text-green-700"
    };

    const lecturerData = {
      title: "Welcome, Lecturer!",
      subtitle: "Manage your classes, share knowledge, and support student success. Sign in to post materials, monitor progress, and communicate with students.",
      image: "/image/lecturer.jpg",
      color: "text-blue-700"
    };

    function switchRole(role) {
      const data = role === 'student' ? studentData : lecturerData;
      welcomeTitle.textContent = data.title;
      welcomeSubtitle.textContent = data.subtitle;
      welcomeImage.src = data.image;
      welcomeTitle.className = `text-4xl md:text-5xl font-bold leading-tight ${data.color}`;
    }

    studentBtn.addEventListener('click', () => switchRole('student'));
    lecturerBtn.addEventListener('click', () => switchRole('lecturer'));

    // Default view
    switchRole('student');
  </script>
</body>
</html>
