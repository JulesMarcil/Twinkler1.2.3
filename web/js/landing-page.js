var showSlide=function(num){
    if(num==-1){
        $('#js-printer').css("display","none");
        return;
    }
    if(num==-2){
        $('#js-demo').css("position","absolute");
        $('#js-demo').css("top",distanceBadge);
        $('#js-button-plans').removeClass("translucent");
        return;
    }
    $('#js-slider').css("left",-ipadScreenSize*num*zoomSize);
    $('#js-demo').css("position","fixed");
    $('#js-demo').css("top",0);
    $('#js-previous').css("visibility","visible");
    $('#js-keyboard').removeClass("animate");
    $('#js-signature').removeClass("animate");
    $('#js-printer').css("display","block");
    $('#js-printer').removeClass("animate");
    $('#js-badge-demo').removeClass("animate");
    $('#js-ipad').removeClass("fade");

    if(num==0){
        $('#js-previous').css("visibility","hidden");
        $('#js-printer').css("display","none");
    }else if(num==1){
        $('#js-keyboard').addClass("animate");
    }else if(num==2){
        $('#js-signature').addClass("animate");
    }else if(num==3){$('#js-flash').fadeIn().fadeOut();
}else if(num==4){
    $('#js-printer').addClass("animate");
    $('#js-badge-demo').addClass("animate");
    $('#js-ipad').addClass("fade");
}
}
var getCurSlide=function(){
    var top=$(this).scrollTop();
    var curSlide=Math.floor((top-(firstSlidePos-(slideHeight/2)))/slideHeight);

    if(top<firstSlidePos-(slideHeight/2)){
        curSlide=-1;
    }
    else if(top>lastSlidePos){curSlide=-2;
    }
    return curSlide;
}


    //-------------------SIGN WITH ENVOY --------------//
    var slideScreen=function(num){
        $('.step-intro').stop().animate(
            { left: 0-num*194 }, {
             duration: 100,
         }); 
        $('.step-data').stop().animate(
            { left: 194-num*194 }, {
             duration: 100,
         }); 
        $('.step-nda').stop().animate(
            { left: 388-num*194 }, {
             duration: 100,
         }); 
    }

    $(document).scroll(function() {

        var num=1000;
        var paytop=jQuery('#payback').offset().top-150;
        var exptop=jQuery('#expense').offset().top-150;
        if (( $(window).scrollTop() > paytop) && !(num==2)) {
            num=2;
            slideScreen(num);
            return;
        }else if ( ($(window).scrollTop() > exptop) && !(num==1) ) {
            num=1;
            slideScreen(num);
            return;
        }else if ( ($(window).scrollTop() < exptop)  && !(num==0)) {
            num=0;
            slideScreen(num);
            return;
        }else if (( $(window).scrollTop() < paytop ) && !(num==1)) {
            num=1;
            slideScreen(num);
            return;
        }

    });

    $(document).ready(function() { 

        jQuery(function($) {
            function fixDiv() {
                var oritop=jQuery('#iphone-pic').offset().top;
                var plantop=jQuery('#planning').offset().top;
                var paytop=jQuery('#payback').offset().top;
                var $cache = $('#iphone-pic');
                if (($(window).scrollTop() > plantop+110-80) && ($(window).scrollTop() < paytop-30)) {
                    $cache.css({'position': 'fixed', 'top': '80px','z-index':'1'}); 
                }else if ($(window).scrollTop() > paytop-30) {
                    $cache.css({'position': 'absolute', 'top': paytop+50+'px','z-index':'1'});
                }else{
                    $cache.css({'position': 'relative', 'top': '80px','z-index':'1'});
                }
            }
            $(window).scroll(fixDiv);
            fixDiv();
        });

        jQuery(function($) {
            function fixDiv() {
                var oritop=jQuery('#blue-arrow-container').offset().top;
                var $cache = $('#blue-arrow');
                var check=new Boolean();
                check=false;
                if ((check==false) && ($(window).scrollTop() > oritop-400)) {  
                    check=true;    
                    $cache.stop().animate(
                        { height: 374 }, {
                         duration: 400,
                     }); 
                }
            }
            $(window).scroll(fixDiv);
            fixDiv();
        });


        jQuery(function($) {
            function fixDiv() {
                var oritop=jQuery('#blue-arrow-container-left').offset().top;
                var $cache = $('#blue-arrow-left');
                var check=new Boolean();
                check=false;
                if ((check==false) && ($(window).scrollTop() > oritop-400)) {  
                    check=true;    
                    $cache.stop().animate(
                        { height: 374 }, {
                         duration: 400,
                     }); 
                }
            }
            $(window).scroll(fixDiv);
            fixDiv();
        });


        jQuery(function($) {
            function fixDiv() {
                var oritop=jQuery('#platform-section').offset().top;
                var $cacheLaptop = $('#flat-laptop');
                var $cacheIphone = $('#flat-iphone');
                var check=new Boolean();
                check=false;
                if ((check==false) && ($(window).scrollTop() > oritop-400)) {  
                    check=true;    
                    $cacheLaptop.stop().animate(
                        { left: 15 }, {
                         duration: 400,
                     });   
                    $cacheIphone.stop().animate(
                        { left: 0 }, {
                         duration: 600,
                     }); 
                }
            }
            $(window).scroll(fixDiv);
            fixDiv();
        });

    }); 




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
    // When #scroll is clicked
    jQuery('#learn-more').click(function(){
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

$('.carousel').carousel({  
  interval: 5000 // in milliseconds  
})



