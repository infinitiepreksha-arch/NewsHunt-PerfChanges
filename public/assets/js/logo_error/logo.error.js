 // Function to handle image errors
// Function to handle image errors
function handleImageError(image) {
    if (image.classList.contains('custom-default-image')) {
        if (image.getAttribute('data-custom-image') != null) {
            image.src = image.getAttribute('data-custom-image');
        } else {
            image.src = '/assets/images/no_image_available.png'; // Static URL
        }
    }
}

// Create a MutationObserver to watch for DOM changes
const observer = new MutationObserver((mutationsList) => {
    mutationsList.forEach((mutation) => {
        if (mutation.addedNodes) {
            mutation.addedNodes.forEach((node) => {
                // Check if the added node is an image element
                if (node instanceof HTMLImageElement) {
                    node.addEventListener('error', () => {
                        handleImageError(node);
                    });
                }
            });
        }
    });
});

// Start observing changes in the DOM
observer.observe(document, {childList: true, subtree: true});

const onErrorImage = (e) => {
    if (!e.target.src.includes('no_image_available.png')) {
        e.target.src = url('/assets/images/no_image_available.png');
    }
};

/* const onErrorImageSidebarHorizontalLogo = (e) => {
    if (!e.target.src.includes('no_image_available.jpg')) {
       e.target.src = "{{asset('/assets/vertical-logo.svg')}}";
    }
}; */