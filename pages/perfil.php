<?php
session_start();
?>
<!DOCTYPE html>
<html lang="gl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <title>TransMiño</title>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>
    <header>
        <div id="logo">
            <a href="../index.php"><img src="../images/logo_trans.png"/></a>
        </div>
        <div id="header_content">
            <div id="title">
                TransMiño: Transportes do Miño S.A.
            </div>
            <div id="username"></div>
            <nav id="nav">
                <a id="mobile_menu" href="javascript:void(0);" class="menu_element" onclick="mobileMenu()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </a>
                <div id="links">
                    <span><a class="menu_element" href="../pages/tarifas.php">Tarifas</a></span>
                    <span><a class="menu_element" href="../pages/horarios.php">Horarios</a></span>
                    <span><a class="menu_element" href="../pages/atencion.php">Atención al cliente</a></span>
                    <?php if(!isset($_SESSION["email"])) { ?>
                    <span><a class="menu_element" href="../pages/sesion.php">Iniciar sesión</a></span>
                    <?php } else { ?>
                    <span><a class="menu_element" href="../pages/perfil.php">Mi perfil</a></span>
                    <span><a class="menu_element" href="../functions/sesion.php?session=close">Cerrar sesión</a></span>
                    <?php } ?>
                    <?php if(isset($_SESSION["rol"])&&$_SESSION["rol"]==1){ ?>
                    <span><a class="menu_element" href="../pages/admin.php">Administración</a></span>
                    <?php } ?>
                </div>
            </nav>
        </div>
    </header>
    <div id="container">
        <section id="profile">
        <?php if(isset($_SESSION["email"])){ ?>
            <form action="../functions/perfil.php" method="post"enctype="multipart/form-data">
                <h2>Información del perfil</h2>
                <span>
                    <div id="profile_img"></div>
                    <input class="form-control" type="file" name="subir_img" id="subir_img" style="display: none;" accept="image/png, image/jpeg, image/gif">
                </span>
                <span>
                    <span id="nombre">Nombre:</span>
                </span>
                <span>
                    <span id="apellidos">Apellidos:</span>
                </span>
                <span>
                    <span id="email">E-mail:</span>
                </span>
                <span>
                    <span id="dni">DNI:</span>
                </span>
                <span>
                    <span id="fecha_nacimiento">Fecha de nacimiento:</span>
                </span>
                <span>
                    <span id="telefono">Teléfono:</span>
                </span>
                <span>
                    <span id="direccion">Dirección:</span>
                </span>
                <span>
                    <button id="modificar" type="button" class="form-control">Modificar perfil</button>
                </span>
                <span>
                    <button id="aceptar" type="submit" class="form-control" style="display: none;">Aplicar cambios</button>
                    <button id="cancelar" type="button" class="form-control" style="display: none;">Cancelar</button>
                </span>
            </form>
            <div id="profile_tables">
                <table class="table" id="profile_table">
                    <thead>
                        <tr>
                            <th>Últimas modificaciones de tu perfil:</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <table class="table" id="points_table">
                    <thead>
                        <tr>
                            <th>Últimas transacciones de puntos:</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <?php } else { ?>
                <div class="error">Acceso denegado.</div>
            <?php } ?>
        </section>
        <aside>
            <h2>Noticias</h2>
            <div></div>
            <a class="menu_element" href="pages/noticias.php">Ver noticias anteriores</a>
        </aside>
    </div>
    <footer>
        <div id="copy">&copy; TransMiño Transportes do Miño S.A. 2022</div>
        <div id="social">
            <a href="#"><img src="../images/twitter.png" alt="Twitter"></a>
            <a href="#"><img src="../images/instagram.png" alt="Instagram"></a>
            <a href="#"><img src="../images/facebook.png" alt="Facebook"></a>
        </div>
    </footer>
    <script src="../js/script.js"></script>
    <script src="../js/script_perfil.js"></script>
    <?php if(isset($_GET['error'])){ ?>
        <script> alert("<?= $_GET['error'] ?>") </script>
    <?php } ?>
</body>
</html>