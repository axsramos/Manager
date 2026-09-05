<!doctype html>

<html lang="pt">

<?php $css = '<link href="Martex/css/blue-theme.css" rel="stylesheet">' ?>
<?php include App\Core\Config::$DIR_BASE . '/App/Views/Martex/Partials/head.php' ?>

<body>

    <!-- PRELOADER SPINNER -->
    <?php include App\Core\Config::$DIR_BASE . '/App/Views/Martex/Partials/preloader.php' ?>

    <!-- PAGE CONTENT -->
    <div id="page" class="page font--jakarta">




        <!-- HEADER -->
        <header id="header" class="tra-menu navbar-dark light-hero-header white-scroll">
            <div class="header-wrapper">


                <!-- MOBILE HEADER -->
                <div class="wsmobileheader clearfix">
                    <span class="smllogo"><img src="Martex/images/logo-portal-siti.png" alt="mobile-logo"></span>
                    <a id="wsnavtoggle" class="wsanimated-arrow"><span></span></a>
                </div>


                <!-- NAVIGATION MENU -->
                <div class="wsmainfull menu clearfix">
                    <div class="wsmainwp clearfix">


                        <!-- HEADER BLACK LOGO -->
                        <div class="desktoplogo">
                            <a href="Home" class="logo-black"><img src="Martex/images/logo-portal-siti.png" alt="logo"></a>
                        </div>


                        <!-- HEADER WHITE LOGO -->
                        <div class="desktoplogo">
                            <a href="Home" class="logo-white"><img src="Martex/images/logo-portal-siti.png" alt="logo"></a>
                        </div>


                        <!-- MAIN MENU -->
                        <nav class="wsmenu clearfix">
                            <ul class="wsmenu-list nav-theme">


                                <!-- DROPDOWN SUB MENU -->
                                <li aria-haspopup="true"><a href="#" class="h-link">Manager <span class="wsarrow"></span></a>
                                    <ul class="sub-menu">
                                        <li aria-haspopup="true"><a href="/Manager/Dashboard">Dashboard</a></li>
                                        <li aria-haspopup="true"><a href="/Manager/MeuPerfil">Meu Perfil</a></li>
                                        <!-- <li aria-haspopup="true"><a href="#lnk-3">Best Solutions</a></li>
                                        <li aria-haspopup="true"><a href="#integrations-1">Integrations</a></li>
                                        <li aria-haspopup="true"><a href="#reviews-2">Testimonials</a></li> -->
                                    </ul>
                                </li>


                                <!-- SIMPLE NAVIGATION LINK -->
                                <!-- <li class="nl-simple" aria-haspopup="true"><a href="#features-11" class="h-link">Features</a></li> -->


                                <!-- MEGAMENU -->
                                <!-- <li aria-haspopup="true" class="mg_link"><a href="#" class="h-link">Pages <span class="wsarrow"></span></a>
                                    <div class="wsmegamenu w-75 clearfix">
                                        <div class="container">
                                            <div class="row"> -->

                                <!-- MEGAMENU LINKS -->
                                <!-- <ul class="col-md-12 col-lg-3 link-list">
                                                    <li class="fst-li"><a href="about.php">About Us</a></li>
                                                    <li><a href="team.php">Our Team</a></li>
                                                    <li><a href="careers.php">Careers <span class="sm-info">4</span></a></li>
                                                    <li><a href="career-role.php">Career Details</a></li>
                                                    <li><a href="contacts.php">Contact Us</a></li>
                                                </ul> -->

                                <!-- MEGAMENU LINKS -->
                                <!-- <ul class="col-md-12 col-lg-3 link-list">
                                                    <li><a href="features.php">Core Features</a></li>
                                                    <li class="fst-li"><a href="projects.php">Our Projects</a></li>
                                                    <li><a href="project-details.php">Project Details</a></li>
                                                    <li><a href="reviews.php">Testimonials</a></li>
                                                    <li><a href="download.php">Download Page</a></li>
                                                </ul> -->

                                <!-- MEGAMENU LINKS -->
                                <!-- <ul class="col-md-12 col-lg-3 link-list">
                                                    <li class="fst-li"><a href="pricing-1.php">Pricing Page #1</a></li>
                                                    <li><a href="pricing-1.php">Pricing Page #2</a></li>
                                                    <li><a href="faqs.php">FAQs Page</a></li>
                                                    <li><a href="help-center.php">Help Center</a></li>
                                                    <li><a href="404.php">404 Page</a></li>
                                                </ul> -->

                                <!-- MEGAMENU LINKS -->
                                <!-- <ul class="col-md-12 col-lg-3 link-list">
                                                    <li class="fst-li"><a href="blog-listing.php">Blog Listing</a></li>
                                                    <li><a href="single-post.php">Single Blog Post</a></li>
                                                    <li><a href="login-2.php">Login Page</a></li>
                                                    <li><a href="signup-2.php">Signup Page</a></li>
                                                    <li><a href="reset-password.php">Reset Password</a></li>
                                                </ul> -->

                                <!-- </div> End row -->
                                <!-- </div> End container -->
                                <!-- </div> End wsmegamenu -->
                                <!-- </li> END MEGAMENU -->


                                <!-- SIMPLE NAVIGATION LINK -->
                                <!-- <li class="nl-simple" aria-haspopup="true"><a href="#projects-1" class="h-link">Projects</a></li> -->


                                <!-- SIMPLE NAVIGATION LINK -->
                                <!-- <li class="nl-simple" aria-haspopup="true"><a href="faqs.php" class="h-link">FAQs</a></li> -->


                                <!-- SIGN IN LINK -->
                                <li class="nl-simple reg-fst-link mobile-last-link" aria-haspopup="true">
                                    <a href="/Auth/Login" class="h-link">Entrar</a>
                                </li>


                                <!-- SIGN UP BUTTON -->
                                <li class="nl-simple" aria-haspopup="true">
                                    <a href="/Auth/Register" class="btn r-04 btn--theme hover--theme last-link">Inscrever-se</a>
                                </li>


                            </ul>
                        </nav> <!-- END MAIN MENU -->


                    </div>
                </div>
                <!-- END NAVIGATION MENU -->


            </div> <!-- End header-wrapper -->
        </header>
        <!-- END HEADER -->




        <!-- HERO-17 -->
        <section id="hero-17" class="bg--fixed hero-section">
            <div class="container">


                <!-- HERO TEXT -->
                <div class="row justify-content-center">
                    <div class="col-md-11 col-lg-10 col-xl-9">
                        <div class="hero-17-txt wow fadeInUp">

                            <!-- Title -->
                            <h2 class="s-60 w-700">Descubra soluções alternativas que fazem a diferença no seu dia a dia!</h2>

                            <!-- Text -->
                            <p class="p-xl">Novas ideias ganhando vida a cada momento. Inscreva-se e seja o primeiro a descobrir!
                            </p>

                        </div>
                    </div> <!-- End row -->
                </div> <!-- END HERO TEXT -->


            </div> <!-- End container -->
        </section> <!-- END HERO-17 -->




        <!-- FEATURES-2 -->
        <section id="features-2" class="pt-100 features-section division">
            <div class="container">


                <!-- FEATURES-2 WRAPPER -->
                <div class="fbox-wrapper text-center">
                    <div class="row row-cols-1 row-cols-md-3">


                        <!-- FEATURE BOX #1 -->
                        <div class="col">
                            <div class="fbox-2 fb-1 wow fadeInUp">

                                <!-- Image -->
                                <div class="fbox-img gr--whitesmoke h-175">
                                    <img class="img-fluid" src="Martex/images/f_04.png" alt="feature-image">
                                </div>

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">cvfacil.com.br</h6>
                                    <p>Currículo Fácil. Transforme seu currículo em oportunidades!</p>
                                </div>

                            </div>
                        </div> <!-- END FEATURE BOX #1 -->


                        <!-- FEATURE BOX #2 -->
                        <div class="col">
                            <div class="fbox-2 fb-2 wow fadeInUp">

                                <!-- Image -->
                                <div class="fbox-img gr--whitesmoke h-175">
                                    <img class="img-fluid" src="Martex/images/f_08.png" alt="feature-image">
                                </div>

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">Shine Stock</h6>
                                    <p>Sistema de gestão. O estoque que faz sua produção brilhar!</p>
                                </div>

                            </div>
                        </div> <!-- END FEATURE BOX #2 -->


                        <!-- FEATURE BOX #3 -->
                        <div class="col">
                            <div class="fbox-2 fb-3 wow fadeInUp">

                                <!-- Image -->
                                <div class="fbox-img gr--whitesmoke h-175">
                                    <img class="img-fluid" src="Martex/images/f_05.png" alt="feature-image">
                                </div>

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">SITI Manager</h6>
                                    <p>Suíte de aplicativos para a gestão do seu negócio!</p>
                                </div>

                            </div>
                        </div> <!-- END FEATURE BOX #3 -->


                    </div> <!-- End row -->
                </div> <!-- END FEATURES-2 WRAPPER -->


            </div> <!-- End container -->
        </section> <!-- END FEATURES-2 -->




        <!-- BOX CONTENT -->
        <section id="lnk-1" class="pt-100 ws-wrapper content-section">
            <div class="container">
                <div class="bc-5-wrapper bg--02 hidd bg--scroll r-16">
                    <div class="section-overlay">


                        <!-- SECTION TITLE -->
                        <div class="row justify-content-center">
                            <div class="col-md-11 col-lg-9">
                                <div class="section-title wow fadeInUp mb-60">

                                    <!-- Title -->
                                    <h2 class="s-50 w-700">Encontre inspiração para seu próximo projeto</h2>

                                    <!-- Text -->
                                    <p class="p-xl">Iniciamos seu projeto com um tema clásico,
                                        responsivo e livre de licenças.
                                    </p>

                                </div>
                            </div>
                        </div>


                        <!-- IMAGE BLOCK -->
                        <div class="row justify-content-center">
                            <div class="col">
                                <div class="bc-5-img bc-5-tablet img-block-hidden video-preview wow fadeInUp">

                                    <!-- Play Icon -->
                                    <!-- <a class="video-popup1" href="#">
                                        <div class="video-btn video-btn-xl bg--theme">
                                            <div class="video-block-wrapper"><span class="flaticon-play-button"></span></div>
                                        </div>
                                    </a> -->

                                    <!-- Preview Image -->
                                    <img class="img-fluid" src="Martex/images/tablet-03.png" alt="content-image">

                                </div>
                            </div>
                        </div>


                    </div> <!-- End section overlay -->
                </div> <!-- End content wrapper -->
            </div> <!-- End container -->
        </section> <!-- END BOX CONTENT -->




        <!-- FEATURES-11 -->
        <section id="features-11" class="pt-100 features-section division">
            <div class="container">


                <!-- SECTION TITLE -->
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-9">
                        <div class="section-title mb-70">

                            <!-- Title -->
                            <h2 class="s-52 w-700">Estamos criando algo novo!</h2>

                            <!-- Text -->
                            <p class="s-21 color--grey">Estamos continuamente desenvolvendo diversas soluções inovadoras e
                                acreditamos que podemos ajudar a resolver suas demandas específicas.
                                Sempre há algo que podemos fazer para otimizar seu negócio e atender às suas necessidades.</p>
                        </div>
                    </div>
                </div>


                <!-- FEATURES-11 WRAPPER -->
                <div class="fbox-wrapper">
                    <div class="row row-cols-1 row-cols-md-2 rows-3">


                        <!-- FEATURE BOX #1 -->
                        <div class="col">
                            <div class="fbox-11 fb-1 wow fadeInUp">

                                <!-- Icon -->
                                <div class="fbox-ico-wrap">
                                    <div class="fbox-ico ico-50">
                                        <div class="shape-ico color--theme">

                                            <!-- Vector Icon -->
                                            <span class="flaticon-search-engine-1"></span>

                                            <!-- Shape -->
                                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
                                            </svg>

                                        </div>
                                    </div>
                                </div> <!-- End Icon -->

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">Programação</h6>
                                    <p>Desenvolvimento de aplicações para resolver problemas, automatizar tarefas e criar funcionalidades
                                        que atendam a requisitos de usuários e sistemas.
                                    </p>
                                </div>

                            </div>
                        </div> <!-- END FEATURE BOX #1 -->


                        <!-- FEATURE BOX #2 -->
                        <div class="col">
                            <div class="fbox-11 fb-2 wow fadeInUp">

                                <!-- Icon -->
                                <div class="fbox-ico-wrap">
                                    <div class="fbox-ico ico-50">
                                        <div class="shape-ico color--theme">

                                            <!-- Vector Icon -->
                                            <span class="flaticon-trophy"></span>

                                            <!-- Shape -->
                                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
                                            </svg>

                                        </div>
                                    </div>
                                </div> <!-- End Icon -->

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">Análise de Dados</h6>
                                    <p>Na análise de dados, coleta-se, processa-se e interpreta-se conjuntos de dados
                                        para extrair informações relevantes, identificar padrões e auxiliar na tomada de decisões informadas.
                                    </p>
                                </div>

                            </div>
                        </div> <!-- END FEATURE BOX #2 -->


                        <!-- FEATURE BOX #3 -->
                        <div class="col">
                            <div class="fbox-11 fb-3 wow fadeInUp">

                                <!-- Icon -->
                                <div class="fbox-ico-wrap">
                                    <div class="fbox-ico ico-50">
                                        <div class="shape-ico color--theme">

                                            <!-- Vector Icon -->
                                            <span class="flaticon-hierarchical-structure"></span>

                                            <!-- Shape -->
                                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
                                            </svg>

                                        </div>
                                    </div>
                                </div> <!-- End Icon -->

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">Desenvolvimento Web & Mobile</h6>
                                    <p>No desenvolvimento web e mobile, cria-se e mantém aplicativos e sites que proporcionam uma experiência
                                        interativa e funcional para os usuários em navegadores e dispositivos móveis.
                                    </p>
                                </div>

                            </div>
                        </div> <!-- END FEATURE BOX #3 -->


                        <!-- FEATURE BOX #4 -->
                        <div class="col">
                            <div class="fbox-11 fb-4 wow fadeInUp">

                                <!-- Icon -->
                                <div class="fbox-ico-wrap">
                                    <div class="fbox-ico ico-50">
                                        <div class="shape-ico color--theme">

                                            <!-- Vector Icon -->
                                            <span class="flaticon-rotate"></span>

                                            <!-- Shape -->
                                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
                                            </svg>

                                        </div>
                                    </div>
                                </div> <!-- End Icon -->

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">Software ERP</h6>
                                    <p>ERP (Enterprise Resource Planning), cria-se e implementa-se um sistema integrado que gerencia e centraliza as informações
                                        de diferentes áreas de uma empresa, como finanças, recursos humanos, produção e vendas, visando aumentar a eficiência e a tomada de decisões.
                                    </p>
                                </div>

                            </div>
                        </div> <!-- END FEATURE BOX #4 -->


                        <!-- FEATURE BOX #5 -->
                        <div class="col">
                            <div class="fbox-11 fb-5 wow fadeInUp">

                                <!-- Icon -->
                                <div class="fbox-ico-wrap">
                                    <div class="fbox-ico ico-50">
                                        <div class="shape-ico color--theme">

                                            <!-- Vector Icon -->
                                            <span class="flaticon-click"></span>

                                            <!-- Shape -->
                                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
                                            </svg>

                                        </div>
                                    </div>
                                </div> <!-- End Icon -->

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">Processo Simples</h6>
                                    <p>Especialização é o caminho. Ao dividir um sistema complexo em módulos específicos,
                                        conseguimos focar em soluções mais eficientes, otimizando cada componente para sua função.
                                    </p>
                                </div>

                            </div>
                        </div> <!-- END FEATURE BOX #5 -->


                        <!-- FEATURE BOX #6 -->
                        <div class="col">
                            <div class="fbox-11 fb-6 wow fadeInUp">

                                <!-- Icon -->
                                <div class="fbox-ico-wrap">
                                    <div class="fbox-ico ico-50">
                                        <div class="shape-ico color--theme">

                                            <!-- Vector Icon -->
                                            <span class="flaticon-hosting"></span>

                                            <!-- Shape -->
                                            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M69.8,-23C76.3,-2.7,57.6,25.4,32.9,42.8C8.1,60.3,-22.7,67,-39.1,54.8C-55.5,42.7,-57.5,11.7,-48.6,-11.9C-39.7,-35.5,-19.8,-51.7,5.9,-53.6C31.7,-55.6,63.3,-43.2,69.8,-23Z" transform="translate(100 100)" />
                                            </svg>

                                        </div>
                                    </div>
                                </div> <!-- End Icon -->

                                <!-- Text -->
                                <div class="fbox-txt">
                                    <h6 class="s-22 w-700">"SaaS" (Software as a Service)</h6>
                                    <p>Software como Serviço. É um modelo de entrega de software que permite aos usuários acessar aplicativos pela internet,
                                        sem instalação local. Com opções de assinatura, o SaaS oferece um custo acessível e elimina a necessidade de investimentos em hardware.
                                    </p>
                                </div>

                            </div>
                        </div> <!-- END FEATURE BOX #6 -->


                    </div> <!-- End row -->
                </div> <!-- END FEATURES-11 WRAPPER -->


            </div> <!-- End container -->
        </section> <!-- END FEATURES-11 -->




        <!-- BOX CONTENT -->
        <section id="lnk-2" class="pt-100 ws-wrapper content-section">
            <div class="container">
                <div class="bc-1-wrapper bg--white-400 bg--fixed r-16">
                    <div class="section-overlay">
                        <div class="row d-flex align-items-center">


                            <!-- IMAGE BLOCK -->
                            <div class="col-md-6">
                                <div class="img-block left-column wow fadeInRight">
                                    <img class="img-fluid" src="Martex/images/img-10.png" alt="content-image">
                                </div>
                            </div>


                            <!-- TEXT BLOCK -->
                            <div class="col-md-6">
                                <div class="txt-block right-column wow fadeInLeft">

                                    <!-- Title -->
                                    <h2 class="s-46 w-700">Currículo Fácil</h2>

                                    <!-- Text -->
                                    <p>Projetado com um visual simples, moderno, limpo e único.
                                        Basta escolher seu plano do básico ao premium. Cadastre-se e comece a usar agora mesmo!
                                    </p>

                                    <!-- Small Title -->
                                    <h5 class="s-24 w-700 h5-title">CVFácil. Seu Porfólio Profissional!</h5>

                                    <!-- Text -->
                                    <p class="mb-0">Aproveite o cumpom de desconto e cadastre-se já!<br>
                                        São 90 dias, seu portfólio livre de anúncios.
                                    </p>
                                    <div class="text-center mt-4">
                                        <h2 style="padding: 0.2em; background-color: #fcf8e3; font-weight: 400; font-size: 2.5rem; line-height: 1.2;">CVFACIL50</h2>
                                    </div>

                                    <a href="https://cvfacil.com.br" target="_blank" class="btn r-04 btn--theme hover--theme last-link">Acessar CVFácil</a>

                                </div>
                            </div> <!-- END TEXT BLOCK -->


                        </div> <!-- End row -->
                    </div> <!-- End section overlay -->
                </div> <!-- End content wrapper -->
            </div> <!-- End container -->
        </section> <!-- END BOX CONTENT -->




        <!-- TEXT CONTENT -->
        <section class="pt-100 ct-01 content-section division">
            <div class="container">


                <!-- SECTION CONTENT (ROW) -->
                <div class="row d-flex align-items-center">


                    <!-- TEXT BLOCK -->
                    <div class="col-md-6 order-last order-md-2">
                        <div class="txt-block left-column wow fadeInRight">


                            <!-- TEXT BOX -->
                            <div class="txt-box">

                                <!-- Title -->
                                <h5 class="s-24 w-700">Soluções alternativas na medida</h5>

                                <!-- Text -->
                                <p>Transforme a maneira como sua empresa gerencia o estoque com o Shine Stock, o software de gestão de estoque
                                    projetado especialmente para atender às necessidades do setor de compras e recebimentos de matéria-prima
                                    em empresas de manufatura.
                                </p>

                            </div> <!-- END TEXT BOX -->


                            <!-- TEXT BOX -->
                            <div class="txt-box mb-0">

                                <!-- Title -->
                                <h5 class="s-24 w-700">Por que escolher o Shine Stock?</h5>

                                <!-- Text -->
                                <!-- <p>Não perca mais tempo com soluções ineficazes! Experimente o Shine Stock e veja como é 
                                    fácil otimizar a gestão do seu estoque, liberando seu tempo para focar no que realmente importa: 
                                    o crescimento da sua empresa!
                                </p> -->

                                <!-- List -->
                                <ul class="simple-list">

                                    <li class="list-item">
                                        <p class="mb-3">Eficiência e Precisão: Elimine erros e atrasos com uma plataforma
                                            intuitiva que automatiza processos críticos, garantindo que você tenha sempre
                                            as informações mais precisas sobre seu estoque.
                                        </p>
                                    </li>

                                    <li class="list-item">
                                        <p class="mb-3">Visibilidade em Tempo Real: Monitore seu estoque em tempo real e tenha acesso
                                            a relatórios detalhados que ajudam a tomar decisões estratégicas para otimizar a produção
                                            e reduzir custos.
                                        </p>
                                    </li>

                                    <li class="list-item">
                                        <p class="mb-3">Facilidade de Uso: Desenvolvido com uma interface amigável, o Shine Stock é fácil de usar,
                                            permitindo que sua equipe se adapte rapidamente e maximize a produtividade.
                                        </p>
                                    </li>

                                    <li class="list-item">
                                        <p class="mb-3">Escalabilidade: Seja você uma pequena empresa ou uma grande manufatura, o Shine Stock se adapta
                                            às suas necessidades, crescendo junto com o seu negócio.
                                        </p>
                                    </li>

                                </ul>

                            </div> <!-- END TEXT BOX -->


                        </div>
                    </div> <!-- END TEXT BLOCK -->


                    <!-- IMAGE BLOCK -->
                    <div class="col-md-6 order-first order-md-2">
                        <div class="img-block right-column wow fadeInLeft">
                            <img class="img-fluid" src="Martex/images/img-12.png" alt="content-image">
                        </div>
                    </div>


                </div> <!-- END SECTION CONTENT (ROW) -->

                <!-- Button -->
                <a href="#" class="btn btn-sm r-04 btn--tra-black hover--theme">
                    Ver mais informação
                </a>


            </div> <!-- End container -->
        </section> <!-- END TEXT CONTENT -->




        <!-- STATISTIC-5 -->
        <div id="statistic-5" class="pt-100 statistic-section division">
            <div class="container">


                <!-- STATISTIC-1 WRAPPER -->
                <div class="statistic-5-wrapper">
                    <div class="row row-cols-1 row-cols-md-3">


                        <!-- STATISTIC BLOCK #1 -->
                        <div class="col">
                            <div id="sb-5-1" class="wow fadeInUp">
                                <div class="statistic-block">

                                    <!-- Digit -->
                                    <!-- <div class="statistic-digit">
                                        <h2 class="s-44 w-700">
                                            <span class="count-element">26</span>.<span class="count-element">62</span>k
                                        </h2>
                                    </div> -->

                                    <!-- Text -->
                                    <div class="statistic-txt">
                                        <h5 class="s-19 w-700">Mestre da Reserva</h5>
                                        <p>Organize as reservas de espaços, como sala de jogos, sala de reuniões, espaço pet e muito mais...</p>
                                    </div>

                                </div>
                            </div>
                        </div> <!-- END STATISTIC BLOCK #1 -->


                        <!-- STATISTIC BLOCK #2 -->
                        <div class="col">
                            <div id="sb-5-2" class="wow fadeInUp">
                                <div class="statistic-block">

                                    <!-- Digit -->
                                    <!-- <div class="statistic-digit">
                                        <h2 class="s-44 w-700">
                                            <span class="count-element">13</span>.<span class="count-element">54</span>k
                                        </h2>
                                    </div> -->

                                    <!-- Text -->
                                    <div class="statistic-txt">
                                        <h5 class="s-19 w-700">Mural Público</h5>
                                        <p>Gestores do condomínio, notifiquem todos os moradores com alta prioridade.</p>
                                    </div>

                                </div>
                            </div>
                        </div> <!-- END STATISTIC BLOCK #2 -->


                        <!-- STATISTIC BLOCK #3 -->
                        <div class="col">
                            <div id="sb-5-3" class="wow fadeInUp">
                                <div class="statistic-block">

                                    <!-- Digit -->
                                    <!-- <div class="statistic-digit">
                                        <h2 class="s-44 w-700">
                                            <span class="count-element">4</span>.<span class="count-element">87</span>/5
                                        </h2>
                                    </div> -->

                                    <!-- Text -->
                                    <div class="statistic-txt">
                                        <h5 class="s-19 w-700">Comerciantes Locais</h5>
                                        <p>Fortaleça a economia da região e as parcerias com o condomínio.</p>
                                    </div>

                                </div>
                            </div>
                        </div> <!-- END STATISTIC BLOCK #3 -->


                    </div> <!-- End row -->
                </div> <!-- END STATISTIC-5 WRAPPER -->


            </div> <!-- End container -->
        </div> <!-- END STATISTIC-5 -->




        <!-- TEXT CONTENT -->
        <section class="py-100 ct-02 content-section division">
            <div class="container">


                <!-- SECTION CONTENT (ROW) -->
                <div class="row d-flex align-items-center">


                    <!-- IMAGE BLOCK -->
                    <div class="col-md-6">
                        <div class="img-block left-column wow fadeInRight">
                            <img class="img-fluid" src="Martex/images/img-11.png" alt="content-image">
                        </div>
                    </div>


                    <!-- TEXT BLOCK -->
                    <div class="col-md-6">
                        <div class="txt-block right-column wow fadeInLeft">

                            <!-- Title -->
                            <h2 class="s-46 w-700">SocialLink para seu condomínio!</h2>

                            <!-- Text -->
                            <p>Rede social inovadora projetada especialmente para condomínios! Aqui, conectamos
                                moradores e facilitamos a gestão do seu espaço comum de forma prática e interativa.
                            </p>

                            <!-- Small Title -->
                            <h5 class="s-24 w-700">Interação e Gestão Simplificadas.</h5>

                            <!-- List -->
                            <ul class="simple-list">

                                <li class="list-item">
                                    <p class="mb-3">Com funcionalidades que vão desde a reserva de áreas de lazer,
                                        agendamentos de eventos até jogos e entretenimento.
                                    </p>
                                </li>

                                <li class="list-item">
                                    <p class="mb-3">SocialLink transforma a convivência em uma experiência única e agradável.
                                        Além disso, oferecemos um espaço para comerciantes locais divulgarem seus serviços,
                                        fortalecendo a economia da região e promovendo uma comunidade mais unida.
                                    </p>
                                </li>

                                <li class="list-item">
                                    <p class="mb-3">Junte-se a nós e descubra como o SocialLink pode tornar seu condomínio um lar ainda melhor!
                                    </p>
                                </li>

                            </ul>

                        </div>

                        <a href="#" class="btn r-04 btn--theme hover--theme last-link">Inscreva-se</a>

                    </div> <!-- END TEXT BLOCK -->


                </div> <!-- END SECTION CONTENT (ROW) -->


            </div> <!-- End container -->
        </section> <!-- END TEXT CONTENT -->




        <!-- TEXT CONTENT -->
        <section class="bg--white-300 py-100 ct-01 content-section division">
            <div class="container">


                <!-- SECTION CONTENT (ROW) -->
                <div class="row d-flex align-items-center">


                    <!-- TEXT BLOCK -->
                    <div class="col-md-6 order-last order-md-2">
                        <div class="txt-block left-column wow fadeInRight">

                            <!-- Title -->
                            <h2 class="s-46 w-700">Nosso Portal de Serviços à sua disposição.</h2>

                            <!-- Text -->
                            <p class="mb-0">O SITI Manager é uma solução inovadora desenvolvida pela Portal SITI,
                                projetada para gerenciar eficientemente os produtos contratados.
                                Nossa suíte de aplicativos, cuidadosamente organizada em nosso site, está sempre disponível
                                para atender suas necessidades.
                            </p>

                            <!-- Button -->
                            <a href="#" class="btn btn-sm r-04 btn--tra-black hover--theme">
                                Explore nossos produtos
                            </a>

                        </div>
                    </div> <!-- END TEXT BLOCK -->


                    <!-- IMAGE BLOCK -->
                    <div class="col-md-6 order-first order-md-2">
                        <div class="img-block right-column wow fadeInLeft">
                            <img class="img-fluid" src="Martex/images/img-02.png" alt="content-image">
                        </div>
                    </div>


                </div> <!-- END SECTION CONTENT (ROW) -->


            </div> <!-- End container -->
        </section> <!-- END TEXT CONTENT -->


        <!-- DIVIDER LINE -->
        <hr class="divider">


        <!-- FOOTER-3 -->
        <footer id="footer-3" class="pt-100 footer">
            <div class="container">


                <!-- FOOTER CONTENT -->
                <div class="row">


                    <!-- FOOTER LOGO -->
                    <div class="col-xl-3">
                        <div class="footer-info">
                            <img class="footer-logo" src="Martex/images/logo-portal-siti.png" alt="footer-logo">
                        </div>
                    </div>


                    <!-- FOOTER LINKS -->
                    <!-- <div class="col-sm-4 col-md-3 col-xl-2">
                        <div class="footer-links fl-1">

                            Title
                            <h6 class="s-17 w-700">Company</h6>

                            Links
                            <ul class="foo-links clearfix">
                                <li>
                                    <p><a href="about.php">About Us</a></p>
                                </li>
                                <li>
                                    <p><a href="careers.php">Careers</a></p>
                                </li>
                                <li>
                                    <p><a href="blog-listing.php">Our Blog</a></p>
                                </li>
                                <li>
                                    <p><a href="contacts.php">Contact Us</a></p>
                                </li>
                            </ul>

                        </div>
                    </div>  -->
                    <!-- END FOOTER LINKS -->


                    <!-- FOOTER LINKS -->
                    <!-- <div class="col-sm-4 col-md-3 col-xl-2">
                        <div class="footer-links fl-2">

                            Title
                            <h6 class="s-17 w-700">Product</h6>

                            Links
                            <ul class="foo-links clearfix">
                                <li>
                                    <p><a href="features.php">Integration</a></p>
                                </li>
                                <li>
                                    <p><a href="reviews.php">Customers</a></p>
                                </li>
                                <li>
                                    <p><a href="pricing-1.php">Pricing</a></p>
                                </li>
                                <li>
                                    <p><a href="help-center.php">Help Center</a></p>
                                </li>
                            </ul>

                        </div>
                    </div>  -->
                    <!-- END FOOTER LINKS -->


                    <!-- FOOTER LINKS -->
                    <!-- <div class="col-sm-4 col-md-3 col-xl-2">
                        <div class="footer-links fl-3">

                            Title
                            <h6 class="s-17 w-700">Legal</h6>

                            Links
                            <ul class="foo-links clearfix">
                                <li>
                                    <p><a href="terms.php">Terms of Use</a></p>
                                </li>
                                <li>
                                    <p><a href="privacy.php">Privacy Policy</a></p>
                                </li>
                                <li>
                                    <p><a href="cookies.php">Cookie Policy</a></p>
                                </li>
                                <li>
                                    <p><a href="#">Site Map</a></p>
                                </li>
                            </ul>

                        </div>
                    </div>  -->
                    <!-- END FOOTER LINKS -->


                    <!-- FOOTER LINKS -->
                    <div class="col-sm-6 col-md-3">
                        <div class="footer-links fl-4">

                            <!-- Title -->
                            <h6 class="s-17 w-700">Nosso contato</h6>

                            <!-- Mail Link -->
                            <p class="footer-mail-link ico-25">
                                <a href="mailto:contato@portalsiti.com.br">contato@portalsiti.com.br</a>
                            </p>

                            <!-- Social Links -->
                            <!-- <ul class="footer-socials ico-25 text-center clearfix">
                                <li><a href="#"><span class="flaticon-facebook"></span></a></li>
                                <li><a href="#"><span class="flaticon-twitter"></span></a></li>
                                <li><a href="#"><span class="flaticon-github"></span></a></li>
                                <li><a href="#"><span class="flaticon-dribbble"></span></a></li>
                            </ul> -->

                        </div>
                    </div> <!-- END FOOTER LINKS -->


                </div> <!-- END FOOTER CONTENT -->


                <hr> <!-- FOOTER DIVIDER LINE -->


                <!-- BOTTOM FOOTER -->
                <div class="bottom-footer">
                    <div class="row row-cols-1 row-cols-md-2 d-flex align-items-center">


                        <!-- FOOTER COPYRIGHT -->
                        <div class="col">
                            <div class="footer-copyright">
                                <p class="p-sm">&copy; 2025 Portal SITI. <span>Todos os direitos reservados</span></p>
                            </div>
                        </div>


                        <!-- FOOTER SECONDARY LINK -->
                        <!-- <div class="col">
                            <div class="bottom-secondary-link ico-15 text-end">
                                <p class="p-sm"><a href="https://themeforest.net/user/dsathemes/portfolio">Made with
                                        <span class="flaticon-heart"></span> by @DSAThemes</a>
                                </p>
                            </div>
                        </div> -->


                    </div> <!-- End row -->
                </div> <!-- END BOTTOM FOOTER -->


            </div> <!-- End container -->
        </footer> <!-- END FOOTER-3 -->




    </div> <!-- END PAGE CONTENT -->

    <!-- EXTERNAL SCRIPTS -->
    <?php include App\Core\Config::$DIR_BASE . '/App/Views/Martex/Partials/script.php' ?>

</body>

</html>