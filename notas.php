<?php
include 'conexion.php';

// Consultar todas las notas
$query = "SELECT * FROM notas_administrativas ORDER BY fecha_modificacion DESC";
$resultado = $mysqli->query($query);

// Si se pasa un ID por URL, cargamos esa nota para editar
$nota_editar = null;
if (isset($_GET['id'])) {
    $id_edit = intval($_GET['id']);
    $res_edit = $mysqli->query("SELECT * FROM notas_administrativas WHERE id = $id_edit");
    $nota_editar = $res_edit->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Notas Administrativas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #5d3129; }
        .bg-card-yellow { background-color: #f1e695; }
        .bg-input-cream { background-color: #fffdf0; }
        .btn-brown { background-color: #7d3c3c; }
        .text-brown { color: #5d3129; }
        .custom-scroll::-webkit-scrollbar { width: 4px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #7d3c3c30; border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen p-4 flex justify-center items-center bg-[#6C2E2C]">

    <div class="bg-[#FDF28F] w-full max-w-6xl h-[85vh] rounded-[45px] shadow-2xl flex overflow-hidden border-8 border-yellow-200/20">
        
        <div class="w-1/3 border-r border-yellow-600/10 flex flex-col bg-yellow-50/20">
            <div class="p-6">
                <a href="home.php" class="btn-brown text-white px-4 py-2 rounded-full text-xs font-bold shadow-md hover:bg-red-900 transition inline-flex items-center mb-6">
                    <i class="fas fa-chevron-left mr-2"></i> INICIO
                </a>
                
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold text-brown italic">Mis Notas</h2>
                    <a href="notas.php" title="Nueva Nota" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-brown shadow-sm hover:scale-110 transition">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
            </div>

            <div class="overflow-y-auto flex-1 px-4 pb-4 space-y-3 custom-scroll">
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while($n = $resultado->fetch_assoc()): ?>
                        <a href="notas.php?id=<?php echo $n['id']; ?>" class="block">
                            <div class="p-4 rounded-[25px] transition shadow-sm border-2 <?php echo ($nota_editar && $nota_editar['id'] == $n['id']) ? 'bg-white border-red-800/20 shadow-md' : 'bg-input-cream border-transparent hover:bg-white'; ?>">
                                <h3 class="font-bold text-brown truncate text-sm"><?php echo htmlspecialchars($n['titulo']); ?></h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] text-gray-400"><?php echo date('d/m', strtotime($n['fecha_creacion'])); ?></span>
                                    <p class="text-[11px] text-gray-500 truncate"><?php echo htmlspecialchars(substr($n['contenido'], 0, 45)); ?>...</p>
                                </div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="opacity-20 text-brown mt-20 text-center">
                        <i class="fas fa-sticky-note text-5xl mb-2"></i>
                        <p class="text-sm font-bold tracking-widest uppercase">Sin notas</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="w-2/3 flex flex-col bg-white/10">
            <form action="procesar_nota.php" method="POST" class="h-full flex flex-col">
                <input type="hidden" name="id" value="<?php echo $nota_editar['id'] ?? ''; ?>">

                <div class="p-6 flex justify-between items-center">
                    <span class="text-[10px] font-bold text-brown/50 uppercase tracking-widest">
                    </span>
                    <div class="flex gap-4">
                        <?php if($nota_editar): ?>
                            <a href="eliminar_nota.php?id=<?php echo $nota_editar['id']; ?>" onclick="return confirm('¿Eliminar esta nota?')" class="text-brown hover:text-red-700 p-2 transition">
                                <i class="fas fa-trash-can text-lg"></i>
                            </a>
                        <?php endif; ?>
                        <button type="submit" class="btn-brown text-white px-8 py-2 rounded-full font-bold text-sm shadow-lg hover:brightness-110 transition">
                            GUARDAR
                        </button>
                    </div>
                </div>

                <div class="flex-1 px-8 pb-8">
                    <div class="h-full bg-input-cream rounded-[35px] p-10 shadow-inner flex flex-col">
                    <input type="text" name="titulo" 
                        class="w-full bg-transparent border-none focus:outline-none focus:ring-0 text-3xl font-extrabold text-brown mb-6 placeholder:text-brown/10 p-6" 
                        placeholder="Nombre o título..." required
                        value="<?php echo htmlspecialchars($nota_editar['titulo'] ?? ''); ?>">
                    <textarea name="contenido" 
                        class="w-full h-full bg-transparent resize-none border-none focus:outline-none focus:ring-0 text-gray-700 leading-relaxed text-lg p-6"
                        placeholder="Escribí acá..."><?php echo htmlspecialchars($nota_editar['contenido'] ?? ''); ?></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
</html>