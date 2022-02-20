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
            <nav id="nav">
                <a id="mobile_menu" href="javascript:void(0);" class="menu_element" onclick="mobileMenu()">
                    <i class="fa fa-bars">MENÚ</i>
                </a>
                <div id="links">
                    <span><a class="menu_element" href="#">Tarifas</a></span>
                    <span><a class="menu_element" href="../pages/horarios.php">Horarios</a></span>
                    <span><a class="menu_element" href="../pages/atencion.php">Atención al cliente</a></span>
                    <span><a class="menu_element" href="../pages/sesion.php">Iniciar sesión</a></span>
                    <span><a class="menu_element" href="#">Cerrar sesión</a></span>
                    <span><a class="menu_element" href="../pages/admin.php">Administración</a></span>
                </div>
            </nav>
        </div>
    </header>
    <div id="container">
        <section>
            <h2>Atención al Cliente</h2>
            <form action="#" method="post">
                    <div class="formsession">
                        <label for="nombre">Nombre </label>
                        <input class="form-control" type="text" name="nombre" id="nombre">
                    </div>
                    <div class="formsession">
                        <label for="email">E-mail: </label>
                        <input class="form-control" type="email" name="email" id="email">
                    </div>
                    <div class="formsession">
                        <label for="pass">Mensaje: </label>
                        <textarea name="mensaje" id="mensaje" class="form-control" placeholder="Introduce aquí tus comentarios, dudas o quejas."></textarea>                    
                    </div>
                    <div class="formsession">
                        <input class="form-control" type="submit" id="sesion" value="Enviar" />
                    </div>
            </form>
            <a class="menu_element" href="../html/registro.html">También puedes ponerte en contacto con nosotros a través de nuestras redes sociales.</a>
        </section>
        <aside>
            <h2>Noticias</h2>
            <!-- TODO bucle PHP por cada noticia, limitado a 3, usa query en una tabla de noticias -->
            <article>
                <img src="#" alt="Noticia 1">
                <h4><a class="menu_element" href="noticias.php#art-3">Título</a></h4>
                <p>Cuerpo de la noticia</p>
            </article>
            <article>
                <img src="#" alt="Noticia 2">
                <h4><a class="menu_element" href="noticias.php#art-2">Título</a></h4>
                <p>Cuerpo de la noticia</p>
            </article>
            <article>
                <img src="#" alt="Noticia 3">
                <h4><a class="menu_element" href="noticias.php#art-1">Título</a></h4>
                <p>Cuerpo de la noticia</p>
            </article>
            <a class="menu_element" href="#">Ver noticias anteriores</a>
        </aside>
    </div>
    <footer>
        &copy; TransMiño Transportes do Miño S.A. 2022
    </footer>

    <script src="../js/script.js"></script>
</body>
</html>