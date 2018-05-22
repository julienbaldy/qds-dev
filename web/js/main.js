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
  
  $(".box-rating>fieldset>label").on("click",function(){
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
		var length = $(".box-form-content > .box-form").length;

		$(".box-form-content > .box-form").each(function(index, element) {

			if(isActive)
			{
				$(this).removeClass("box-form-inactive");
				$(this).addClass("box-form-active");
				isActive = false;

				$('html, body').animate({scrollTop: $(".box-title-child").offset().top}, 500);

        if (index === (length - 1)) {
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

  //function qui permet d'activer les onglets du menu si actif
  function _activeMenu(iIndex)
  {
    $("#menu-step > li").each(function(){
      $(this).removeClass("active-menu");
    });
    iIndex = iIndex + 1;
    $("#menu-step > li:nth-child("+ iIndex +")").addClass("active-menu");
  }

  //limite les QCM à deux choix
  $("input[class='qcm-choices']").change(function () {
    var maxAllowed = 2;
    var cnt = $("input[class='qcm-choices']:checked").length;
    if (cnt > maxAllowed)
    {
       $(this).prop("checked", "");
   }
  });

  $(".box-0to10").parent().parent().next().css( "display", "none" );
  $(".content-0t10 > input").on("change",function(){
      $el = $(".box-0to10").parent().parent();
      $boxToDisabled = $el.next();
      if($(this).val() <= 8)
      {
        $boxToDisabled.css( "display", "block" );
      }else
      {
        $boxToDisabled.css( "display", "none" );
      }
  });


  $('.ls-modal').on('click', function(e){
    e.preventDefault();
    $('#myModal').modal('show').find('.modal-body').load($(this).attr('href'));
  });


});
