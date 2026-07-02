// <><><><><><> START CHANNEL FOLLOW MODEL <><><><><><>
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('channels-follow-model');
  const doneButton = document.getElementById('done-button');

  if (modal && doneButton) {
    doneButton.addEventListener('click', function (event) {
      event.preventDefault();
      modal.style.display = 'none';
    });
  }
});
// <><><><><><> END CHANNEL FOLLOW MODEL <><><><><><>

// <><><><><><> START JS FOR MEMBERSHIP PLAN JS <><><><><><>
document.addEventListener('DOMContentLoaded', function () {
  // Initialize all tenure selectors on page load
  initializeTenureSelectors();

  // Use event delegation to handle changes on dynamically added .tenure-selector elements
  document.addEventListener('change', function (event) {
    if (event.target.classList.contains('tenure-selector')) {
      updateDisplayedValues(event.target);
    }
  });

  // Function to initialize tenure selectors
  function initializeTenureSelectors() {
    const tenureSelectors = document.querySelectorAll('.tenure-selector');
    tenureSelectors.forEach(selector => {
      // Set initial values
      updateDisplayedValues(selector);
    });
  }

  // Function to update displayed values
  function updateDisplayedValues(selector) {
    let price, duration, tenureId;

    if (selector.tagName === 'SELECT') {
      const selectedOption = selector.options[selector.selectedIndex];
      price = selectedOption.getAttribute('data-price');
      duration = selectedOption.getAttribute('data-duration');
      tenureId = selector.value;
    } else {
      // For non-select elements like div
      price = selector.getAttribute('data-price');
      duration = selector.getAttribute('data-duration');
      tenureId = selector.getAttribute('data-tenure-id');
    }

    const planId = selector.getAttribute('data-plan-id');
    const planName = selector.getAttribute('data-plan-name');

    // Find the card that contains this selector
    const card = selector.closest('.card');

    // Get currency from the first .fs-10 element in the card
    const currencyElement = card.querySelector('.fs-10');
    const currency = currencyElement ? currencyElement.textContent : '$'; // Fallback to '$' if not found

    // Update the displayed price and duration
    const priceElement = card.querySelector('.fw-bold[style="font-size: 45px"]');
    const durationElement = card.querySelector('.fs-6.text-muted');

    if (priceElement) {
      // Clear and update the price element
      priceElement.innerHTML = `<span class="fs-10">${currency}</span>${Number(price).toLocaleString()}`;
    }

    if (durationElement) {
      // Update the duration element
      const monthText = parseInt(duration) > 1 ? 's' : '';
      durationElement.textContent = `/${duration} month${monthText}`;
    }

    // Update the form values (if the form exists)
    const form = card.querySelector('.plan-form');
    if (form) {
      const tenureInput = form.querySelector('.tenure-id-input');
      const amountInput = form.querySelector('.amount-input');
      if (tenureInput) tenureInput.value = tenureId;
      if (amountInput) amountInput.value = price;
    }
  }
});
// <><><><><><> END JS OF MEMBERSHIP PLAN JS <><><><><><>

