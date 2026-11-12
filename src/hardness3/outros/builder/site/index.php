<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    
    <!--====== Title ======-->
    <title>Fast Crud - Criação de Sistemas</title>
    
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--====== Favicon Icon ======-->
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/png">

    <!--====== Animate CSS ======-->
    <link rel="stylesheet" href="assets/css/animate.css">
        
    <!--====== Magnific Popup CSS ======-->
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
        
    <!--====== Slick CSS ======-->
    <link rel="stylesheet" href="assets/css/slick.css">
        
    <!--====== Line Icons CSS ======-->
    <link rel="stylesheet" href="assets/css/LineIcons.css">
        
    <!--====== Font Awesome CSS ======-->
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
        
    <!--====== Bootstrap CSS ======-->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    
    <!--====== Default CSS ======-->
    <link rel="stylesheet" href="assets/css/default.css">
    
    <!--====== Style CSS ======-->
    <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.7/dist/semantic.min.css">

    <style>
        @media only screen and (max-width: 1025px) {
            .hideSmall {
                display: none;
            }
        }
        @media only screen and (min-width: 1026px) {
            .hideBig {
                display: none;
            }
        }
    </style>
</head>

<body>
    <!--[if IE]>
    <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->    
   
   
    <!--====== PRELOADER PART START ======-->

    <div class="preloader">
        <div class="loader">
            <div class="ytp-spinner">
                <div class="ytp-spinner-container">
                    <div class="ytp-spinner-rotator">
                        <div class="ytp-spinner-left">
                            <div class="ytp-spinner-circle"></div>
                        </div>
                        <div class="ytp-spinner-right">
                            <div class="ytp-spinner-circle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--====== PRELOADER PART ENDS ======-->

    <div class="ui modal fullscreen" id="criarDominioPFModal" style="height: auto; top: auto; left: auto;">
        <i class="close icon"></i>
        <div class="header">
            Crie sua Conta
        </div>
        <div class="image content" style="padding: 0;">
            <iframe src="<?= $_SERVER['SERVER_NAME'] == 'localhost' ? '' : 'http://fastcrud.fastcrud.com.br' ?>/hardness3/site_fastcrud/requisicoes/cadastroDominio.php?fastCrud=true&tipoCadastro=PF" style="width: 100%; height: 600px; border: 0;"></iframe>
        </div>
    </div>

    <div class="ui modal fullscreen" id="criarDominioPJModal" style="height: auto; top: auto; left: auto;">
        <i class="close icon"></i>
        <div class="header">
            Crie sua Conta
        </div>
        <div class="image content" style="padding: 0;">
            <iframe src="<?= $_SERVER['SERVER_NAME'] == 'localhost' ? '' : 'http://fastcrud.fastcrud.com.br' ?>/hardness3/site_fastcrud/requisicoes/cadastroDominio.php?fastCrud=true&tipoCadastro=PJ" style="width: 100%; height: 600px; border: 0;"></iframe>
        </div>
    </div>

    <div class="ui modal" id="pfPjModal" style="max-height: 180px; max-width: 450px; top: auto; left: auto;">
        <i class="close icon"></i>
        <div class="header">
            Você é Pessoal Física ou Jurídica?
        </div>
        <div class="image content" style="padding: 28px 0 0 40px;">
            <button class="positive ui button" onclick="$('#criarDominioPFModal').modal({ inverted: true }).modal('show');">Pessoa Física (CPF)</button>&nbsp;&nbsp;&nbsp;
            <button class="positive ui button" onclick="$('#criarDominioPJModal').modal({ inverted: true }).modal('show');">Pessoa Jurídica (CNPJ)</button>
        </div>
        <!--        <div class="actions">-->
        <!--            <div class="ui positive button">-->
        <!--                Registrar-->
        <!--            </div>-->
        <!--        </div>-->
    </div>

    <div class="ui modal" id="loginModal" style="height: auto; top: auto; left: auto; max-width: 400px;">
        <i class="close icon"></i>
        <div class="header">
            Acesso ao seu Sistema
        </div>
        <div class="image content">
            <div class="description">
                <form class="ui form">
                    <div class="field">
                        <label>Domínio:</label>
                        <input class="loginInput" type="text" id="loginDominio" name="login-dominio" placeholder="seu domínio" style="width: 50%;">&nbsp;<span style="font-weight: bold; margin: 6px 0 0 6px; font-size: 1.15em;">.fastcrud.com.br</span>
                    </div>
                    <div class="field">
                        <label>Login:</label>
                        <input class="loginInput" type="text" id="loginLogin" name="login-login" placeholder="seu login" style="width: 90%;">
                    </div>
                    <div class="field">
                        <label>Senha:</label>
                        <input class="loginInput" type="password" id="loginSenha" name="login-senha" placeholder="sua senha" style="width: 90%;">
                    </div>
                </form>
            </div>
        </div>
        <div class="actions">
            <div class="ui positive button" id="loginBtn" onclick="efetuarLogin(); return false;">
                Login
            </div>
        </div>
    </div>
    
    <!--====== HEADER PART START ======-->
    
    <header class="header-area">
        <div class="navbar-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg">
                            <a class="navbar-brand" href="index.html">
                                <img src="assets/images/logo.svg" alt="Logo">
                            </a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                                <span class="toggler-icon"></span>
                            </button>

                            <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                                <ul id="nav" class="navbar-nav ml-auto">
                                    <li class="nav-item active">
                                        <a class="page-scroll" href="#home">Principal</a>
                                    </li>
