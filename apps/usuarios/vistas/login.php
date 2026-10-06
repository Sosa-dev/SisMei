<?php
// Aseguramos que la sesión esté activa para poder leer los errores y el token CSRF
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si por alguna razón el token CSRF no existe en la sesión del usuario, lo generamos de una vez
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Escuela Bíblica MEI · NJCP - Iniciar sesión</title>

    <link href="/SisInventarioVentas/public/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <link href="/SisInventarioVentas/public/css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Escuela Bíblica MEI</h1>
                                        <p class="text-muted mb-4">Niños y Jóvenes con Propósito (NJCP)</p>
                                    </div>

                                    <?php if (isset($_SESSION['error_mensaje'])): ?>
                                        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            <?php 
                                                echo $_SESSION['error_mensaje']; 
                                                unset($_SESSION['error_mensaje']); // Lo borramos para que no vuelva a aparecer al recargar
                                            ?>
                                            <button type="text" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    <?php endif; ?>

                                    <form class="user" action="index.php?modulo=usuarios&accion=login" method="POST" autocomplete="off">
                                        
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user"
                                                id="exampleInputEmail" name="correo" aria-describedby="emailHelp"
                                                placeholder="Introduce tu correo electrónico..." required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="exampleInputPassword" name="contrasena" placeholder="Contraseña" required>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Iniciar Sesión
                                        </button>
                                        <hr>
                                        
                                    </form>
                                    <hr>
                                    
                                    <div class="text-center">
                                        <a class="small" href="index.php?modulo=usuarios&accion=registro">¡Crea una cuenta!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script src="/SisInventarioVentas/public/vendor/jquery/jquery.min.js"></script>
    <script src="/SisInventarioVentas/public/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="/SisInventarioVentas/public/vendor/jquery-easing/jquery.easing.min.js"></script>

    <script src="/SisInventarioVentas/public/js/sb-admin-2.min.js"></script>

</body>

</html>