// <><><><><><><><><><><><><><> START JS FOR UPDATE PROFILE IMAGES <><><><><><><><><><><><><><>
document.addEventListener('DOMContentLoaded', function () {
  const profileImagePreview = document.getElementById('profile-image-preview');
  const profileImageInput = document.getElementById('change-profile');
  if (profileImageInput) {

    profileImageInput.addEventListener('change', function (event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          profileImagePreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }
});

const profileImage = document.getElementById('profileImage');
if (profileImage) {
  profileImage.addEventListener('click', function () {
    document.getElementById('dropdownMenu').classList.toggle('show');
  });
}
const logoutLink = document.getElementById('logout-link');
if (logoutLink) {
  logoutLink.addEventListener('click', function (event) {
    event.preventDefault(); // Prevent the default anchor click behavior
    document.getElementById('logout-form').submit(); // Submit the logout form
  });
}

window.onclick = function (event) {
  if (!event.target.matches('#profileImage')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
//  <><><><><><><><><><><><><><> END JS OF UPDATE PROFILE IMAGES <><><><><><><><><><><><><><>

//  <><><><><><><><><><><><><><> START JS FOR REGISTRATION MODEL  <><><><><><><><><><><><><><>
document.addEventListener('DOMContentLoaded', () => {
  const openSignupModalMobile = document.querySelector('.open-signup-modal-mobile');
  const openSignupModal = document.querySelector('.open-signup-modal');
  const ucAccountModal = document.getElementById('uc-account-modal');

  // Check if the elements exist before adding event listeners
  if (openSignupModalMobile && ucAccountModal) {
    openSignupModalMobile.addEventListener('click', () => {
      ucAccountModal.querySelector('li:nth-child(2) a').click();
    });
  }

  if (openSignupModal && ucAccountModal) {
    openSignupModal.addEventListener('click', () => {
      ucAccountModal.querySelector('li:nth-child(2) a').click();
    });
  }
});
//  <><><><><><><><><><><><><><> END JS OF UPDATE PROFILE IMAGES <><><><><><><><><><><><><><>

//  <><><><><><><> ADD JS FOR TRANSLATE POST DETAILS PAGE <><><><><><><>
document.addEventListener('DOMContentLoaded', () => {
  // Select the language dropdown
  const languageSelect = document.querySelector('select[name="language"]');
  if (!languageSelect) {
    return;
  }

  // Select the text container
  const textContainer = document.querySelector('#translateMe');
  if (!textContainer) {
    return;
  }

  // List of supported language codes
  const availableLanguages = [
    'af', 'sq', 'am', 'ar', 'hy', 'az', 'eu', 'be', 'bn', 'bs', 'bg', 'ca', 'ceb', 'ny',
    'zh-cn', 'zh-tw', 'co', 'hr', 'cs', 'da', 'nl', 'en', 'eo', 'et', 'tl', 'fi', 'fr',
    'fy', 'gl', 'ka', 'de', 'el', 'gu', 'ht', 'ha', 'haw', 'iw', 'hi', 'hmn', 'hu', 'is',
    'ig', 'id', 'ga', 'it', 'ja', 'jw', 'kn', 'kk', 'km', 'rw', 'ko', 'ku', 'ky', 'lo',
    'la', 'lv', 'lt', 'lb', 'mk', 'mg', 'ms', 'ml', 'mt', 'mi', 'mr', 'mn', 'my', 'ne',
    'no', 'or', 'ps', 'fa', 'pl', 'pt', 'pa', 'ro', 'ru', 'sm', 'gd', 'sr', 'st', 'sn',
    'sd', 'si', 'sk', 'sl', 'so', 'es', 'su', 'sw', 'sv', 'tg', 'ta', 'tt', 'te', 'th',
    'tr', 'tk', 'uk', 'ur', 'ug', 'uz', 'vi', 'cy', 'xh', 'yi', 'yo', 'zu'
  ];

  // Function to split text into chunks
  const splitText = (text, maxLength) => {
    const chunks = [];
    for (let i = 0; i < text.length; i += maxLength) {
      chunks.push(text.slice(i, i + maxLength));
    }
    return chunks;
  };

  // Function to translate text with retry logic
  async function translateText(text, targetLang, retries = 3) {
    const maxLength = 2000; // Max length per request to avoid 400 errors
    const chunks = splitText(text, maxLength);
    const translations = [];
    const delay = ms => new Promise(resolve => setTimeout(resolve, ms));

    for (const chunk of chunks) {
      for (let i = 0; i < retries; i++) {
        try {
          const response = await fetch(
            `https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=${targetLang}&dt=t&q=${encodeURIComponent(chunk)}`
          );
          if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP ${response.status}: ${errorText}`);
          }
          const data = await response.json();
          if (!data || !data[0]) throw new Error('Invalid API response');
          translations.push(data[0].map(item => item[0]).join(''));
          break; // Success, move to next chunk
        } catch (error) {
          if (i < retries - 1) {
            console.warn(`Retry ${i + 1} for chunk: ${error.message}`);
            await delay(1000 * (i + 1)); // Exponential backoff
          } else {
            throw error; // All retries failed
          }
        }
      }
    }
    return translations.join('');
  }

  // Add change event listener to the language dropdown
  languageSelect.addEventListener('change', async function () {
    const targetLang = availableLanguages.includes(this.value) ? this.value : 'gu';
    const originalText = textContainer.innerText.trim();
    if (!originalText) {

      // Use SweetAlert for a more user-friendly error message
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'warning',
          title: 'No Text Found',
          text: 'There is no text available to translate.',
          confirmButtonText: 'OK',
          confirmButtonColor: '#3085d6'
        });
      }

      textContainer.innerHTML = 'No text available';
      return;
    }

    try {
      // Log text length for debugging
      console.log('Original text length:', originalText.length);
      console.log('Encoded text length:', encodeURIComponent(originalText).length);

      // Translate the text
      const translatedText = await translateText(originalText, targetLang);
      textContainer.innerHTML = translatedText;
    } catch (error) {

      // Use SweetAlert for translation error
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Translation Failed',
          text: 'Unable to translate the text. Please try again later.',
          confirmButtonText: 'OK',
          confirmButtonColor: '#3085d6'
        });
      }

      textContainer.innerHTML = 'Translation failed. Please try again later.';
    }
  });
});
// <><><><><><> END TRANSLATE POST DETAILS PAGE <><><><><><>


// <><><><><><><> START JS FOR POST DAILY AND SUBSCRIPTION LIMIT <><><><><><><>
document.addEventListener('DOMContentLoaded', function () {
  const wrapper = document.getElementById('wrapper');
  if (!wrapper) return;

  const contentType = wrapper.dataset.contentType; // 'post', 'story', or 'epaper'
  const dailyLimitValue = parseInt(wrapper.dataset.dailyLimitValue);
  const isDailyEligible = wrapper.dataset.isDailyEligible === '1';

  const dailyLimitModalEl = document.getElementById('dailyLimitModal');
  const subscriptionLimitModalEl = document.getElementById('subscriptionLimitFreeTrialModal');

  // Initialize modals
  const dailyLimitModal = dailyLimitModalEl ? new bootstrap.Modal(dailyLimitModalEl) : null;
  const subscriptionLimitModal = subscriptionLimitModalEl ? new bootstrap.Modal(subscriptionLimitModalEl) : null;

  function applyBlurAndShowModal() {
    // Blur specific content if it's a post detail page
    if (contentType === 'post') {
      document.querySelectorAll('.selective-blur').forEach(el => {
        el.classList.add('blur-content');
        el.setAttribute('inert', 'true');
      });
    }

    if (dailyLimitModal) {
      dailyLimitModal.show();
    }
  }

  // Manage daily limit in localStorage
  if (isDailyEligible && contentType && !isNaN(dailyLimitValue)) {
    const page = wrapper.dataset.page;
    let shouldStoreCount = false;

    // Logic based on content type and current page
    if (contentType === 'story') {
      if (page === 'webstory-viewer') shouldStoreCount = true;
    } else if (contentType === 'epaper' || contentType === 'magazine') {
      if (page === 'webstory-viewer' || page === 'pdf-viewer') shouldStoreCount = true;
    } else {
      // Default to true for other content types like 'post'
      shouldStoreCount = true;
    }

    const key = `daily_limit_${contentType}`;
    const now = new Date().getTime();
    let data = JSON.parse(localStorage.getItem(key));
    let wasReset = false;

    // auto delete expired data
    if (data && now > data.resetAt) {
      localStorage.removeItem(key);
      console.log('data reset !!');
      data = null;
    }
    // Reset if expired (after 24 hours) or not exists
      if (!data || now > data.resetAt) {
        data = {
          limit: dailyLimitValue,
          count: 0,
          resetAt: now + (24 * 60 * 60 * 1000)
          
        };
        console.log('data reset successfully !!');
        wasReset = true;
        // Immediately save the fresh data to localStorage to remove old expired data
        localStorage.setItem(key, JSON.stringify(data));
      }

    // requirement: "should not be affected by any updates made from the admin panel during that 24-hour period"
    // So we use data.limit (the one saved when first fetched) rather than the current dailyLimitValue 
    // passed from the backend, UNLESS we just reset the data above.

    if (shouldStoreCount) {
      data.count++;
      localStorage.setItem(key, JSON.stringify(data));
    }

    if (data.count > data.limit && data.limit !== -1) {
      const redirectUrl = wrapper.dataset.redirectUrl;
      const isSubscriptionLimit = wrapper.dataset.subscriptionLimit === '1';
      const hasSubscription = wrapper.dataset.hasSubscription === '1';
      const isDailyLimit = true; // Since we are in the block where count > limit

      const shouldBlock = isSubscriptionLimit || (isDailyLimit && !hasSubscription);

      if (redirectUrl && shouldBlock) {
        window.location.href = redirectUrl;
      } else if (!isSubscriptionLimit && !hasSubscription) {
        // Only show daily limit modal if NOT a subscriber and subscription limit is NOT reached
        applyBlurAndShowModal();
      }
    }
  }

  // Show subscription modal if backend says so AND daily limit is reached
  if (subscriptionLimitModalEl && subscriptionLimitModal && wrapper.dataset.subscriptionLimit === '1') {
    const dailyKey = `daily_limit_${contentType}`;
    const dailyData = JSON.parse(localStorage.getItem(dailyKey));
    const isDailyLimit = dailyData && dailyData.count > dailyData.limit && dailyData.limit !== -1;

    // requirement: "this modal display when $subscriptionLimitReached and with $dailyLimitReached"
    if (isDailyLimit || wrapper.dataset.dailyLimit === '1') {
      subscriptionLimitModal.show();
      // Blur content only for posts if subscription limit reached
      if (contentType === 'post') {
        document.querySelectorAll('.selective-blur').forEach(el => {
          el.classList.add('blur-content');
          el.setAttribute('inert', 'true');
        });
      }
    }
  }

  // Handle "Read more" link click
  document.addEventListener('click', function (event) {
    const link = event.target.closest('.read-more-link, #readMoreLink');
    if (!link) return;

    const wrapper = document.getElementById('wrapper');
    if (!wrapper) return;

    const currentContentType = wrapper.dataset.contentType;
    const key = `daily_limit_${currentContentType}`;
    const data = JSON.parse(localStorage.getItem(key));
    const isDailyLimit = data && data.count > data.limit && data.limit !== -1;
    const isSubscriptionLimit = wrapper.dataset.subscriptionLimit === '1';
    const hasSubscription = wrapper.dataset.hasSubscription === '1';

    // Subscribed users should only be blocked if they reached BOTH subscription limit AND daily limit.
    // Guest/Free users are blocked if they hit the daily free trial limit.
    const isSubscriptionBlocked = isSubscriptionLimit && (isDailyLimit || wrapper.dataset.dailyLimit === '1');
    const isGuestBlocked = isDailyLimit && !hasSubscription;
    const shouldBlock = isSubscriptionBlocked || isGuestBlocked;

    if (shouldBlock) {
      event.preventDefault();
      if (isSubscriptionLimit && subscriptionLimitModal) {
        subscriptionLimitModal.show();
      } else if (dailyLimitModal) {
        dailyLimitModal.show();
      }
    }
  });

  // Handle WebStory "story-link" click using event delegation
  // On listing pages (webstory / webstory_by_topic), subscription users
  // should always be able to navigate — the slide page (show) handles their limit.
  document.addEventListener('click', function (event) {
    const link = event.target.closest('.story-link');
    if (!link) return;

    const wrapper = document.getElementById('wrapper');
    if (!wrapper) return;

    const currentContentType = wrapper.dataset.contentType;
    const currentPage = wrapper.dataset.page || '';
    const isListingPage = currentPage !== 'webstory-viewer';
    const hasSubscription = wrapper.dataset.hasSubscription === '1';

    const key = `daily_limit_${currentContentType}`;
    const data = JSON.parse(localStorage.getItem(key));
    const isDailyLimit = data && data.count > data.limit && data.limit !== -1;
    const isSubscriptionLimit = wrapper.dataset.subscriptionLimit === '1';

    if (isListingPage) {
      // On listing pages:
      // - ONLY block if daily limit is reached AND user has NO active subscription.
      // - If user has a subscription (even if daily guest limit is hit), allow navigation.
      if (isDailyLimit && !hasSubscription) {
        event.preventDefault();
        if (dailyLimitModal) {
          dailyLimitModal.show();
        }
      }
    } else {
      // On the slide viewer page (webstory-viewer), block based on limits.
      // Subscribed users now only blocked if both subscription and daily limits are reached.
      const isSubscriptionBlocked = isSubscriptionLimit && (isDailyLimit || wrapper.dataset.dailyLimit === '1');
      const isGuestBlocked = isDailyLimit && !hasSubscription;
      const shouldBlock = isSubscriptionBlocked || isGuestBlocked;

      if (shouldBlock) {
        event.preventDefault();
        if (isSubscriptionLimit && subscriptionLimitModal) {
          subscriptionLimitModal.show();
        } else if (dailyLimitModal) {
          dailyLimitModal.show();
        }
      }
    }
  });

  // Clean up blur when modal is closed (optional, usually we want it to stay blurred)
  dailyLimitModalEl?.addEventListener('hidden.bs.modal', function () {
    // wrapper?.classList.remove('blur-background'); 
    // Note: User might want to keep it blurred to prevent reading without limit reset.
  });
});
// <><><><><><><> END JS FOR POST DAILY AND SUBSCRIPTION LIMIT <><><><><><><>

// <><><><><><><> START JS FOR EPAPER AND MAGAZINE DAILY AND SUBSCRIPTION LIMIT <><><><><><><>
// Unified with the logic above. No additional code needed here unless specific behavior is required.
// <><><><><><><> END JS OF EPAPER AND MAGAZINE DAILY AND SUBSCRIPTION LIMIT  <><><><><><><>
// // <><><><><><><> START JS FOR FILTER VIDEO BY NEWEST AND OLDEST <><><><><><><>
document.addEventListener('DOMContentLoaded', function () {
  const sortSelect = document.getElementById('sort_by');

  if (sortSelect) {
    const videosUrl = sortSelect.getAttribute('data-videos-url') || "";
  }
  const videosContainer = document.getElementById('videos-container');
  const paginationContainer = document.getElementById('pagination-container');
  const videoCountSpan = document.getElementById('video-count');

  // Set selected filters from URL if present
  const urlParams = new URLSearchParams(window.location.search);
  const currentSort = urlParams.get('sort');

  if (currentSort) sortSelect.value = currentSort;

  function fetchVideos(sortValue, typeValue) {
    const url = new URL(videosUrl, window.location.origin);
    url.searchParams.set('sort', sortValue);

    videosContainer.innerHTML = '<div class="text-center py-5 w-100">Loading videos...</div>';

    fetch(url)
      .then(response => response.text())
      .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const newVideos = doc.getElementById('videos-container');
        const newPagination = doc.getElementById('pagination-container');
        const newVideoCount = doc.getElementById('video-count');

        if (newVideos && videosContainer) {
          videosContainer.innerHTML = newVideos.innerHTML;
        }

        if (newPagination && paginationContainer) {
          paginationContainer.innerHTML = newPagination.innerHTML;
        }

        if (newVideoCount && videoCountSpan) {
          videoCountSpan.innerHTML = newVideoCount.innerHTML;
        }

        // Update browser URL
        window.history.replaceState(null, '', url);
      })
      .catch({
      });
  }
  // ✅ Add this to respond to dropdown changes

  if (sortSelect) {
    sortSelect.addEventListener('change', function () {
      fetchVideos(sortSelect.value);
    });
  }

  // ✅ Optional: Load videos on page load if `sort` is set
  if (currentSort) {
    fetchVideos(currentSort);
  }
});
// <><><><><><><> END JS OF FILTER VIDEO BY NEWEST AND OLDEST  <><><><><><><>

// <><><><><><><> START JS FOR GOOGLE LOGIN ON WEB <><><><><><><>
class FirebaseAuth {
  constructor() {
    this.app = null;
    this.auth = null;
    this.provider = null;
    this.isInitialized = false;
    this.config = null;
  }

  // Get Firebase config from hidden div with base64 encoded data
  getConfigFromHiddenDiv() {
    try {
      const hiddenDiv = document.getElementById('firebase-config');
      if (!hiddenDiv) {
        throw new Error('Firebase config element not found');
      }

      const encodedConfig = hiddenDiv.dataset.config;
      if (!encodedConfig) {
        throw new Error('Firebase config data not found in element');
      }

      const decodedConfig = JSON.parse(atob(encodedConfig));

      // Validate required fields
      if (!decodedConfig || !decodedConfig.apiKey || !decodedConfig.projectId) {
        throw new Error('Invalid Firebase configuration - missing required fields');
      }

      console.log('Firebase config loaded successfully from hidden div');
      return decodedConfig;

    } catch (error) {
      return null;
    }
  }

  async init() {
    try {
      // Get config from hidden div
      this.config = this.getConfigFromHiddenDiv();
      if (!this.config) {
        return false;
      }

      console.log('Initializing Firebase with config:', { ...this.config, apiKey: '[HIDDEN]' });

      // Import Firebase modules dynamically
      const { initializeApp } = await import('https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js');
      const { getAuth, GoogleAuthProvider } = await import('https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js');

      // Initialize Firebase
      this.app = initializeApp(this.config);
      this.auth = getAuth(this.app);
      this.provider = new GoogleAuthProvider();

      // Configure Google Auth Provider
      this.provider.addScope('profile');
      this.provider.addScope('email');
      this.provider.setCustomParameters({
        'prompt': 'select_account'
      });

      this.isInitialized = true;
      console.log('Firebase initialized successfully');
      return true;

    } catch (error) {
      return false;
    }
  }

  async signInWithGoogle() {
    if (!this.isInitialized) {
      return null;
    }

    try {
      const { signInWithPopup } = await import('https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js');
      const result = await signInWithPopup(this.auth, this.provider);
      const user = result.user;

      return {
        uid: user.uid,
        email: user.email,
        name: user.displayName,
        photo: user.photoURL,
        token: await user.getIdToken()
      };

    } catch (error) {
      throw error;
    }
  }

  async sendToBackend(userData, endpoint = '/google-auth') {
    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(userData),
        credentials: 'same-origin'
      });

      if (!response.ok) {
        const errorData = await response.json().catch(() => ({}));
        throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
      }

      return await response.json();

    } catch (error) {
      throw error;
    }
  }

  // Update button state
  updateButtonState(loading = false, text = null) {
    const btn = document.getElementById('google-login-btn');
    if (!btn) return;

    if (loading) {
      btn.innerHTML = '<i class="icon icon-1 unicon-logo-google"></i> Sign in with Google';
      btn.disabled = true;
      btn.style.opacity = '0.7';
    } else {
      btn.innerHTML = text || '<i class="icon icon-1 unicon-logo-google"></i><span>Sign in with Google</span>';
      btn.disabled = false;
      btn.style.opacity = '1';
    }
  }

  // Show error message
  showError(message) {
    const errorContainer = document.getElementById('google-login-error');
    if (errorContainer) {
      errorContainer.textContent = message;
      errorContainer.style.display = 'block';

      // Auto-hide error after 5 seconds
      setTimeout(() => {
        errorContainer.style.display = 'none';
      }, 5000);
    }
  }

  // Get user-friendly error message
  getUserFriendlyErrorMessage(error) {
    const errorMessages = {
      'auth/popup-closed-by-user': 'Sign-in was cancelled.',
      'auth/popup-blocked': 'Pop-up was blocked by your browser. Please allow pop-ups and try again.',
      'auth/cancelled-popup-request': 'Sign-in was cancelled.',
      'auth/network-request-failed': 'Network error. Please check your connection and try again.',
      'auth/too-many-requests': 'Too many failed attempts. Please try again later.',
      'auth/user-disabled': 'This account has been disabled.',
      'auth/operation-not-allowed': 'Google sign-in is not enabled.',
      'auth/invalid-api-key': 'Invalid API key configuration.',
      'auth/app-deleted': 'Firebase app configuration error.',
    };

    return errorMessages[error.code] || error.message || 'Authentication failed. Please try again.';
  }

  async handleGoogleAuth(endpoint = '/google-auth') {
    const btn = document.getElementById('google-login-btn');


    // Clear any previous errors
    const errorContainer = document.getElementById('google-login-error');
    if (errorContainer) {
      errorContainer.style.display = 'none';
    }

    this.updateButtonState(true);

    try {
      // Initialize Firebase if not already done
      if (!this.isInitialized) {
        const initialized = await this.init();
        if (!initialized) {
          throw new Error('Failed to initialize Firebase');
        }
      }

      // Sign in with Google
      const userData = await this.signInWithGoogle();
      if (!userData) {
        throw new Error('Failed to get user data from Google');
      }

      console.log('User signed in:', userData.email);

      // Send to backend
      const response = await this.sendToBackend(userData, endpoint);

      if (response.error === false || response.success === true) {
        this.updateButtonState(false, '<i class="icon icon-1 unicon-logo-google"></i> Sign in with Google');

        setTimeout(() => {
          window.location.href = response.redirect || '/';
        }, 1000);
      } else {
        throw new Error(response.message || 'Authentication failed on server');
      }

    } catch (error) {

      const errorMessage = this.getUserFriendlyErrorMessage(error);
      this.showError(errorMessage);
      this.updateButtonState(false);
    }
  }
}
// Initialize Firebase Auth when DOM is ready
document.addEventListener('DOMContentLoaded', function () {
  const firebaseAuth = new FirebaseAuth();

  // Set up click handler for Google login button
  const btn = document.getElementById('google-login-btn');
  if (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      firebaseAuth.handleGoogleAuth();
    });
  }
});
// <><><><><><><> END JS OF GOOGLE LOGIN ON WEB <><><><><><><>

// <><><><><><><> START CENTRALIZED CLICK TRACKING FUNCTION <><><><><><><>
function trackAdClick(rawId) {
  const adId = parseInt(String(rawId).replace(/[^\d]/g, ''), 10);
  if (!adId) return;
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  fetch("/ads/click", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
      "X-CSRF-TOKEN": csrfToken
    },
    body: `ad_id=${adId}`
  }).catch(err => console.error("Click tracking failed:", err));
}

$(document).ready(function () {
  $.ajaxSetup({
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
    }
  });

  $(document).on("click", "a[data-ad-id]", function (e) {
    const rawId = $(this).data("ad-id");
    const adId = parseInt(String(rawId).replace(/[^\d]/g, ''), 10);

    if (!adId) return;

    $.ajax({
      url: "/ads/click",
      method: "POST",
      data: { ad_id: adId },
      error: function (err) {
        console.error("Click tracking failed:", err);
      }
    });
  });
});

// <><><><><><><> END JS OF CENTRALIZED CLICK TRACKING FUNCTION <><><><><><><>

// <><><><><><><> START JS FOR POST DETAIL PAGE ADS <><><><><><><>
$(function () {
  let sponsorRotationSeconds = parseInt($('#sponsor-rotation-duration').val(), 10) || 20;
  let sponsorRotationInterval = sponsorRotationSeconds * 1000;
  console.log('postdetails page ads ', sponsorRotationInterval);

  function fetchAd(key, cb) {
    $.getJSON(`/ads/${key}`)
      .done(function (ad) {
        cb(ad);
      })
      .fail(function () {
        cb(null);
      });
  }

  function adCard(ad) {
    if (!ad || !ad.horizontal_image || !ad.id) return '';

    return `
        <div class="post-author panel py-4 px-3 sm:p-3 xl:p-4 bg-gray-25 dark:bg-opacity-10 rounded lg:rounded-2">
        <div class="row g-4 items-center">
                  <div class="col-12 sm:col-5 xl:col-3 position-relative">
                    <p class="badge bg-white text-primary fw-bold  position-absolute top-0 start-0 mt-1  ms-3 fw-semibold" style="z-index: 10;">
            Sponsored Advertisement </p>
              <!-- Ad Image -->
              <figure class="featured-image m-0 ratio ratio-4x3 rounded uc-transition-toggle overflow-hidden bg-gray-25 dark:bg-gray-800">
                  <a href="${ad.imageUrl || '#'}" target="_blank" class="ad-click-link"  data-ad-id="${ad.id}"
                    data-caption="${ad.title || 'Sponsored'}">
                      <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                          src="${ad.vertical_image}" 
                          alt="${ad.image_alt || ad.title || 'Sponsored Ad'}"
                          loading="lazy" 
                          style="object-fit: cover;">
                  </a>
              </figure>
          </div>

          <div class="col">
              <div class="panel vstack items-start gap-2 md:gap-3 text-start">
                  <h4 class="h5 lg:h4 m-0 text-truncate-2 text-start">${ad.title || ''}</h4>
                  <p class="fs-6 lg:fs-5 text-truncate-2 text-start">${ad.description || ''}</p>
              </div>
          </div>
      </div>
  </div>

    `;
  }

  function rotatePostDetailAd(container, key, interval) {
    let holder = $(container);
    if (!holder.length) return;

    function loadAd() {
      fetchAd(key, ad => {
        const adHtml = adCard(ad);
        if (adHtml) {
          holder.html(adHtml);
        } else {
          holder.empty();
        }
      });
    }

    loadAd();
    setInterval(loadAd, interval);
  }

  // Post detail placement
  rotatePostDetailAd("#post-detail-ad", "post_detail", sponsorRotationInterval);

  // Centralized click tracking
  $(document).on("click", "a[data-ad-id]", function () {
    let adId = $(this).data("ad-id");
    if (adId) {
      trackAdClick(adId);
    }
  });
});
// <><><><><><><> END JS OF POST DETAIL PAGE ADS <><><><><><><>

// <><><><><><><> START JS FOR BANNER POSTS ADS <><><><><><><>
document.addEventListener("DOMContentLoaded", function () {
  function loadAd() {
    let adImage = document.getElementById("rotatingAdBanner");
    let adMobile = document.getElementById("rotatingAdBannerMobile");
    let adLink = document.getElementById("bannerAdLink");
    let adOverlay = document.getElementById("bannerAdOverlay");
    let postHeader = document.querySelector(".post-header");

    if (!adImage || !adLink || !adOverlay) return;

    let apiUrl = adImage.dataset.url;
    if (!apiUrl) return;

    fetch(apiUrl)
      .then(res => res.ok ? res.json() : Promise.reject('Network error'))
      .then(ad => {
        if (ad && ad.horizontal_image && ad.id) {
          adImage.src = ad.horizontal_image;
          adImage.alt = ad.image_alt || ad.title || 'Sponsored Ad';

          if (adMobile) {
            adMobile.src = ad.horizontal_image;
            adMobile.alt = ad.image_alt || ad.title || 'Sponsored Ad';
          }

          adLink.href = ad.imageUrl || "#";
          adOverlay.href = ad.imageUrl || "#";
          adLink.dataset.adId = ad.id;
          adOverlay.dataset.adId = ad.id;

          const adTitle = document.getElementById("adTitle");
          const adClicks = document.getElementById("adClicks");

          if (adTitle) adTitle.textContent = ad.title || "";
          if (adClicks) adClicks.textContent = ad.clicks || 0;

          if (postHeader) postHeader.style.display = "block";
        } else {
          // Hide the banner if no valid ad
          if (postHeader) postHeader.style.display = "none";
        }
      })
      .catch(err => {
        if (postHeader) postHeader.style.display = "none";
      });
  }

  // Centralized click tracking
  document.addEventListener("click", function (e) {
    let target = e.target.closest("a[data-ad-id]");
    if (!target) return;
    trackAdClick(target.dataset.adId);
  });

  // Load ad once on page load (no interval)
  loadAd();
});
// <><><><><><><> END JS OF BANNER POSTS ADS <><><><><><><>

// <><><><><><><> START JS FOR LATEST AND POPULAR POSTS ADS <><><><><><><>
$(document).ready(function () {
  function fetchAd(placementKey, userId = null, callback) {
    $.ajax({
      url: userId ? `/ads/${placementKey}?userId=${userId}` : `/ads/${placementKey}`,
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        callback(data);
      },
      error: function () {
        callback(null); // Silent fail
      }
    });
  }

  function createLatestAdHtml(ad) {
    if (!ad || !ad.vertical_image || !ad.id) return '';

    const adTitle = ad.title || 'Advertisement';
    const adUrl = ad.imageUrl || '#';
    const adImage = ad.vertical_image;
    const adImageAlt = ad.image_alt || 'Advertisement';
    const adDescription = ad.description || 'Advertisement Content';
    return `
            <div class="widget-content">
                <article class="post type-post panel uc-transition-toggle">
                    <div class="row child-cols g-2 lg:g-3" data-uc-grid>
                        <div class="col-auto">
                            <div class="post-media panel overflow-hidden max-w-150px min-w-100px lg:min-w-250px">
                                <div class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-3x2">
                                    <a href="${adUrl}" target="_blank" class="position-cover" data-ad-id="${ad.id}">
                                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                             src="${adImage}" 
                                             alt="${adImageAlt}" 
                                             loading="lazy" >
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="post-header panel vstack justify-between gap-1">
                                <h3 class="post-title h5 lg:h4 m-0 text-truncate-2 hover:text-primary">
                                    <a class="text-none duration-150" href="${adUrl}" title="${adTitle}" data-ad-id="${ad.id}">${adTitle}</a>
                                </h3>
                            </div>
                            <p class="post-excrept ft-tertiary fs-6 text-gray-900 dark:text-white text-opacity-60 text-truncate-2 my-1">
                                ${adDescription}
                            </p>
                            <div class="d-flex justify-between">
                                <div>
                                    <a  class="post-comments text-none hstack gap-narrow badge bg-primary text-white" title="Sponsored">
                                        Sponsor Ads
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        `;
  }

  function insertStaticAd(sectionSelector, placementKey, position = 'latestRandom', userId = null) {
    const $section = $(sectionSelector);
    if (!$section.length) return;

    const posts = $section.children('div');
    if (posts.length === 0) return;

    const $adContainer = $('<div class="ad-placeholder full-width-ad"></div>');

    if (position === 'latestRandom') {
      // Ensure we don't exceed the number of posts
      const maxIndex = Math.min(6, posts.length - 1);
      const randomIndex = Math.floor(Math.random() * (maxIndex + 1)); // 0 to maxIndex
      $(posts[randomIndex]).after($adContainer);
    } else if (position === 'last') {
      $(posts[posts.length - 1]).after($adContainer); // Popular post: after last
    }

    fetchAd(placementKey, userId, function (ad) {
      if (!ad) {
        $adContainer.hide();
        return;
      }

      const adHtml = createLatestAdHtml(ad);
      if (!adHtml) {
        $adContainer.hide();
        return;
      }

      $adContainer.html(adHtml).show();
    });
  }

  // Latest posts: ad after first post (or random later if needed)
  insertStaticAd('.latest-news .block-content .row', 'latest', 'latestRandom');

  // Popular posts: ad at last position (fixed)
  insertStaticAd('.popular-widget .widget-content .row', 'popular', 'last');
});
// <><><><><><><> END JS FOR LATEST AND POPULAR POSTS ADS (STATIC) <><><><><><><>

// <><><><><><><> START JS FOR TOPIC POSTS ADS <><><><><><><>
$(document).ready(function () {
  function fetchAd(placementKey, callback) {
    $.ajax({
      url: `/ads/${placementKey}`,
      type: "GET",
      dataType: "json",
      success: (data) => callback(data),
      error: (xhr, status, error) => {
        // Silent fail - don't log errors for unavailable ads
        callback(null);
      },
    });
  }

  function createAdHtml(ad) {
    if (!ad || !ad.vertical_image || !ad.id) return "";

    return `
            <div class="col ad-card">
                <article class="post type-post panel vstack gap-2">
                    <div class="post-image panel overflow-hidden">
                        <figure class="featured-image m-0 ratio ratio-16x9 rounded overflow-hidden bg-gray-25 dark:bg-gray-800">
                            <a href="${ad.imageUrl || "#"}" target="_blank" class="position-cover" data-ad-id="${ad.id}">
                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                                     src="${ad.vertical_image}" 
                                     alt="${ad.image_alt || "Advertisement"}"
                                     loading="lazy" >
                            </a>
                        </figure>
                    </div>
                    <div class="post-header panel vstack gap-1">
                        <h3 class="post-title h6 sm:h5 xl:h4 m-0">
                            <a class="text-none hover:text-primary duration-150  text-truncate-2 m-0"
                               href="${ad.imageUrl || "#"}" data-ad-id="${ad.id}">${ad.title || "Sponsored Ad"}</a>
                        </h3>
                        <div class="post-meta panel text-gray-900 dark:text-white text-opacity-60">
                            <h6 class="badge bg-primary text-white">Sponsored</h6>
                        </div>
                    </div>
                </article>
            </div>
        `;
  }

  function insertInlineAd(containerId, placementKey) {
    var $container = $(`#${containerId}`);
    if (!$container.length) return;

    var posts = $container.children("div");
    if (posts.length === 0) return;

    // Pick a random post position
    var randomIndex = Math.floor(Math.random() * posts.length);
    var adContainer = $('<div class="ad-placeholder"></div>');
    $(posts[randomIndex]).after(adContainer);

    // Fetch and insert ad once
    fetchAd(placementKey, function (ad) {
      if (!ad) {
        adContainer.hide();
        return;
      }

      var adHtml = createAdHtml(ad);
      if (!adHtml) {
        adContainer.hide();
        return;
      }

      adContainer.html(adHtml).show();
    });
  }

  insertInlineAd("topic_post_ads", "topic_posts");
});
// <><><><><><><> END JS OF TOPIC POSTS ADS <><><><><><><>

// <><><><><><><> START JS FOR VIDEOS POSTS ADS <><><><><><><>
$(function () {
  function fetchAd(key, cb) {
    $.getJSON(`/ads/${key}`)
      .done(function (ad) {
        cb(ad);
      })
      .fail(function () {
        cb(null);
      });
  }

  function adCard(ad) {
    if (!ad || !ad.vertical_image || !ad.id) return '';

    return `
            <div>
                <article class="post type-post panel vstack gap-2 f-cat border p-2 dark:bg-gray-800 bg-white rounded">
                    <div class="post-image panel overflow-hidden">
                        <figure class="featured-image m-0 ratio ratio-16x9 rounded overflow-hidden bg-gray-25 dark:bg-gray-800">
                            <a href="${ad.imageUrl || '#'}" target="_blank" class="position-cover" data-ad-id="${ad.id}">
                                <img class="media-cover image" src="${ad.vertical_image}" alt="${ad.image_alt || 'Sponsored Ad'}">
                                <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                    <span class="fs-6 fw-bold bg-primary px-2 py-1 rounded">Ad</span>
                                </div>
                            </a>
                        </figure>
                    </div>
                    <div class="post-header panel vstack gap-1 lg:gap-2 rounded dark:bg-gray-800 bg-white">
                        <h5 class="post-title h6 sm:h5 xl:h5 m-0 text-truncate-2">
                            <a href="${ad.imageUrl || '#'}" target="_blank" class="text-none hover:text-primary duration-150" data-ad-id="${ad.id}">
                                ${ad.title || 'Sponsored Ad'}
                            </a>
                        </h5>
                        <div class="post-meta panel fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60">
                            <span class="badge bg-primary text-white">Sponsored</span>
                        </div>
                    </div>
                </article>
            </div>`;
  }

  function insertAdOnce(container, key) {
    let $container = $(container);
    if (!$container.length) return;

    let posts = $(`${container} > div`);
    let adHolder = $('<div class="ad-placeholder" style="display: none;"></div>');

    if (posts.length) {
      posts.eq(Math.floor(Math.random() * posts.length)).after(adHolder);
    } else {
      $container.prepend(adHolder);
    }

    // Load ad once
    fetchAd(key, ad => {
      const adHtml = adCard(ad);
      if (adHtml) {
        adHolder.html(adHtml).show();
      } else {
        adHolder.hide();
      }
    });
  }

  insertAdOnce("#videos-container", "videos");
});
// <><><><><><><> END JS OF VIDEOS POSTS ADS <><><><><><><>

// <><><><><><><> START JS FOR ALL POSTS ADS <><><><><><><>
$(function () {
  function fetchAd(key, cb) {
    $.getJSON(`/ads/${key}`)
      .done(function (ad) {
        cb(ad);
      })
      .fail(function () {
        cb(null);
      });
  }

  function adCard(ad) {
    if (!ad || !ad.vertical_image || !ad.id) return '';

    return `
            <div>
                <article class="post type-post panel vstack gap-2">
                    <div class="post-image panel overflow-hidden">
                        <figure class="featured-image m-0 ratio ratio-16x9 rounded uc-transition-toggle overflow-hidden">
                            <a href="${ad.imageUrl || '#'}" target="_blank" class="position-cover" data-ad-id="${ad.id}">
                                <img class="media-cover image uc-transition-scale-up uc-transition-opaque" src="${ad.vertical_image}" alt="${ad.image_alt || 'Sponsored Ad'}">
                                <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                                    <span class="fs-6 fw-bold bg-primary px-2 py-1 rounded">Ad</span>
                                </div>
                            </a>
                        </figure>
                    </div>
                    <div class="post-header panel vstack gap-1 lg:gap-2">
                        <h5 class="post-title h6 sm:h5 xl:h5 m-0 text-truncate-2">
                            <a href="${ad.imageUrl || '#'}" target="_blank" class="text-none hover:text-primary duration-150" data-ad-id="${ad.id}">
                                ${ad.title || 'Sponsored Ad'}
                            </a>
                        </h5>
                        <div class="post-meta panel fs-7 fw-medium text-gray-900 dark:text-white text-opacity-60">
                            <span class="badge bg-primary text-white">Sponsored Ad</span>
                        </div>
                    </div>
                </article>
            </div>`;
  }

  function insertAdOnce(container, key) {
    let $container = $(container);
    if (!$container.length) return;

    let posts = $(`${container} > div`);
    let adHolder = $('<div class="ad-placeholder" style="display: none;"></div>');

    if (posts.length) {
      posts.eq(Math.floor(Math.random() * posts.length)).after(adHolder);
    } else {
      $container.prepend(adHolder);
    }

    // Load ad once
    fetchAd(key, ad => {
      const adHtml = adCard(ad);
      if (adHtml) {
        adHolder.html(adHtml).show();
      } else {
        adHolder.hide();
      }
    });
  }

  insertAdOnce("#posts-ad-container", "posts");
});
// <><><><><><><> END JS OF ALL POSTS ADS <><><><><><><>

// <><><><><><><> START JS FOR FOOTER AND HEADER ADS <><><><><><><>
$(function () {

  let sponsorRotationSeconds =
    parseInt(document.getElementById("sponsor-rotation-duration")?.value, 10) || 20;

  let sponsorRotationInterval = sponsorRotationSeconds * 1000;
  console.log('Footer/Header Ad Interval:', sponsorRotationInterval);

  function fetchAd(key, cb) {
    $.getJSON(`/ads/${key}`)
      .done(function (ad) {
        cb(ad);
      })
      .fail(function () {
        cb(null);
      });
  }

  function footerAdCard(ad) {
    if (!ad || !ad.horizontal_image || !ad.id) return "";

    return `
            <div class="footer-ad panel vstack gap-2 justify-center items-center">
                <a href="${ad.imageUrl || '#'}" 
                   data-ad-id="${ad.id}" 
                   target="_blank" 
                   class="ad-click-link d-inline-block">
                    <img src="${ad.horizontal_image}" 
                         alt="${ad.image_alt || 'Sponsored Ad'}" 
                         class="img-fluid rounded shadow-sm mx-auto rotatingAd ">
                </a>
            </div>`;
  }

  function rotateFooterAd(container, key, interval) {
    let holder = $(container);
    if (!holder.length) return;
    function loadAd() {
      fetchAd(key, ad => {
        const adHtml = footerAdCard(ad);
        if (adHtml) {
          holder.fadeOut(400, function () {
            holder.html(adHtml).fadeIn(400);
          });
        } else {
          holder.fadeOut(400).empty();
        }
      });
    }

    loadAd();
    setInterval(loadAd, interval);
  }

  rotateFooterAd("#footer-ad-container", "footer", sponsorRotationInterval);
  rotateFooterAd("#header-ad-container", "header", sponsorRotationInterval);
});
// <><><><><><><> END JS FOR FOOTER AND HEADER ADS <><><><><><><>

// <><><><><><><> START JS FOR SIDEBAR ADS <><><><><><><>
$(function () {
  let sponsorRotationSeconds =
    parseInt(document.getElementById("sponsor-rotation-duration")?.value, 10) || 20;

  let sponsorRotationInterval = sponsorRotationSeconds * 1000;

  function fetchAd(key, cb) {
    $.getJSON(`/ads/${key}`)
      .done(function (ad) {
        cb(ad);
      })
      .fail(function () {
        cb(null);
      });
  }

  function adCard(ad) {
    if (!ad || !ad.horizontal_image || !ad.id) return '';

    return `
            <a href="${ad.imageUrl || '#'}" target="_blank" data-ad-id="${ad.id}" class="ad-click-link">
                <img src="${ad.horizontal_image}" 
                     alt="${ad.image_alt || 'Sponsored Ad'}">
            </a>`;
  }

  function rotateSidebarAd(container, key, interval) {
    let holder = $(container);
    if (!holder.length) return;

    function loadAd() {
      fetchAd(key, ad => {
        const adHtml = adCard(ad);
        if (adHtml) {
          holder.html(adHtml);
        } else {
          holder.empty();
        }
      });
    }

    loadAd();
    setInterval(loadAd, interval);
  }

  rotateSidebarAd("#left-sidebar-ad", "left_sidebar", sponsorRotationInterval);
  rotateSidebarAd("#right-sidebar-ad", "right_sidebar", sponsorRotationInterval);
});
// <><><><><><><> END JS FOR SIDEBAR ADS <><><><><><><>

// <><><><><><><> START JS FOR SUBSCRIBE MODEL <><><><><><><>
$(document).ready(function () {
  $('#web-subscriber-button').click(function (event) {
    event.preventDefault();

    let email = $('#subscriber_email').val().trim();
    let input = $('#subscriber_email');
    let errorDiv = $('#subscriber-error-top');
    let button = $(this);

    // Get validation messages from data attributes
    let msgRequired = input.data('email-required');
    let msgInvalid = input.data('email-invalid');
    let msgTaken = input.data('email-taken');
    let msgSubscribed = input.data('email-subscribed');

    // Hide previous error
    errorDiv.addClass('d-none').text('');

    // Basic client-side validation before AJAX
    if (email === '') {
      iziToast.error({
        title: msgRequired,
        position: 'topCenter',
      });
      return;
    }

    // Simple email format check
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      iziToast.error({
        title: msgInvalid,
        position: 'topCenter',
      });
      return;
    }

    // Disable button to prevent multiple clicks
    button.prop('disabled', true);

    $.ajax({
      url: '/subscribe/store', // Laravel route
      method: 'POST',
      data: {
        email: email,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function (response) {
        if (response.status == 1) {
          // Success toast
          iziToast.success({
            title: response.message,
            position: 'topCenter',
          });

          // Backup original state
          const originalHtml = button.html();
          const originalClass = button.attr('class');

          // Update button state with localized subscribed message
          button.removeClass('btn-primary').addClass('btn-success');
          button.html('<i class="bi bi-check-circle"></i> ' + msgSubscribed);
          $('#subscriber_email').val(''); // clear input

          // Reset button after 2 seconds
          setTimeout(function () {
            button.html(originalHtml);
            button.attr('class', originalClass);
            button.prop('disabled', false);
          }, 2000);
        } else {
          // Failure toast
          iziToast.error({
            title: response.message,
            position: 'topCenter',
          });
          button.prop('disabled', false);
        }
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          let errors = xhr.responseJSON.errors;
          if (errors.email) {
            let errorMsg = errors.email[0];
            // Replace Laravel's default with localized message
            if (errorMsg.toLowerCase().includes('taken')) {
              errorMsg = msgTaken;
            }
            iziToast.error({
              title: errorMsg,
              position: 'topCenter',
            });
          }
        }
        button.prop('disabled', false);
      }
    });
  });
});

$(document).ready(function () {
  $('#index-subscriber-button').click(function (event) {
    event.preventDefault();

    let email = $('#index_subscriber_email').val().trim();
    let input = $('#index_subscriber_email');
    let errorDiv = $('#subscriber-error-top-index');
    let button = $(this);

    // Get validation messages from data attributes
    let msgRequired = input.data('email-required');
    let msgInvalid = input.data('email-invalid');
    let msgTaken = input.data('email-taken');
    let msgSubscribed = input.data('email-subscribed');

    // Hide previous error
    errorDiv.addClass('d-none').text('');

    // Basic client-side validation before AJAX
    if (email === '') {
      iziToast.error({
        title: msgRequired,
        position: 'topCenter',
      });
      return;
    }

    // Simple email format check
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      iziToast.error({
        title: msgInvalid,
        position: 'topCenter',
      });
      return;
    }

    // Disable button to prevent multiple clicks
    button.prop('disabled', true);

    $.ajax({
      url: '/subscribe/store', // Laravel route
      method: 'POST',
      data: {
        email: email,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function (response) {
        if (response.status == 1) {
          // Success toast
          iziToast.success({
            title: response.message,
            position: 'topCenter',
          });

          // Backup original state
          const originalHtml = button.html();
          const originalClass = button.attr('class');

          // Update button state with localized subscribed message
          button.removeClass('btn-primary').addClass('btn-success');
          button.html('<i class="bi bi-check-circle"></i> ' + msgSubscribed);
          $('#index_subscriber_email').val(''); // clear input

          // Reset button after 2 seconds
          setTimeout(function () {
            button.html(originalHtml);
            button.attr('class', originalClass);
            button.prop('disabled', false);
          }, 2000);
        } else {
          // Failure toast
          iziToast.error({
            title: response.message,
            position: 'topCenter',
          });
          button.prop('disabled', false);
        }
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          let errors = xhr.responseJSON.errors;
          if (errors.email) {
            let errorMsg = errors.email[0];
            // If Laravel says "email has already been taken", show our localized message
            if (errorMsg.toLowerCase().includes('taken')) {
              errorMsg = msgTaken;
            }
            iziToast.error({
              title: errorMsg,
              position: 'topCenter',
            });
          }
        }
        button.prop('disabled', false);
      }
    });
  });
});

$(document).ready(function () {
  $('#model-subscriber-button').click(function (event) {
    event.preventDefault();

    let email = $('#model-subscriber_email').val().trim();
    let input = $('#model-subscriber_email');
    let errorDiv = $('#model-subscriber-error-top');
    let button = $(this);

    // Get validation messages from data attributes
    let msgRequired = input.data('email-required');
    let msgInvalid = input.data('email-invalid');
    let msgTaken = input.data('email-taken');
    let msgSubscribed = input.data('email-subscribed');

    // Hide previous error
    errorDiv.addClass('d-none').text('');

    // Basic client-side validation before AJAX
    if (email === '') {
      iziToast.error({
        title: msgRequired,
        position: 'topCenter',
      });
      return;
    }

    // Simple email format check
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      iziToast.error({
        title: msgInvalid,
        position: 'topCenter',
      });
      return;
    }

    // Disable button to prevent multiple clicks
    button.prop('disabled', true);

    $.ajax({
      url: '/subscribe/store', // Laravel route
      method: 'POST',
      data: {
        email: email,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      success: function (response) {
        if (response.status == 1) {
          // Success toast
          iziToast.success({
            title: response.message,
            position: 'topCenter',
          });

          // Backup original state
          const originalHtml = button.html();
          const originalClass = button.attr('class');

          // Update button state with localized subscribed message
          button.removeClass('btn-primary').addClass('btn-success');
          button.html('<i class="bi bi-check-circle"></i> ' + msgSubscribed);
          $('#model-subscriber_email').val(''); // clear input

          // Reset button after 2 seconds
          setTimeout(function () {
            button.html(originalHtml);
            button.attr('class', originalClass);
            button.prop('disabled', false);
          }, 2000);
        } else {
          // Failure toast
          iziToast.error({
            title: response.message,
            position: 'topCenter',
          });
          button.prop('disabled', false);
        }
      },
      error: function (xhr) {
        if (xhr.status === 422) {
          let errors = xhr.responseJSON.errors;
          if (errors.email) {
            let errorMsg = errors.email[0];
            // Localize "email taken" message
            if (errorMsg.toLowerCase().includes('taken')) {
              errorMsg = msgTaken;
            }
            iziToast.error({
              title: errorMsg,
              position: 'topCenter',
            });
          }
        }
        button.prop('disabled', false);
      }
    });
  });

  // Clear modal input when closed
  if (document.getElementById('uc-newsletter-modal')) {
    UIkit.util.on('#uc-newsletter-modal', 'hidden', function () {
      $('#model-subscriber_email').val('');
      $('#model-subscriber-error-top').addClass('d-none').text('');
    });
  }
  // Clear web dropdown input when closed
  const webDropdown = document.querySelector('.uc-navbar-dropdown');
  if (webDropdown) {
    UIkit.util.on(webDropdown, 'hidden', function () {
      $('#subscriber_email').val('');
      $('#subscriber-error-top').addClass('d-none').text('');
    });
  }
});
// <><><><><><><> END  JS FOR SUBSCRIBE MODEL <><><><><><><>

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll("[id^='timer-']").forEach(function (el) {
    let seconds = parseInt(el.getAttribute("data-seconds"), 10);
    let btnId = el.getAttribute("data-btn");
    let btn = document.getElementById(btnId);

    function updateTimer() {
      if (seconds <= 0) {
        el.innerText = "Expired";
        if (btn) {
          btn.disabled = true;
          btn.classList.add('btn-secondary');
          btn.classList.remove('btn-success');
        }
        return;
      }
      let h = Math.floor(seconds / 3600);
      let m = Math.floor((seconds % 3600) / 60);
      let s = seconds % 60;
      el.innerText = `${h}h ${m}m ${s}s`;
      seconds--;
      setTimeout(updateTimer, 1000);
    }

    if (!isNaN(seconds) && seconds > 0) {
      updateTimer();
    }
  });
});

//  <><><><><><><><><><><><><><> START JS FOR ADD AUDIO WAVES FOR BETTER UI <><><><><><><><><><><><><><>
document.addEventListener('DOMContentLoaded', function () {
  const waveformContainers = document.querySelectorAll('[id^="waveform-"]');

  waveformContainers.forEach(function (container) {
    const postId = container.id.replace('waveform-', '');
    const audioUrl = container.dataset.audioUrl;

    if (!audioUrl) return;

    const wavesurfer = WaveSurfer.create({
      container: container,
      waveColor: '#4F4A85',
      progressColor: '#eb4432',
      height: 60,
      responsive: true,
      normalize: true,
      backend: 'MediaElement',
      barWidth: 2,
      barGap: 1,
      barRadius: 3
    });

    wavesurfer.load(audioUrl);

    const playBtn = document.getElementById(`play-btn-${postId}`);
    const playIcon = document.getElementById(`play-icon-${postId}`);
    const pauseIcon = document.getElementById(`pause-icon-${postId}`);

    // Play button click - toggle play/pause
    if (playBtn) {
      playBtn.addEventListener('click', function () {
        wavesurfer.playPause();
      });
    }

    // Play/Pause on waveform click (optional)
    wavesurfer.on('interaction', function () {
      wavesurfer.playPause();
    });

    // Update icon on play
    wavesurfer.on('play', function () {
      playIcon.style.display = 'none';
      pauseIcon.style.display = 'inline-block';
    });

    // Update icon on pause
    wavesurfer.on('pause', function () {
      playIcon.style.display = 'inline-block';
      pauseIcon.style.display = 'none';
    });

    // Update icon when audio finishes
    wavesurfer.on('finish', function () {
      playIcon.style.display = 'inline-block';
      pauseIcon.style.display = 'none';
    });

    // Ready event
    wavesurfer.on('ready', function () {
      console.log(`Waveform ready for post ${postId}`);
    });

    // Store wavesurfer instance
    window.wavesurfers = window.wavesurfers || {};
    window.wavesurfers[postId] = wavesurfer;
  });
});
//  <><><><><><><><><><><><><><> END JS OF ADD AUDIO WAVES FOR BETTER UI <><><><><><><><><><><><><><>

//  <><><><><><><><><><><><><><> START JS FOR PIN BOOKMARK JS <><><><><><><><><><><><><><>
$(document).on('click', '.pin-post-btn', function () {
  let btn = $(this);
  let postId = btn.data('post-id');
  let url = btn.data('url');
  let csrf = btn.data('csrf');

  $.ajax({
    url: url,
    type: "POST",
    data: {
      post_id: postId,
      _token: csrf
    },
    success: function (response) {
      if (response.success) {
        // Toggle icon visually
        if (response.is_pinned) {
          btn.html('<i class="bi bi-pin-angle-fill text-primary fs-4"></i>');
          btn.attr('title', 'Unpin Post');
        } else {
          btn.html('<i class="bi bi-pin-angle fs-4"></i>');
          btn.attr('title', 'Pin Post');
        }
        // Success toast with iziToast
        iziToast.success({
          title: response.message,
          position: 'topCenter',
        });
      } else {
        iziToast.error({
          title: response.message,
          position: 'topCenter',
        });
      }
    },
    error: function () {
      iziToast.error({
        title: "Something went wrong.",
        position: 'topCenter',
      });
    }
  });
});
//  <><><><><><><><><><><><><><> END JS OF PIN BOOKMARK JS <><><><><><><><><><><><><><>

//  <><><><><><><><><><><><><><> START JS FOR NEWS LANGUAGE SELECTION MODEL ACTIVE AND INACTIVE TABS <><><><><><><><><><><><><><>
document.addEventListener("DOMContentLoaded", function () {
  const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');

  tabButtons.forEach(btn => {
    btn.addEventListener("shown.bs.tab", function (e) {

      // Reset all tabs
      document.querySelectorAll(".nav-link div").forEach(div => {
        div.classList.remove("bg-primary", "text-white");
        div.classList.add("dark:bg-gray-700", "dark:text-white");
      });

      // Apply active style
      const activeTab = e.target.querySelector("div");
      if (activeTab) {
        activeTab.classList.add("bg-primary", "text-white");
        activeTab.classList.remove("dark:bg-gray-700", "dark:text-white");
      }
    });
  });

  // ⭐ Set default active tab highlight on first load
  const defaultTab = document.querySelector("#news-language-tab div");
  if (defaultTab) {
    defaultTab.classList.add("bg-primary", "text-white");
    defaultTab.classList.remove("dark:bg-gray-700", "dark:text-white");
  }
});
//  <><><><><><><><><><><><><><> END JS OF NEWS LANGUAGE SELECTION MODEL ACTIVE AND INACTIVE TABS <><><><><><><><><><><><><><>

//  <><><><><><><><><><><><><><> START JS FOR CHANNEL POSTS GET THROGH AJAX WITH LOCAL STORAGE <><><><><><><><><><><><><><>
(function () {
  'use strict';

  // Wait for DOM to load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  function init() {
    const channelCache = {};
    const storedCache = localStorage.getItem('channel_posts_cache');

    if (storedCache) {
      try {
        Object.assign(channelCache, JSON.parse(storedCache));
      } catch (e) {
        console.error('Error loading cache:', e);
      }
    }

    const storedChannelId = localStorage.getItem('selected_channel_id');
    const channelsData = window.channelsData || [];
    const defaultImage = window.defaultImage || '';

    document.querySelectorAll('.uc-navbar-switcher-nav .uc-tab-left li').forEach(function (li, index) {
      const link = li.querySelector('a.text-start');

      if (link && channelsData[index]) {
        const channelId = channelsData[index].id;
        const channelName = channelsData[index].name;

        link.setAttribute('data-channel-id', channelId);
        link.setAttribute('data-channel-name', channelName);

        if (channelId != 0) {
          link.addEventListener('click', function (e) {
            e.preventDefault();
            const selectedChannelId = this.getAttribute('data-channel-id');
            localStorage.setItem('selected_channel_id', selectedChannelId);

            if (channelCache[selectedChannelId]) {
              console.log('Loading from cache for channel:', selectedChannelId);
              updateChannelPosts(index, channelCache[selectedChannelId]);
            } else {
              console.log('Fetching from API for channel:', selectedChannelId);
              fetchChannelPosts(selectedChannelId, index);
            }
          });
        }
      }
    });

    if (storedChannelId && storedChannelId != 0) {
      const channelLinks = document.querySelectorAll('.uc-navbar-switcher-nav .uc-tab-left li a.text-start');
      channelLinks.forEach(function (link) {
        if (link.getAttribute('data-channel-id') === storedChannelId) {
          const idx = Array.from(channelLinks).indexOf(link);
          if (channelCache[storedChannelId]) {
            updateChannelPosts(idx, channelCache[storedChannelId]);
          } else {
            setTimeout(() => link.click(), 100);
          }
        }
      });
    }

    function fetchChannelPosts(channelId, channelIndex) {
      const contentDiv = document.querySelectorAll('#uc-navbar-switcher-tending > div')[channelIndex];

      if (contentDiv) {
        const postsContainer = contentDiv.querySelector('.row.child-cols');
        if (postsContainer) {
          postsContainer.innerHTML = '<div class="col-12 text-center p-4"><i class="bi bi-hourglass-split fs-3"></i><p>Loading...</p></div>';
        }
      }

      fetch(`channel-posts/${channelId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            channelCache[channelId] = data.posts;
            localStorage.setItem('channel_posts_cache', JSON.stringify(channelCache));
            updateChannelPosts(channelIndex, data.posts);
          } else {
            console.error('Error:', data.message);
            showError(channelIndex);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showError(channelIndex);
        });
    }

    function updateChannelPosts(channelIndex, posts) {
      const contentDiv = document.querySelectorAll('#uc-navbar-switcher-tending > div')[channelIndex];
      if (!contentDiv) return;

      const postsContainer = contentDiv.querySelector('.row.child-cols');
      if (!postsContainer) return;

      if (posts.length === 0) {
        postsContainer.innerHTML = '<div class="col-12 text-center p-4"><p>No posts available</p></div>';
        return;
      }

      let postsHtml = '';
      posts.forEach(post => {
        const imageUrl = post.image;
        const videoThumb = post.video_thumb ? post.image : post.image;

        const postUrl = `/posts/${post.slug}`;

        let mediaContent = '';
        if (post.type === 'video' || post.type === 'youtube') {
          mediaContent = `
                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                            src="${videoThumb}" data-src="${videoThumb}"
                            alt="${post.title || ''}" title="${post.title || ''}"
                            loading="lazy" fetchpriority="high">
                        <div class="post-category hstack gap-narrow justify-center align-items-center text-white">
                            <a class="text-none" href="${postUrl}" title="${post.title}">
                                <i class="bi bi-play-circle font-size-45"></i>
                            </a>
                        </div>
                    `;
        } else {
          mediaContent = `
                        <img class="media-cover image uc-transition-scale-up uc-transition-opaque"
                            src="${imageUrl}" data-src="${imageUrl}"
                            alt="${post.title || ''}" title="${post.title || ''}"
                            loading="lazy" fetchpriority="high">
                    `;
        }

        postsHtml += `
                    <div>
                        <article class="post type-post panel uc-transition-toggle vstack gap-1">
                            <div class="post-media panel overflow-hidden">
                                <div class="featured-image bg-gray-25 dark:bg-gray-800 ratio ratio-16x9">
                                    <a href="${postUrl}" class="position-cover">
                                        ${mediaContent}
                                    </a>
                                </div>
                            </div>
                            <div class="post-header panel vstack gap-narrow">
                                <h3 class="post-title h6 m-0 text-truncate-2">
                                    <a class="text-none hover:text-primary duration-150"
                                        href="${postUrl}" title="${post.title || ''}">
                                        ${post.title || ''}
                                    </a>
                                </h3>
                                <div class="post-meta panel hstack justify-start gap-1 fs-7 ft-tertiary fw-medium text-gray-900 dark:text-white text-opacity-60 d-none md:d-flex z-1 d-none md:d-block">
                                    <div>
                                        <div class="post-date hstack gap-narrow">
                                            <span title="${post.publish_date || post.pubdate || ''}">
                                                ${post.publish_date || post.pubdate || ''}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="${postUrl}#comment-form"
                                            class="post-comments text-none hstack gap-narrow" title="comments">
                                            <i class="icon-narrow unicon-chat ms-1"></i>
                                            <span>${post.comment || ''}</span>
                                            <i class="bi bi-eye fs-5 ms-1" title="Views"></i>
                                            <span title="Views">${post.view_count || 0}</span>
                                            <i class="bi bi-heart-fill ms-1"></i>
                                            <span>${post.reaction || ''}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                `;
      });

      postsContainer.innerHTML = postsHtml;
    }

    function showError(channelIndex) {
      const contentDiv = document.querySelectorAll('#uc-navbar-switcher-tending > div')[channelIndex];
      if (contentDiv) {
        const postsContainer = contentDiv.querySelector('.row.child-cols');
        if (postsContainer) {
          postsContainer.innerHTML = '<div class="col-12 text-center p-4 text-danger"><p>Error loading posts</p></div>';
        }
      }
    }
  }

  // Global function to clear cache
  window.clearChannelCache = function () {
    localStorage.removeItem('channel_posts_cache');
    localStorage.removeItem('selected_channel_id');
    console.log('Channel cache cleared');
    location.reload();
  };
})();
//  <><><><><><><><><><><><><><> END JS OF CHANNEL POSTS GET THROGH AJAX WITH LOCAL STORAGE <><><><><><><><><><><><><><>

//  <><><><><><><><><><><><><><> START JS FOR ENEWS PAPER AND MAGAZINE FILTER BY CHANNEL AND TOPIC <><><><><><><><><><><><><><>
(function () {
  'use strict';
  // Wait for DOM to be ready
  document.addEventListener('DOMContentLoaded', function () {
    // Get filter elements
    const topicFilter = document.getElementById('topic_filter');
    const channelFilter = document.getElementById('channel_filter');
    const dateFilter = document.getElementById('date-picker');
    const newspapersContainer = document.getElementById('newspapers-container');
    const noDataPanel = document.getElementById('no-data-panel');
    const newspaperCount = document.getElementById('newspaper-count');

    // Get the base URL
    const baseUrl = topicFilter.getAttribute('data-epaper-url');

    if (!baseUrl) {
      console.error('Base URL not found!');
      return;
    }

    // Store the template from the first newspaper item
    let newspaperTemplate = null;
    const firstNewspaperItem = document.querySelector('.newspaper-item');
    if (firstNewspaperItem) {
      newspaperTemplate = firstNewspaperItem.cloneNode(true);
    }

    /**
     * Fetch and display filtered newspapers
     */
    function fetchFilteredNewspapers() {
      // Get current filter values
      const topic = topicFilter.value;
      const channel = channelFilter.value;
      const date = dateFilter.value;

      // Build query parameters
      const params = new URLSearchParams();
      if (topic) params.append('topic', topic);
      if (channel) params.append('channel', channel);
      if (date) params.append('date', date);

      const queryString = params.toString();

      // Show loading state
      newspapersContainer.style.opacity = '0.5';
      newspapersContainer.style.pointerEvents = 'none';

      // Make AJAX request
      const url = `${baseUrl}${queryString ? '?' + queryString : ''}`;

      fetch(url, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          if (!data.success) {
            throw new Error('Response indicated failure');
          }

          updateNewspapersDisplay(data);

          // Update URL without page reload
          const newUrl = `${window.location.pathname}${queryString ? '?' + queryString : ''}`;
          window.history.pushState({}, '', newUrl);
        })
        .catch(error => {
          console.error('Error fetching newspapers:', error);
          showError('Failed to load newspapers. Please try again. Error: ' + error.message);
        })
        .finally(() => {
          // Remove loading state
          newspapersContainer.style.opacity = '1';
          newspapersContainer.style.pointerEvents = 'auto';
        });
    }

    /**
     * Update the newspapers display with fetched data
     */
    function updateNewspapersDisplay(data) {
      console.log('Updating display with', data.newspapers.length, 'newspapers');

      if (!data.newspapers || data.newspapers.length === 0) {
        // Show no data panel
        newspapersContainer.style.display = 'none';
        noDataPanel.style.display = 'block';
        newspaperCount.textContent = 'Showing 0 e-newspapers.';
        return;
      }

      // Hide no data panel
      noDataPanel.style.display = 'none';
      newspapersContainer.style.display = '';

      // Update counter
      const count = data.total || data.newspapers.length;
      newspaperCount.textContent = `Showing ${count} e-newspapers.`;

      // Clear existing newspapers
      newspapersContainer.innerHTML = '';

      // Render each newspaper using template
      data.newspapers.forEach(newspaper => {
        const newspaperCard = createNewspaperCardFromTemplate(newspaper);
        if (newspaperCard) {
          newspapersContainer.appendChild(newspaperCard);
        }
      });

      console.log('Display updated successfully');
    }

    /**
     * Create newspaper card from template
     */
    function createNewspaperCardFromTemplate(newspaper) {
      if (!newspaperTemplate) {
        console.error('No template available');
        return null;
      }

      const card = newspaperTemplate.cloneNode(true);

      // Update data-date attribute
      card.setAttribute('data-date', newspaper.date || '');

      // Update thumbnail image
      const thumbnailImg = card.querySelector('.featured-image img');
      if (thumbnailImg && newspaper.thumbnail_url) {
        thumbnailImg.src = newspaper.thumbnail_url;
        thumbnailImg.setAttribute('data-src', newspaper.thumbnail_url);
      }

      // Update PDF links (both the cover link and title link)
      const pdfLinks = card.querySelectorAll('.read-more-link');
      pdfLinks.forEach(link => {
        if (newspaper.pdf_url) {
          link.href = newspaper.pdf_url;
        }
        // Remove daily limit attributes
        link.removeAttribute('data-daily-limit');
        link.removeAttribute('data-subscription-limit');
      });

      // Update topic badge
      const topicBadge = card.querySelector('.post-category');
      if (topicBadge) {
        if (newspaper.show_topic && newspaper.topic_name) {
          topicBadge.style.display = '';
          const topicLink = topicBadge.querySelector('a');
          if (topicLink) {
            topicLink.href = newspaper.topic_url || '#';
            topicLink.textContent = newspaper.topic_name;
            topicLink.title = newspaper.topic_name;
          }
        } else {
          topicBadge.style.display = 'none';
        }
      }

      // Update channel logo and name
      const channelLogo = card.querySelector('.meta img');
      if (channelLogo && newspaper.channel_logo) {
        channelLogo.src = newspaper.channel_logo;
        channelLogo.alt = 'Channel Logo';
      }

      const channelLinks = card.querySelectorAll('.meta a');
      channelLinks.forEach(link => {
        if (newspaper.channel_url) {
          link.href = newspaper.channel_url;
          link.title = newspaper.channel_name || '';
        }
        // Update channel name in text content
        if (link.classList.contains('fw-bold')) {
          link.textContent = newspaper.channel_name || '';
        }
      });

      // Update title
      const titleLink = card.querySelector('.post-title a');
      if (titleLink && newspaper.title) {
        titleLink.textContent = newspaper.title;
      }

      // Update date
      const dateSpan = card.querySelector('.post-date span');
      if (dateSpan && newspaper.date) {
        dateSpan.textContent = newspaper.date;
      }

      return card;
    }

    /**
     * Show error message
     */
    function showError(message) {
      console.error(message);
      alert(message);
    }

    /**
     * Clear all filters
     */
    function clearFilters() {
      topicFilter.value = '';
      channelFilter.value = '';
      dateFilter.value = '';
      fetchFilteredNewspapers();
    }

    // Event listeners for filters
    topicFilter.addEventListener('change', function () {
      fetchFilteredNewspapers();
    });

    channelFilter.addEventListener('change', function () {
      fetchFilteredNewspapers();
    });

    dateFilter.addEventListener('change', function () {
      fetchFilteredNewspapers();
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function () {
      location.reload();
    });

  });
})();
//  <><><><><><><><><><><><><><> END JS OF ENEWS PAPER AND MAGAZINE FILTER BY CHANNEL AND TOPIC <><><><><><><><><><><><><><>

document.addEventListener('DOMContentLoaded', function () {
  function updateTime() {
    const date = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const time = date.toLocaleTimeString();
    const timeEl = document.getElementById('time');
    const day = date.toLocaleDateString(undefined, options);

    if (timeEl) {
      timeEl.innerHTML = `${day}`;
    }
  }

  setInterval(updateTime, 1000);
  updateTime();
});


document.addEventListener("DOMContentLoaded", function () {
  const dropdown = document.getElementById("newsletter-dropdown");
  const emailInput = document.getElementById("subscriber_email");
  const errorBox = document.getElementById("subscriber-error-top");

  if (dropdown) {
    dropdown.addEventListener("beforeshow", function () {
      // Clear email field
      if (emailInput) {
        emailInput.value = "";
      }

      // Hide error message if visible
      if (errorBox) {
        errorBox.classList.add("d-none");
        errorBox.innerHTML = "";
      }
    });
  }
});