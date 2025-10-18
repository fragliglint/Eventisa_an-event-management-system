// Invoice List Interactivity
document.addEventListener("DOMContentLoaded", () => {
  const invoices = document.querySelectorAll(".invoice");
  const searchInput = document.querySelector(".invoice-list input");

  // Highlight active invoice
  invoices.forEach(inv => {
    inv.addEventListener("click", () => {
      invoices.forEach(i => i.classList.remove("active"));
      inv.classList.add("active");
      // TODO: Replace details dynamically when backend is connected
    });
  });

  // Search filter
  searchInput.addEventListener("input", (e) => {
    const term = e.target.value.toLowerCase();
    invoices.forEach(inv => {
      inv.style.display = inv.textContent.toLowerCase().includes(term)
        ? "block"
        : "none";
    });
  });
});
