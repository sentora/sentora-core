(function($)     {
$.fn.extend(     {
jqf1: function() {

  //Textos dos inputs
  //Inputs texts
  var jqf1InpFileText   = 'Choose file';
  var jqf1InpFileText2  = 'Use the button to choose file';
  var jqf1InpSearchText = 'Type here to filter';

  //Detectar navegador
  //Browser detection
  var jqf1Browser = navigator.userAgent.toLowerCase();
  var j1f1Tag = $(this)[0].tagName;
  if ( (j1f1Tag != "SELECT") && (j1f1Tag != "INPUT") && (j1f1Tag != "TEXTAREA") && (j1f1Tag != "BUTTON") && j1f1Tag!=null ) {
  //Transforms all form elements inside the selected element.
  //Transforma tudo dentro do elemento escolhido.

    // Update select values to original selects.
    // Funcao que repassa valores dos select criados aos originais.
    $.jqf1SelectValue = function(tamanho,idJqf1,idSelect,jqf1SelOptVal,jqf1SelOptHtml){
      var jqf1SelTempVal = $('#'+idSelect).val();
      if (jqf1SelTempVal == null ) { var jqf1SelTempVal = Array (tamanho); }
      if ($('#'+idSelect).attr('multiple') == true) {
        var jqf1Index = jQuery.inArray(jqf1SelOptVal, jqf1SelTempVal);
        if(jqf1Index== -1) jqf1SelTempVal.push(jqf1SelOptVal); else jqf1SelTempVal.splice(jqf1Index,1);
        $('#'+idSelect).val(jqf1SelTempVal);
        /*Funciona no select especial*/ $('#'+idJqf1).text($('#'+idSelect+' option[selected]').size()+' item(s)');
        $('#'+idSelect).trigger('change');
      } else {
        $('#'+idJqf1).text(jqf1SelOptHtml);
        if (jqf1SelOptVal != jqf1SelTempVal) {
          $('#'+idSelect).val(jqf1SelOptVal);
          $('#'+idSelect).trigger('change');
        }
      }
    }

    // Manipulation string function for select with search field. (By Raimundo Neto)
    // Funcao de manipulacao de string para Select com Search (Por Raimundo Neto)
    $.jqf1Strstr = function(haystack, needle, bool) {
      var pos = 0;
      haystack += '';
      pos = haystack.indexOf(needle);
      if (pos == -1) {
        return false;
      } else {
        if (bool) {
          return haystack.substr(0, pos);
        } else {
          return haystack.slice(pos);
        }
      }
    }

    $(this).addClass('jqf1_temp');
    // Global vars

    var jqf1InpArray = $('.jqf1_temp input');
    var jqf1TxtArray = $('.jqf1_temp textarea');
    var jqf1SelArray = $('.jqf1_temp select');
    var jqf1BtnArray = $('.jqf1_temp input[type="button"], .jqf1_temp input[type="submit"], .jqf1_temp input[type="reset"], .jqf1_temp button');
    var jqf1TxtInner;
    var jqf1SelClickOut = true;



    // Buttons
    jQuery.each(jqf1BtnArray, function(i) {
    var jqf1BtnWid = $(this).width();
    if ( ($(this).attr("jqf1") == null) && (jqf1BtnWid != 0) ) {
      $(this).attr("jqf1","ok");
	  var jqf1BtnWidIe = 0;
	  if (jqf1Browser.indexOf('firefox') != -1){  jqf1BtnWidIe = 8 }
	  if (jqf1Browser.indexOf('msie') != -1){  jqf1BtnWidIe = 0 }
      // Inputs text
      var jqf1BtnVal;
      var jqf1BtnBold = $(this).css("font-weight");
      if ($(this)[0].tagName == "INPUT") {
        var jqf1BtnVal = $(this).attr("value");
      } else {
        var jqf1BtnVal = $(this).text();
      }
      $(this).before("<div class='jqf1Btn jqf1BtnNormal jqf1Btn"+i+"'><div class='div'><div><span>"+jqf1BtnVal+"</span></div></div></div>");
      $(this).appendTo("div.jqf1Btn"+i);
      $("div.jqf1Btn"+i+" .div span").attr("style","font-weight:"+jqf1BtnBold+";width:"+(jqf1BtnWid-jqf1BtnWidIe+6)+"px");
      $(this).attr('style',' float:left;clear:both;margin-top:-22px;width:'+(jqf1BtnWid+5)+'px;height:22px;');
      $(this).addClass('jqf1Hidden');
	}
    });




    $(this).addClass('jqf1');
  }// FIM DO IF

  //Faz updade se executado para um elemento select.
  if ($(this)[0].tagName == "SELECT") {

    var j1f1UpSelName = $(this).attr('name');
    var jqf1Multiple = $(this).attr('multiple');
    var jqf1Special = $(this).attr('size');
    $(this).show();
    $(this).removeAttr("jqf1");
    if (jqf1Multiple == true) {
      if (jqf1Special != '1') {
        $('.jqf1SelMulti'+j1f1UpSelName).remove(); 
      }
    } else {
      $('.jqf1Sel'+j1f1UpSelName).remove(); 
    }
    var parentSel = $(this).parent();
    parentSel.jqf1();
    if (parentSel[0].tagName != 'FORM') {
      parentSel.removeClass('jqf1');
    }

  }
  //Faz updade se executado para um radio ou checkbox parei aqui.
  if ($(this)[0].tagName == "INPUT") {
    var jqf1TypeClass;
    var jqf1Id = $(this).attr('id');
    var jqf1Type = $(this).attr('type');
    if (jqf1Type == "radio") { jqf1TypeClass = 'Radio'; }
    if (jqf1Type == "checkbox") { jqf1TypeClass = 'Check'; }
    if ($(this).attr("checked") == true){
      $('.jqf1Inp'+jqf1TypeClass+jqf1Id+' div').attr('className','inp'+jqf1TypeClass+'On');
	} else {
      $('.jqf1Inp'+jqf1TypeClass+jqf1Id+' div').attr('className','');
	}
  }

  //Select Toggle
  function jqf1ToggleSelect(item) {
    if ($('.jqf1Sel'+item+' .jqf1SelList ul').css('display') == 'none') {
      $('.jqf1Sel .jqf1SelList ul').hide();
      $('.jqf1Sel'+item+' input').hide();
    }
    $('.jqf1Sel'+item+' .jqf1SelList ul').slideToggle();
    $('.jqf1Sel'+item+' input').toggle();
  }

  //Select click verify
  $(document).click( function(){
    if(jqf1SelClickOut==true){
      jqf1SelClickOut = false;
      $('.jqf1SelList input').attr('value','').keyup();
      $('.jqf1Sel .jqf1SelList ul').slideUp();
    }
  });




}
});
})(jQuery);