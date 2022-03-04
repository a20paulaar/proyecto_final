<!DOCTYPE html>
<html lang="gl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/style_admin.css">
    <title>TransMiño</title>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
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
                    <span><a class="menu_element" href="../pages/tarifas.php">Tarifas</a></span>
                    <span><a class="menu_element" href="../pages/horarios.php">Horarios</a></span>
                    <span><a class="menu_element" href="../pages/atencion.php">Atención al cliente</a></span>
                    <? if(!isset($_SESSION)) { ?>
                    <span><a class="menu_element" href="../pages/sesion.php">Iniciar sesión</a></span>
                    <? } else { ?>
                    <span><a class="menu_element" href="../pages/perfil">Mi perfil</a></span>
                    <span><a class="menu_element" href="../functions/sesion.php?session=close">Cerrar sesión</a></span>
                    <? } ?>
                    <? if(isset($_SESSION)&&$_SESSION["rol"]==2){ ?>
                    <span><a class="menu_element" href="../pages/admin.php">Administración</a></span>
                    <? } ?>
                </div>
            </nav>
        </div>
    </header>
    <div id="container">
        <section>
            <h2>Panel de administración</h2>
            <div id="admin">
                <div id="admin_reservas" class="card">
                    <div class="card-header" id="headingOne">
                        <h4 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#div_lista_reservas" aria-expanded="false" aria-controls="div_lista_reservas">
                            <h4>Gestión de reservas</h4>
                            </button>
                        </h4>
                    </div>
                    <div id="div_lista_reservas" class="collapse" aria-labelledby="headingOne" data-parent="#admin">
                        <div class="card-body">
                            <table id="lista_reservas" class="table"><tr>
                                <th>DNI</th><th>Fecha</th><th>Origen (Hora)</th><th>Destino (Hora)</th><th>Asiento</th><th>&nbsp;</th>
                            </tr></table>
                        </div>
                    </div>
                </div>

                <div id="admin_usuarios" class="card">
                    <div class="card-header" id="headingOne">
                        <h4 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#div_lista_usuarios" aria-expanded="false" aria-controls="div_lista_usuarios">
                            <h4>Gestión de roles de usuarios</h4>
                            </button>
                        </h4>
                    </div>
                    <div id="div_lista_usuarios" class="collapse" aria-labelledby="headingOne" data-parent="#admin">
                        <div class="card-body">
                            <div id="lista_usuarios" class="row"></div>
                        </div>
                    </div>
                </div>

                <div id="admin_tarifas" class="card">
                    <div class="card-header" id="headingOne">
                        <h4 class="mb-0">
                            <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#div_lista_tarifas" aria-expanded="false" aria-controls="div_lista_tarifas">
                            <h4>Gestión de tarifa</h4>
                            </button>
                        </h4>
                    </div>
                    <div id="div_lista_tarifas" class="collapse" aria-labelledby="headingOne" data-parent="#admin">
                        <div class="card-body">
                            <table id="lista_tarifas" class="table"><tr/></table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
    <script src="../js/script_admin.js"></script>
    <?php if(isset($_GET['error'])){ ?>
        <script> alert("<?= $_GET['error'] ?>") </script>
    <?php } ?>
</body>
</html>