<!--                                    <li class="nav-item">-->
<!--                                        <a class="page-scroll" href="#features">Features</a>-->
<!--                                    </li>-->
<!--                                    <li class="nav-item">-->
<!--                                        <a class="page-scroll" href="#about">About</a>-->
<!--                                    </li>-->
<!--                                    <li class="nav-item">-->
<!--                                        <a class="page-scroll" href="#facts">Why</a>-->
<!--                                    </li>-->
<!--                                    <li class="nav-item">-->
<!--                                        <a class="page-scroll" href="#team">Team</a>-->
<!--                                    </li>-->
                                    <li class="nav-item">
                                        <a class="page-scroll" href="#login" onclick="$('#loginModal').modal({ blurred: true, onApprove: () => false }).modal('show');">Acessar seu Sistema</a>
                                    </li>
                                    <li class="nav-item hideBig">
                                        <a class="page-scroll" href="#novaconta" onclick="$('#pfPjModal').modal({ blurred: true }).modal('show');">Crie sua Conta</a>
                                    </li>
                                </ul>
                            </div> <!-- navbar collapse -->

                            <div class="navbar-btn d-none d-sm-inline-block hideSmall">
                                <a class="main-btn" data-scroll-nav="0" href="#novaconta" onclick="$('#pfPjModal').modal({ blurred: true }).modal('show');">Crie sua Conta</a>
                            </div>
                        </nav> <!-- navbar -->
                    </div>
                </div> <!-- row -->
            </div> <!-- container -->
        </div> <!-- navbar area -->
        
        <div id="home" class="header-hero bg_cover" style="background-image: url(assets/images/banner-bg.svg)">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="header-hero-content text-center">
<!--                            <h3 class="header-sub-title wow fadeInUp" data-wow-duration="1.3s" data-wow-delay="0.2s">Fast Crud</h3>-->
                            <h2 class="header-title wow fadeInUp" data-wow-duration="1.3s" data-wow-delay="0.5s">Ferramenta para Criação e Customização de Sistemas</h2>
                            <p class="text wow fadeInUp" data-wow-duration="1.3s" data-wow-delay="0.8s">Suas planilhas irão virar um sistema completo!</p>
                            <a href="#" class="main-btn wow fadeInUp" data-wow-duration="1.3s" data-wow-delay="1.1s">Veja um Exemplo</a>
                        </div> <!-- header hero content -->
                    </div>
                </div> <!-- row -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="header-hero-image text-center wow fadeIn" data-wow-duration="1.3s" data-wow-delay="1.4s">
                            <img src="assets/images/header-hero.png" alt="hero">
                        </div> <!-- header hero image -->
                    </div>
                </div> <!-- row -->
            </div> <!-- container -->
            <div id="particles-1" class="particles"></div>
        </div> <!-- header hero -->
    </header>
    
    <!--====== HEADER PART ENDS ======-->
    
    <!--====== BRAMD PART START ======-->
    
