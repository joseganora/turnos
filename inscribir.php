<div id="content-1" class="transp">

  <div class="scrollcontent">
    <h3 style="margin-bottom: 5px;">Enviar Solicitud</h3>
    <p  style="text-align:left" >
      Enviá tus datos para sumarte a la adoración comprometiendote con un horario.
    </p>
<table>
  <tbody>
    <tr>
      <td class="label">
        Nombre Completo:
      </td>
      <td>
        <input class="valid" type="text" id="nombre" value="" placeholder="Nombre completo...">
      </td>
    </tr>
    <tr>
      <td class="label">
        Mail:
      </td>
      <td>
        <input class="valid" type="text" id="mail" value="" placeholder="Mail...">
      </td>
    </tr>
    <tr>
      <td class="label">
        Teléfono:
      </td>
      <td>
        <input class="valid" type="text" id="telefono" value="" placeholder="Telefono...">
      </td>
    </tr>
    <tr>
      <td class="label">
        Grupo:
      </td>
      <td>
        <select class="valid" id="id_grupo">
          <option value="">Elige un Grupo</option>
          <?php
          $grupos=$db->get_results("SELECT * from grupos where activo=1 ");
          foreach ($grupos as $key => $value) {
            ?>
            <option value="<?php echo $value->id ?>"><?php echo $value->nombre ?> (<?php echo $value->pseud ?>)</option>
            <?php
          }
           ?>
           <option value="0">Otro</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="label">
        Turno:
      </td>
      <td>
        <select class="valid" id="id_turno">
          <option value="">Elige un turno</option>
          <?php
          $turnos=$db->get_results("SELECT * from turnos where activo=1 order by dia,orden");
          $dias=array("","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado","Domingo");
          foreach ($turnos as $key => $value) {
            ?>
            <option value="<?php echo $value->id ?>"><?php echo $dias[$value->dia] ?> <?php echo $value->hora ?></option>
            <?php
          }
           ?>
        </select>
      </td>
    </tr>
  </tbody>
</table>


  </div>
  <div class="preguntas foot">
    <a id="enviarDatos" href="#" onclick="enviarDatos();return false;" class="btn" style="">Enviar Datos</a>
    <a href="?" class="btn" style="">Volver</a>
  </div>
</div>

<script type="text/javascript">
$( document ).ready(function() {
  viewContent('content-1');
});
function validar(){
  error = '';
  $('.valid').each(function(){
    if(!$(this).val()){
    	error += ' '+$(this).attr('id');
    	$(this).addClass('required');
  	}else{
  		$(this).removeClass('required');
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





function enviarDatos(){
  $('#enviarDatos').text("Cargando...");
  if(validar()){
    $.post("cargarDatos.php",
                {
                  nombre: $('#nombre').val(),
                  mail: $('#mail').val(),
                  telefono: $('#telefono').val(),
                  id_grupo: $('#id_grupo').val(),
                  id_turno: $('#id_turno').val(),
                },
              function(data){
                var respuesta=JSON.parse(data);
                if(respuesta.estado==0){
                  alert(respuesta.error_text);
                }
                if(respuesta.estado==1){
                  alert(respuesta.text);
                  location.href="?";
                }

              });
  }
  $('#enviarDatos').text("Enviar Datos");
}
</script>
