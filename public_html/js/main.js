;(function () {
	
	'use strict';

	var mobileMenuOutsideClick = function() {

		$(document).click(function (e) {
	    var container = $("#offcanvas, .js-nav-toggle");
	    if (!container.is(e.target) && container.has(e.target).length === 0) {

	    	if ( $('body').hasClass('offcanvas') ) {

    			$('body').removeClass('offcanvas');
    			$('.js-nav-toggle').removeClass('active');
				
	    	}
	    
	    	
	    }
		});

	};


	var offcanvasMenu = function() {

		$('#page').prepend('<div id="offcanvas" />');
		$('#page').prepend('<a href="#" class="js-nav-toggle nav-toggle nav-white"><i></i></a>');
		var clone1 = $('.menu-1 > ul').clone();
		$('#offcanvas').append(clone1);
		var clone2 = $('.menu-2 > ul').clone();
		$('#offcanvas').append(clone2);

		$('#offcanvas .has-dropdown').addClass('offcanvas-has-dropdown');
		$('#offcanvas')
			.find('li')
			.removeClass('has-dropdown');

		// Hover dropdown menu on mobile
		$('.offcanvas-has-dropdown').mouseenter(function(){
			var $this = $(this);

			$this
				.addClass('active')
				.find('ul')
				.slideDown(500, 'easeOutExpo');				
		}).mouseleave(function(){

			var $this = $(this);
			$this
				.removeClass('active')
				.find('ul')
				.slideUp(500, 'easeOutExpo');				
		});


		$(window).resize(function(){

			if ( $('body').hasClass('offcanvas') ) {

    			$('body').removeClass('offcanvas');
    			$('.js-nav-toggle').removeClass('active');
				
	    	}
		});
	};


	var burgerMenu = function() {

		$('body').on('click', '.js-nav-toggle', function(event){
			var $this = $(this);


			if ( $('body').hasClass('overflow offcanvas') ) {
				$('body').removeClass('overflow offcanvas');
			} else {
				$('body').addClass('overflow offcanvas');
			}
			$this.toggleClass('active');
			event.preventDefault();

		});
	};



	var contentWayPoint = function() {
		var i = 0;
		$('.animate-box').waypoint( function( direction ) {

			if( direction === 'down' && !$(this.element).hasClass('animated-fast') ) {
				
				i++;

				$(this.element).addClass('item-animate');
				setTimeout(function(){

					$('body .animate-box.item-animate').each(function(k){
						var el = $(this);
						setTimeout( function () {
							var effect = el.data('animate-effect');
							if ( effect === 'fadeIn') {
								el.addClass('fadeIn animated-fast');
							} else if ( effect === 'fadeInLeft') {
								el.addClass('fadeInLeft animated-fast');
							} else if ( effect === 'fadeInRight') {
								el.addClass('fadeInRight animated-fast');
							} else {
								el.addClass('fadeInUp animated-fast');
							}

							el.removeClass('item-animate');
						},  k * 50, 'easeInOutExpo' );
					});
					
				}, 100);
				
			}

		} , { offset: '85%' } );
	};


	var dropdown = function() {

		$('.has-dropdown').mouseenter(function(){

			var $this = $(this);
			$this
				.find('.dropdown')
				.css('display', 'block')
				.addClass('animated-fast fadeInUpMenu');

		}).mouseleave(function(){
			var $this = $(this);

			$this
				.find('.dropdown')
				.css('display', 'none')
				.removeClass('animated-fast fadeInUpMenu');
		});

	};


	var goToTop = function() {

		$('.js-gotop').on('click', function(event){
			
			event.preventDefault();

			$('html, body').animate({
				scrollTop: $('html').offset().top
			}, 500, 'easeInOutExpo');
			
			return false;
		});

		$(window).scroll(function(){

			var $win = $(window);
			if ($win.scrollTop() > 200) {
				$('.js-top').addClass('active');
			} else {
				$('.js-top').removeClass('active');
			}

		});
	
	};

    var autoUpdate, last_id;
    last_id = 0;
    $(document).ready(function() {
        autoUpdate = setInterval(checkUpdate, 5000);
        fetchData();
    });
	// Loading page
	var loaderPage = function() {
		$(".loader").fadeOut("slow");
	};

	var counter = function() {
		$('.js-counter').countTo({
			 formatter: function (value, options) {
	      return value.toFixed(options.decimals);
	    },
		});
	};

	var counterWayPoint = function() {
		if ($('#counter').length > 0 ) {
			$('#counter').waypoint( function( direction ) {
										
				if( direction === 'down' && !$(this.element).hasClass('animated') ) {
					setTimeout( counter , 400);					
					$(this.element).addClass('animated');
				}
			} , { offset: '90%' } );
		}
	};
    
    $('#view').click(function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $("#live-feed").offset().top
        }, 1000);
    })
    $('#view_map').click(function(e){
       e.preventDefault();
        openMap();
    });
    function openMap() {
        window.open("index.php?view=map", "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,width=600,height=600");
    }
    function checkUpdate() {
        $.get('index.php?action=update_check', function( data ) {
            var j;
            j = $.parseJSON(data);
            if (j.last_id != last_id) {
                fetchData(last_id);
            }
        });
    }
    function fetchData(id) {
        var imgHTML = '';
        if (typeof id === 'undefined') {
            id = 0;
        }
        $.get('index.php?action=fetch_data&id=' + id, function( data ) {
            var j;
            j = $.parseJSON(data);
            for(var i=0;i < j.images.length;i++) {
                imgHTML += "<span class='ac_img_container'><img src='" + j.images[i].img + "' title='" + j.images[i].reg + "'>\n<br />\n<span>" + j.images[i].reg + "</span><br /></span>\n"
            }
            $('#airplane-photos').hide().append(imgHTML).fadeIn('slow');
            
            if ($('#airplane-photos > span').length > 4) {
               var i, p;
                p = $('#airplane-photos > span').length - 4;
                for (i=1;i<=p;i++) {
                    var el;
                    el = $('#airplane-photos').find('span:first-child');
                    el.remove();
               }
            }
            $('#messages').hide().append(j.html).fadeIn('slow');
            $('#messages').scrollTop($('#messages')[0].scrollHeight);
            last_id = j.last_id;
        }); 
    }
    
    $("#messages").on('click', ":checkbox", function(){
        if ($(this).attr('name') == 'flag_msg') {
          if ($(this).is(':checked')) {
              $(this).attr('disabled', 'disabled');
              $.post('flag.php', {id: $(this).val()});
          }
        }
    });
    
	$(function(){
		mobileMenuOutsideClick();
		offcanvasMenu();
		burgerMenu();
		contentWayPoint();
		dropdown();
		goToTop();
		loaderPage();
		counterWayPoint();
	});


}());