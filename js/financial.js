// ===== Financial Dashboard Charts =====

// ---------- Cashflow Chart ----------
const ctxCashflow = document.getElementById("cashflowChart").getContext("2d");
new Chart(ctxCashflow, {
  type: "bar",
  data: {
    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct"],
    datasets: [
      {
        label: "Income",
        data: [6500, 7200, 6800, 7500, 6815, 7900, 8200, 8600, 9100, 9400],
        backgroundColor: "#c084fc", // purple
        borderRadius: 6
      },
      {
        label: "Expense",
        data: [4200, 5100, 4800, 5300, 5120, 5500, 5900, 6100, 6400, 6700],
        backgroundColor: "#f9a8d4", // pink
        borderRadius: 6
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: "top",
        labels: { usePointStyle: true, padding: 20 }
      }
    },
    scales: {
      x: { stacked: false, grid: { display: false } },
      y: { beginAtZero: true, grid: { color: "#f3f4f6" } }
    }
  }
});

// ---------- Sales Revenue Chart ----------
const ctxRevenue = document.getElementById("revenueChart").getContext("2d");
new Chart(ctxRevenue, {
  type: "doughnut",
  data: {
    labels: ["Music", "Fashion", "Sports", "Art & Design", "Health & Wellness", "Technology"],
    datasets: [
      {
        data: [45000, 30000, 24000, 21000, 15000, 15000],
        backgroundColor: [
          "#c084fc", // purple
          "#f472b6", // pink
          "#60a5fa", // blue
          "#facc15", // yellow
          "#34d399", // green
          "#f87171"  // red
        ],
        borderWidth: 2,
        borderColor: "#fff"
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: "70%",
    plugins: {
      legend: {
        position: "right",
        labels: { usePointStyle: true, padding: 15 }
      }
    }
  }
});

// ---------- Expense Breakdown Chart ----------
const ctxExpense = document.getElementById("expenseChart").getContext("2d");
new Chart(ctxExpense, {
  type: "doughnut",
  data: {
    labels: ["Marketing", "Venue", "Staffing", "Equipment", "Miscellaneous", "Utilities"],
    datasets: [
      {
        data: [13846, 12115, 8653, 5192, 3461, 1730],
        backgroundColor: [
          "#e879f9", // pink
          "#a78bfa", // violet
          "#38bdf8", // cyan
          "#fbbf24", // amber
          "#34d399", // green
          "#f87171"  // red
        ],
        borderWidth: 2,
        borderColor: "#fff"
      }
    ]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: "70%",
    plugins: {
      legend: {
        position: "right",
        labels: { usePointStyle: true, padding: 15 }
      }
    }
  }
});
