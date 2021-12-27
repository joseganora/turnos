<?php

session_start();
if(!$_SESSION['idusuario']){
  $_SESSION['hcode']=$_GET['hcode'];
  $_SESSION['ruca']='1';
  header('location:login.php');
}

$hash=$_GET['hcode'];
if(!$hash){
  echo "No hay hash";
  die();
}
include('./core.php');
$ruca=$db->get_row("SELECT * from rucas where hash_access='$hash'");
if(!isset($ruca)){
  echo "hash incorrecto";
  die();
}
if($_GET['semana']){
  $week=$_GET['semana'];
}else{
  $week=date('W-Y');
}
$inscriptos=$db->get_results("SELECT *,(SELECT nombre FROM secciones where id=inscriptos_rucas.id_seccion) seccion from inscriptos_rucas where id_seccion in (SELECT id from secciones where id_ruca=$ruca->id) and FROM_UNIXTIME(timestamp, '%V-%Y')='$week' order by checked,apellido,nombre");
$countLista=count($inscriptos);
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
      .form_modal select{
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
      .from_subtitle{
        color: #fff;
        font-weight: bold;
        text-align: center;
        font-size: 20px;
        margin: 5px;
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
        $.get("set_check.php",{id:id_inscripticion,value:value,action:'rucas'});
      }
      function deleteInsc(id_inscripticion,tar){
        var conf=confirm("¿SEGURO QUE DESEA BORRAR ESTE REGISTRO?");
        if(conf){
          $.get("set_check.php",{id:id_inscripticion,action:'delete_rucas'});
          $(tar).closest('tr').remove();
        }
      }
      function valid(){
        error = '';
        $('.valid').each(function(){
          if(!$(this).val()){

            error += ' '+$(this).attr('name')+'. ';
            $(this).addClass('required');
          }else{
            $(this).removeClass('required');
          }
          });
        if(error.length>0){
          alert("Debes completar los siguientes campos: "+error);
          return false;
        }else{
          return true;
        }
      }
      function agregarIns(){
        if(valid()){
          $('#enviar_agregar').prop('disabled',true);
          $.get("add_inscripcion.php",
          {
            id_seccion:$('select[name=id_seccion]').val(),
            apellido:$('input[name=apellido]').val(),
            nombre:$('input[name=nombre]').val(),
            apellido_t:$('input[name=apellido_t]').val(),
            nombre_t:$('input[name=nombre_t]').val(),
            telefono:$('input[name=telefono]').val(),
            },
          function(){
            alert("¡Agregaste un nuevo participante!");
            location.reload();
          });
        }else{
          $('#enviar_agregar').prop('disabled',false);
        }
      }
      function change_semana(value){
        if(value){
          var loc="<?php echo rewriteGet('list_online_rucas.php',array('semana'),array('reemplazar')); ?>";
          location.href=loc.replace('reemplazar', value);
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
        <select class="valid" name="id_seccion">
          <option value="">Sección</option>
          <?php
          $secciones=$db->get_results("SELECT * from secciones where id_ruca=$ruca->id");
          foreach ($secciones as $key => $value) {
            ?>
            <option value="<?php echo $value->id ?>"><?php echo $value->nombre ?></option>
            <?php
          }
           ?>
        </select>
        <p class="from_subtitle">Miliciano</p>
        <input type="text" name="apellido" class="valid" placeholder="Apellido...">
        <input type="text" name="nombre" class="valid" placeholder="Nombre...">
        <p class="from_subtitle">Tutor</p>
        <input type="text" name="apellido_t" class="valid" placeholder="Apellido...">
        <input type="text" name="nombre_t" class="valid" placeholder="Nombre...">
        <input type="text" name="telefono" class="valid" placeholder="Teléfono...">
        <button id="enviar_agregar" type="button" name="button" onclick="agregarIns()">Enviar</button>
      </div>
    </div>
    <div class="container">
      <h3>Lista de inscriptos <?php echo $ruca->nombre ?> (<?php echo $countLista ?>)</h3>
      <h5>
        <select style="margin-bottom: 5px;width: calc(100% - 6px);" class="buscar" onchange="change_semana(this.value)">
          <?php
          $meses=$db->get_results("SELECT FROM_UNIXTIME(timestamp, '%V-%Y') semana, min(timestamp) time from inscriptos_rucas where id_seccion in (SELECT id from secciones where id_ruca=$ruca->id) group by semana order by time desc");
          foreach ($meses as $k => $v) {
            ?>
            <option value="<?php echo $v->semana ?>" <?php if(isset($_GET['semana']) && $_GET['semana']==$v->semana) echo 'selected'; ?>>Semana <?php echo $v->semana ?></option>
            <?php
          }
           ?>

        </select>
        <input id="busqueda" type="text" class="buscar" name="" value="" placeholder="Buscar..."></h5>
      <table>

        <?php
        foreach ($inscriptos as $key => $value) {

          ?>
          <tr>
            <td><?php echo $value->apellido.' '.$value->nombre ?></td>
            <td><?php
            $seccion=$value->seccion;
            $seccion=preg_replace("/\([^)]+\)/","",$seccion);
            $seccion=tres_letras($seccion);
            echo $seccion ?></td>
            <td style="text-align:center"> <input class="check" type="checkbox" name="" onclick="set_checkBox(<?php echo $value->id ?>,this.checked)" <?php if($value->checked) echo 'checked'; ?>> </td>
            <td style="padding-left: 5px;padding-right: 5px;">
              <a href="#" onclick="deleteInsc(<?php echo $value->id ?>,this);return false;" style="text-decoration: none;color: #000;"> <i class="icon-trash"></i> </a>
            </td>
          </tr>
          <?php
        }
         ?>
      </table>
    </div>


  </body>
</html>
<?php
function tres_letras($seccion){
  $nuevo_texto='';
  $seccion=trim($seccion);
  $palabras=explode(' ',$seccion);
  foreach ($palabras as $key => $value) {
   if($key>0){
     $nuevo_texto.=' ';

   }
   $nuevo_texto.=substr($value,0,3).'.';
  }
  return $nuevo_texto;
}
 ?>
