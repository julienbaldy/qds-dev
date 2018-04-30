var $ = require('jquery');
require('bootstrap-sass');
require('../css/main.scss');

$(document).ready(function() 
{ 

  $(".full").hover(function(){ //fonction hover
    $(this).addClass('starFull');
    //je recupe l'id radio
    var radioID = $(this).attr('for');
    //je recupe la radio
    var radioBtn = $("#"+radioID);
    //je recupe la valeur de la satisfaction
    var satisfaction = radioBtn.val() - 1;
    //je recupe la box parente
    var box = $(this).parent().parent();
    //je recupe la div qui contient les message à afficher
    var divMess = box.find("div");
    var isActive = false;
    var count = -1;

    _whatAboutFirstStarMessage(divMess , "hide");

    divMess.children('span').each(function () {
      if($(this).hasClass("active"))
        {
          isActive = true;
        }
    });
    if(!isActive)
      {
        divMess.children('span').each(function () {
            
          if(count == satisfaction)
            {
              $(this).css( "display", "block" );
            }
            count++;
        });
      }
    
  }, function(){ //fonction out
    
    $(this).removeClass('starFull');
    //je recupe la box parente
    var box = $(this).parent().parent();
    //je recupe la div qui contient les message à afficher
    var divMess = box.find("div");
    var radioID = $(this).attr('for');
    var radioBtn = $("#"+radioID);
    var satisfaction = radioBtn.val() - 1;
    var count = -1;
    divMess.children('span').each(function () {
      if(count == satisfaction)
      {
        $(this).css( "display", "none" );
      }
      count++;
    });

    _whatAboutFirstStarMessage(divMess, "visible");
  
  });

  //Quoi faire du premier message "choisissez votre"
  //remplacer les name des inputs en dessous pour checker les bon label
  function _whatAboutFirstStarMessage(divMess, option)
  {
    if(option == "hide")
    {
      divMess.children('span').each(function () {
        var objSpan = $(this);
        divBoxRating = divMess.parent();
        if ($(this).hasClass( "first-sf" )){
          divBoxRating.find("input[type=radio]").each(function () {
            if($(this).is(":not(:checked)"))
            {
              objSpan.css( "display", "none" );
              return false;
            }
          });
        }
      });
    }
    else if (option == "visible")
    {
      divMess.children('span').each(function () {
        var objSpan = $(this);
        divBoxRating = divMess.parent();

        if ($(this).hasClass( "first-sf" )){

             divBoxRating.find("input[type=radio]").each(function () {
              if($(this).is(":checked"))
              {
                objSpan.css( "display", "none" );
                return false;
              }
              objSpan.css( "display", "block" );
            });
        }
      });
    }
  }
  
  $("label").on("click",function(){
    //je recupe l'id radio
    var radioID = $(this).attr('for');
    //je recupe la radio
    var radioBtn = $("#"+radioID);
    //je recupe la valeur de la satisfaction
    var satisfaction = radioBtn.val() - 1;
    //je recupe la box parente
    var box = $(this).parent().parent();
    //je recupe la div qui contient les message à afficher
    var divMess = box.find("div");
    var count = 0;
    divMess.children('span').each(function () {

      if ($(this).hasClass( "first-sf" )){
          $(this).css( "display", "none" );
      }
      else
      {
        if($(this).hasClass("active"))
        {
          $(this).removeClass("active");
        }
      }

      
    });
    divMess.children('span').each(function () {
      if ($(this).hasClass( "first-sf" )){
          $(this).css( "display", "none" );
      }
      else
      {
        if(count == satisfaction)
          {
            $(this).addClass("active");
            return false;
          }
        count++;
      }
    });
    
  });
  
  
	$("#btn-suivant").on("click",function(){
		var isActive = false;
		var length = $(".box-form-content > div").length;

		$(".box-form-content > div").each(function(index, element) {

			if(isActive)
			{
				$(this).removeClass("box-form-inactive");
				$(this).addClass("box-form-active");
				isActive = false;

				$('html, body').animate({scrollTop: $(".box-title-child").offset().top}, 500);

        if (index === (length - 2)) {
					$("#btn-suivant").css("display","none");
					$("#btn-valider").css("display","block");
				}
        _activeMenu(index);
				return false;
			}

			if($(this).hasClass("box-form-active"))
			{
				$(this).removeClass("box-form-active");
				$(this).addClass("box-form-inactive");
				isActive = true;
			} 

			
      });
	});

  function _activeMenu(iIndex)
  {
    $("#menu-step > li").each(function(){
      $(this).removeClass("active-menu");
    });
    iIndex = iIndex + 1;
    $("#menu-step > li:nth-child("+ iIndex +")").addClass("active-menu");
  }

  var sMarque = getUrlParameter('marque');
  if(sMarque == "66-nord")
  {
    $(".box-form > h3").each(function(){
      if($(this).is(':contains("Qu\'avez-vous pensé du site internet")')){
        $(this).before("<div id='icon-internet' class='icon-title-h3'></div>");
      }
      else if($(this).is(':contains("Pourquoi avez-vous choisi")')){
        $(this).before("<div id='icon-marque' class='icon-title-h3'></div>");
      }
      else if($(this).is(':contains("votre conseiller voyage")')){
        $(this).before("<div id='icon-conseiller' class='icon-title-h3'></div>");
      }
      else if($(this).is(':contains("vos documents de voyage")')){
        $(this).before("<div id='icon-documents' class='icon-title-h3'></div>");
      }
      else if($(this).is(':contains("transport aérien")')){
        $(this).before("<div id='icon-transport' class='icon-title-h3'></div>");
      }
      else if($(this).is(':contains("accueil sur place")')){
        $(this).before("<div id='icon-accueil' class='icon-title-h3'></div>");
      }
      else if($(this).is(':contains("votre guide accompagnateur")')){
        $(this).before("<div id='icon-guide' class='icon-title-h3'></div>");
      }
      else if($(this).is(':contains("des hébergements")')){
        $(this).before("<div id='icon-hebergements' class='icon-title-h3'></div>");
      }
      else if($(this).is(':contains("des repas")')){
        $(this).before("<div id='icon-repas' class='icon-title-h3'></div>");
      }
      else if($(this).is(':contains("de la logistique")')){
        $(this).before("<div id='icon-logistique' class='icon-title-h3'></div>");
      }
      else if($(this).is(':contains("du programme")')){
        $(this).before("<div id='icon-programme' class='icon-title-h3'></div>");
      }
    });
  }

  function getUrlParameter(sParam) {
      var sPageURL = decodeURIComponent(window.location.search.substring(1)),
          sURLVariables = sPageURL.split('&'),
          sParameterName,
          i;

      for (i = 0; i < sURLVariables.length; i++) {
          sParameterName = sURLVariables[i].split('=');

          if (sParameterName[0] === sParam) {
              return sParameterName[1] === undefined ? true : sParameterName[1];
          }
      }
  };

});