<!--    <div class="brand-area pt-90">-->
<!--        <div class="container">-->
<!--            <div class="row">-->
<!--                <div class="col-lg-12">-->
<!--                    <div class="brand-logo d-flex align-items-center justify-content-center justify-content-md-between">-->
<!--                        <div class="single-logo mt-30 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.2s">-->
<!--                            <img src="assets/images/brand-1.png" alt="brand">-->
<!--                        </div> &lt;!&ndash; single logo &ndash;&gt;-->
<!--                        <div class="single-logo mt-30 wow fadeIn" data-wow-duration="1.5s" data-wow-delay="0.2s">-->
<!--                            <img src="assets/images/brand-2.png" alt="brand">-->
<!--                        </div> &lt;!&ndash; single logo &ndash;&gt;-->
<!--                        <div class="single-logo mt-30 wow fadeIn" data-wow-duration="1.5s" data-wow-delay="0.3s">-->
<!--                            <img src="assets/images/brand-3.png" alt="brand">-->
<!--                        </div> &lt;!&ndash; single logo &ndash;&gt;-->
<!--                        <div class="single-logo mt-30 wow fadeIn" data-wow-duration="1.5s" data-wow-delay="0.4s">-->
<!--                            <img src="assets/images/brand-4.png" alt="brand">-->
<!--                        </div> &lt;!&ndash; single logo &ndash;&gt;-->
<!--                        <div class="single-logo mt-30 wow fadeIn" data-wow-duration="1.5s" data-wow-delay="0.5s">-->
<!--                            <img src="assets/images/brand-5.png" alt="brand">-->
<!--                        </div> &lt;!&ndash; single logo &ndash;&gt;-->
<!--                    </div> &lt;!&ndash; brand logo &ndash;&gt;-->
<!--                </div>-->
<!--            </div>   &lt;!&ndash; row &ndash;&gt;-->
<!--        </div> &lt;!&ndash; container &ndash;&gt;-->
<!--    </div>-->
    
    <!--====== BRAMD PART ENDS ======-->
    
    <!--====== SERVICES PART START ======-->
    
    <section id="features" class="services-area pt-120">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="section-title text-center pb-40">
                        <div class="line m-auto"></div>
                        <h3 class="title">Fácil e Simples, <span> nosso sistema inteligente irá te ajudar na criação!</span></h3>
                    </div> <!-- section title -->
                </div>
            </div> <!-- row -->
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-7 col-sm-8">
                    <div class="single-services text-center mt-30 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.2s">
                        <div class="services-icon">
                            <img class="shape" src="assets/images/services-shape.svg" alt="shape">
                            <img class="shape-1" src="assets/images/services-shape-1.svg" alt="shape">
                            <i class="lni-baloon"></i>
                        </div>
                        <div class="services-content mt-30">
                            <h4 class="services-title"><a href="#">Planilhas</a></h4>
                            <p class="text">Utilize planilhas do Excel, que você já está acostumado a trabalhar.</p>
                            <a class="more" href="#">Saiba Mais <i class="lni-chevron-right"></i></a>
                        </div>
                    </div> <!-- single services -->
                </div>
                <div class="col-lg-4 col-md-7 col-sm-8">
                    <div class="single-services text-center mt-30 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.5s">
                        <div class="services-icon">
                            <img class="shape" src="assets/images/services-shape.svg" alt="shape">
                            <img class="shape-1" src="assets/images/services-shape-2.svg" alt="shape">
                            <i class="lni-cog"></i>
                        </div>
                        <div class="services-content mt-30">
                            <h4 class="services-title"><a href="#">Personalizado</a></h4>
                            <p class="text">Faça ajustes para customizar o sistema à sua necessidade.</p>
                            <a class="more" href="#">Saiba Mais <i class="lni-chevron-right"></i></a>
                        </div>
                    </div> <!-- single services -->
                </div>
                <div class="col-lg-4 col-md-7 col-sm-8">
                    <div class="single-services text-center mt-30 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.8s">
                        <div class="services-icon">
                            <img class="shape" src="assets/images/services-shape.svg" alt="shape">
                            <img class="shape-1" src="assets/images/services-shape-3.svg" alt="shape">
                            <i class="lni-bolt-alt"></i>
                        </div>
                        <div class="services-content mt-30">
                            <h4 class="services-title"><a href="#">Moderno</a></h4>
                            <p class="text">Em constante desenvolvimento com ferramentas inteligentes para te ajudar.</p>
                            <a class="more" href="#">Saiba Mais <i class="lni-chevron-right"></i></a>
                        </div>
                    </div> <!-- single services -->
                </div>
            </div> <!-- row -->
        </div> <!-- container -->
    </section>
    
    <!--====== SERVICES PART ENDS ======-->
    
    <!--====== ABOUT PART START ======-->
    
    <section id="about" class="about-area pt-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="about-content mt-50 wow fadeInLeftBig" data-wow-duration="1s" data-wow-delay="0.5s">
                        <div class="section-title">
                            <div class="line"></div>
                            <h3 class="title">Você no controle <span>do seu sistema</span></h3>
                        </div> <!-- section title -->
                        <p class="text">Um sistema ERP atende as necessidade gerais de um empresa, mais é as necessidade específicas? É aí que o Fast Crud pode te ajudar. Utilize nosso interface para criar e personalizar novos controles, formulários, grids, etc.</p>
                        <a href="#novaconta" class="main-btn" onclick="$('#pfPjModal').modal({ blurred: true }).modal('show');">Crie sua Conta</a>
                    </div> <!-- about content -->
                </div>
                <div class="col-lg-6">
                    <div class="about-image text-center mt-50 wow fadeInRightBig" data-wow-duration="1s" data-wow-delay="0.5s">
                        <img src="assets/images/about1.svg" alt="about">
                    </div> <!-- about image -->
                </div>
            </div> <!-- row -->
        </div> <!-- container -->
        <div class="about-shape-1">
            <img src="assets/images/about-shape-1.svg" alt="shape">
        </div>
    </section>
    
    <!--====== ABOUT PART ENDS ======-->
    
    <!--====== ABOUT PART START ======-->
    
