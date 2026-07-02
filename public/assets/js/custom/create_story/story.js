let slideCount = 0;
let slideOrder = [];
let currentStep = 1;
const currentSlideIndex = slideCount > 0 ? getCurrentSlideIndex() : 0;
const totalSteps = 5;

// Current or Default Animations 
let currentAnimations = {
    title: { type: 'fade-in', delay: 0, duration: 1 },
    description: { type: 'fade-in', delay: 0.2, duration: 1 },
    image: { type: 'fade-in', delay: 0.4, duration: 1 }
};

// DOM Elements
const form = document.getElementById('storyForm');
const nextBtn = document.getElementById('nextStep');
const prevBtn = document.getElementById('prevStep');
const submitBtn = document.getElementById('submitForm');
const progressBar = document.querySelector('.progress-bar');
const noSlidesMessage = document.getElementById('noSlidesMessage');

// ============ STEP 1: Story Details Handling ============

// validateStoryDetails() => Basic Valdation for Step 1 
function validateStoryDetails() {
    const title = document.querySelector('input[name="title"]');
    const topic = document.querySelector('select[name="topic_id"]');
    const newslanguage = document.querySelector('select[name="news_language_id"]');
    let isValid = true;

    if (!title.value.trim()) {
        showError(title, 'Story title field is required.');
        isValid = false;
    } else {
        hideError(title);
    }
    if (!topic.value) {
        showError(topic, 'Topic field is required.');
        isValid = false;
    } else {
        hideError(topic);
    }

    if (!newslanguage.value) {
        showError(newslanguage, 'News language field is required.');
        isValid = false;
    } else {
        hideError(newslanguage);
    }

    return isValid;
}

// ============ STEP 2: Slide Management ============

// Add new slides 
document.getElementById('addMoreSlides').addEventListener('click', function () {
    const accordionItem = createAccordionItem(slideCount);
    document.getElementById('accordionSlides').appendChild(accordionItem);

    const slidePreview = createSlidePreview(slideCount);
    document.getElementById('slides-order').appendChild(slidePreview);

    noSlidesMessage.style.display = 'none';
    slideCount++;
    updateAnimationPreview(); // Update preview when new slide is added
});

function createAccordionItem(index) {
    const accordionItem = document.createElement('div');
    const base_url = window.location.origin;

    const defaultImage = base_url + '/assets/images/no_image_available.png';
    accordionItem.classList.add('accordion-item');
    accordionItem.innerHTML = `
        <h2 class="accordion-header" id="heading${index}">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#collapse${index}" aria-expanded="true" 
                    aria-controls="collapse${index}">
                Slide ${index + 1}
            </button>
            <button type="button" class="btn btn-link text-danger delete-slide" 
                    data-slide-index="${index}">
                <i class="fas fa-trash"></i>
            </button>
        </h2>
        <div id="collapse${index}" class="accordion-collapse collapse show" 
             aria-labelledby="heading${index}">
            <div class="accordion-body">
                <div class="mb-3">
                    <label class="form-label">Slide Title</label>
                    <input type="text" name="slides[${index}][title]" class="form-control">
                    <div class="error-message">Please enter a slide title</div>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Slide Description</label>
                    <textarea name="slides[${index}][description]" class="form-control" rows="3" required></textarea>
                    <div class="error-message">Please enter a slide description</div>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Slide Image</label>
                    <input type="file" name="slides[${index}][image]" class="form-control" 
                           required accept="image/*" onchange="previewImage(event, ${index})">
                    <div class="error-message">Please select an image</div>
                    <div class="mt-3">
                        <img id="imagePreview${index}" 
                             src="${defaultImage}" 
                             alt="Preview" class="img-slide rounded">
                    </div>
                </div>
            </div>
        </div>
    `;

    // Delete Slide Handler
    const deleteBtn = accordionItem.querySelector('.delete-slide');
    deleteBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        deleteSlide(index);
    });

    return accordionItem;
}

