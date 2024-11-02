<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redireccionamiento</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

    <!-- Modal -->
    <div class="modal fade" id="redirectModal" tabindex="-1" aria-labelledby="redirectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="redirectModalLabel">¿A dónde quieres ir?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Selecciona la sección a la que deseas acceder:</p>
                    <div class="d-grid gap-2">
                        <a href="asistencia_profesor.php" class="btn btn-primary">Asistencia</a>
                        <a href="notas_profesor.php" class="btn btn-secondary">Notas</a>
                        <a href="primero_a.php" class="btn btn-success">Materias</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar el modal al cargar la página
        window.onload = function () {
            var redirectModal = new bootstrap.Modal(document.getElementById('redirectModal'), {
                backdrop: 'static', // Evita que se cierre al hacer clic fuera del modal
                keyboard: false // Evita que se cierre al presionar Esc
            });
            redirectModal.show();
        };
    </script>

</body>
</html>
