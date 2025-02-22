<?php
require "stranky.php";

session_start();

$chyba = "";

// zpracovani prihl. formulare
if (array_key_exists("prihlasit",$_POST))

{ 
    $jmeno = $_POST["jmeno"];
    $heslo = $_POST["heslo"];

        if ($jmeno =="admin" && $heslo =="1234")
        {
            // uzivatel. zadal platne prihlasovaci udaje
            $_SESSION["prihlasenyUzivatel"] = $jmeno;
        }
        else
        {
            // spatne prihlasovaci udaje
            $chyba = "Nesprávné přihlašovací údaje";
        }
}
// zpracování odhl. formuláře
if (array_key_exists("odhlasit", $_POST))
    {
        unset($_SESSION["prihlasenyUzivatel"]);
        header("Location: ?");
    }

// zpracovani akci v administraci pouze pro prihlasene uzivatele
if (array_key_exists("prihlasenyUzivatel", $_SESSION))
{
    // promenna predstavujici stranku s kterou zrovna editujeme
    $instanceAktualniStranky = null;

    // zpracovani vyberu aktualni stranky
    if (array_key_exists("stranka", $_GET))
    {
        $idStranky = $_GET["stranka"];
        $instanceAktualniStranky = $seznamStranek[$idStranky];
    }
    // zpracovani formulare pro ulozeni
    if (array_key_exists("ulozit", $_POST))
    {
        $obsah = $_POST["obsah"];
        $instanceAktualniStranky->setObsah($obsah);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrace</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="css/admin.css">

</head>
<body>
    <div class="admin-body">
        <?php
            if (array_key_exists("prihlasenyUzivatel", $_SESSION)==false)
            {
                ?>
                    <main class="form-signin">
                        <form method="post">
                          <h1 class="h3 mb-3 fw-normal">Přihlašte se prosím</h1>
                          <?php if ($chyba != "") { ?> 
                            <div class="alert alert-danger" role="alert">
                                <?php echo $chyba; ?>
                            </div>
                            <?php } ?>
                          <div class="form-floating">
                            <input name="jmeno" type="text" class="form-control" id="floatingInput" placeholder="login">
                            <label for="floatingInput">Email address</label>
                          </div>
                          <div class="form-floating">
                            <input name="heslo" type="password" class="form-control" id="floatingPassword" placeholder="Password">
                            <label for="floatingPassword">Heslo</label>
                          </div>

                          <button name="prihlasit" class="btn btn-primary w-100 py-2" type="submit">Přihlásit</button>
                        </form>
                    </main>
    </div>
        <?php 
            echo $chyba; 
            
        }
        else
        {
            echo "<main class='admin'>";

            ?>
            
            <div class="continer">
            <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
            <div>Přihlášený uživatel: <?php $_SESSION["prihlasenyUzivatel"]; ?></div>

            <div class="col-md-3 text-end">
            <form method='post'>
                <button name='odhlasit' class="btn btn-outline-primary me-2">Odhlásit</button>
                </form>
            </div>
            </header>
        </div>

        <?php
            // sekce pro prihlasene uzivatele
            echo "Přihlášený uživatel:{$_SESSION["prihlasenyUzivatel"]} ";

        // vypiseme seznam stranek, ktere lze editovat
            echo "<ul id='stranky' class='list-group-item'>";
            foreach ($seznamStranek as $idStranky => $instanceStranky)
                {
                    $active = '';
                    $buttonClass = 'btn-primary';
                    if ($instanceStranky == $instanceAktualniStranky)
                    {
                        $active = 'active';
                        $buttonClass = 'btn-secondary';
                    } 
                    echo "<li class='list-group-item $active'>

                    <a class='btn $buttonClass' href='?stranka=$instanceStranky->id'><i class='fa-solid fa-pen-to-square'></i> </a>

                    <a class='btn $buttonClass' href='$instanceStranky->id' target='_blank'><i class='fa-solid fa-eye'></i></a>


                    <span>$instanceStranky->id</span>
                    </li>";
                }

            echo "</ul>";
             // editacni formular
            // zobbrazit pokud je nejaka stranka vybrana k editaci
            if ($instanceAktualniStranky != null)
            {
                echo "<div class='alert alert-secondary' role='alert'><h1>Editace stránky: $instanceAktualniStranky->id</h1></div>";

                ?>
                    <form method="post">
                        <textarea id="obsah" name="obsah" cols="80" rows="15"><?php
                        echo htmlspecialchars($instanceAktualniStranky->getObsah());
                        ?></textarea>
                        <br>
                        <button name="ulozit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Uložit </button>
                    </form>
                    <script src="vendor/tinymce/tinymce/tinymce.min.js"></script>
                    <script type="text/javascript">
                        tinymce.init({
                            selector: '#obsah',
                            language: 'cs',
                            language_url: '<?php echo dirname($_SERVER["PHP_SELF"]); ?> /vendor/tweeb/tinymce-i18n/langs/cs.js',
                            height: '50vh',
                            entity_encoding: "raw",
                            verify_html: false,
                            content_css:[
                                'css/reset.css',
                                'css/section.css',
                                'css/style.css',
                                'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
                                'https://fonts.googleapis.com/css2?family=Kaushan+Script&display=swap',
                            ],
                            body_id:"content",
                            plugins: 'advlist anchor autolink charmap code colorpicker contextmenu directionality emoticons fullscreen hr image imagetools insertdatetime link lists nonbreaking noneditable pagebreak paste preview print save searchreplace tabfocus table textcolor textpattern visualchars',
                            toolbar1: "insertfile undo redo | styleselect | fontselect | fontsizeselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | forecolor backcolor",
                            toolbar2: "link unlink anchor | fontawesome | image media | responsivefilemanager | preview code",
		                    external_plugins: {
		                    	'responsivefilemanager': '<?php echo dirname($_SERVER['PHP_SELF']); ?>/vendor/primakurzy/responsivefilemanager/tinymce/plugins/responsivefilemanager/plugin.min.js',
		                    },
		                    external_filemanager_path: "<?php echo dirname($_SERVER['PHP_SELF']); ?>/vendor/primakurzy/responsivefilemanager/filemanager/",
		                    filemanager_title: "Správce souborů",
                        });
                    </script>
                <?php
            }
            echo "</main>";
        }
        ?>
    </div>
</body>
</html>