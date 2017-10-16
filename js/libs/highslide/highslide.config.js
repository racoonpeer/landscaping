/**
*	Site-specific configuration settings for Highslide JS
*/
hs.graphicsDir 		  = '/js/libs/highslide/graphics/';
hs.showCredits 		  = false; /* hs.creditsPosition = 'bottom center'; */
hs.showCredits            = false;
hs.creditsText            = '';
hs.creditsHref            = '';
hs.outlineType            = 'rounded-white';
//hs.captionEval            = 'this.a.title';
hs.wrapperClassName 	  = 'borderless floating-caption'
hs.dimmingOpacity   	  = 0.75;
hs.align 		  = 'center';
hs.dragByHeading 	  = false;
hs.allowSizeReduction 	  = false;
//hs.padToMinWidth 	  = false;
//hs.allowMultipleInstances = false;
//hs.registerOverlay({
//	html: '<div class="close-simple-white" onclick="return hs.close(this)" title=""></div>',
//	position: 'top right',
//	useOnHtml: true,
//	fade: 2 // fading the semi-transparent overlay looks bad in IE
//});

// Ajax iframe config object
var ajaxOptions = {
    objectType              : 'iframe',
    align                   : 'center',
    headingText             : '',
    wrapperClassName        : 'draggable-header',
    outlineWhileAnimating   : true,
    preserveContent         : false,
    outlineType             : null, 
    dragByHeading           : false,
    allowSizeReduction      : false,
    dimmingOpacity          : 0.67,
    width                   : 500
};