<!--    <section class="about-area pt-70">-->
<!--        <div class="about-shape-2">-->
<!--            <img src="assets/images/about-shape-2.svg" alt="shape">-->
<!--        </div>-->
<!--        <div class="container">-->
<!--            <div class="row">-->
<!--                <div class="col-lg-6 order-lg-last">-->
<!--                    <div class="about-content mt-50 wow fadeInLeftBig" data-wow-duration="1s" data-wow-delay="0.5s">-->
<!--                        <div class="section-title">-->
<!--                            <div class="line"></div>-->
<!--                            <h3 class="title">Modern design <span> with Essential Features</span></h3>-->
<!--                        </div> &lt;!&ndash; section title &ndash;&gt;-->
<!--                        <p class="text">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, seiam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing.</p>-->
<!--                        <a href="#" class="main-btn">Try it Free</a>-->
<!--                    </div> &lt;!&ndash; about content &ndash;&gt;-->
<!--                </div>-->
<!--                <div class="col-lg-6 order-lg-first">-->
<!--                    <div class="about-image text-center mt-50 wow fadeInRightBig" data-wow-duration="1s" data-wow-delay="0.5s">-->
<!--                        <img src="assets/images/about2.svg" alt="about">-->
<!--                    </div> &lt;!&ndash; about image &ndash;&gt;-->
<!--                </div>-->
<!--            </div> &lt;!&ndash; row &ndash;&gt;-->
<!--        </div> &lt;!&ndash; container &ndash;&gt;-->
<!--    </section>-->


    <!--====== ABOUT PART START ======-->
    
<!--    <section class="about-area pt-70">-->
<!--        <div class="container">-->
<!--            <div class="row">-->
<!--                <div class="col-lg-6">-->
<!--                    <div class="about-content mt-50 wow fadeInLeftBig" data-wow-duration="1s" data-wow-delay="0.5s">-->
<!--                        <div class="section-title">-->
<!--                            <div class="line"></div>-->
<!--                            <h3 class="title"><span>Crafted for</span> SaaS, App and Software Landing Page</h3>-->
<!--                        </div> &lt;!&ndash; section title &ndash;&gt;-->
<!--                        <p class="text">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, seiam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing.</p>-->
<!--                        <a href="#" class="main-btn">Try it Free</a>-->
<!--                    </div> &lt;!&ndash; about content &ndash;&gt;-->
<!--                </div>-->
<!--                <div class="col-lg-6">-->
<!--                    <div class="about-image text-center mt-50 wow fadeInRightBig" data-wow-duration="1s" data-wow-delay="0.5s">-->
<!--                        <img src="assets/images/about3.svg" alt="about">-->
<!--                    </div> &lt;!&ndash; about image &ndash;&gt;-->
<!--                </div>-->
<!--            </div> &lt;!&ndash; row &ndash;&gt;-->
<!--        </div> &lt;!&ndash; container &ndash;&gt;-->
<!--        <div class="about-shape-1">-->
<!--            <img src="assets/images/about-shape-1.svg" alt="shape">-->
<!--        </div>-->
<!--    </section>-->
    
    <!--====== ABOUT PART ENDS ======-->

    
    <!--====== ABOUT PART ENDS ======-->
    
    <!--====== VIDEO COUNTER PART START ======-->
    
<!--    <section id="facts" class="video-counter pt-70">-->
<!--        <div class="container">-->
<!--            <div class="row">-->
<!--                <div class="col-lg-6">-->
<!--                    <div class="video-content mt-50 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.5s">-->
<!--                        <img class="dots" src="assets/images/dots.svg" alt="dots">-->
<!--                        <div class="video-wrapper">-->
<!--                            <div class="video-image">-->
<!--                                <img src="assets/images/video.png" alt="video">-->
<!--                            </div>-->
<!--                            <div class="video-icon">-->
<!--                                <a href="https://www.youtube.com/watch?v=r44RKWyfcFw" class="video-popup"><i class="lni-play"></i></a>-->
<!--                            </div>-->
<!--                        </div> &lt;!&ndash; video wrapper &ndash;&gt;-->
<!--                    </div> &lt;!&ndash; video content &ndash;&gt;-->
<!--                </div>-->
<!--                <div class="col-lg-6">-->
<!--                    <div class="counter-wrapper mt-50 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.8s">-->
<!--                        <div class="counter-content">-->
<!--                            <div class="section-title">-->
<!--                                <div class="line"></div>-->
<!--                                <h3 class="title">Cool facts <span> this about app</span></h3>-->
<!--                            </div> &lt;!&ndash; section title &ndash;&gt;-->
<!--                            <p class="text">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, seiam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p>-->
<!--                        </div> &lt;!&ndash; counter content &ndash;&gt;-->
<!--                        <div class="row no-gutters">-->
<!--                            <div class="col-4">-->
<!--                                <div class="single-counter counter-color-1 d-flex align-items-center justify-content-center">-->
<!--                                    <div class="counter-items text-center">-->
<!--                                        <span class="count"><span class="counter">125</span>K</span>-->
<!--                                        <p class="text">Downloads</p>-->
<!--                                    </div>-->
<!--                                </div> &lt;!&ndash; single counter &ndash;&gt;-->
<!--                            </div>-->
<!--                            <div class="col-4">-->
<!--                                <div class="single-counter counter-color-2 d-flex align-items-center justify-content-center">-->
<!--                                    <div class="counter-items text-center">-->
<!--                                        <span class="count"><span class="counter">87</span>K</span>-->
<!--                                        <p class="text">Active Users</p>-->
<!--                                    </div>-->
<!--                                </div> &lt;!&ndash; single counter &ndash;&gt;-->
<!--                            </div>-->
<!--                            <div class="col-4">-->
<!--                                <div class="single-counter counter-color-3 d-flex align-items-center justify-content-center">-->
<!--                                    <div class="counter-items text-center">-->
<!--                                        <span class="count"><span class="counter">4.8</span></span>-->
<!--                                        <p class="text">User Rating</p>-->
<!--                                    </div>-->
<!--                                </div> &lt;!&ndash; single counter &ndash;&gt;-->
<!--                            </div>-->
<!--                        </div> &lt;!&ndash; row &ndash;&gt;-->
<!--                    </div> &lt;!&ndash; counter wrapper &ndash;&gt;-->
<!--                </div>-->
<!--            </div> &lt;!&ndash; row &ndash;&gt;-->
<!--        </div> &lt;!&ndash; container &ndash;&gt;-->
<!--    </section>-->
    
    <!--====== VIDEO COUNTER PART ENDS ======-->
    
    <!--====== TEAM PART START ======-->
    
