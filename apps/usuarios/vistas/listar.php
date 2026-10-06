<?php
use apps\usuarios\controladores\UsuarioController;
$usuarioController = new UsuarioController();
$usuarios = $usuarioController->listarUsuarios();
?>



                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                     
                    <h1 class="h3 mb-4 text-gray-800">Usuarios</h1>
                    <a href="index.php?modulo=usuarios&accion=registro" class="btn btn-primary btn-user btn-block">
                        Registrar Usuario
                    </a>

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
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Listado de Usuarios</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nombre Completo</th>
                                            <th>Rol</th>
                                            <th>Correo</th>
                                            <th>estado</th>
                                            <th>Se unio:</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Nombre Completo</th>
                                            <th>Rol</th>
                                            <th>Correo</th>
                                            <th>estado</th>
                                            <th>Se unio:</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </tfoot>
                                    <?php if(!empty($usuarios)): ?>
                                    <tbody>
                                        <?php foreach($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($usuario['nombre_completo']); ?></td>
                                            <td><?php echo htmlspecialchars($usuario['role_nombre']); ?></td>
                                            <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                                            <td><?php echo htmlspecialchars($usuario['estado'] ? 'Activo' : 'Inactivo'); ?></td>
                                            <td><?php echo htmlspecialchars($usuario['created_at']); ?></td>
                                            <td class="d-flex flex-row">

                                                <form action="index.php?modulo=usuarios&accion=procesar" method="post"><input type="hidden" name="usuario_id" value="<?php echo $usuario['id_usuario']; ?>"><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"><button type="submit" class="btn btn-sm btn-primary m-2">Editar</button></form>
                                                <form action="index.php?modulo=usuarios&accion=eliminar" method="post"><input type="hidden" name="usuario_id" value="<?php echo $usuario['id_usuario']; ?>"><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"><button type="submit" class="btn btn-sm btn-danger m-2">Eliminar</button></form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                <?php else: ?>
                                    <tbody>
                                        <tr>
                                            <td colspan="6" class="text-center">No se encontraron usuarios.</td>
                                        </tr>
                                    </tbody>
                                <?php endif; ?>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->