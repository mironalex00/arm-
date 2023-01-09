<?php
    include_once('./assets/php/controller/session.php');
    require_once('./assets/php/views/header.php');
    if(!isset($_SESSION['user'])){
        echo "\n" . <<<HTML
        <script type="module">
        import { ValidateForm } from './assets/js/app.js'
        import { SignIn, SignUp } from './assets/js/session.js'
        document.forms.login.addEventListener('submit', (e) => {
            ValidateForm(e, (res) => {
                if(res !== true) {
                    alert(res);
                    return;
                }
                SignIn(e.srcElement).then(res => {
                    if(res?.errors){
                        alert(res.errors?.join(', ').replace(/, ([^,]*)$/, ' and $1'));
                        return;
                    }
                    window.location.href = './';
                });
            });
        });
        document.forms.signup.addEventListener('submit', (e) => {
            ValidateForm(e, (res) => {
                if(res !== true) {
                    alert(res);
                    return;
                }
                SignUp(e.srcElement).then(res => {
                    if(res?.errors){
                        alert(res.errors?.join(', ').replace(/, ([^,]*)$/, ' and $1'));
                        return;
                    }
                    document.querySelector("label[for='reg-log']").click();
                });
            });
        });
        </script>
        HTML;
    }
?>
<link rel="stylesheet" href="./assets/css/signin_signup.css">
<div class="section">
  <div class="container">
    <div class="row full-height justify-content-center">
      <div class="col-12 text-center align-self-center py-md-3">
        <div class="section pb-5 pt-5 pt-sm-2 text-center">
            <h6 class="mb-0 pb-3"><span>Log In </span><span>Sign Up</span></h6>
            <input class="checkbox" type="checkbox" id="reg-log" name="reg-log"/>
            <label for="reg-log" onclick="resetForms()"></label>
            <div class="card-3d-wrap mx-auto">
                <div class="card-3d-wrapper">
                    <div class="card-front">
                        <div class="center-wrap">
                            <h4 class="mb-4 pb-3">Sign In</h4>
                            <form id="login" name="login" class="">
                                <div class="mb-3">
                                    <label for="l_user" class="form-label text-white">Nombre de usuario</label>
                                    <input type="text" class="form-control" id="login_user" name="user" pattern="<?= USERREGEX; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="login_pass" class="form-label text-white">Contraseña</label>
                                    <div class="grid">
                                        <div>
                                            <input type="password" class="form-control" id="login_pass" name="pass" pattern="<?= PASSREGEX; ?>" required>
                                        </div>
                                        <div onclick="togglePassword(this)">
                                            <a class="g-col-2 btn btn-light h-100 togglePassword">
                                                <i class="fa-regular fa-eye-slash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-back">
                        <div class="center-wrap">
                        <h4 class="mb-4 pb-3">Sign Up</h4>
                            <form id="signup" name="signup">
                                <div class="mb-3">
                                    <label for="mail" class="form-label text-white">Email</label>
                                    <input type="email" class="form-control" id="mail" name="mail" pattern="<?= MAILREGEX; ?>" required />
                                </div>
                                <div class="mb-3">
                                    <label for="s_user" class="form-label text-white">Nombre de usuario</label>
                                    <input type="text" class="form-control" id="signup_user" name="user" pattern="<?= USERREGEX; ?>" required />
                                </div>
                                <div class="mb-3">
                                    <label for="signup_pass" class="form-label text-white">Contraseña</label>
                                    <div class="grid">
                                        <div>
                                            <input type="password" class="form-control" id="signup_pass" name="pass" pattern="<?= PASSREGEX; ?>" required />
                                        </div>
                                        <div onclick="togglePassword(this)">
                                            <a class="g-col-2 btn btn-light h-100 togglePassword">
                                                <i class="fa-regular fa-eye-slash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="fname" class="form-label text-white">Nombres</label>
                                    <input type="text" class="form-control" id="fname" name="fname" pattern="<?= NAMEREGEX ?>" required />
                                </div>
                                <div class="mb-3">
                                    <label for="sname" class="form-label text-white">Apellidos</label>
                                    <input type="text" class="form-control" id="sname" name="sname" pattern="<?= NAMEREGEX ?>" required />
                                </div>
                                <button type="submit" data-mdb-ripple-duration="0" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php
    require_once('./assets/php/views/footer.php');
?>