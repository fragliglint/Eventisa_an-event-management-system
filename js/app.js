(() => {
  // ---------- DASHBOARD DATA ----------
  const donutData = {
    labels: ["Sold", "Booked", "Available"],
    datasets: [{
      data: [1251, 834, 695],
      backgroundColor: ["#b08bff", "#ff8bd1", "#e6e8f8"],
      borderWidth: 0
    }]
  };

  const barData = {
    labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug"],
    datasets: [{
      label: "Revenue",
      data: [12000,18000,15000,56000,32000,40000,28000,36000],
      backgroundColor: ctx => ctx.dataIndex === 3
        ? "rgba(231,157,246,0.95)"
        : "rgba(176,139,255,0.8)",
      borderRadius: 6
    }]
  };

  // ---------- CREATE DONUT (static) ----------
  function createDonut() {
    const el = document.getElementById("donut");
    if (!el) return;

    new Chart(el.getContext("2d"), {
      type: "doughnut",
      data: donutData,
      options: {
        cutout: "68%",
        animation: false,
        animations: { numbers: false, colors: false, radius: false },
        transitions: {
          active: { animation: false },
          resize: { animation: false },
          show: { animation: false },
          hide: { animation: false }
        },
        hover: { mode: null },
        interaction: { mode: null },
        plugins: {
          legend: { display: false },
          tooltip: { enabled: false }
        }
      }
    });
  }

  // ---------- CREATE BAR ----------
  function createBar() {
    const el = document.getElementById("bar");
    if (!el) return;
    new Chart(el.getContext("2d"), {
      type: "bar",
      data: barData,
      options: {
        animation: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false }, ticks: { color: "#9aa7b2" } },
          y: { grid: { color: "rgba(0,0,0,0.04)" }, ticks: { color: "#9aa7b2" }, beginAtZero: true }
        }
      }
    });
  }

  // ---------- MINI CALENDAR ----------
  function renderMiniCalendar(year, month) {
    const cal = document.getElementById("miniCalendar");
    const title = document.getElementById("calendarTitle");
    if (!cal || !title) return;

    const monthNames = ["January","February","March","April","May","June",
      "July","August","September","October","November","December"];

    title.textContent = `${monthNames[month]} ${year}`;

    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const today = new Date();
    const isThisMonth = today.getFullYear() === year && today.getMonth() === month;

    cal.innerHTML = "";
    for (let i = 1; i <= daysInMonth; i++) {
      const d = document.createElement("div");
      d.className = "day";
      if (isThisMonth && i === today.getDate()) d.classList.add("today");
      d.textContent = i;
      cal.appendChild(d);
    }
  }

  // ---------- PAGE SWITCHING ----------
  function setupMenu() {
    document.querySelectorAll(".menu-item").forEach(it => {
      it.addEventListener("click", (e) => {
        // Let the href handle navigation for PHP files
        document.querySelectorAll(".menu-item").forEach(x => x.classList.remove("active"));
        it.classList.add("active");
      });
    });
  }

  // ---------- INIT ----------
  window.addEventListener("load", () => {
    createDonut();
    createBar();
    renderMiniCalendar(2025, 8); // September 2025
    setupMenu();
  });
})();