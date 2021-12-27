<?php
include('./connect.php');
session_start();
$_SESSION['marca']=getCode();
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Turnos misas</title>
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

      function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
      }
      function validar(){
        error = '';
        $('.valid').each(function(){
          if(!$(this).val() || $(this).val().length<3){

            error += ' '+$(this).attr('name');
            $(this).addClass('required');
            }else{
              if(this.name=='mail'){
                if(!validateEmail(this.value)){
                  error += ' formato de email incorrecto';
                  $(this).addClass('required');
                }else{
                  $(this).removeClass('required');
                }
              }else{
                $(this).removeClass('required');
              }

              }
          });
          if(error){
            alert('Por favor complete los siguientes campos: '+error);
            $('.required').focus();
            return false;
            }else{
              return true;
              }

      }
      function enviarMensaje(){

        var valid=validar();
        if(valid){
          $.post("cargarDatos.php",
          {
            action:'addSolicitud',
            nombre:$('input[name=nombre]').val(),
            apellido:$('input[name=apellido]').val(),
            telefono:$('input[name=telefono]').val(),
            mail:$('input[name=mail]').val(),
            nombre_capilla:$('input[name=nombre_capilla]').val(),
            provincia:$('input[name=provincia]').val(),
            ciudad:$('input[name=ciudad]').val() },
          function(data){
            var result=JSON.parse(data);
            alert(result.text);
            if(result.estado=='1'){
              window.location="./index.php";
            }
          });
        }

      }

      $( document ).ready(function() {
        viewContent('content-1');
      });
    </script>
  </head>
  <body>
    <div class="container" onclick="viewContent('content-1')">
      <div id="content-1" class="transp">
        <br>
        <h3 style="margin-top: 10px;">¡Quiero agregar mi capilla!</h3>
        <br>
        <p>Dejanos un mensaje y nosotros nos contactamos con vos para agregar tu capilla.</p>
        <br>
        <div class="scrollcontent">
          <table>
            <tbody>
              <tr>
                <td class="label">Nombre:</td><td> <input type="text" class="valid" name="nombre" value="" placeholder="Indique su nombre..."> </td>
              </tr>
              <tr>
                <td  class="label">Apellido:</td><td><input type="text" class="valid" name="apellido" value="" placeholder="Indique apellido..."></td>
              </tr>
              <tr>
                <td class="label">Teléfono:</td><td><input type="text" class="valid" name="telefono" value="" placeholder="Indique un teléfono..."></td>
              </tr>
              <tr>
                <td class="label">Mail:</td><td><input type="text" class="valid" name="mail" value="" placeholder="Indique un Mail..."></td>
              </tr>
              <tr>
                <td class="label">Capilla:</td><td><input type="text" class="valid" name="nombre_capilla" value="" placeholder="Indique el nombre de la capilla..."></td>
              </tr>
              <tr>
                <td class="label">Provincia:</td><td><input type="text" class="valid" name="provincia" value="" placeholder="Indique su provincia..."></td>
              </tr>
              <tr>
                <td class="label">Ciudad:</td><td><input type="text" class="valid" name="ciudad" value="" placeholder="Indique su ciudad..."></td>
              </tr>
              <tr>
                <td class="label"></td> <td>
                  <a href="#" onclick="enviarMensaje()" class="btn">Enviar datos</a>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="preguntas foot">
          <a href="./index.php" class="btn">Volver</a>
        </div>

      </div>

    </div>
  </body>
</html>
