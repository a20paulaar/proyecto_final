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
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                </a>
                <div id="links">
                    <span><a class="menu_element" href="pages/tarifas.php">Tarifas</a></span>
                    <span><a class="menu_element" href="pages/horarios.php">Horarios</a></span>
                    <span><a class="menu_element" href="pages/atencion.php">Atención al cliente</a></span>
                    <?php if(!isset($_SESSION)) { ?>
                    <span><a class="menu_element" href="pages/sesion.php">Iniciar sesión</a></span>
                    <?php } else { ?>
                    <span><a class="menu_element" href="../pages/perfil">Mi perfil</a></span>
                    <span><a class="menu_element" href="functions/sesion.php?session=close">Cerrar sesión</a></span>
                    <?php } ?>
                    <?php if(isset($_SESSION)&&$_SESSION["rol"]==2){ ?>
                    <span><a class="menu_element" href="pages/admin.php">Administración</a></span>
                    <?php } ?>
                </div>
            </nav>
        </div>
    </header>
    <div id="container">
        <section>
            <h2>Comprar billetes</h2>
            <form action="comprar.php" method="post" onsubmit="return validarCompra()">
                <div id="ida">
                    <h3>Trayecto de ida</h3>
                    <div class="formarea">
                        <table id="t_ida">
                        </table>
                    </div>
                    <div class="formarea">
                        <span id="formarea_ida">
                            <label class="form-check-label" for="a_ida">Asientos disponibles:</label>
                            <select id="a_ida" name="a_ida">
                                <option>Por favor, seleccione trayecto</option>
                            </select>
                            <a href="../images/seats.png" target="_blank">Guía de asientos</a>
                        </span>
                    </div>
                </div>
                <div id="vuelta">
                    <h3>Trayecto de vuelta</h3>
                    <div class="formarea">
                        <table id="t_vuelta">
                        </table>
                    </div>
                    <div class="formarea">
                        <span id="formarea_vuelta">
                            <label class="form-check-label" for="a_vuelta">Asientos disponibles:</label>
                            <select id="a_vuelta" name="a_vuelta">
                                <option>Por favor, seleccione trayecto</option>
                            </select>
                            <a href="../images/seats.png" target="_blank">Guía de asientos</a>
                        </span>
                    </div>
                </div>
                <div class="formarea">
                    <span>
                        <label for="pago">Seleccione una forma de pago:</label>
                        <select name="pago" id="pago">
                            <option value="PayPal">PayPal</option>
                            <option value="Tarjeta">Tarjeta de crédito / débito</option>
                            <option value="Bizum">Bizum</option>
                        </select>
                    </span>
                </div>
                <div class="formarea">
                    <span id="formarea_conf">
                        <input class="form-control submit" type="submit" id="submit" value="Comprar" />
                    </span>
                </div>
            </form>
        </section>
        <aside>
            <h2>Noticias</h2>
            <div></div>
            <!-- <a class="menu_element" href="#">Ver noticias anteriores</a> -->
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
    <script src="../js/script_compra.js"></script>
    <script>
        //VIA JS
        // var cookie_traveldata = (document.cookie.split(';').filter(c => c.startsWith('traveldata_i')))[0];
        // var json_traveldata = JSON.parse(decodeURIComponent(cookie_traveldata).replace(/traveldata_i=/g,''));
        // var cookie_traveldata = (document.cookie.split(';').filter(c => c.startsWith('traveldata_v')))[0];
        // var json_traveldata = JSON.parse(decodeURIComponent(cookie_traveldata).replace(/traveldata_v=/g,''));
        //VIA PHP+JS
        // var json_traveldata = JSON.parse('<?= $_COOKIE["traveldata_i"] ?>');
        // console.log(json_traveldata);
        // var json_traveldata = JSON.parse('<?= $_COOKIE["traveldata_v"] ?>');
        // console.log(json_traveldata);

        <? if(array_key_exists('traveldata_v', $_COOKIE)){ ?>
            gestionarDatosCompra(JSON.parse('<?= $_COOKIE["traveldata_i"] ?>'), JSON.parse('<?= $_COOKIE["traveldata_v"] ?>'));
        <? } else { ?>
            gestionarDatosCompra(JSON.parse('<?= $_COOKIE["traveldata_i"] ?>'));
        <? } ?>
    </script>
</body>
</html>