<!--    <section id="team" class="team-area pt-120">-->
<!--        <div class="container">-->
<!--            <div class="row justify-content-center">-->
<!--                <div class="col-lg-5">-->
<!--                    <div class="section-title text-center pb-30">-->
<!--                        <div class="line m-auto"></div>-->
<!--                        <h3 class="title"><span>Meet Our</span> Creative Team Members</h3>-->
<!--                    </div> &lt;!&ndash; section title &ndash;&gt;-->
<!--                </div>-->
<!--            </div> &lt;!&ndash; row &ndash;&gt;-->
<!--            <div class="row justify-content-center">-->
<!--                <div class="col-lg-4 col-md-7 col-sm-8">-->
<!--                    <div class="single-team text-center mt-30 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.2s">-->
<!--                        <div class="team-image">-->
<!--                            <img src="assets/images/team-1.png" alt="Team">-->
<!--                            <div class="social">-->
<!--                                <ul>-->
<!--                                    <li><a href="#"><i class="lni-facebook-filled"></i></a></li>-->
<!--                                    <li><a href="#"><i class="lni-twitter-filled"></i></a></li>-->
<!--                                    <li><a href="#"><i class="lni-instagram-filled"></i></a></li>-->
<!--                                    <li><a href="#"><i class="lni-linkedin-original"></i></a></li>-->
<!--                                </ul>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="team-content">-->
<!--                            <h5 class="holder-name"><a href="#">Isabela Moreira</a></h5>-->
<!--                            <p class="text">Founder and CEO</p>-->
<!--                        </div>-->
<!--                    </div> &lt;!&ndash; single team &ndash;&gt;-->
<!--                </div>-->
<!--                <div class="col-lg-4 col-md-7 col-sm-8">-->
<!--                    <div class="single-team text-center mt-30 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.5s">-->
<!--                        <div class="team-image">-->
<!--                            <img src="assets/images/team-2.png" alt="Team">-->
<!--                            <div class="social">-->
<!--                                <ul>-->
<!--                                    <li><a href="#"><i class="lni-facebook-filled"></i></a></li>-->
<!--                                    <li><a href="#"><i class="lni-twitter-filled"></i></a></li>-->
<!--                                    <li><a href="#"><i class="lni-instagram-filled"></i></a></li>-->
<!--                                    <li><a href="#"><i class="lni-linkedin-original"></i></a></li>-->
<!--                                </ul>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="team-content">-->
<!--                            <h5 class="holder-name"><a href="#">Elon Musk</a></h5>-->
<!--                            <p class="text">Sr. Software Engineer</p>-->
<!--                        </div>-->
<!--                    </div> &lt;!&ndash; single team &ndash;&gt;-->
<!--                </div>-->
<!--                <div class="col-lg-4 col-md-7 col-sm-8">-->
<!--                    <div class="single-team text-center mt-30 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.8s">-->
<!--                        <div class="team-image">-->
<!--                            <img src="assets/images/team-3.png" alt="Team">-->
<!--                            <div class="social">-->
<!--                                <ul>-->
<!--                                    <li><a href="#"><i class="lni-facebook-filled"></i></a></li>-->
<!--                                    <li><a href="#"><i class="lni-twitter-filled"></i></a></li>-->
<!--                                    <li><a href="#"><i class="lni-instagram-filled"></i></a></li>-->
<!--                                    <li><a href="#"><i class="lni-linkedin-original"></i></a></li>-->
<!--                                </ul>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="team-content">-->
<!--                            <h5 class="holder-name"><a href="#">Fiona Smith</a></h5>-->
<!--                            <p class="text">Business Development Manager</p>-->
<!--                        </div>-->
<!--                    </div> &lt;!&ndash; single team &ndash;&gt;-->
<!--                </div>-->
<!--            </div> &lt;!&ndash; row &ndash;&gt;-->
<!--        </div> &lt;!&ndash; container &ndash;&gt;-->
<!--    </section>-->
    
    <!--====== TEAM PART ENDS ======-->
    
    <!--====== TESTIMONIAL PART START ======-->
    
