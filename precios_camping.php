<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Viejo Molino</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="m-0 h-screen w-full flex items-center justify-center p-12 bg-[#6C2E2C]">

    <!-- BOTÓN VOLVER -->
    <div class="absolute top-6 left-6 z-50">
        <a href="home.php" 
           class="bg-[#8f2f2f] text-white px-6 py-3 rounded-full 
                  hover:bg-[#6e1f1f] cursor-pointer font-extrabold 
                  transition duration-150 shadow-xl flex items-center">
            <i class="fas fa-chevron-left mr-2"></i> INICIO
        </a>
    </div>

    <!-- CONTENEDOR CARRUSEL -->
    <div class="relative w-[90vw] h-[85vh] overflow-hidden rounded-3xl ">

        <!-- SLIDES -->
        <div id="carousel" class="flex h-full transition-transform duration-700 ease-in-out">

            <div class="w-full h-full flex items-center justify-center shrink-0">
                <img src="camping.jpg" class="h-full max-w-[85vh] object-contain">
            </div>

            <div class="w-full h-full flex items-center justify-center shrink-0">
                <img src="acceso.jpg" class="h-full max-w-[85vh] object-contain">
            </div>

            <div class="w-full h-full flex items-center justify-center shrink-0">
                <img src="bungalows.jpg" class="h-full max-w-[85vh] object-contain">
            </div>

        </div>

        <!-- BOTÓN IZQUIERDA -->
        <button onclick="prevSlide()" 
                class="absolute left-6 top-1/2 -translate-y-1/2 
                       bg-black/50 text-[#FDF28F] w-14 h-14 rounded-full 
                       flex items-center justify-center text-2xl 
                       hover:bg-black/70 transition z-40">
            <i class="fas fa-chevron-left"></i>
        </button>

        <!-- BOTÓN DERECHA -->
        <button onclick="nextSlide()" 
                class="absolute right-6 top-1/2 -translate-y-1/2 
                       bg-black/50 text-[#FDF28F] w-14 h-14 rounded-full 
                       flex items-center justify-center text-2xl 
                       hover:bg-black/70 transition z-40">
            <i class="fas fa-chevron-right"></i>
        </button>

    </div>

    <!-- SCRIPT -->
    <script>
        let index = 0;
        const carousel = document.getElementById("carousel");
        const totalSlides = carousel.children.length;

        function updateCarousel() {
            carousel.style.transform = `translateX(-${index * 100}%)`;
        }

        function nextSlide() {
            index = (index + 1) % totalSlides;
            updateCarousel();
        }

        function prevSlide() {
            index = (index - 1 + totalSlides) % totalSlides;
            updateCarousel();
        }
    </script>

</body>
</html>