function deleteSlide(index) {
    Swal.fire({
        title: `Delete Slide ${index + 1}?`,
        text: 'Do you really want to delete this slide?',
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it",
        cancelButtonText: "Cancel",
        allowEnterKey: false,
        allowEscapeKey: false,
        allowOutsideClick: false,
        customClass: {
            popup: 'dark:bg-black dark:text-white'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const accordionItem = document.querySelector(
                `.accordion-item:has([data-slide-index="${index}"])`
            );
            const slidePreview = document.querySelector(
                `.slide-preview[data-index="${index}"]`
            );

            if (accordionItem) accordionItem.remove();
            if (slidePreview) slidePreview.remove();

            updateSlideNumbers();

            if (document.querySelectorAll(".accordion-item").length === 0) {
                noSlidesMessage.style.display = "block";
            }

            Swal.fire({
                icon: "success",
                title: "Deleted!",
                text: `Slide ${index + 1} has been deleted.`,
                cancelButtonText: "Ok",
                allowEnterKey: false,
                allowEscapeKey: false,
                allowOutsideClick: false,
            });
        }
    });
}

// ============ STEP 3: Slide Ordering ============

// createSlidePreview() => This function user for ordering 
function createSlidePreview(index) {
    const preview = document.createElement('div');
    preview.classList.add('slide-preview');
    preview.setAttribute('draggable', 'true');
    preview.setAttribute('data-index', index);

    const mainPreview = document.getElementById(`imagePreview${index}`);
    const imageSource = mainPreview ? mainPreview.src : "{{ asset('assets/images/no_image_available.png') }}";

    preview.innerHTML = `
        <img src="${imageSource}" 
             id="imagePreviewThumbnail${index}" 
             alt="Slide Preview" draggable="false"
             style="width: 120px; height: 80px; object-fit: cover;">
        <span draggable="false">Slide ${index + 1}</span>
    `;

    preview.addEventListener('dragstart', handleDragStart);
    preview.addEventListener('dragend', handleDragEnd);

    return preview;
}

// Drag & Drop Functionality 
function handleDragStart(e) {
    e.target.classList.add('dragging');
    e.dataTransfer.setData('text/plain', e.target.getAttribute('data-index'));
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
}

function handleDragOver(e) {
    e.preventDefault();
    const afterElement = getDragAfterElement(e.clientY);
    const draggable = document.querySelector('.dragging');
    const container = document.getElementById('slides-order');

    if (!draggable) return;

    if (afterElement == null) {
        container.appendChild(draggable);
    } else {
        container.insertBefore(draggable, afterElement);
    }
}

function handleDrop(e) {
    e.preventDefault();
    const draggable = document.querySelector('.dragging');
    if (draggable) {
        draggable.classList.remove('dragging');
        updateSlideNumbers();
        updateAnimationPreview(); // Ensure preview updates after reordering
    }
}