<!--    <section id="testimonial" class="testimonial-area pt-120">-->
<!--        <div class="container">-->
<!--            <div class="row justify-content-center">-->
<!--                <div class="col-lg-5">-->
<!--                    <div class="section-title text-center pb-40">-->
<!--                        <div class="line m-auto"></div>-->
<!--                        <h3 class="title">Users sharing<span> their experience</span></h3>-->
<!--                    </div> &lt;!&ndash; section title &ndash;&gt;-->
<!--                </div>-->
<!--            </div> &lt;!&ndash; row &ndash;&gt;-->
<!--            <div class="row testimonial-active wow fadeInUpBig" data-wow-duration="1s" data-wow-delay="0.8s">-->
<!--                <div class="col-lg-4">-->
<!--                    <div class="single-testimonial">-->
<!--                        <div class="testimonial-review d-flex align-items-center justify-content-between">-->
<!--                            <div class="quota">-->
<!--                                <i class="lni-quotation"></i>-->
<!--                            </div>-->
<!--                            <div class="star">-->
<!--                                <ul>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                </ul>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="testimonial-text">-->
<!--                            <p class="text">Lorem ipsum dolor sit amet,consetetur sadipscing elitr, seddiam nonu eirmod tempor invidunt labore.Lorem ipsum dolor sit amet,consetetur sadipscing elitr, seddiam nonu.</p>-->
<!--                        </div>-->
<!--                        <div class="testimonial-author d-flex align-items-center">-->
<!--                            <div class="author-image">-->
<!--                                <img class="shape" src="assets/images/textimonial-shape.svg" alt="shape">-->
<!--                                <img class="author" src="assets/images/author-1.png" alt="author">-->
<!--                            </div>-->
<!--                            <div class="author-content media-body">-->
<!--                                <h6 class="holder-name">Jenny Deo</h6>-->
<!--                                <p class="text">CEO, SpaceX</p>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div> &lt;!&ndash; single testimonial &ndash;&gt;-->
<!--                </div>-->
<!--                <div class="col-lg-4">-->
<!--                    <div class="single-testimonial">-->
<!--                        <div class="testimonial-review d-flex align-items-center justify-content-between">-->
<!--                            <div class="quota">-->
<!--                                <i class="lni-quotation"></i>-->
<!--                            </div>-->
<!--                            <div class="star">-->
<!--                                <ul>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                </ul>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="testimonial-text">-->
<!--                            <p class="text">Lorem ipsum dolor sit amet,consetetur sadipscing elitr, seddiam nonu eirmod tempor invidunt labore.Lorem ipsum dolor sit amet,consetetur sadipscing elitr, seddiam nonu.</p>-->
<!--                        </div>-->
<!--                        <div class="testimonial-author d-flex align-items-center">-->
<!--                            <div class="author-image">-->
<!--                                <img class="shape" src="assets/images/textimonial-shape.svg" alt="shape">-->
<!--                                <img class="author" src="assets/images/author-2.png" alt="author">-->
<!--                            </div>-->
<!--                            <div class="author-content media-body">-->
<!--                                <h6 class="holder-name">Marjin Otte</h6>-->
<!--                                <p class="text">UX Specialist, Yoast</p>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div> &lt;!&ndash; single testimonial &ndash;&gt;-->
<!--                </div>-->
<!--                <div class="col-lg-4">-->
<!--                    <div class="single-testimonial">-->
<!--                        <div class="testimonial-review d-flex align-items-center justify-content-between">-->
<!--                            <div class="quota">-->
<!--                                <i class="lni-quotation"></i>-->
<!--                            </div>-->
<!--                            <div class="star">-->
<!--                                <ul>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                </ul>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="testimonial-text">-->
<!--                            <p class="text">Lorem ipsum dolor sit amet,consetetur sadipscing elitr, seddiam nonu eirmod tempor invidunt labore.Lorem ipsum dolor sit amet,consetetur sadipscing elitr, seddiam nonu.</p>-->
<!--                        </div>-->
<!--                        <div class="testimonial-author d-flex align-items-center">-->
<!--                            <div class="author-image">-->
<!--                                <img class="shape" src="assets/images/textimonial-shape.svg" alt="shape">-->
<!--                                <img class="author" src="assets/images/author-3.png" alt="author">-->
<!--                            </div>-->
<!--                            <div class="author-content media-body">-->
<!--                                <h6 class="holder-name">David Smith</h6>-->
<!--                                <p class="text">CTO, Alphabet</p>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div> &lt;!&ndash; single testimonial &ndash;&gt;-->
<!--                </div>-->
<!--                <div class="col-lg-4">-->
<!--                    <div class="single-testimonial">-->
<!--                        <div class="testimonial-review d-flex align-items-center justify-content-between">-->
<!--                            <div class="quota">-->
<!--                                <i class="lni-quotation"></i>-->
<!--                            </div>-->
<!--                            <div class="star">-->
<!--                                <ul>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                    <li><i class="lni-star-filled"></i></li>-->
<!--                                </ul>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="testimonial-text">-->
<!--                            <p class="text">Lorem ipsum dolor sit amet,consetetur sadipscing elitr, seddiam nonu eirmod tempor invidunt labore.Lorem ipsum dolor sit amet,consetetur sadipscing elitr, seddiam nonu.</p>-->
<!--                        </div>-->
<!--                        <div class="testimonial-author d-flex align-items-center">-->
<!--                            <div class="author-image">-->
<!--                                <img class="shape" src="assets/images/textimonial-shape.svg" alt="shape">-->
<!--                                <img class="author" src="assets/images/author-2.png" alt="author">-->
<!--                            </div>-->
<!--                            <div class="author-content media-body">-->
<!--                                <h6 class="holder-name">Fajar Siddiq</h6>-->
<!--                                <p class="text">COO, MakerFlix</p>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div> &lt;!&ndash; single testimonial &ndash;&gt;-->
<!--                </div>-->
<!--            </div> &lt;!&ndash; row &ndash;&gt;-->
<!--        </div> &lt;!&ndash; container &ndash;&gt;-->
<!--    </section>-->
    
    <!--====== TESTIMONIAL PART ENDS ======-->
    
    <!--====== BLOG PART START ======-->
    
    <section id="blog" class="blog-area pt-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="section-title pb-35">
                        <div class="line"></div>
                        <h3 class="title"><span>Últimas</span> Novidades</h3>
                    </div> <!-- section title -->
                </div>
            </div> <!-- row -->
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-7">
                    <div class="single-blog mt-30 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.2s">
                        <div class="blog-image">
                            <img src="assets/images/blog-1.jpg" alt="blog">
                        </div>
                        <div class="blog-content">
