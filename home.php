<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Viejo Molino - Inicio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="m-0 min-h-screen w-full flex flex-col items-center justify-center font-sans text-white p-6 
             bg-[linear-gradient(rgba(0,0,0,0.3),rgba(0,0,0,0.3)),url('fondo.jpeg')] 
             bg-cover bg-center bg-no-repeat bg-fixed">

    <div class="bg-white p-6 rounded-[2rem] mb-10 shadow-[0_10px_25px_rgba(0,0,0,0.2)]">
        <img src="logo.png" alt="El Viejo Molino" class="w-48 md:w-56 h-auto">
    </div>

    <div class="flex flex-col gap-5 w-full max-w-sm">
        
        <a href="ingresar.php" 
           class="bg-[#8B2522] text-[#FDF28F] tracking-[0.25em] py-4 rounded-full text-center font-bold text-lg uppercase shadow-xl 
                  transition-all duration-300 ease-in-out hover:scale-[1.03] hover:bg-[#FDF28F] hover:text-[#8B2522]
                  hover:shadow-[0_10px_15px_-3px_rgba(0,0,0,0.3)] drop-shadow-[1px_1px_2px_rgba(0,0,0,0.2)]">
            CARPAS
        </a>

        <a href="productos.php" 
           class="bg-[#8B2522] text-[#FDF28F] tracking-[0.25em] py-4 rounded-full text-center font-bold text-lg uppercase shadow-xl 
                  transition-all duration-300 ease-in-out hover:scale-[1.03] hover:bg-[#FDF28F] hover:text-[#8B2522]
                  hover:shadow-[0_10px_15px_-3px_rgba(0,0,0,0.3)] drop-shadow-[1px_1px_2px_rgba(0,0,0,0.2)]">
            PRECIOS CANTINA
        </a>

        <a href="notas.php" 
           class="bg-[#8B2522] text-[#FDF28F] tracking-[0.25em] py-4 rounded-full text-center font-bold text-lg uppercase shadow-xl 
                  transition-all duration-300 ease-in-out hover:scale-[1.03] hover:bg-[#FDF28F] hover:text-[#8B2522]
                  hover:shadow-[0_10px_15px_-3px_rgba(0,0,0,0.3)] drop-shadow-[1px_1px_2px_rgba(0,0,0,0.2)]">
            NOTAS
        </a>

        <a href="reporte_diario.php" 
           class="bg-[#8B2522] text-[#FDF28F] tracking-[0.25em] py-4 rounded-full text-center font-bold text-lg uppercase shadow-xl 
                  transition-all duration-300 ease-in-out hover:scale-[1.03] hover:bg-[#FDF28F] hover:text-[#8B2522]
                  hover:shadow-[0_10px_15px_-3px_rgba(0,0,0,0.3)] drop-shadow-[1px_1px_2px_rgba(0,0,0,0.2)]">
            REPORTE DIARIO
        </a>

    </div>

    <div class="mt-12 flex gap-10">
        <a href="https://www.instagram.com/el.viejo.molino" target="_blank" 
           class="text-[#8B2522] hover:text-[#FDF28F] text-5xl hover:scale-110 transition-transform duration-300 drop-shadow-lg">
            <i class="fab fa-instagram"></i>
        </a>
        
        <a href="https://wa.me/5493442575039" target="_blank" 
           class="text-[#8B2522] hover:text-[#FDF28F] text-5xl hover:scale-110 transition-transform duration-300 drop-shadow-lg">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>

</body>
</html>