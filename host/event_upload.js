// Get all necessary elements from the DOM
const menuBtn = document.getElementById("menuBtn");
const sidenav = document.getElementById("mySidenav");
const overlay = document.getElementById("overlay");
const form = document.getElementById("eventForm");
const cancelBtn = document.getElementById("cancelBtn");
const bannerBox = document.getElementById("bannerBox");
const bannerInput = document.getElementById("banner");
const bannerPreview = document.getElementById("bannerPreview");
const bannerPlaceholder = document.getElementById("bannerPlaceholder");

// Function to open the sidebar
function openNav() {
  sidenav.classList.add("open");
  overlay.classList.add("active");
}

// Function to close the sidebar
function closeNav() {
  sidenav.classList.remove("open");
  overlay.classList.remove("active");
}

// Event listeners for sidebar and overlay
menuBtn.addEventListener("click", openNav);
overlay.addEventListener("click", closeNav);

// Drag-and-drop for banner box
bannerBox.addEventListener("dragover", (e) => {
  e.preventDefault();
  bannerBox.classList.add("drag-over");
});

bannerBox.addEventListener("dragleave", () => {
  bannerBox.classList.remove("drag-over");
});

bannerBox.addEventListener("drop", (e) => {
  e.preventDefault();
  bannerBox.classList.remove("drag-over");
  const file = e.dataTransfer.files[0];
  if (file && file.type.startsWith("image/")) {
    bannerInput.files = e.dataTransfer.files;
    handleFile(file);
  }
});

// Click to trigger file input
bannerBox.addEventListener("click", () => {
  bannerInput.click();
});

// Event listener for file input change
bannerInput.addEventListener("change", (e) => {
  const file = e.target.files[0];
  handleFile(file);
});

// Function to handle file preview
function handleFile(file) {
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      bannerPreview.src = e.target.result;
      bannerPreview.style.display = "block";
      bannerPlaceholder.style.display = "none";
    };
    reader.readAsDataURL(file);
  } else {
    bannerPreview.src = "";
    bannerPreview.style.display = "none";
    bannerPlaceholder.style.display = "flex";
  }
}

// Form submission and reset handlers
form.addEventListener("submit", (e) => {
  e.preventDefault();
  alert("Event uploaded successfully!");
  form.reset();
  handleFile(null); // Reset file preview
});

cancelBtn.addEventListener("click", () => {
  if (confirm("Are you sure you want to cancel?")) {
    form.reset();
    handleFile(null); // Reset file preview
  }
});

// Auto-expanding textarea
const description = document.getElementById("description");
if (description) {
  const autoResize = (el) => {
    el.style.height = "auto";
    el.style.height = el.scrollHeight + "px";
  };
  description.addEventListener("input", () => autoResize(description));
  // Call it once on load in case there's pre-filled text
  autoResize(description);
}


document.querySelectorAll(".input-with-icon .icon").forEach((icon) => {
  icon.addEventListener("click", () => {
    icon.previousElementSibling.showPicker(); // native date/time picker
  });
});