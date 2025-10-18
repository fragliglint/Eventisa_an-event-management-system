// Sidebar functionality
const menuBtn = document.getElementById("menuBtn");
const sidenav = document.getElementById("mySidenav");
const overlay = document.getElementById("overlay");

function openNav() {
  sidenav.classList.add("open");
  overlay.classList.add("active");
}
function closeNav() {
  sidenav.classList.remove("open");
  overlay.classList.remove("active");
}
menuBtn.addEventListener("click", openNav);
overlay.addEventListener("click", closeNav);
document.addEventListener("keydown", (e) => {
  if (e.key === "Escape" && sidenav.classList.contains("open")) closeNav();
});

// --- PIE CHART LOGIC ---
function updateSoldUnsoldPieChart(sold, unsold) {
  const pieChart = document.getElementById("soldUnsoldPieChart");
  const percentageDisplay = document.getElementById("soldPercentageDisplay");
  const totalCount = document.getElementById("totalTicketsCount");
  const soldColor = getComputedStyle(document.documentElement)
    .getPropertyValue("--sold-color")
    .trim();
  const unsoldColor = getComputedStyle(document.documentElement)
    .getPropertyValue("--unsold-color")
    .trim();

  const total = sold + unsold;
  if (total === 0) {
    percentageDisplay.textContent = "0% Sold";
    totalCount.textContent = "0";
    pieChart.style.background = "#e0e0e0";
    return;
  }
  const soldPercentage = (sold / total) * 100;
  percentageDisplay.textContent = `${Math.round(soldPercentage)}% Sold`;
  totalCount.textContent = total.toLocaleString();
  pieChart.style.background = `conic-gradient(${soldColor} 0% ${soldPercentage}%, ${unsoldColor} ${soldPercentage}% 100%)`;
}

function setupPieChartHover(sold, unsold) {
  const wrap = document.getElementById("pieChartWrap");
  const tooltip = document.getElementById("pieChartTooltip");
  const total = sold + unsold;
  const soldAngle = (sold / total) * 360;

  wrap.addEventListener("mousemove", (e) => {
    const rect = wrap.getBoundingClientRect();
    const x = e.clientX - (rect.left + rect.width / 2);
    const y = e.clientY - (rect.top + rect.height / 2);
    const distance = Math.sqrt(x * x + y * y);
    const radius = rect.width / 2;
    if (distance > radius * 0.6 && distance < radius) {
      let angle = (Math.atan2(y, x) * (180 / Math.PI) + 90 + 360) % 360;
      tooltip.style.display = "block";
      tooltip.style.left = `${e.clientX - rect.left + 10}px`;
      tooltip.style.top = `${e.clientY - rect.top - 20}px`;
      tooltip.innerHTML =
        angle <= soldAngle
          ? `Sold: <strong>${sold.toLocaleString()}</strong>`
          : `Unsold: <strong>${unsold.toLocaleString()}</strong>`;
    } else {
      tooltip.style.display = "none";
    }
  });
  wrap.addEventListener("mouseleave", () => (tooltip.style.display = "none"));
}

// --- GENERATE CONFIG DYNAMICALLY ---
function generateChartConfig(data) {
  const maxValue = Math.max(...data.map((d) => d.amount));
  const step = 500; // increment
  const maxSale = Math.ceil(maxValue / step) * step;

  let yAxisLabels = [];
  for (let i = 0; i <= maxSale; i += step) {
    yAxisLabels.push("৳" + i.toLocaleString());
  }

  return { maxSale, yAxisLabels };
}

// --- DYNAMIC COMBINED BAR + LINE CHART ---
function updateCombinedChart(data, config) {
  const { maxSale, yAxisLabels } = config;
  const chartGrid = document.getElementById("chartGrid");
  const xAxisContainer = document.getElementById("xAxisLabels");
  const yAxisContainer = document.getElementById("yAxisLabels");
  const svg = document.getElementById("lineGraphSvg");

  // Clear old content
  chartGrid.querySelectorAll(".bar").forEach((bar) => bar.remove());
  svg.innerHTML = "";
  xAxisContainer.innerHTML = "";
  yAxisContainer.innerHTML = "";

  // Y-axis labels
  yAxisLabels
    .slice()
    .reverse()
    .forEach((label) => {
      yAxisContainer.innerHTML += `<span>${label}</span>`;
    });

  const numLines = yAxisLabels.length - 1;
  chartGrid.style.backgroundSize = `100% ${100 / numLines}%`;

  // Correct chart height
  const chartHeight = chartGrid.offsetHeight;
  const chartWidth = chartGrid.clientWidth;
  const numDataPoints = data.length;
  const barWidth = chartWidth / (numDataPoints * 1.5); // evenly spaced bars

  let linePoints = [];
  let pathData = "M";

  data.forEach((item, index) => {
    const barHeight = Math.max(0, (item.amount / maxSale) * chartHeight);
    const percentAcross = (index + 0.5) / numDataPoints;
    const barCenter = percentAcross * chartWidth;
    const barLeft = barCenter - barWidth / 2;

    // Bar
    const bar = document.createElement("div");
    bar.className = `bar ${index % 2 !== 0 ? "alternate" : ""}`;
    bar.style.height = `${barHeight}px`;
    bar.style.width = `${barWidth}px`;
    bar.style.left = `${barLeft}px`;
    bar.style.bottom = "0"; // ensure bar aligns to bottom
    chartGrid.appendChild(bar);

    // Line points
    const pointY = chartHeight - barHeight;
    linePoints.push({ x: barCenter, y: pointY });
    pathData += `${barCenter},${pointY} `;

    // X-axis labels
    const dateLabel = document.createElement("span");
    const options = { month: "short", day: "numeric" };
    dateLabel.textContent = item.date.toLocaleDateString("en-US", options);
    xAxisContainer.appendChild(dateLabel);
  });

  if (linePoints.length > 1) {
    const linePath = document.createElementNS(
      "http://www.w3.org/2000/svg",
      "path"
    );
    linePath.setAttribute("d", pathData);
    linePath.classList.add("chart-line");
    svg.appendChild(linePath);

    linePoints.forEach((point) => {
      const circle = document.createElementNS(
        "http://www.w3.org/2000/svg",
        "circle"
      );
      circle.setAttribute("cx", point.x);
      circle.setAttribute("cy", point.y);
      circle.classList.add("line-point");
      svg.appendChild(circle);
    });
  }
}

// --- ON PAGE LOAD ---
document.addEventListener("DOMContentLoaded", () => {
  const soldTickets = 750,
    unsoldTickets = 250;
  updateSoldUnsoldPieChart(soldTickets, unsoldTickets);
  setupPieChartHover(soldTickets, unsoldTickets);

  // Example data
  let dailySalesData = [
    { date: new Date("2025-09-20"), amount: 1750 },
    { date: new Date("2025-09-21"), amount: 2450 },
    { date: new Date("2025-09-22"), amount: 3100 },
    { date: new Date("2025-09-23"), amount: 1800 },
    { date: new Date("2025-09-24"), amount: 2700 },
  ];

  let chartConfig = generateChartConfig(dailySalesData);
  updateCombinedChart(dailySalesData, chartConfig);
});