function getDragAfterElement(y) {
    const draggableElements = [...document.querySelectorAll('.slide-preview:not(.dragging)')];

    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;

        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

// Safe slide order getter
function getSlideOrder() {
    const order = [];
    const slidePreviews = document.querySelectorAll('.slide-preview');

    if (!slidePreviews) return order;

    slidePreviews.forEach(slide => {
        const index = slide.getAttribute('data-index');
        if (index !== null) {
            order.push(parseInt(index));
        }
    });

    return order;
}
// ============ STEP 4: Animation Management ============

// Initialize animation controls
document.addEventListener('DOMContentLoaded', function () {
    // Animation control handlers
    const animationControls = document.querySelectorAll('#animationAccordion select, #animationAccordion input');
    animationControls.forEach(control => {
        control.addEventListener('change', updateAnimationPreview);
    });

    // Accordion handlers
    const accordionButtons = document.querySelectorAll('#animationAccordion .accordion-button');
    accordionButtons.forEach(button => {
        button.addEventListener('click', function () {
            setTimeout(updateAnimationPreview, 300);
        });
    });

    // Initialize drag and drop
    const slidesOrder = document.getElementById('slides-order');
    if (slidesOrder) {
        slidesOrder.addEventListener('dragover', handleDragOver);
        slidesOrder.addEventListener('drop', handleDrop);
    }
});

function updateAnimationPreview() {
    const previewContainer = document.getElementById('animation-preview');
    if (!previewContainer) return;

    const currentSlideIndex = getCurrentSlideIndex();
    const slideData = getSlideData(currentSlideIndex);

    previewContainer.innerHTML = '';

    const previewContent = document.createElement('div');
    previewContent.className = 'preview-content position-relative';

    const titleElement = createPreviewElement('title', slideData.title);
    const descriptionElement = createPreviewElement('description', slideData.description);
    const imageElement = createPreviewElement('image', slideData.imageUrl);

    previewContent.appendChild(imageElement);
    previewContent.appendChild(titleElement);
    previewContent.appendChild(descriptionElement);
    previewContainer.appendChild(previewContent);

    updateAnimationSettings();
    triggerAnimations();
}

function createPreviewElement(type, content) {
    const element = document.createElement(type === 'image' ? 'img' : 'div');
    element.className = `preview-${type}`;

    switch (type) {
        case 'title':
            element.className += ' fw-bold fs-5 mb-2';
            element.textContent = content || 'Sample Title';
            break;
        case 'description':
            element.className += ' fs-6';
            element.textContent = content || 'Sample description text';
            break;
        case 'image':
            element.className += ' img-fluid mb-3 rounded';
            element.src = content || '{{ asset("assets/images/no_image_available.png") }}';
            element.alt = 'Preview';
            break;
    }

    return element;
}

function updateAnimationSettings() {
    ['title', 'description', 'image'].forEach(type => {
        currentAnimations[type] = {
            type: document.querySelector(`select[name="${type}_animation"]`).value,
            delay: document.querySelector(`input[name="${type}_delay"]`).value,
            duration: document.querySelector(`input[name="${type}_duration"]`).value
        };
    });
}

function triggerAnimations() {
    Object.entries(currentAnimations).forEach(([elementType, settings]) => {
        const element = document.querySelector(`.preview-${elementType}`);
        if (!element) return;

        // Reset animation
        element.style.animation = 'none';
        element.offsetHeight; // Trigger reflow

        // Apply animation settings
        element.style.opacity = '0';
        element.style.animation = '';
        element.style.animationDelay = `${settings.delay}s`;
        element.style.animationDuration = `${settings.duration}s`;
        element.style.animationFillMode = 'forwards';

        // Apply animation class
        switch (settings.type) {
            case 'fade-in':
                element.style.animation = `fadeIn ${settings.duration}s ease ${settings.delay}s forwards`;
                break;
            case 'slide-up':
                element.style.animation = `slideUp ${settings.duration}s ease ${settings.delay}s forwards`;
                break;
            case 'slide-down':
                element.style.animation = `slideDown ${settings.duration}s ease ${settings.delay}s forwards`;
                break;
            case 'zoom-in':
                element.style.animation = `zoomIn ${settings.duration}s ease ${settings.delay}s forwards`;
                break;
            case 'slide-in':
                element.style.animation = `slideIn ${settings.duration}s ease ${settings.delay}s forwards`;
                break;
        }
    });
}

document.querySelectorAll(".delay-select").forEach(select => {
    select.addEventListener("change", function () {
        document.querySelector(`input[name='${this.getAttribute("data-target")}']`).value = this.value;
    });
});

document.querySelectorAll(".duration-select").forEach(select => {
    select.addEventListener("change", function () {
        document.querySelector(`input[name='${this.getAttribute("data-target")}']`).value = this.value;
    });
});

// Set default values on page load
document.querySelectorAll(".delay-select").forEach(select => {
    document.querySelector(`input[name='${select.getAttribute("data-target")}']`).value = select.value;
});

document.querySelectorAll(".duration-select").forEach(select => {
    document.querySelector(`input[name='${select.getAttribute("data-target")}']`).value = select.value;
});
// ============ Navigation and Validation ============

nextBtn.addEventListener('click', () => {
    if (validateCurrentStep()) {
        showStep(currentStep + 1);
    }
});

prevBtn.addEventListener('click', () => {
    showStep(currentStep - 1);
});

function validateCurrentStep() {
    switch (currentStep) {
        case 1:
            return validateStoryDetails();
        case 2:
            const slides = document.querySelectorAll('.accordion-item');
            if (slides.length === 0) {
                showErrorToast('Please add at least one slide');
                return false;
            }
            return validateSlides();
        case 3:
        case 4:
            return true;
        default:
            return false;
    }
}

function validateSlides() {
    let isValid = true;
    const imageSizeType = document.querySelector('input[name="image_size_type"]:checked')?.value || 'fixed';

    document.querySelectorAll('.accordion-item').forEach(item => {
        const inputs = item.querySelectorAll('input[required], textarea[required]');
        inputs.forEach(input => {
            if (!input.value.trim()) {
                showError(input, `Please enter ${input.name.includes('title') ? 'a title' :
                    input.name.includes('description') ? 'a description' : 'an image'}`);
                isValid = false;
            } else {
                hideError(input);
            }
        });

        // Check image dimensions based on type
        const imageInput = item.querySelector('input[type="file"]');
        if (imageInput && imageInput.files.length > 0) {
            const width = parseInt(imageInput.getAttribute('data-width'));
            const height = parseInt(imageInput.getAttribute('data-height'));

            if (imageSizeType === 'fixed') {
                if (width !== 1080 || height !== 1920) {
                    showError(imageInput, 'Please add fixed dimension image 1080x1920 otherwise select random option for image');
                    isValid = false;
                }
            }
        }
    });
    return isValid;
}

function showStep(step) {
    // Hide all step contents
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('d-none'));
    const stepElement = document.getElementById(`step${step}`);
    if (stepElement) {
        stepElement.classList.remove('d-none');
    }
    currentStep = step;

    // Update Tabler steps-counter (using only Tabler's built-in classes)
    document.querySelectorAll('.step-item').forEach((item, index) => {
        const itemStep = index + 1;

        // Remove all state classes
        item.classList.remove('active');

        if (itemStep < step) {
            // Completed steps - make them clickable
            item.classList.add('completed');
            item.onclick = (e) => {
                e.preventDefault();
                showStep(itemStep);
            };
        } else if (itemStep === step) {
            // Current active step
            item.classList.add('active');
            item.onclick = (e) => e.preventDefault();
        } else {
            // Future steps - remove completed class
            item.classList.remove('completed');
            item.onclick = (e) => e.preventDefault();
        }
    });

    // Update animation preview if on step 4
    if (step === 4 && typeof updateAnimationPreview === 'function') {
        updateAnimationPreview();
    }

    updateNavButtons();
}

