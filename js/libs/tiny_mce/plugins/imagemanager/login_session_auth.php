<?php
// Set session
session_start();

// Some settings
$msg      = "";
$username = "admin";
$password = "weblife"; // Change the password to something suitable
$sitezone = empty($_SESSION['WLCMS_ZONE']) ? false : $_SESSION['WLCMS_ZONE']; // [ BACKEND | FRONTEND ]
                

if(!$password) $msg = 'You must set a password in the file "login_session_auth.php" inorder to login using this page or reconfigure it the authenticator config options to fit your needs. Consult the <a href="http://wiki.moxiecode.com/index.php/Main_Page" target="_blank">Wiki</a> for more details.';

if(isset($_POST['submit_button'])) {
    // If password match, then set login
    if($_POST['login'] == $username && $_POST['password'] == $password && $password) {
        $return_url = $_POST['return_url'];
        $ssprefix   = ucfirst(strtolower(basename(realpath(dirname(__FILE__).DIRECTORY_SEPARATOR))));
        $config     = @$_SESSION['ar'.$ssprefix];

        if($config) {
            $_SESSION[$config['config_prefix'].$config['logged_in_key']] = true;
            $_SESSION[$config['config_prefix'].$config['logged_in_timeout_key']] = time() + $config['logged_in_timeout'];
            $_SESSION[$config['config_prefix'].$config['user_key']] = $_POST['login'];
        }
        if(!$return_url) $return_url = 'index.php?type=fm';

        // Override any config option
        //$_SESSION['imagemanager.filesystem.rootpath'] = 'some path';
        //$_SESSION['filemanager.filesystem.rootpath'] = 'some path';
        // Redirect
        header("location: {$return_url}");
        die;
        
    } else $msg = "Wrong username/password.";
}
?><html>
<head>
<? if(isset($_SESSION['auser_obj']) || isset($_SESSION['suser_obj'])) { ?>
    <script type="text/javascript">
        if(window.parent.length) window.parent.location.reload();
    </script>
<? } ?>
    <title>Login Page</title>
    <style>
        body { font-family: Arial, Verdana; font-size: 11px; }
        fieldset { display: block; width: 170px; }
        legend { font-weight: bold; }
        label { display: block; }
        div { margin-bottom: 10px; }
        div.last { margin: 0; }
        div.container { position: absolute; top: 50%; left: 50%; margin: -100px 0 0 -85px; }
        h1 { font-size: 14px; }
        .button { border: 1px solid gray; font-family: Arial, Verdana; font-size: 11px; }
        .error { color: red; margin: 0; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <form action="login_session_auth.php" method="post">
            <input type="hidden" name="return_url" value="<?php echo isset($_REQUEST['return_url']) ? htmlentities($_REQUEST['return_url']) : ""; ?>" />

            <fieldset>
                <legend>Login Data Form</legend>

                <div>
                    <label>Username:</label>
                    <input type="text" name="login" class="text" value="<?php echo isset($_POST['login']) ? htmlentities($_POST['login']) : ""; ?>" />
                </div>

                <div>
                    <label>Password:</label>
                    <input type="password" name="password" class="text" value="<?php echo isset($_POST['password']) ? htmlentities($_POST['password']) : ""; ?>" />
                </div>

                <div class="last">
                    <input type="submit" name="submit_button" value="Login" class="button" />
                </div>

<?php if ($msg) { ?>
                <div class="error"><?php echo $msg; ?></div>
<?php } ?>
            </fieldset>
        </form>
    </div>
</body>
</html>