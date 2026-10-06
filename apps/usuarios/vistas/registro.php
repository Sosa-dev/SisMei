<div class="container">
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                
                <div class="col-lg-7">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4"><i class="fas fa-user-plus mr-2"></i>Agregar nuevo usuario</h1>
                        </div>
                        
                        <form class="user" method="POST" action="index.php?modulo=usuarios&accion=registro" autocomplete="off">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            
                            <div class="form-group">
                                <label class="text-xs font-weight-bold text-primary text-uppercase mb-1" for="nombre_completo">Nombre Completo</label>
                                <input type="text" class="form-control form-control-user" id="nombre_completo"
                                       placeholder="Ej. Juan Pérez" name="nombre_completo" required>
                            </div>

                            <div class="form-group">
                                <label class="text-xs font-weight-bold text-primary text-uppercase mb-1" for="id_rol">Rol asignado</label>
                                <select class="form-control custom-select-user" id="id_rol" name="id_rol" required>
                                    <option value="" selected disabled>Selecciona un rol...</option>
                                    <option value="1">Administrador</option>
                                    <option value="4">Docente</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="text-xs font-weight-bold text-primary text-uppercase mb-1" for="correo">Correo Electrónico</label>
                                <input type="email" class="form-control form-control-user" id="correo"
                                       placeholder="usuario@correo.com" name="correo" required>
                            </div>

                            <div class="form-group">
                                <label class="text-xs font-weight-bold text-primary text-uppercase mb-1" for="contrasena">Contraseña</label>
                                <input type="password" class="form-control form-control-user" id="contrasena" 
                                       placeholder="********" name="contrasena" required>
                            </div>
                            
                            <hr>
                            
                            <button type="submit" class="btn btn-primary btn-user btn-block shadow-sm">
                                <i class="fas fa-save mr-2"></i>Registrar Usuario
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>