// Initialize first step on page load
document.addEventListener('DOMContentLoaded', function () {
    showStep(1);
    updateNavButtons();
});

// ============ Utility Functions ============
// Improved error handling function with null checks
function showError(element, message) {
    if (!element) {
        console.warn('Attempted to show error for non-existent element');
        return;
    }

    element.classList.add('is-invalid');

    // Find or create error message element
    let errorDiv = element.nextElementSibling;
    if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
        errorDiv = document.createElement('div');
        errorDiv.classList.add('invalid-feedback');
        element.parentNode.insertBefore(errorDiv, element.nextSibling);
    }

    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

// Safe error hiding function with null check
function hideError(element) {
    if (!element) return;

    element.classList.remove('is-invalid');
    const errorDiv = element.nextElementSibling;
    if (errorDiv?.classList.contains('invalid-feedback')) {
        errorDiv.style.display = 'none';
    }
}

// Add form change handlers to clear errors
document.querySelectorAll('input, select, textarea').forEach(element => {
    element.addEventListener('change', function () {
        hideError(this);
    });

    element.addEventListener('input', function () {
        hideError(this);
    });
});

// Image size type change handler to clear dimension errors
document.querySelectorAll('input[name="image_size_type"]').forEach(radio => {
    radio.addEventListener('change', function () {
        if (this.value === 'random') {
            document.querySelectorAll('input[type="file"]').forEach(input => {
                hideError(input);
                input.setAttribute('data-valid-dimension', 'true');
            });
        }
    });
});

function updateNavButtons() {
    prevBtn.style.display = currentStep > 1 ? 'block' : 'none';
    nextBtn.style.display = currentStep < totalSteps ? 'block' : 'none';
    submitBtn.style.display = currentStep === totalSteps ? 'block' : 'none';
}

