function fallback(video) {
  var img = video.querySelector("img");
  if (img) video.parentNode.replaceChild(img, video);
}

document.addEventListener("DOMContentLoaded", function () {
  const postId = document.getElementById("post_id")
    ? document.getElementById("post_id").value
    : "";

  if (postId) {
    fetch(`/posts/${postId}/reactors`)
      .then((response) => response.json())
      .then((data) => {
        renderReactions(data);
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
      });
  }
});

function reactToPost(postId, type, uuid, getTopReactions) {
  // Fix: Check if getTopReactions is a valid JSON string before parsing
  let emojiReactores = [];
  try {
    if (typeof getTopReactions === "string") {
      emojiReactores = JSON.parse(getTopReactions);
      // Make sure emojiReactores is an array after parsing
      if (!Array.isArray(emojiReactores)) {
        emojiReactores = [];
      }
    }
  } catch (e) {
    console.error("Error parsing getTopReactions:", e);
    emojiReactores = [];
  }

  const isMatch = emojiReactores.some((reaction) => reaction.name === type);

  // Get elements safely
  const emojiLoop_1 = document.getElementById("emoji_loop_1");
  const matchReactionIcons = document.getElementById("match_reaction_icons");
  const reaction_icons = document.getElementById("reaction_icons");
  const emojiBox = document.getElementById("emoji-box");
  const emojiCount = document.getElementById("emoji_count");
  const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

  if (!reaction_icons || !emojiCount) {
    console.error("Error: Required elements are missing in the DOM.");
    return;
  }

  emojiBox?.classList.add("d-none");
  fetch(`/posts/${postId}/react`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": csrfToken,
    },
    body: JSON.stringify({ type: type }),
  })
    .then((response) => response.json())
    .then((data) => {
      emojiBox?.classList.add("d-none");

      if (data.remove_user_review == true) {
        emojiLoop_1?.classList.add("d-none");
      }

      if (data.isRemove === true || data.isNew === 1) {
        // Remove existing reaction emojis safe

        reaction_icons
          .querySelectorAll(".reaction-uuid")
          ?.forEach((el) => el.remove());

        if (data.count > 0) {
          reaction_icons.classList.remove("bi-hand-thumbs-up-fill");
          reaction_icons.classList.add("text-primary");

          const newReactionIcon = document.createElement("span");
          newReactionIcon.textContent = uuid;
          newReactionIcon.classList.add("reaction-uuid");
          reaction_icons.appendChild(newReactionIcon);
        }

        if (!isMatch) {
          emojiLoop_1?.classList.add("d-none");
          matchReactionIcons?.classList.remove("d-none");
          matchReactionIcons.innerHTML = `<span>${uuid}</span>`;
        } else {
          emojiLoop_1?.classList.remove("d-none");
          matchReactionIcons?.classList.add("d-none");
        }

        emojiCount.textContent =
          data.count === 1 ? "You" : `You + ${data.count - 1}`;
      } else {
        reaction_icons.classList.add("bi-hand-thumbs-up-fill");
        reaction_icons.classList.remove("text-primary");

        reaction_icons
          .querySelectorAll(".reaction-uuid")
          ?.forEach((el) => el.remove());
        matchReactionIcons?.classList.add("d-none");
        emojiCount.textContent = data.count === 0 ? "" : data.count;
      }

      renderReactions(data.reactors?.original || {});
    })
    .catch((error) => {
      console.error("Error:", error);
      iziToast.error({
        title: "An error occurred",
        position: "topCenter",
      });
    });
}

