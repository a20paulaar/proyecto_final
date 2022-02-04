<!DOCTYPE html>
<html lang="gl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>TransMiño</title>
</head>
<body>
    <header>
        <div id="logo">
            <a href="index.php"><img src="images/logo.png"/></a>
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
                    <span><a class="menu_element" href="#">Horarios</a></span>
                    <span><a class="menu_element" href="#">Mapa</a></span>
                    <span><a class="menu_element" href="#">Atención al cliente</a></span>
                    <span><a class="menu_element" href="#">Iniciar sesión</a></span>
                </div>
            </nav>
        </div>
    </header>
    <div id="container">
        <section>
            <h2>Comprar billetes</h2>
            <form action="comprar.php" method="post">
                <div class="formarea">
                    <span id="formarea_ida">
                        <input type="radio" id="i" name="trayecto" value="Ida">
                        <label for="i">Ida</label>
                    </span>
                    <span class="formarea_vuelta">
                        <input type="radio" id="iv" name="trayecto" value="Vuelta" checked>
                        <label for="iv">Ida y vuelta</label>
                    </span>
                </div>
                <div class="formarea">
                    <span id="formarea_origen">
                        <label for="origen">Origen</label><br/>
                        <select id="origen"></select>
                    </span>
                    <span id="formarea_destino">
                        <label for="destino">Destino</label><br/>
                        <select id="destino"></select>
                    </span>
                </div>
                <div class="formarea">
                    <span id="formarea_fechaida">
                        <label for="ida">Fecha de ida:</label><br/>
                        <input type="date" id="ida" name="ida" />
                    </span>
                    <span id="formarea_fechavuelta">
                        <label for="vuelta">Fecha de vuelta:</label><br/>
                        <input type="date" id="vuelta" name="vuelta" />
                    </span>
                </div>
                <div class="formarea">
                    <span id="formarea_anc">
                        <label for="anc">Ancianos (+65)</label><br/>
                        <input type="number" id="anc" value="0" />
                    </span>
                    <span id="formarea_adu">
                        <label for="adu">Adultos (16-64)</label><br/>
                        <input type="number" id="adu" value="0" />
                    </span>
                    <span id="formarea_jov">
                        <label for="jov">Jóvenes (-15)</label><br/>
                        <input type="number" id="jov" value="0" />
                    </span>
                </div>
                <div class="formarea">
                    <span id="formarea_conf">
                        <input type="submit" id="submit" value="Comprar" />
                    </span>
                </div>
            </form>
        </section>
        <aside>
            <h2>Noticias</h2>
            <!-- TODO bucle PHP por cada noticia, limitado a 3, usa query en una tabla de noticias -->
            <article>
                <h4><a class="menu_element" href="noticias.php#art-3">Título</a></h4>
                <p>Cuerpo de la noticia</p>
            </article>
            <article>
                <h4><a class="menu_element" href="noticias.php#art-2">Título</a></h4>
                <p>Cuerpo de la noticia</p>
            </article>
            <article>
                <h4><a class="menu_element" href="noticias.php#art-1">Título</a></h4>
                <p>Cuerpo de la noticia</p>
            </article>
            <a class="menu_element" href="#">Ver noticias anteriores</a>
        </aside>
    </div>
    <footer>
        &copy; TransMiño Transportes do Miño S.A. 2022
    </footer>

    <script src="js/script.js"></script>
</body>
</html>