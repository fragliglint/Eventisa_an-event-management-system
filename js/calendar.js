// Inline the JavaScript to avoid path issues
    document.addEventListener("DOMContentLoaded", () => {
      const calendarDates = document.getElementById("calendarDates");
      const calendarTitle = document.getElementById("monthYear");
      const detailTitle = document.getElementById("detailTitle");
      const detailDate = document.getElementById("detailDate");
      const prevBtn = document.getElementById("prevMonth");
      const nextBtn = document.getElementById("nextMonth");
      const scheduleDetails = document.getElementById("scheduleDetails");

      let year = 2029;
      let month = 4; // May

      // Sample event data
      const events = {
        "2029-5-1": [
          { type: "meeting", title: "Team Meeting", time: "10:00 AM", location: "Conference Room A", contact: "John Smith", role: "Project Manager", phone: "+1-800-123-4567", email: "john@example.com" }
        ],
        "2029-5-5": [
          { type: "event", title: "Product Launch", time: "2:00 PM", location: "Grand Hall", contact: "Sarah Johnson", role: "Event Coordinator", phone: "+1-800-765-4321", email: "sarah@example.com" }
        ],
        "2029-5-10": [
          { type: "setup", title: "Venue Setup", time: "9:00 AM", location: "Exhibition Center", contact: "Mike Wilson", role: "Logistics Manager", phone: "+1-800-555-1234", email: "mike@example.com" }
        ],
        "2029-5-15": [
          { type: "task", title: "Deadline: Project X", time: "5:00 PM", location: "Office", contact: "Emily Davis", role: "Team Lead", phone: "+1-800-987-6543", email: "emily@example.com" }
        ],
        "2029-5-20": [
          { type: "meeting", title: "Client Presentation", time: "11:00 AM", location: "Board Room", contact: "David Brown", role: "Account Executive", phone: "+1-800-246-8101", email: "david@example.com" }
        ],
        "2029-5-25": [
          { type: "event", title: "Company Anniversary", time: "6:00 PM", location: "Garden Venue", contact: "Lisa Taylor", role: "HR Director", phone: "+1-800-135-7924", email: "lisa@example.com" }
        ]
      };

      function monthLabel(y, m) {
        return new Date(y, m).toLocaleString("default", { month: "long", year: "numeric" });
      }

      function formatDate(date) {
        return new Date(date).toLocaleDateString("en-US", { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
      }

      function renderCalendar() {
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date();
        const isCurrentMonth = today.getMonth() === month && today.getFullYear() === year;

        calendarTitle.textContent = monthLabel(year, month);
        calendarDates.innerHTML = "";

        // empty slots
        for (let i = 0; i < firstDay; i++) {
          const empty = document.createElement("div");
          empty.className = "date-cell empty";
          calendarDates.appendChild(empty);
        }

        // dates
        for (let d = 1; d <= daysInMonth; d++) {
          const cell = document.createElement("div");
          const dateKey = `${year}-${month + 1}-${d}`;
          const dayEvents = events[dateKey] || [];
          
          cell.className = "date-cell";
          if (isCurrentMonth && d === today.getDate()) {
            cell.classList.add("today");
          }
          
          cell.innerHTML = `<div class="date-num">${d}</div>`;
          
          // Add events to the cell
          dayEvents.forEach(event => {
            const eventEl = document.createElement("button");
            eventEl.className = `event ${event.type}`;
            eventEl.textContent = event.title;
            eventEl.addEventListener("click", (e) => {
              e.stopPropagation();
              showEventDetails(event, dateKey);
            });
            cell.appendChild(eventEl);
          });
          
          cell.addEventListener("click", () => {
            // Remove selected class from all cells
            document.querySelectorAll('.date-cell').forEach(c => c.classList.remove('selected-day'));
            // Add selected class to clicked cell
            cell.classList.add('selected-day');
            
            if (dayEvents.length > 0) {
              showEventDetails(dayEvents[0], dateKey);
            } else {
              showEmptyDate(d);
            }
          });
          
          calendarDates.appendChild(cell);
        }
      }

      function showEventDetails(event, dateKey) {
        detailTitle.textContent = event.title;
        detailDate.textContent = formatDate(dateKey);
        
        // Update event details
        scheduleDetails.querySelector('.tag').textContent = event.type.charAt(0).toUpperCase() + event.type.slice(1);
        scheduleDetails.querySelector('p:nth-of-type(3)').textContent = `📍 ${event.location}`;
        
        // Update contact info
        const contactSection = scheduleDetails.querySelector('.contact');
        contactSection.innerHTML = `
          <p><strong>${event.contact}</strong></p>
          <p>${event.role}</p>
          <p>${event.phone}</p>
          <p>${event.email}</p>
        `;
        
        // Add time information
        if (!scheduleDetails.querySelector('.event-time')) {
          const timeEl = document.createElement('div');
          timeEl.className = 'event-time';
          scheduleDetails.insertBefore(timeEl, scheduleDetails.querySelector('.tag').nextSibling);
        }
        scheduleDetails.querySelector('.event-time').textContent = event.time;
      }

      function showEmptyDate(day) {
        detailTitle.textContent = `No events on ${day} ${monthLabel(year, month)}`;
        detailDate.textContent = "";
        scheduleDetails.querySelector('.tag').textContent = "No events";
        scheduleDetails.querySelector('p:nth-of-type(3)').textContent = "📍 No location specified";
        
        // Clear time if it exists
        if (scheduleDetails.querySelector('.event-time')) {
          scheduleDetails.querySelector('.event-time').remove();
        }
        
        // Reset contact info
        const contactSection = scheduleDetails.querySelector('.contact');
        contactSection.innerHTML = `
          <p><strong>Contact Name</strong></p>
          <p>Role</p>
          <p>+1-800-000-0000</p>
          <p>email@example.com</p>
        `;
      }

      prevBtn.addEventListener("click", () => {
        month--;
        if (month < 0) { month = 11; year--; }
        renderCalendar();
      });

      nextBtn.addEventListener("click", () => {
        month++;
        if (month > 11) { month = 0; year++; }
        renderCalendar();
      });

      // Add event listener for new agenda button
      document.querySelector('.new-agenda').addEventListener('click', () => {
        alert('Create new agenda functionality would go here');
      });

      renderCalendar();
    });