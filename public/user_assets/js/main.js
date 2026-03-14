/**
 * Main JavaScript functionality for the Office of JS & CAO website
 * Implements interactive features, animations, and user experience enhancements
 */

document.addEventListener("DOMContentLoaded", () => {
  // Initialize tooltips
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.map(
    (tooltipTriggerEl) =>
      new bootstrap.Tooltip(tooltipTriggerEl, {
        trigger: "hover focus",
      }),
  )

  // Initialize popovers
  const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
  popoverTriggerList.map((popoverTriggerEl) => new bootstrap.Popover(popoverTriggerEl))

  // Mobile search toggle
  const mobileSearchToggle = document.getElementById("mobile-search-toggle")
  const mobileSearchForm = document.querySelector(".mobile-search-form")

  if (mobileSearchToggle && mobileSearchForm) {
    mobileSearchToggle.addEventListener("click", () => {
      mobileSearchForm.classList.toggle("d-none")
      if (!mobileSearchForm.classList.contains("d-none")) {
        mobileSearchForm.querySelector("input").focus()
      }
    })
  }

  // Announcements ticker control
  const tickerControl = document.querySelector(".ticker-control")
  const tickerContent = document.querySelector(".ticker-content")

  if (tickerControl && tickerContent) {
    tickerControl.addEventListener("click", () => {
      tickerContent.classList.toggle("paused")

      if (tickerContent.classList.contains("paused")) {
        tickerControl.innerHTML = '<i class="fas fa-play"></i>'
        tickerControl.setAttribute("aria-label", "Play announcements")
      } else {
        tickerControl.innerHTML = '<i class="fas fa-pause"></i>'
        tickerControl.setAttribute("aria-label", "Pause announcements")
      }
    })
  }

  // Enhanced hover effects for cards
  const enhancedCards = document.querySelectorAll(".card-hover, .hover-translate, .hover-shadow")

  enhancedCards.forEach((card) => {
    card.addEventListener("mouseenter", () => {
      card.classList.add("elevation-3")
    })

    card.addEventListener("mouseleave", () => {
      card.classList.remove("elevation-3")
    })
  })

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault()

      const targetId = this.getAttribute("href")
      const targetElement = document.querySelector(targetId)

      if (targetElement) {
        const headerOffset = 100
        const elementPosition = targetElement.getBoundingClientRect().top
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset

        window.scrollTo({
          top: offsetPosition,
          behavior: "smooth",
        })

        // Update URL without page jump
        history.pushState(null, null, targetId)
      }
    })
  })

  // Back to top button
  const backToTopBtn = document.getElementById("back-to-top")

  if (backToTopBtn) {
    window.addEventListener("scroll", () => {
      if (window.pageYOffset > 300) {
        backToTopBtn.classList.add("show")
      } else {
        backToTopBtn.classList.remove("show")
      }
    })

    backToTopBtn.addEventListener("click", (e) => {
      e.preventDefault()
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      })
    })
  }

  // Enhanced button interactions
  const buttons = document.querySelectorAll(".btn")

  buttons.forEach((button) => {
    // Add pulse effect on click
    button.addEventListener("click", function () {
      this.classList.add("btn-pulse")
      setTimeout(() => {
        this.classList.remove("btn-pulse")
      }, 500)
    })

    // Add hover state class for custom styling
    button.addEventListener("mouseenter", function () {
      this.classList.add("btn-hover")
    })

    button.addEventListener("mouseleave", function () {
      this.classList.remove("btn-hover")
    })

    // Add focus visible class for accessibility
    button.addEventListener("focus", function () {
      this.classList.add("btn-focus-visible")
    })

    button.addEventListener("blur", function () {
      this.classList.remove("btn-focus-visible")
    })
  })

  // Form validation enhancement
  const forms = document.querySelectorAll(".needs-validation")

  forms.forEach((form) => {
    form.addEventListener(
      "submit",
      (event) => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()

          // Find the first invalid element and focus it
          const firstInvalid = form.querySelector(":invalid")
          if (firstInvalid) {
            firstInvalid.focus()

            // Scroll to the first invalid element with offset
            const headerOffset = 120
            const elementPosition = firstInvalid.getBoundingClientRect().top
            const offsetPosition = elementPosition + window.pageYOffset - headerOffset

            window.scrollTo({
              top: offsetPosition,
              behavior: "smooth",
            })
          }
        }

        form.classList.add("was-validated")
      },
      false,
    )
  })

  // Dropdown menu enhancement for accessibility
  const dropdownItems = document.querySelectorAll(".dropdown-item")

  dropdownItems.forEach((item) => {
    item.addEventListener("keydown", function (e) {
      // Handle arrow key navigation
      if (e.key === "ArrowDown") {
        e.preventDefault()
        if (this.nextElementSibling) {
          this.nextElementSibling.focus()
        } else {
          this.parentElement.querySelector(".dropdown-item").focus()
        }
      } else if (e.key === "ArrowUp") {
        e.preventDefault()
        if (this.previousElementSibling) {
          this.previousElementSibling.focus()
        } else {
          const items = this.parentElement.querySelectorAll(".dropdown-item")
          items[items.length - 1].focus()
        }
      }
    })
  })

  // Detect keyboard navigation for enhanced focus styles
  function handleFirstTab(e) {
    if (e.key === "Tab") {
      document.body.classList.add("keyboard-focus")
      window.removeEventListener("keydown", handleFirstTab)
    }
  }

  window.addEventListener("keydown", handleFirstTab)

  // Remove keyboard focus styles when mouse is used
  document.body.addEventListener("mousedown", () => {
    document.body.classList.remove("keyboard-focus")
  })
})
