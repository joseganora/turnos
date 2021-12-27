<?php

session_start();
if(!$_SESSION['idusuario']){
  $_SESSION['hcode']=$_GET['hcode'];
  header('location:login.php');
}

$hash=$_GET['hcode'];
if(!$hash){
  echo "No hay hash";
  die();
}
include('./core.php');
$turno=$db->get_row("SELECT * from turnos where hash='$hash'");
if(!isset($turno)){
  echo "hash incorrecto";
  die();
}
$inscriptos=$db->get_results("SELECT f.*,tf.id as id_reserva, tf.checked  from turnos_fieles tf join fieles f on tf.id_fiel=f.id where id_turno='$turno->id' order by tf.checked, f.nombre");
$countLista=count($inscriptos);
$dias=array("",'Lunes','Martes','Miercoles','Jueves','Viernes','Sábado','Domingo');
$dia=$dias[$turno->dia];
?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
    <title>Lista de Inscriptos</title>
    <style>
      .container{
        max-width: 400px;
        margin: auto;
      }
      table{
        width:100%;
        font-size: 18px;
      }
      table td{
        border-bottom: solid 1px #ccc;
      }
      input.check{
        width: 20px;
        height: 20px;
      }
      .buscar{
        width:calc(100% - 20px);
        padding: 5px;
      }
      h3{
        margin-bottom: 0px;
      }
      h4{
        margin-top: 0px;
      }
      .button-float{
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #d0d0d0;
        padding: 10px;
        border-radius: 30px;
        height: 20px;
        width: 20px;
        display: flex;
        border: solid 1px;
      }
      .button-float:active{
        background: #eae9e9;
      }
      .button-float i{
        margin:auto;
      }
      .form_modal{
        height: 100%;
        width: 100%;
        position: fixed;
        z-index: 1000;
        background: #000000b8;
        margin: 0px;
        margin-top: -19px;
        margin-left: -8px;

      }
      .form_modal .close_btn{
        position: absolute;
        top: 20px;
        right: 20px;
        color: white;
      }
      .form_modal .content{
        padding: 10px;
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: auto;
      }
      .form_modal h3{
        text-align: center;
        color: #fff;
        text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;
        margin-bottom: 20px;
        font-size: 28px;
      }
      .form_modal input{
        margin:5px;
        padding: 5px;
        font-size: 16px;
      }
      .form_modal button{
        margin:5px;
        padding: 5px;
        font-size: 16px;
        margin-top:auto;
        margin-bottom: 20px;
      }
    </style>
    <script type="text/javascript">
      function open_modal(){
        $('.form_modal').show(300);
      }
      function close_modal(){
        $('.form_modal').hide(300);
      }
      $( document ).ready(function(){
        $("#busqueda").on("input", function(){
          value= $(this).val();
          if(value.length>0){
            $("table tr").filter(function() {
              var bandR=$(this).text().toLowerCase().indexOf(value.toLowerCase()) > -1;
              $(this).toggle(bandR);
            });
          }else{
            $("table tr").filter(function() {
              $(this).toggle(true);
            });
          }
        });
      });
      function set_checkBox(id_inscripticion,value){
        $.get("set_check.php",{id:id_inscripticion,value:value});
      }
      function agregarFiel(){

        $('#enviar_agregar').prop('disabled',true);
        if($('input[name=nombre]').val().length>0){
          $.get("add_fiel.php",
          {id_turno:<?php echo $turno->id ?>,
            nombre:$('input[name=nombre]').val(),
            telefono:$('input[name=telefono]').val(),
            mail:$('input[name=mail]').val()},
          function(){
            alert("¡Agregaste un nuevo participante!");
            location.reload();
          });
        }else{
          alert("Debes completar el campo nombre");
          $('#enviar_agregar').prop('disabled',false);
        }

      }
    </script>
  </head>
  <body>
    <div class="button-float" onclick="open_modal()">
      <i class="icon-plus"></i>
    </div>
    <div class="form_modal" style="display:none">
      <div class="close_btn">
        <i class="icon-remove" onclick="close_modal()"></i>
      </div>
      <div class="content">
        <h3>Agregar</h3>
        <input type="text" name="nombre" value="" placeholder="Nombre...">
        <input type="text" name="telefono" value="" placeholder="Teléfono...">
        <input type="text" name="mail" value="" placeholder="Email...">
        <button id="enviar_agregar" type="button" name="button" onclick="agregarFiel()">Enviar</button>
      </div>
    </div>
    <div class="container">
      <h3>Lista de inscriptos <?php echo dame('capillas',$turno->id_capilla,'nombre') ?></h3>
      <h4><?php echo $dia.' '.$turno->fecha.' '.$turno->hora.' h. - CANTIDAD: '.$countLista ?></h4>
      <h5><input id="busqueda" type="text" class="buscar" name="" value="" placeholder="Buscar..."></h5>
      <table>

        <?php
        foreach ($inscriptos as $key => $value) {
          ?>
          <tr>
            <td><?php echo $value->nombre ?></td><td style="text-align:center"> <input class="check" type="checkbox" name="" onclick="set_checkBox(<?php echo $value->id_reserva ?>,this.checked)" <?php if($value->checked) echo 'checked'; ?>> </td>
          </tr>
          <?php
        }
         ?>
      </table>
    </div>


  </body>
</html>