// Render the tabs and user content dynamically
function renderReactions(data) {
  const emojiTabs = document.getElementById("emojiTabs");
  const emojiContent = document.getElementById("emojiContent");
  emojiTabs.innerHTML = ""; // Clear any existing tabs
  emojiContent.innerHTML = ""; // Clear any existing content

  let firstTabActive = true;

  // Use for...in to iterate over the object
  for (const key in data) {
    if (data.hasOwnProperty(key)) {
      const reaction = data[key];

      // Create the tab
      const tabItem = document.createElement("li");
      tabItem.classList.add("nav-item");
      tabItem.classList.add("d-flex");
      tabItem.classList.add("justify-between");
      const tabLink = document.createElement("a");
      tabLink.href = `#${reaction.name}`;
      tabLink.classList.add("nav-link");
      tabLink.classList.add("dark:bg-gray-100");
      tabLink.classList.add("dark:bg-opacity-5");
      tabLink.classList.add("p-1");
      tabLink.setAttribute("data-bs-toggle", "tab");
      tabLink.setAttribute("role", "tab");
      if (firstTabActive) tabLink.classList.add("active");
      tabLink.innerHTML = `<h6 class="mb-0 dark:text-white">${reaction.uuid} ${reaction.count}</h6>`;
      tabItem.appendChild(tabLink);
      emojiTabs.appendChild(tabItem);

      const tabPane = document.createElement("div");
      tabPane.classList.add("tab-pane");
      if (firstTabActive) tabPane.classList.add("active", "show");
      tabPane.id = reaction.name;

      reaction.users.forEach((user) => {
        const defaultProfileUrl = `${window.location.origin}/front_end/classic/images/default/profile-avatar.jpg`;
        const profileImage = user.profile ?? defaultProfileUrl;
        const userLi = document.createElement("li");
        userLi.classList.add(
          "d-flex",
          "align-items-center",
          "position-relative"
        );
        const avatarContainer = document.createElement("div");
        avatarContainer.classList.add("avatar-container", "position-relative");
        const avatarImg = document.createElement("img");
        avatarImg.classList.add(
          "avatar",
          "w-32px",
          "h-32px",
          "rounded-circle",
          "object-fit-cover",
          "pointer-cursor"
        );
        avatarImg.src = profileImage;
        avatarImg.alt = "User Avatar";

        const reactionEmoji = document.createElement("div");
        reactionEmoji.classList.add(
          "reaction-emoji",
          "position-absolute",
          "bottom-0",
          "text-primary",
          "end-0"
        );
        reactionEmoji.innerHTML = `<span class="reaction-icon" style="font-size: 10px;">${reaction.uuid}</span>`;

        avatarContainer.appendChild(avatarImg);
        avatarContainer.appendChild(reactionEmoji);
        userLi.appendChild(avatarContainer);

        // Add user name
        const userName = document.createElement("h6");
        userName.classList.add("ms-2", "mt-1", "mb-1", "dark:text-white");
        userName.innerText = user.name ?? "Unknown";
        userLi.appendChild(userName);

        // Append to tab content
        tabPane.appendChild(userLi);
      });

      emojiContent.appendChild(tabPane);

      // Mark the first tab as inactive for subsequent loops
      firstTabActive = false;
    }
  }

  // Initialize Bootstrap tabs (required to make tabs functional)
  const tabElements = document.querySelectorAll('[data-bs-toggle="tab"]');
  tabElements.forEach((tabElement) => {
    new bootstrap.Tab(tabElement);
  });
}

/* Reaction JS starts from here. */
document.addEventListener("DOMContentLoaded", function () {
  const reactionOpen = document.getElementById("reaction_open");
  const emojiBox = document.getElementById("emoji-box");
  const emojiReactores = document.getElementById("open_reactores");
  const emoji_collaction = document.getElementById("emoji_collaction");

  // Handle clicking outside
  document.addEventListener("click", function (event) {
    const isClickedOutside =
      (!emojiBox || !emojiBox.contains(event.target)) &&
      (!emoji_collaction || !emoji_collaction.contains(event.target)) &&
      (!reactionOpen || !reactionOpen.contains(event.target)) &&
      (!emojiReactores || !emojiReactores.contains(event.target));

    if (isClickedOutside) {
      emojiBox?.classList.add("d-none");
      emoji_collaction?.classList.add("d-none");
    }
  });
  if (reactionOpen)
    reactionOpen.addEventListener("click", function (event) {
      event.preventDefault();
      event.stopPropagation();
      if (emojiBox.classList.contains("d-none")) {
        emojiBox.classList.remove("d-none");
      } else {
        emojiBox.classList.add("d-none");
      }
      if (!emoji_collaction.classList.contains("d-none")) {
        emoji_collaction.classList.add("d-none");
      }
    });

  if (emojiReactores)
    emojiReactores.addEventListener("click", function (event) {
      event.preventDefault();
      event.stopPropagation();
      if (emoji_collaction.classList.contains("d-none")) {
        emoji_collaction.classList.remove("d-none");
      } else {
        emoji_collaction.classList.add("d-none");
      }
      if (!emojiBox.classList.contains("d-none")) {
        emojiBox.classList.add("d-none");
      }
    });
});