function updateSlideNumbers() {
    document.querySelectorAll('.slide-preview').forEach((preview, index) => {
        const span = preview.querySelector('span');
        if (span) {
            span.textContent = `Slide ${index + 1}`;
        }
    });
}
function getCurrentSlideIndex() {
    const orderList = document.getElementById('slides-order');
    if (!orderList || !orderList.children.length) return 0;
    return parseInt(orderList.children[0].getAttribute('data-index'));
}

function getSlideData(index) {
    return {
        title: document.querySelector(`input[name="slides[${index}][title]"]`)?.value || 'Sample Title',
        description: document.querySelector(`textarea[name="slides[${index}][description]"]`)?.value || 'Sample Description',
        imageUrl: document.querySelector(`#imagePreview${index}`)?.src || '{{ asset("assets/images/no_image_available.png") }}'
    };
}

// Image Preview Handling
function previewImage(event, index) {
    const file = event.target.files[0];
    const input = event.target;
    const imageSizeType = document.querySelector('input[name="image_size_type"]:checked')?.value || 'fixed';

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const img = new Image();
            img.src = e.target.result;

            img.onload = function () {
                const width = this.width;
                const height = this.height;

                input.setAttribute('data-width', width);
                input.setAttribute('data-height', height);

                if (imageSizeType === 'fixed') {
                    if (width !== 1080 || height !== 1920) {
                        showError(input, 'Please add fixed dimension image 1080x1920 otherwise select random option for image');
                    } else {
                        hideError(input);
                    }
                } else {
                    hideError(input);
                }

                document.getElementById(`imagePreview${index}`).src = e.target.result;
                document.getElementById(`imagePreviewThumbnail${index}`).src = e.target.result;
                updateAnimationPreview(); // Ensure preview updates instantly
            };
        };
        reader.readAsDataURL(file);
    }
}

// Form Submission
form.addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent default only if validation fails

    console.log("Form submission initiated.");

    if (validateForm()) {
        console.log("Form is valid. Submitting...");
        // Add slide order to form
        const orderInput = document.createElement('input');
        orderInput.type = 'hidden';
        orderInput.name = 'slide_order';
        orderInput.value = JSON.stringify(getSlideOrder());
        this.appendChild(orderInput);

        // Add animation settings to form
        const animationSettings = document.createElement('input');
        animationSettings.type = 'hidden';
        animationSettings.name = 'animation_settings';
        animationSettings.value = JSON.stringify(currentAnimations);
        this.appendChild(animationSettings);

        this.submit(); // Submit form
    } else {
        console.log("Form validation failed. Not submitting.");
    }
});
document.addEventListener('DOMContentLoaded', function () {
    const formElements = document.querySelectorAll('input, select, textarea');
    formElements?.forEach(element => {
        if (element) {
            element.addEventListener('change', function () {
                hideError(this);
            });

            element.addEventListener('input', function () {
                hideError(this);
            });
        }
    });
});


function validateForm() {
    let isValid = true;


    // Validate title
    const title = document.querySelector('input[name="title"]');
    if (!title.value.trim()) {
        showError(title, 'Please enter a story title');
        isValid = false;
    } else {
        hideError(title);
    }
    // Validate topic
    const topic = document.querySelector('select[name="topic_id"]');
    if (!topic.value) {
        showError(topic, 'Please select a topic');
        isValid = false;
    } else {
        hideError(topic);
    }

    // Validate slides
    const slides = document.querySelectorAll('.accordion-item');
    if (slides.length === 0) {
        showErrorToast('Please add at least one slide');
        isValid = false;
    } else {
        slides.forEach((slide, index) => {
            const slideTitle = slide.querySelector(`input[name="slides[${index}][title]"]`);
            const slideDescription = slide.querySelector(`textarea[name="slides[${index}][description]"]`);
            const slideImage = slide.querySelector(`input[name="slides[${index}][image]"]`);

            /*
            if (slideTitle && !slideTitle.value.trim()) {
                showError(slideTitle, 'Please enter a slide title');
                isValid = false;
            }

            */
            if (slideDescription && !slideDescription.value.trim()) {
                showError(slideDescription, 'Please enter a slide description');
                isValid = false;
            }

            if (slideImage && (!slideImage.files || slideImage.files.length === 0)) {
                showError(slideImage, 'Please select an image');
                isValid = false;
            }
        });
    }

    return isValid;
}

// Initialize
updateNavButtons();

