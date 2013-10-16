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
                if (($(window).scrollTop() > plantop+110-80-30) && ($(window).scrollTop() < paytop-30)) {
                    $cache.css({'position': 'fixed', 'top': '80px','z-index':'1'}); 
                }else if ($(window).scrollTop() > paytop-30) {
                    $cache.css({'position': 'absolute', 'top': paytop-515+'px','z-index':'1'});
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
                if (($(window).scrollTop() > oritop-400)) {   
                    $cache.animate(
                        { height: 374 }, {
                         duration: 400,
                     }); 
                    $cache.off('scroll');
                }
            }
            $(window).scroll(fixDiv);
            fixDiv();
        });


        jQuery(function($) {
            function fixDiv() {
                var oritop=jQuery('#blue-arrow-container-left').offset().top;
                var $cache = $('#blue-arrow-left');
                if (($(window).scrollTop() > oritop-400)) {     
                    $cache.animate(
                        { height: 374 }, {
                         duration: 400,
                     }); 
                    $cache.off('scroll');
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
                if (($(window).scrollTop() > oritop-400)) {    
                    $cacheLaptop.animate(
                        { left: 15 }, {
                         duration: 400,
                     });   
                    $cacheLaptop.off('scroll');
                    $cacheIphone.animate(
                        { left: 0 }, {
                         duration: 600,
                     }); 
                    $cacheIphone.off('scroll');
                }
            }
            $(window).scroll(fixDiv);
            fixDiv();
        });

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



