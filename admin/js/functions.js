// JavaScript Document

function validar(form){
error = '';
$('.valid').each(function(){
	if(!$(this).val()){
		error += ' '+$(this).attr('name');
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
function toggletActive(id_atu,tar,table){
	$.get("toggletActive.php?id="+id_atu+"&table="+table,
				function(data){
					var respuesta=JSON.parse(data);
					if(respuesta.estado==1){
						if(respuesta.action=="active"){
							$('#atu'+id_atu).removeClass('desactivado');
							$(tar).html('<i style="color:#063" class="icon-ok-sign"></i>');
						}else
						if(respuesta.action=="disactive"){
							$('#atu'+id_atu).addClass('desactivado');
							$(tar).html('<i style="color:#900" class="icon-remove-sign"></i>');
						}
					}else{
						alert(respuesta.error_text);
					}


				});
}
