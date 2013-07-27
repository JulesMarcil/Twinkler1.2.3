var h=10;

function ScaleMosaic(){
	h=$(document).width()/($('#pic1-1').width()+$('#pic1-2').width()+$('#pic1-3').width()+$('#pic1-4').width()+100);

$('#row1').css('height',$('#row1').height()*h);

$('#pic1-1').css('width',$('#pic1-1').width()*h);
$('#pic1-1').css('height',$('#pic1-1').height()*h);
$('#pic1-2').css('width',$('#pic1-2').width()*h);
$('#pic1-2').css('height',$('#pic1-2').height()*h);
$('#pic1-3').css('width',$('#pic1-3').width()*h);
$('#pic1-3').css('height',$('#pic1-3').height()*h);
$('#pic1-4').css('width',$('#pic1-4').width()*h);
$('#pic1-4').css('height',$('#pic1-4').height()*h);

h=$(document).width()/($('#pic2-1').width()+$('#pic2-2').width()+$('#pic2-3').width()+$('#pic2-4').width()+$('#pic2-5').width()+110);

$('#row2').css('height',$('#row2').height()*h);

$('#pic2-1').css('width',$('#pic2-1').width()*h);
$('#pic2-1').css('height',$('#pic2-1').height()*h);
$('#pic2-2').css('width',$('#pic2-2').width()*h);
$('#pic2-2').css('height',$('#pic2-2').height()*h);
$('#pic2-3').css('width',$('#pic2-3').width()*h);
$('#pic2-3').css('height',$('#pic2-3').height()*h);
$('#pic2-4').css('width',$('#pic2-4').width()*h);
$('#pic2-4').css('height',$('#pic2-4').height()*h);
$('#pic2-5').css('width',$('#pic2-5').width()*h);
$('#pic2-5').css('height',$('#pic2-5').height()*h);

h=$(document).width()/($('#pic3-1').width()+$('#pic3-2').width()+$('#pic3-3').width()+$('#pic3-4').width()+100);

$('#row3').css('height',$('#row3').height()*h);

$('#pic3-1').css('width',$('#pic3-1').width()*h);
$('#pic3-1').css('height',$('#pic3-1').height()*h);
$('#pic3-2').css('width',$('#pic3-2').width()*h);
$('#pic3-2').css('height',$('#pic3-2').height()*h);
$('#pic3-3').css('width',$('#pic3-3').width()*h);
$('#pic3-3').css('height',$('#pic3-3').height()*h);
$('#pic3-4').css('width',$('#pic3-4').width()*h);
$('#pic3-4').css('height',$('#pic3-4').height()*h);
}

$(document).ready(function() {
    $('.carousel').carousel({
    interval: 2000
    })
});

window.onload = function () {
ScaleMosaic();
  $('#mosaic').height($('#row1').height()+$('#row2').height()+$('#row3').height()+40);


}

$(window).resize(function() {
  ScaleMosaic();
  $('#mosaic').height($('#row1').height()+$('#row2').height()+$('#row3').height()+40);
});

window.onresize = function() {
ScaleMosaic();
}

$(document).scroll(function() {

    if( $(this).scrollTop() > jQuery('#mosaic').offset().top -100) {	
    $('#login-mosaic').fadeIn(700);
      $('#login-mosaic').css({display:'block',  position: 'fixed', left:0, top:50});
    }else if( $(this).scrollTop() < jQuery('#mosaic').offset().top -80) {
    $('#login-mosaic').fadeOut(400);
    }
    else{
    $('#login-mosaic').css({display:'none',  position: 'fixed', left:0, top:50});
    }

});


// When the Document Object Model is ready
$(document).ready(function(){
    // 'catTopPosition' is the amount of pixels #cat
    // is from the top of the document
    var catTopPosition1 = jQuery('#features').offset().top;
    
    // When #scroll is clicked
    jQuery('#feature').click(function(){
        // Scroll down to 'catTopPosition'
        jQuery('html, body').animate({scrollTop:catTopPosition1}, 'slow');
        // Stop the link from acting like a normal anchor link
        return false;
    });

        var catTopPosition2 = jQuery('#gettheapp').offset().top-40;
    
    // When #scroll is clicked
    jQuery('#getappstandapp').click(function(){
        // Scroll down to 'catTopPosition'
        jQuery('html, body').animate({scrollTop:catTopPosition2}, 'slow');
        // Stop the link from acting like a normal anchor link
        return false;
    });

        var catTopPosition3 = jQuery('#mosaic').offset().top;
    
    // When #scroll is clicked
    jQuery('#gimmemore').click(function(){
        // Scroll down to 'catTopPosition'
        jQuery('html, body').animate({scrollTop:catTopPosition3}, 'slow');
        // Stop the link from acting like a normal anchor link
        return false;
    });


        var catTopPosition4 = jQuery('#planning').offset().top;
    
    // When #scroll is clicked
    jQuery('#learn-more-planning').click(function(){
        // Scroll down to 'catTopPosition'
        jQuery('html, body').animate({scrollTop:catTopPosition4}, 'slow');
        // Stop the link from acting like a normal anchor link
        return false;
    });

    // When #scroll is clicked
    jQuery('#learn-more-planning-link').click(function(){
        // Scroll down to 'catTopPosition'
        jQuery('html, body').animate({scrollTop:catTopPosition4}, 'slow');
        // Stop the link from acting like a normal anchor link
        return false;
    });

        var catTopPosition5 = jQuery('#expense').offset().top;
    
    // When #scroll is clicked
    jQuery('#learn-more-expense').click(function(){
        // Scroll down to 'catTopPosition'
        jQuery('html, body').animate({scrollTop:catTopPosition5}, 'slow');
        // Stop the link from acting like a normal anchor link
        return false;
    });

    // When #scroll is clicked
    jQuery('#learn-more-expense-link').click(function(){
        // Scroll down to 'catTopPosition'
        jQuery('html, body').animate({scrollTop:catTopPosition5}, 'slow');
        // Stop the link from acting like a normal anchor link
        return false;
    });


        var catTopPosition6 = jQuery('#payback').offset().top;
    
    // When #scroll is clicked
    jQuery('#learn-more-payback').click(function(){
        // Scroll down to 'catTopPosition'
        jQuery('html, body').animate({scrollTop:catTopPosition6}, 'slow');
        // Stop the link from acting like a normal anchor link
        return false;
    });

    // When #scroll is clicked
    jQuery('#learn-more-payback-link').click(function(){
        // Scroll down to 'catTopPosition'
        jQuery('html, body').animate({scrollTop:catTopPosition6}, 'slow');
        // Stop the link from acting like a normal anchor link
        return false;
    });


    // When #scroll is clicked
    jQuery('#tk-logo-hp').click(function(){
        // Scroll down to 'catTopPosition'
        jQuery('html, body').animate({scrollTop:0}, 'fast');
        // Stop the link from acting like a normal anchor link
        return false;
    });

});