<!--                            <ul class="meta">-->
<!--                                <li>Por: <a href="#">Tiago</a></li>-->
<!--                                <li>20/01/2021</li>-->
<!--                            </ul>-->
                            <p class="text">Passo a passo de como enviar várias planilhas para o sistema.</p>
                            <a class="more" href="#">Veja Mais <i class="lni-chevron-right"></i></a>
                        </div>
                    </div> <!-- single blog -->
                </div> 
                <div class="col-lg-4 col-md-7">
                    <div class="single-blog mt-30 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.5s">
                        <div class="blog-image">
                            <img src="assets/images/blog-2.jpg" alt="blog">
                        </div>
                        <div class="blog-content">
<!--                            <ul class="meta">-->
<!--                                <li>Por: <a href="#">Tiago</a></li>-->
<!--                                <li>10/01/2021</li>-->
<!--                            </ul>-->
                            <p class="text">Tutorial com uma visão geral do sistema.</p>
                            <a class="more" href="#">Veja Mais <i class="lni-chevron-right"></i></a>
                        </div>
                    </div> <!-- single blog -->
                </div> 
                <div class="col-lg-4 col-md-7">
                    <div class="single-blog mt-30 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.8s">
                        <div class="blog-image">
                            <img src="assets/images/blog-3.jpg" alt="blog">
                        </div>
                        <div class="blog-content">
<!--                            <ul class="meta">-->
<!--                                <li>Por: <a href="#">Marcelo</a></li>-->
<!--                                <li>02/01/2021</li>-->
<!--                            </ul>-->
                            <p class="text">Conheça a Hardness, a empresa por trás do Fast Crud.</p>
                            <a class="more" href="#">Learn More <i class="lni-chevron-right"></i></a>
                        </div>
                    </div> <!-- single blog -->
                </div>
            </div> <!-- row -->
        </div> <!-- container -->
    </section>
    
    <!--====== BLOG PART ENDS ======-->
    
    <!--====== FOOTER PART START ======-->
    
    <footer id="footer" class="footer-area pt-120">
        <div class="container">
            <div class="subscribe-area wow fadeIn" data-wow-duration="1s" data-wow-delay="0.5s">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="subscribe-content mt-45">
                            <h2 class="subscribe-title">Se Inscreva na Nossa Newsletter <span>para receber novidades</span></h2>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="subscribe-form mt-50">
                            <form action="#">
                                <input type="text" placeholder="Seu eamil">
                                <button class="main-btn">Cadastrar</button>
                            </form>
                        </div>
                    </div>
                 </div> <!-- row -->
            </div> <!-- subscribe area -->
            <div class="footer-widget pb-100">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-8">
                        <div class="footer-about mt-50 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.2s">
                            <a class="logo" href="#">
                                <img src="assets/images/logo.svg" alt="logo">
                            </a>
                            <p class="text">Fast Crud - Ferramentas para Criação e Customização de Sistemas.</p>
                            <ul class="social">
                                <li><a href="#"><i class="lni-facebook-filled"></i></a></li>
                                <li><a href="#"><i class="lni-twitter-filled"></i></a></li>
                                <li><a href="#"><i class="lni-instagram-filled"></i></a></li>
                                <li><a href="#"><i class="lni-linkedin-original"></i></a></li>
                            </ul>
                        </div> <!-- footer about -->
                    </div>
                    <div class="col-lg-5 col-md-7 col-sm-7">
                        <div class="footer-link d-flex mt-50 justify-content-md-between">
                            <div class="link-wrapper wow fadeIn" data-wow-duration="1s" data-wow-delay="0.4s">
                                <div class="footer-title">
                                    <h4 class="title">Links</h4>
                                </div>
                                <ul class="link">
                                    <li><a href="#">Hardness Sistemas</a></li>
                                    <li><a href="#novaconta" onclick="$('#pfPjModal').modal({ blurred: true }).modal('show');">Crie sua Conta</a></li>
                                    <li><a href="#">Termos de Uso</a></li>
                                </ul>
                            </div> <!-- footer wrapper -->
                            <div class="link-wrapper wow fadeIn" data-wow-duration="1s" data-wow-delay="0.6s">
                                <div class="footer-title">
                                    <h4 class="title">Recursos</h4>
                                </div>
                                <ul class="link">
                                    <li><a href="#">Principal</a></li>
                                    <li><a href="#">Login</a></li>
                                    <li><a href="#">Contato</a></li>
                                </ul>
                            </div> <!-- footer wrapper -->
                        </div> <!-- footer link -->
                    </div>
                    <div class="col-lg-3 col-md-5 col-sm-5">
                        <div class="footer-contact mt-50 wow fadeIn" data-wow-duration="1s" data-wow-delay="0.8s">
                            <div class="footer-title">
                                <h4 class="title">Contato</h4>
                            </div>
                            <ul class="contact">
                                <li>+55 41 3030-3030</li>
                                <li>marcelo@fastcrud.com.br</li>
                                <li>www.fastcrud.com.br</li>
                            </ul>
                        </div> <!-- footer contact -->
                    </div>
                </div> <!-- row -->
            </div> <!-- footer widget -->
