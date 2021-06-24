var $document = jQuery(document);

$document.ready(function() {

	'use strict';

	/* MOUSEMOVE HANDLER
	-------------------------------------------------- */
	$document.on( 'mousemove', '.taq-user-rate-active', function(e){
		var $rated = jQuery(this);
		if( $rated.hasClass('rated-done') ){
			return false;
		}

		if( !e.offsetX ){
			e.offsetX = e.clientX - jQuery(e.target).offset().left;
		}

		var offset = e.offsetX + 4;
		if( offset > 100 ){
			offset = 100;
		}

		$rated.find( '.user-rate-image span' ).css( 'width', offset + '%' );
		var score = Math.floor(((offset / 10) * 5)) / 10;
		if( score > 5 ){
			score = 5;
		}
	});


	/* MOUSEMOVE HANDLER
	-------------------------------------------------- */
	$document.on( 'click', '.taq-user-rate-active', function(){

		var $rated = jQuery(this),
		    $ratedParent = $rated.parent(),
		    $ratedCount  = $ratedParent.find('.taq-count'),
		    post_id      = $rated.attr( 'data-id' ),
		    numVotes     = $ratedCount.text();

		if( $rated.hasClass('rated-done') || $rated.hasClass('rated-in-progress') ){
			return false;
		}

		$rated.addClass('rated-in-progress');

		var userRatedValue = $rated.find('.user-rate-image span').width();
		$rated.find( '.user-rate-image' ).hide();
		$rated.append('<span class="taq-load"></span>');

		if (userRatedValue >= 95) {
			userRatedValue = 100;
		}

		var userRatedValueCalc = (userRatedValue*5)/100;

		// Ajax Call ----------
		jQuery.post(
			taqyeem.ajaxurl,
			{
				action: 'taqyeem_rate_post',
				post  : post_id,
				value : userRatedValueCalc
			},
			function( data ) {
				$rated.addClass('rated-done').attr('data-rate',userRatedValue);
				$rated.find('.user-rate-image span').width(userRatedValue+'%');

				jQuery('.taq-load').fadeOut(function () {
					$ratedParent.find('.taq-score').html( userRatedValueCalc );

					if( $ratedCount.length > 0 ){
						numVotes =  parseInt(numVotes)+1;
						$ratedCount.html(numVotes);
					}
					else{
						$ratedParent.find('small').hide();
					}

					$ratedParent.find('strong').html(taqyeem.your_rating);
					$rated.find('.user-rate-image').fadeIn();
			});
		}, 'html');

		return false;
	});


	/* MOUSELEAVE HANDLER
	-------------------------------------------------- */
	$document.on( 'mouseleave', '.taq-user-rate-active' , function(){
		var $rated = jQuery(this);
		if( $rated.hasClass('rated-done') ){
			return false;
		}
		var post_rate = $rated.attr('data-rate');
		$rated.find('.user-rate-image span').css('width', post_rate + '%');
	});

});
