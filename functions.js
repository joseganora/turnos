function viewContent(idContent){
  try{
    if($('#'+idContent).css('display')=='none'){
      $('#'+idContent).css("display", "flex")
      .hide()
      .fadeIn(500);
    }
  }catch(exc){
    document.getElementById(idContent).style.display="flex";
  }

}

$( document ).ready(function() {
    $('body').append('<backGrey id="loader"><loader></loader></backGrey>')
});

function loading(){
  $('#loader').show();
  $('loader').show();
}

function dismiss_load(){
  $('#loader').hide();
  $('loader').hide();
}
