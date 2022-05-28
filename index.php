<?php
session_start();
?>
<!DOCTYPE html>
<html lang="gl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>TransMiño</title>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <div id="logo">
            <a href="#"><img src="images/logo_trans.png"/></a>
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
                    <span><a class="menu_element" href="pages/tarifas.php">Tarifas</a></span>
                    <span><a class="menu_element" href="pages/horarios.php">Horarios</a></span>
                    <span><a class="menu_element" href="pages/atencion.php">Atención al cliente</a></span>
                    <?php if(!isset($_SESSION["email"])) { ?>
                    <span><a class="menu_element" href="pages/sesion.php">Iniciar sesión</a></span>
                    <?php } else { ?>
                    <span><a class="menu_element" href="pages/perfil.php">Mi perfil</a></span>
                    <span><a class="menu_element" href="functions/sesion.php?session=close">Cerrar sesión</a></span>
                    <?php } ?>
                    <?php if(isset($_SESSION["rol"])&&$_SESSION["rol"]==1){ ?>
                    <span><a class="menu_element" href="pages/admin.php">Administración</a></span>
                    <?php } ?>
                </div>
            </nav>
        </div>
    </header>
        <div id="container">
            <section>
                <div id="carouselIndex" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="images/bus_1.png" class="d-block w-100" alt="Autobus en la carretera">
                        </div>
                        <div class="carousel-item">
                            <img src="images/bus_2.png" class="d-block w-100" alt="Asientos del autobus">
                        </div>
                        <div class="carousel-item">
                            <img src="images/bus_3.png" class="d-block w-100" alt="Conductor del autobus">
                        </div>
                        <div class="carousel-item">
                            <img src="images/vigo.png" class="d-block w-100" alt="Ciudad de Vigo">
                        </div>
                        <div class="carousel-item">
                            <img src="images/bridge_tui.png" class="d-block w-100" alt="Puente de Tui">
                        </div>
                        <div class="carousel-item">
                            <img src="images/oporto.png" class="d-block w-100" alt="Ciudad de Oporto">
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselIndex" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselIndex" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            <h2>Comprar billetes</h2>
            <form action="functions/compra.php" method="post" onsubmit="return validarCompra()">
                <div class="formarea">
                    <span id="formarea_ida">
                        <input class="form-check-input" type="radio" id="i" name="trayecto" value="i">
                        <label class="form-check-label" for="i">Ida</label>
                    </span> 
                    <span class="formarea_vuelta">
                        <input class="form-check-input" type="radio" id="iv" name="trayecto" value="iv" checked>
                        <label class="form-check-label" for="iv">Ida y vuelta</label>
                    </span>
                </div>
                <div class="formarea">
                    <span id="formarea_origen">
                        <label for="origen">Origen</label><br/>
                        <select class="form-select form-select-sm" id="origen" name="origen"></select>
                    </span>
                    <span id="formarea_destino">
                        <label for="destino">Destino</label><br/>
                        <select class="form-select form-select-sm" id="destino" name="destino"></select>
                    </span>
                </div>
                <div class="formarea">
                    <span id="formarea_fechaida">
                        <label for="ida">Fecha de ida:</label><br/>
                        <input class="form-control" type="date" id="ida" name="ida" />
                    </span>
                    <span id="formarea_fechavuelta">
                        <label for="vuelta">Fecha de vuelta:</label><br/>
                        <input class="form-control" type="date" id="vuelta" name="vuelta" />
                    </span>
                </div>
                <div class="formarea">
                    <span id="formarea_anc">
                        <label for="anc">Ancianos (+65)</label><br/>
                        <input class="form-control" type="number" id="anc" name="anc" value="0" min="0"/>
                    </span>
                    <span id="formarea_adu">
                        <label for="adu">Adultos (16-64)</label><br/>
                        <input class="form-control" type="number" id="adu" name="adu" value="0" min="0"/>
                    </span>
                    <span id="formarea_jov">
                        <label for="jov">Jóvenes (-15)</label><br/>
                        <input class="form-control" type="number" id="jov" name="jov" value="0" min="0"/>
                    </span>
                </div>
                <div class="formarea">
                    <span id="formarea_conf">
                        <input class="form-control submit" type="submit" id="submit" name="step" value="Comprar" />
                    </span>
                </div>
            </form>
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
            <a href="#"><img src="images/twitter.png" alt="Twitter"></a>
            <a href="#"><img src="images/instagram.png" alt="Instagram"></a>
            <a href="#"><img src="images/facebook.png" alt="Facebook"></a>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script src="js/script_index.js"></script>
    <script>
        $(function(){
    <?php if(isset($_SESSION["nombre"])){ ?>
        sessionStorage.setItem('nombre', '<?=$_SESSION["nombre"];?>');
    <?php } else { ?>
        sessionStorage.removeItem('nombre');
    <?php } ?>
        });
    </script>
</body>
</html>