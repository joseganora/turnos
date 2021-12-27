<?php
include('./connect.php');
session_start();
$_SESSION['marca']=getCode();
if($_GET['borrar']==1){
  session_destroy();
  header('location:index.php');
}
if(!isset($_GET['provincia']) || !isset($_GET['ciudad'])){
  header("Location: index.php");
}else{

}
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Rucas</title>
    <link rel="image_src" href="./img/img.jpg"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1d1815">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" href="./styles.css" charset="utf-8">
    <script type="text/javascript" src="./functions.js?3"></script>
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Raleway" />
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-174104181-1"></script>
    <script>

      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'UA-174104181-1');


      function goRuca(hash){
        window.location="./secciones_ruca.php?h="+hash;
      }
      $( document ).ready(function() {

        viewContent('content-1');
      });
    </script>
  </head>
  <body>
    <div class="container" onclick="viewContent('content-1')">
      <div id="content-1" class="transp">
        <h3>Rucas de <?php echo $_GET['provincia'] ?>, <?php echo $_GET['ciudad'] ?> </h3>
        <br>
        <?php
        $ciudad=$_GET['provincia'];
        $provincia=$_GET['ciudad'];
        if(isset($ciudad)&&isset($provincia)){
          $rucas=$db->get_results("SELECT * from rucas where ciudad='$ciudad' and provincia='$provincia'");
        }
        if(isset($rucas)&&count($rucas)>0){
          ?>
          <div class="scrollcontent">
            <table cellspacing="0" id="capillas" style="-webkit-user-select: none;-moz-user-select: none;-khtml-user-select: none;-ms-user-select:none;">
              <tbody>
              <?php
              foreach ($rucas as $key => $value) {
                ?>
                  <tr class="tr-capillas" onclick="goRuca('<?php echo $value->hash_access ?>')" style="cursor:pointer">
                    <td style="padding:15px 25px;"><?php echo $value->nombre ?></td>
                  </tr>
                <?php
              }
               ?>
             </tbody>
            </table>
          </div>
          <?php
        }else{
          ?>
          <p> <i>No hay rucas para esta provincia/ciudad</i> </p>
          <?php
        }
        ?>

        <div class="preguntas foot">
          <a id="btn-rucas" href="index.php" class="btn">Volver</a>
        </div>


      </div>

    </div>
  </body>
</html>
