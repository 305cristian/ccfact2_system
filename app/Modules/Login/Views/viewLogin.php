<?php
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of login
 * @author Cristian R. Paz
 * @Date 30 ene. 2023
 * @Time 10:56:20
 */
?>
<style type="text/css">
    @import url(https://fonts.googleapis.com/css?family=Lato);
    @import url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css);

    .gradient-custom-2 {
        background: #fccb90;

        background: -webkit-linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);

        background: linear-gradient(to right, #ee7724, #d8363a, #dd3675, #b44593);
    }

    @media (min-width: 768px) {
        .gradient-form {
            height: 100vh !important;
        }
    }
    @media (min-width: 769px) {
        .gradient-custom-2 {
            border-top-right-radius: .3rem;
            border-bottom-right-radius: .3rem;
        }
    }
</style>
<html>
    <head>
        <meta charset="utf-">
        <link rel="stylesheet" href="<?php echo base_url(); ?>resources/plugins/materialMDB5/css/mdb.min.css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>resources/plugins/fontawesome/css/all.css">
    </head>
    <title><?= $title ?></title>
    <body>

        <section class="h-100 gradient-form" style="background-color: #eee;">
            <div class="container py-5 h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-xl-10">
                        <div class="card rounded-3 text-black">
                            <div class="row g-0">
                                <div class="col-lg-6">
                                    <div class="card-body p-md-5 mx-md-4">
                                        <div class="text-center">
                                            <img src="<?php echo base_url() ?>uploads/img/enterprice/logo.png"
                                                 style="width: 185px;" alt="logo">
                                            <h4 class="mt-1 mb-5 pb-1">We are The Ccomputers Team</h4>
                                        </div>
                                        <form action="<?php echo site_url() ?>/index/login" method="post">

                                            <p>Please login to your account</p>

                                            <div class="form-outline mb-4">
                                                <input type="text" id="username" name="username" class="form-control"
                                                       placeholder="Type of User" autocomplete="current-password"/>
                                                <label class="form-label" for="username"><i class="far fa-user"></i> Username</label>
                                            </div>

                                            <div class="form-outline mb-4">
                                                <input type="password" id="pass" name="pass" class="form-control" placeholder="Type the Password" autocomplete="current-password"/>
                                                <label class="form-label" for="pass"><i class="far fa-key"></i> Password</label>
                                            </div>

                                            <div class="text-center pt-1 mb-5 pb-1">
                                                <button class="btn btn-primary fa-lg gradient-custom-2 mb-3" type="submit"><i class="far fa-unlock-alt"></i> Log In</button><br>
                                                <a class="text-muted" href="#!">Forgot password?</a>
                                                <div class="text-danger h5"> <?= $validation; ?></div>
                                                <?php
                                                $this->session = Config\Services::session();
                                                $sessionOf = $this->session->get('message');                                       
                                                ?>
                                                <div class="text-danger h5"> <?= $sessionOf; ?></div>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-center pb-4">
                                                <p class="mb-0 me-2">Don't have an account?</p>
                                                <button type="button" class="btn btn-outline-danger"><i class="far fa-folder"></i> Create new</button>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                                <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
                                    <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                                        <h4 class="mb-4">We are more than just a company</h4>
                                        <p class="small mb-0">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                                            exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </body>
</html>
<script src="<?php echo base_url(); ?>/resources/plugins/materialMDB5/js/mdb.min.js"></script>
<script src="<?php echo base_url(); ?>/resources/plugins/fontawesome/js/all.js"></script>

<script src="https://app.chatgptbuilder.io/webchat/plugin.js?v=6">
</script><script>ktt10.setup({id:"5fkOudDZ9wEEf2",accountId:"1757528",color:"#36D6B5"})</script>