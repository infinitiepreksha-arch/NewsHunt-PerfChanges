function queryStringToObject() {
  const params = new URLSearchParams(window.location.search);
  const result = {};
  for (const [key, value] of params.entries()) {
    if (result[key]) {
      if (Array.isArray(result[key])) {
        result[key].push(value);
      } else {
        result[key] = [result[key], value];
      }
    } else {
      result[key] = value;
    }
  }
  return result;
}
$(document).ready(function () {
  const popupStatus = document.getElementById("popup-status")?.value;

  // Only run if popup is ON
  if (popupStatus != "1") {
    return;
  }

  // Define openInApp globally (only once)
  function openInApp(pathName) {
    const androidLink = document.getElementById("android-link")?.textContent.trim();
    const iosLink = document.getElementById("ios-link")?.textContent.trim();

    const androidAppStoreLink = androidLink || "https://play.google.com/store/apps/details?id=eShop.multivendor.customer";
    const iosAppStoreLink = iosLink || "https://apps.apple.com/fr/app/microsoft-word/id462054704?l=en-GB&mt=12";
    const userAgent = navigator.userAgent || navigator.vendor || window.opera;
    const isAndroid = /android/i.test(userAgent);
    const isIOS = /iPad|iPhone|iPod/.test(userAgent) && !window.MSStream;

    if (isAndroid) {
      const scheme = document.getElementById("android-scheme")?.textContent.trim();
      if (confirm("Do you want to open in app?")) {
        window.location.href = `${scheme}://app${pathName}`;
      }

      setTimeout(function () {
        if (document.hidden || document.webkitHidden) {
          // App opened successfully
        } else if (
          confirm("News app is not installed. Would you like to download it from the Play Store?")
        ) {
          window.location.href = androidAppStoreLink;
        }
      }, 1000);

    } else if (isIOS) {
      const scheme = document.getElementById("ios-scheme")?.textContent.trim();
      if (confirm("Do you want to open in app?")) {
        window.location.href = `${scheme}://app${pathName}`;
      }

      setTimeout(function () {
        if (document.hidden || document.webkitHidden) {
          // App opened successfully
        } else if (
          confirm("News app is not installed. Would you like to download it from the App Store?")
        ) {
          window.location.href = iosAppStoreLink;
        }
      }, 1000);
    }
  }

  // Handle mobile bottom sheet popup
  function isMobileOrTablet() {
    return window.matchMedia("(max-width: 1024px)").matches;
  }

  if (isMobileOrTablet() && !sessionStorage.getItem("bottomSheetShown")) {
    const pathName = window.location.pathname;
    const shareDiv = document.getElementsByClassName("share-div")[0];

    if (!shareDiv) return;

    shareDiv.innerHTML =
      `<div class="bottom-sheet p-4" id="bottomSheet">
          <h5>Open in App</h5>
          <p>Get a better experience by using our mobile app!</p>
          <button class="btn btn-outline-secondary w-100" onclick="hideBottomSheet()">Close</button>
        </div>` + shareDiv.innerHTML;

    // Helper functions
    window.toggleBottomSheet = function (show = true) {
      const bottomSheet = document.getElementById("bottomSheet");
      if (show) bottomSheet.classList.add("show");
      else bottomSheet.classList.remove("show");
    };

    window.hideBottomSheet = function () {
      toggleBottomSheet(false);
      sessionStorage.setItem("bottomSheetShown", "true");
    };

    openInApp(pathName);
    toggleBottomSheet(true);
  }
});
