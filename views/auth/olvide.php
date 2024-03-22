<div class="contenedor olvide">
<?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>
    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recupera tu contraseña de Uptask</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
        <form action="/olvide" method="POST" class="formulario" novalidate> <!-- es enviado a la misma pagina el formulario -->
            <div class="campo">
                <label for="email">Tu Email</label>
                <input type="email" id="email" placeholder="Tu Email" name="email">
            </div>

            <input type="submit" class="boton" value="Enviar instrucciones">
        </form>
        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Regístrate</a>
            <a href="/">¿Ya tienes cuenta? Inicia Sesión</a>
        </div>

    </div> <!--.contenedor-sm -->
</div>