/**
*	Site-specific configuration settings for Highslide JS
*/
hs.graphicsDir = '/js/libs/highslide/graphics/';
hs.showCredits = false; /* hs.creditsPosition = 'bottom center'; */
hs.outlineType = 'gallery';
hs.dimmingOpacity = 0.8;
hs.fadeInOut = true;
hs.align = 'center';
hs.captionEval = 'this.a.title';

// Register HS Overley
hs.registerOverlay({
	html: '<div class="closebutton" onclick="return hs.close(this)" title="'+hs.lang.closeText+'"></div>',
	position: 'top right',
	useOnHtml: true,
	fade: 2 // fading the semi-transparent overlay looks bad in IE
});

// Add the slideshow controller
hs.addSlideshow({
	slideshowGroup: 'gallery',
	interval: 5000,
	repeat: true,
	useControls: true,
	fixedControls: 'fit',
	overlayOptions: {
		className: 'large-dark',
		opacity: '0.6',
		position: 'bottom center',
		offsetX: '0',
		offsetY: '-15',
		hideOnMouseOut: true
	}
});

// gallery config object
var galleryOptions = {
	slideshowGroup: 'gallery',
	numberPosition: 'caption',
	transitions: ['expand', 'crossfade']
};
