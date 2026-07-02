// <><><><><><><> START JS FOR ENEWSPAPER PDF PREVIEW ICONS DISPLAY <><><><><><><>
(function () {
    function initializeFlipbook() {
        // Check if required variables exist
        if (typeof pdfUrl === 'undefined' || typeof flipbookAssets === 'undefined') {
            console.error('Required variables (pdfUrl or flipbookAssets) not found. Retrying...');
            setTimeout(initializeFlipbook, 100);
            return;
        }

        if (!pdfUrl) {
            console.error('PDF path is missing. Ensure the e-paper file is uploaded.');
            return;
        }

        // Initialize the flipbook
        var $container = $('#container');

        // Load the complete template HTML
        var templateHTML = `
            <div id="fb3d-ctx" class="flip-book">
                <div class="view">
                    <div class="fnav">
                        <div class="prev">
                            <a class="cmdBackward" href="#"><span class="icon"><i class="fa fa-angle-left"></i></span></a>
                        </div>
                        <div class="next">
                            <a class="cmdForward" href="#"><span class="icon"><i class="fa fa-angle-right"></i></span></a>
                        </div>
                    </div>
                    <div class="widLoadingProgress loading-progress hidden">
                        <div class="progress"></div>
                        <div class="txtLoadingProgress caption"></div>
                    </div>
                    <div class="widLoading page-loading hidden"></div>
                </div>
               
                <div class="controls">
                    <div class="ctrl js-center">
                        <nav class="fnavbar">
                            <ul class="fnav">
                                <li class="fnav-item cmdZoomIn"><a href="#"><span class="icon"><i class="fa fa-search-plus" title="Zoom in"></i></span></a></li>
                                <li class="fnav-item cmdZoomOut"><a href="#"><span class="icon"><i class="fa fa-search-minus" title="Zoom out"></i></span></a></li>
                                <li class="fnav-item cmdBackward"><a href="#"><span class="icon"><i class="fa fa-backward" title="Previous page"></i></span></a></li>
                                <li class="fnav-item cmdForward"><a href="#"><span class="icon"><i class="fa fa-forward" title="Next page"></i></span></a></li>
                                <li class="fnav-item cmdFullScreen"><a href="#"><span class="icon"><i class="fa fa-arrows-alt" title="Full screen"></i></span></a></li>
                                <li class="dropup fnav-item toggle widSettings">
                                    <a href="#"><div class="icon-caret"><span class="icon"><i class="fa fa-cog" title="Settings"></i> <i class="caret"></i></span></div></a>
                                    <ul class="menu hidden">
                                        <li class="cmdSinglePage"><a href="#"><span class="icon"><i class="fa fa-file-o"></i></span> Single page</a></li>
                                        <li class="cmdSounds"><a href="#"><span class="icon"><i class="fa fa-volume-up"></i></span> Sounds</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        `;

        $container.html(templateHTML);

        var $flipbook = $('#fb3d-ctx');

        // Initialize default book view
        if (typeof init === 'function') {
            init($flipbook);
        }

        // Initialize the flipbook with PDF
        setTimeout(function () {
            try {
                // The API is $.fn.FlipBook (capital F and B)
                if (typeof $flipbook.FlipBook === 'function') {
                    // Get default template and add sounds
                    var templateWithSounds = {
                        html: flipbookAssets.templateHtml,
                        styles: [flipbookAssets.whiteBookCss],
                        links: [{
                            rel: 'stylesheet',
                            href: flipbookAssets.fontAwesomeCss
                        }],
                        script: flipbookAssets.defaultView,
                        // Enable sounds with file paths
                        sounds: {
                            startFlip: flipbookAssets.startSound,
                            endFlip: flipbookAssets.endSound
                        }
                    };

                    var flipbookInstance = $flipbook.FlipBook({
                        pdf: pdfUrl,
                        // Pass template with sounds configuration
                        template: templateWithSounds
                    });

                    setTimeout(function () {
                        // Verify handlers are bound by checking if elements exist
                        var cmdElements = $flipbook.find(
                            '.cmdSmartPan, .cmdSinglePage, .cmdSounds, .cmdStats, .cmdLightingUp, .cmdLightingDown'
                        );
                        console.log('Flipbook initialized successfully');
                    }, 1000);
                } else {
                    console.error(
                        'FlipBook plugin not found. Check if dist/flip-book.js is loaded correctly.'
                    );
                }
            } catch (e) {
                console.error('FlipBook initialization error:', e);
                console.error('Stack:', e.stack);
            }
        }, 500);
    }
    $(document).ready(function () {
        initializeFlipbook();
    });
})();   
// <><><><><><><> END JS OF ENEWSPAPER PDF PREVIEW ICONS DISPLAY <><><><><><><>