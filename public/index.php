<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

switch ($_SESSION['rol']) {
    case 'usuario':
        header("Location: ../usuario/dashboard_usuario.php");
        break;
    case 'admin':
        header("Location: ../psicologo/dashboard.php");
        break;
    default:
        echo "Rol no reconocido";
}

?>
<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>GlowMind</title>

  <!-- slider stylesheet -->
  <link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/assets/owl.carousel.min.css" />

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css?family=Dosis:400,500|Poppins:400,700&display=swap" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />

  <style>
    /* Tamaño fijo para el carrusel y alineación de imágenes */
    .slider_section .carousel-inner {
      min-height: 400px;
      height: 400px;
      display: flex;
      align-items: center;
    }

    .slider_img-box {
      min-height: 320px;
      height: 320px;
      display: flex !important;
      align-items: center;
      justify-content: center;
    }

    .slider_img-box img {
      max-height: 220px;
      width: auto;
      height: auto;
      display: block;
      margin: 0 auto;
    }
  </style>
</head>

<body>
  <div class="hero_area">
    <!-- header section strats -->
    <header class="header_section">
      <div class="container-fluid">
        <nav class="navbar navbar-expand-lg custom_nav-container ">
          <a class="navbar-brand" href="index.html">
            <img src="images/LogoFinal (1).png" alt="" style="max-width: 60px; height: auto;">
            <span>
              GlowMind
            </span>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="d-flex mx-auto flex-column flex-lg-row align-items-center">
              <ul class="navbar-nav  ">
                <li class="nav-item active">
                  <a class="nav-link" href="index.php">Inicio <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="Servicios.html">Nuestros Servicios</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="About.html"> Acerca De Nosotros</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="Expertos.html"> Expertos </a>
                </li>
                
              </ul>
            </div>
            <div class="quote_btn-container  d-flex justify-content-center">
              <a href="/glowmind/public/registro.php" class="btn btn-outline-info mx-2">Registrarse</a>
              <a href="/glowmind/public/login.php" class="btn btn-info mx-2">Iniciar Sesión</a>
            </div>
          </div>
        </nav>
      </div>
    </header>
    <!-- end header section -->
    <!-- slider section -->
    <section class=" slider_section position-relative">
      <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
          <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
          <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
          <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
        </ol>
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-4 offset-md-2">
                  <div class="slider_detail-box">
                    <h1>
                      <span>
                        Bienvenido/a
                      </span>
                    </h1>
                    <p>
                      Este es un espacio inclusivo de apoyo psicológico para hombres y mujeres.
                      Encuentra orientación en maternidad, sexualidad, violencia y más.
                    </p>
                   <br>
                   <br>
                    
                  </div>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-center" style="min-height:320px; min-width:320px;">
                  <div class="slider_img-box w-100 h-100 d-flex align-items-center justify-content-center">
                    <img src="images/7694332.png">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-4 offset-md-2">
                  <div class="slider_detail-box">
                    <h1>
                      <span>
                      Un espacio seguro para ti
                      </span>
                    </h1>
                    <p>
                      Somos una comunidad segura donde todos pueden encontrar apoyo sin discriminación. 
                      Salud mental sin barreras de género.
                    </p>
                   
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="slider_img-box">
                    <img src="images/pngtree-the-concept-of-breaking-gender-stereotypes-about-traditional-relationships-png-image_6253023.png" alt="">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-4 offset-md-2">
                  <div class="slider_detail-box">
                    <h1>
                      <span>
                        Encuentra aquí!
                      </span>
                    </h1>
                    <p>
                      Cuestionarios emocionales, ayuda psicólogoica, recursos educativos y mucho más.
                    </p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="slider_img-box">
                    <img src="images/istockphoto-1363659665-612x612-Photoroom.png" alt="">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-4 offset-md-2">
                  <div class="slider_detail-box">
                    <h1>
                      <span>
                        Conócenos hoy
                      </span>
                    </h1>
  <p>Únete a nuestra comunidad de apoyo psicológico inclusivo.
  Salud mental sin barreras - comienza tu camino al bienestar emocional</p>
                   
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="slider_img-box">
                    <img src="images/Diseno-sin-titulo-1080x675-Photoroom.png" alt="">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </section>
    <!-- end slider section -->
  </div>

  <!-- about section -->

  <section class="about_section layout_padding">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="img-box">
            <br>
            <img src="images/1__tKX6mYu55DpgiAvp-eEgQ-Photoroom.png" alt="">
          </div>
        </div>
        <div class="col-md-6 d-flex align-items-start justify-content-center flex-column" style="height:100%;">
          <div class="detail-box w-100">
            <h2 class="custom_heading">
              ¿Quiénes 
              <span>
                Somos?
              </span>
            </h2>
            <br>
            <p style="text-align: justify;">
              En Glow Mind, trabajamos por la igualdad de género a través del apoyo psicológico. 
              Brindamos un espacio seguro e inclusivo para quienes enfrentan desafíos emocionales
               relacionados con la discriminación, la violencia o los estereotipos de género. Nuestro
                objetivo es fortalecer el bienestar y la autonomía de cada persona, promoviendo una 
                sociedad más justa y equitativa.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="service_section layout_padding">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6 offset-md-2">
          <h2 class="custom_heading">
             <span>Servicios</span>
          </h2>
          <div class="container layout_padding2">
            <div class="row">
              <div class="col-md-4">
                <div class="img_box">
                  <img src="images/cuesti.png" alt="">
                </div>
                <div class="detail_box">
                  <h6>
                    Cuestionarios
                  </h6>
                  <p>
                    Evalúa tu salud emocional con tests diseñados por expertos.
                     Recibe análisis y recomendaciones personalizadas.
                  </p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="img_box">
                  <img src="images/librooo.png" alt="">
                </div>
                <div class="detail_box">
                  <h6>
                    Material de Apoyo
                  </h6>
                  <p>
                    Biblioteca digital y material audiovisual con técnicas de control, artículos y recursos para el autocuidado emocional.
                  </p>
                </div>
              </div>
              <div class="col-md-4">
                <div class="img_box">
                  <img src="images/exper.png" alt="">
                </div>
                <div class="detail_box">
                  <h6>
                    Consultas con Expertos
                  </h6>
                  <p>
                    Pregunta a psicólogos especializados en diferentes areas. 
                    Soporte confidencial cuando lo necesites.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <img src="images/relaciones.png" alt="Psicología de género" class="w-100">
        </div>
      </div>
    </div>
  </section>


  <section class="client_section layout_padding-bottom">
    <div class="container">
      <h2 class="custom_heading text-center">
        Conoce a nuestros
        <span>
          Expertos
        </span>
      </h2>
      <br>
      <p class="text-center" style="text-align: justify;">
        Nuestros profesionales están aquí para brindarte apoyo y orientación en tu camino hacia el bienestar emocional.
        Cada uno de ellos aporta su experiencia y compromiso para ayudarte.
      </p>
      <div id="carouselExample2Indicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#carouselExample2Indicators" data-slide-to="0" class="active"></li>
          <li data-target="#carouselExample2Indicators" data-slide-to="1"></li>
          <li data-target="#carouselExample2Indicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="layout_padding2 pl-100">
              <div class="client_container ">
                <div class="img_box">
                  <img src="images/perfil11.jpg" alt="" width="100" height="100">
                </div>
                <div class="detail_box">
                  <h5>
                     Dra. Valeria Rojas - Especialista en violencia de género
                  </h5>
                  <p style="text-align: justify;">
                   Psicóloga clínica con 8 años de experiencia en atención a víctimas de violencia doméstica y abuso. Formada en terapia cognitivo-conductual y enfoque de género. Coordinadora del programa 'Espacios Seguros' en el Centro de Atención a Mujeres.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="layout_padding2 pl-100">
              <div class="client_container ">
                <div class="img_box">
                  <img src="images/perfil3.jpeg " width="100" height="100" alt="">
                </div>
                <div class="detail_box">
                  <h5>
                     Dr. Andrés Mendoza - Experto en salud mental masculina
                  </h5>
                  <p style="text-align: justify;">
                    Especialista en psicología masculina y deconstrucción de roles de género. Magíster en Terapia Sistémica. Creador del taller 'Hombres que Sienten' con 5 años de experiencia en grupos de apoyo emocional para varones.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="layout_padding2 pl-100">
              <div class="client_container ">
                <div class="img_box">
                  <img src="images/perfil22.jpg" alt="" width="100" height="100">
                </div>
                <div class="detail_box">
                  <h5>
                    Mtra. Camila Ortega - Psicóloga perinatal y sexualidad
                  </h5>
                  <p style="text-align: justify;">
                    Terapeuta certificada en salud reproductiva y sexualidad con enfoque de género. 6 años acompañando procesos de maternidad/paternidad y educación sexual inclusiva. Autora de guías sobre crianza no sexista.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>


    </div>

  </section>
 
  <section class="info_section layout_padding2">
    <div class="container">
      <div class="info_items">
       
        <a href="">
          <div class="item ">
            <div class="img-box box-3">
              <img src="" alt="">
            </div>
            <div class="detail-box">
              <p>
                GlowMind@gmail.com
              </p>
            </div>
          </div>
        </a>
         <a href="">
          <div class="item ">
            <div class="img-box box-2">
              <img src="" alt="">
            </div>
            <div class="detail-box">
              <p>
                +57 123 456 7890
              </p>
            </div>
          </div>
        </a>
        <a href="">
          <div class="item ">
            <div class="img-box box-3">
              <img src="" alt="">
            </div>
            <div class="detail-box">
              <p>
                GlowMindRespaldo@gmail.com
              </p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </section>

  <!-- end info_section -->

  <!-- footer section -->
  <section class="container-fluid footer_section">
    <p>
      &copy; 2025 GlowMind. Todos los derechos reservados.
    </p>
  </section>
  <!-- footer section -->

  <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
  <script type="text/javascript" src="js/bootstrap.js"></script>

</body>
</body>

</html>

