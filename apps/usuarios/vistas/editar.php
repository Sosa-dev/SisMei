<div class="container">
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                
                <div class="col-lg-7">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">
                                <i class="fas fa-user-edit mr-2"></i>Editar usuario
                            </h1>
                        </div>
                        
                        <form class="user" method="POST" action="index.php?modulo=usuarios&accion=editar" autocomplete="off">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div class="form-group">
                                <label class="text-xs font-weight-bold text-primary text-uppercase mb-1" for="nombre_completo">Nombre Completo</label>
                                <input type="text" class="form-control form-control-user" id="nombre_completo"
                                       placeholder="Ej. Juan Pérez" name="nombre_completo" 
                                       value="<?php echo htmlspecialchars($_SESSION['usuario_editar']['nombre_completo']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="text-xs font-weight-bold text-primary text-uppercase mb-1" for="id_rol">Rol asignado</label>
                                <select class="form-control custom-select-user" id="id_rol" name="id_rol" required>
                                    <option value="" selected disabled>Selecciona un rol...</option>
                                    <option value="1" <?php echo ($_SESSION['usuario_editar']['id_rol'] == 1) ? 'selected' : ''; ?>>Administrador</option>
                                    <option value="4" <?php echo ($_SESSION['usuario_editar']['id_rol'] == 4) ? 'selected' : ''; ?>>Docente</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="text-xs font-weight-bold text-primary text-uppercase mb-1" for="correo">Correo Electrónico</label>
                                <input type="email" class="form-control form-control-user" id="correo"
                                       placeholder="usuario@correo.com" name="correo" 
                                       value="<?php echo htmlspecialchars($_SESSION['usuario_editar']['correo']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="text-xs font-weight-bold text-primary text-uppercase mb-1" for="estado">Estado del Usuario</label>
                                <select class="form-control custom-select-user" id="estado" name="estado" required>
                                    <option value="" disabled>Seleccionar Estado</option>
                                    <option value="1" <?php echo ($_SESSION['usuario_editar']['estado'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                    <option value="0" <?php echo ($_SESSION['usuario_editar']['estado'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                </select>
                            </div>
                            
                            <hr>
                            
                            <button type="submit" class="btn btn-success btn-user btn-block shadow-sm">
                                <i class="fas fa-save mr-2"></i>Guardar Cambios
                            </button>

                            <a href="index.php?modulo=usuarios&accion=listar" class="btn btn-secondary btn-user btn-block shadow-sm mt-2">
                                <i class="fas fa-arrow-left mr-2"></i>Regresar al listado
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>