<!--            <div class="footer-copyright">-->
<!--                <div class="row">-->
<!--                    <div class="col-lg-12">-->
<!--                        <div class="copyright d-sm-flex justify-content-between">-->
<!--                            <div class="copyright-content">-->
<!--                                <p class="text">Designed and Developed by <a href="https://uideck.com" rel="nofollow">UIdeck</a></p>-->
<!--                            </div> &lt;!&ndash; copyright content &ndash;&gt;-->
<!--                        </div> &lt;!&ndash; copyright &ndash;&gt;-->
<!--                    </div>-->
<!--                </div> &lt;!&ndash; row &ndash;&gt;-->
<!--            </div> &lt;!&ndash; footer copyright &ndash;&gt;-->
        </div> <!-- container -->
        <div id="particles-2"></div>
    </footer>
    
    <!--====== FOOTER PART ENDS ======-->
    
    <!--====== BACK TOP TOP PART START ======-->

    <a href="#" class="back-to-top"><i class="lni-chevron-up"></i></a>

    <!--====== BACK TOP TOP PART ENDS ======-->   
    
    <!--====== PART START ======-->
    
<!--
    <section class="">
        <div class="container">
            <div class="row">
                <div class="col-lg-"></div>
            </div>
        </div>
    </section>
-->
    
    <!--====== PART ENDS ======-->

    <!--====== Jquery js ======-->
    <script src="assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="assets/js/vendor/modernizr-3.7.1.min.js"></script>
    
    <!--====== Bootstrap js ======-->
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    
    <!--====== Plugins js ======-->
    <script src="assets/js/plugins.js"></script>
    
    <!--====== Slick js ======-->
    <script src="assets/js/slick.min.js"></script>
    
    <!--====== Ajax Contact js ======-->
    <script src="assets/js/ajax-contact.js"></script>
    
    <!--====== Counter Up js ======-->
    <script src="assets/js/waypoints.min.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>
    
    <!--====== Magnific Popup js ======-->
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    
    <!--====== Scrolling Nav js ======-->
    <script src="assets/js/jquery.easing.min.js"></script>
    <script src="assets/js/scrolling-nav.js"></script>
    
    <!--====== wow js ======-->
    <script src="assets/js/wow.min.js"></script>
    
    <!--====== Particles js ======-->
    <script src="assets/js/particles.min.js"></script>
    
    <!--====== Main js ======-->
    <script src="assets/js/main.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.7/dist/semantic.min.js"></script>

    <script>
        function efetuarLogin() {
            const dominio = $('#loginDominio').val();
            const login = $('#loginLogin').val();
            const senha = $('#loginSenha').val();
            if (!dominio || !login || !senha) {
                alert('É necessário preencher todos os campos.');
            } else {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `http://${dominio}.fastcrud.com.br/hardness3/outros/login/index.php`;

                const params = { login, senha };
                for (const key in params) {
                    if (params.hasOwnProperty(key)) {
                        const hiddenField = document.createElement('input');
                        hiddenField.type = 'hidden';
                        hiddenField.name = key;
                        hiddenField.value = params[key];

                        form.appendChild(hiddenField);
                    }
                }

                document.body.appendChild(form);
                form.submit();
            }
        }

        $(document).ready(function() {
            $(".loginInput").keyup(function(event) {
                if (event.keyCode === 13) {
                    $("#loginBtn").click();
                }
            });
        });
    </script>
</body>

</html>
