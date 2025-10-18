// ---------- BOOKINGS PAGE DATA ----------
document.addEventListener("DOMContentLoaded", () => {
  // --- Donut Chart ---
  const ctxDonut = document.getElementById("donutBookings");
  if (ctxDonut) {
    new Chart(ctxDonut, {
      type: "doughnut",
      data: {
        labels: ["Music", "Sport", "Fashion", "Art & Design"],
        datasets: [{
          data: [14172, 12476, 9806, 7661],
          backgroundColor: ["#db2777", "#2563eb", "#f59e0b", "#10b981"],
          borderWidth: 0
        }]
      },
      options: {
        cutout: "70%",
        animation: {
          animateRotate: true,
          animateScale: true,
          duration: 1200,
          easing: "easeOutQuart"
        },
        plugins: { legend: { display: false } },
        interaction: { mode: null } // 🚫 no hover effect
      }
    });
  }

  // --- Line Chart (Bookings Overview) ---
  const ctxLine = document.getElementById("lineBookings");
  if (ctxLine) {
    new Chart(ctxLine, {
      type: "line",
      data: {
        labels: ["Week 1", "Week 2", "Week 3", "Week 4"],
        datasets: [{
          label: "Bookings",
          data: [1200, 2100, 1800, 2500],
          borderColor: "#9333ea",
          backgroundColor: "rgba(147,51,234,0.2)",
          fill: true,
          tension: 0.4,
          pointRadius: 4,
          pointBackgroundColor: "#9333ea"
        }]
      },
      options: {
        responsive: true,
        animation: false,
        plugins: { legend: { display: false } },
        interaction: { mode: null },
        scales: {
          y: { beginAtZero: true, grid: { color: "#f3f4f6" } },
          x: { grid: { display: false } }
        }
      }
    });
  }

  // --- Table Data ---
  const bookingData = [
    { id: "INV1001", date: "2029-02-15", name: "Jackson Moore", event: "Symphony Under the Stars", Email: "dvndncs@gmail.com", Phone: "01928429943" , qty: 2, amount: "$100", status: "Confirmed" },
    { id: "INV1002", date: "2029-02-16", name: "Alicia Smithson", event: "Runway Revolution 2024", Email: "dvndncs@gmail.com", Phone: "01928429943" ,qty: 1, amount: "$120", status: "Pending"},
    { id: "INV1003", date: "2029-02-17", name: "Natalie Johnson", event: "Global Wellness Summit", Email: "Wellness@gmail.com", Phone: "01928429943" ,qty: 3, amount: "$240", status: "Confirmed" },
    { id: "INV1004", date: "2029-02-18", name: "Patrick Cooper", event: "Champions League Screening Night", Email: "Sport@gmail.com", Phone: "01928429943" , qty: 4, amount: "$120", status: "Pending" }
  ];

  const tableBody = document.getElementById("bookingPageRows");
  const tabs = document.querySelectorAll(".tab-btn");

  function renderTable(filter = "all") {
    tableBody.innerHTML = "";
    bookingData.forEach(b => {
      if (filter === "all" || b.status.toLowerCase() === filter) {
        const row = `
          <tr>
            <td>${b.id}</td>
            <td>${b.date}</td>
            <td>${b.name}</td>
            <td>${b.event}</td>
            <td>${b.Email}</td>
            <td>${b.Phone}</td>
            <td>${b.qty}</td>
            <td>${b.amount}</td>
            <td><span class="badge ${b.status.toLowerCase()}">${b.status}</span></td>
          </tr>`;
        tableBody.insertAdjacentHTML("beforeend", row);
      }
    });
  }

  // Initial table render
  renderTable();

  // Tabs filter
  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      tabs.forEach(t => t.classList.remove("active"));
      tab.classList.add("active");
      renderTable(tab.dataset.filter);
    });
  });

  // --- Progress Bars Animation ---
  document.querySelectorAll('.category-progress').forEach(function(bar) {
    const width = bar.getAttribute('data-width');
    setTimeout(() => { bar.style.width = width; }, 200);
  });
});
