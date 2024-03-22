<div class="contenedor reestablecer">
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>
    <div class="contenedor-sm">
        
        <p class="descripcion-pagina">Coloca tu nueva contraseña</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
        <?php if($mostrar){ ?>
        <form method="POST" class="formulario"> <!-- es enviado a la misma pagina el formulario, no tiene action porque quita el token -->
            <div class="campo">
                <label for="password">Password</label>
                <input type="password" id="password" placeholder="Tu Nueva Contraseña" name="password">
            </div>
            <input type="submit" class="boton" value="Guardar Contraseña">
        </form>
        <?php } ?>
        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Regístrate</a>
            <a href="/olvide">¿Olvidaste tu contraseña?</a>
        </div>

    </div> <!--.contenedor-sm -->
</div>