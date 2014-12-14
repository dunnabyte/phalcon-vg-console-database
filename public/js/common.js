$(document).ready(function() {
  //processAjax("signup/register/","test","prepareAjax");
  $("body").on("click",".btn-submit",function(e){ 
    e.preventDefault();
    var thisForm = $(this).parents("form");
    processAjax(thisForm.attr("action"),thisForm.serialize(),"prepareAjax");
    
  });
});


function processAjax(thisUrl, thisData, thisPrepareFunction) {
  
  if(typeof window[thisPrepareFunction] === 'function') {
    window[thisPrepareFunction]();
  }
  $.ajax({
    url: thisUrl,
    data: thisData,
    dataType: "json",
    type: "post",
    timeout: 15000,
    success: function(responseData,responseStatus) {
      if(typeof responseData !== "object") {
        responseData = $.parseJSON(responseData);
      }
      
      try {
        //var thisResponse = $.parseJSON(responseData);
        if(responseData.message) {
          $(".container-message").html(responseData.message);
        }
      }
      catch(e) {
        alert(e);
      }
      
    },
    error: function(e, textStatus, textError) {
      //alert("error");
      alert(textError);
    }
  });
}
