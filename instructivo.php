
<div id="content-1" class="transp">
  <h3 style="margin-bottom: 5px;">Dirección Zonal Santo Domingo</h3>
  <p style="margin-top:0px;font-style: italic;text-align:center;font-size:13px;">
    Nuestra misión, la Ciudad
  </p>
  <div class="scrollcontent">

    <p style="text-align:center">
      <b>Adoración eucarística por los sacerdotes.</b>
    </p>
    <p onclick="view('instructivo')"  class="btn-2">
      Instructivo
    </p>
    <div id="instructivo" class="content-p">
      <p >
        <b>1. Disponer un lugar de la casa apropiado</b>, silencioso,
        donde no tengas interrupciones: poner en una mesita un mantel, una imagen de
        la Virgen o una cruz, una velita encendida.
      </p>
      <p>
        <b>2. Buscar un sitio de internet donde se haga Adoración Eucarística en vivo</b>. Hay
        muchos y de Adoración perpetua, es decir: a toda hora.
      </p>
      <p>
        <b>3. Hacer la guardia Eucarística en la que te anotaste</b>, respetando tu horario. Si por
        alguna razón no podés, te pedimos que consigas un reemplazante.
      </p>
      <p>
        <b>4.</b> Tené en cuenta que <b>la presencia de Jesucristo es real aunque sea por medio de
        una pantalla</b>. Es importante que seamos conscientes que, aunque físicamente
        no estemos frente al Santísimo expuesto, sí lo estamos temporalmente.
      </p>
    </div>
    <p onclick="view('links')"  class="btn-2">
      Links de adoración
    </p>
    <div id="links" class="content-p">
      <?php
      $links=$db->get_results("SELECT * from links where activo=1");
      if($links){
        foreach ($links as $key => $value) {
          ?>
          <a target="_blank" href="<?php echo $value->href ?>"><?php echo $value->nombre ?></a>
          <?php
        }
      }else{
        echo "No tenemos links cargados actualmente";
      }

       ?>
    </div>
    <p onclick="view('quehacer')" class="btn-2">
      ¿Que hacer en adoración?
    </p>
    <div id="quehacer" class="content-p">
      <p>
        Durante tu hora de Adoración Eucarística te sugerimos:
        <ul style="padding-right: 20px;text-align: left;">
          <li>Rezar la Liturgia de las horas (podes bajar la App en el celular)</li>
          <li>Rezar el Rosario</li>
          <li>Leer la Biblia: lectura orante y meditada de la Palabra.</li>
          <li>Leer algún libro de piedad.</li>
          <li>Poné alguna canción de alabanza a Jesús Eucaristía y cantá. El que canta
          reza dos veces.</li>
          <li>En la App Convivum podés encontrar varias oraciones, si no, te pasamos
          algunas en un archivo adjunto, o las que sean de tu preferencia.</li>
          <li>No olvides rezar por el Papa, la Iglesia y los sacerdotes. Hoy más que
          nunca necesitan nuestra oración.</li>
        </ul>
      </p>
    </div>

  </div>
  <div class="preguntas foot">
    <a href="?" class="btn" style="">Volver</a>
  </div>
</div>

<script type="text/javascript">
$( document ).ready(function() {
  viewContent('content-1');
});
function view(id){
  if($('#'+id).css('display')=='none'){
    $('.content-p').hide();
    $('#'+id).show(300);
  }else{
    $('#'+id).hide(300);
  }
}
</script>
