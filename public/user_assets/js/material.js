/**
 * Material Design JavaScript functionality
 * Adds ripple effects, elevation changes, and other material design interactions
 */

document.addEventListener("DOMContentLoaded", () => {
  // Ripple effect for buttons and interactive elements
  const rippleElements = document.querySelectorAll(".ripple-effect")

  rippleElements.forEach((element) => {
    element.addEventListener("click", (e) => {
      const rect = element.getBoundingClientRect()
      const x = e.clientX - rect.left
      const y = e.clientY - rect.top

      const ripple = document.createElement("span")
      ripple.classList.add("ripple")
      ripple.style.left = `${x}px`
      ripple.style.top = `${y}px`

      element.appendChild(ripple)

      setTimeout(() => {
        ripple.remove()
      }, 600)
    })
  })

  // Portal slider functionality
  const portalSlider = document.querySelector(".portal-slider-container")
  const prevButton = document.querySelector(".portal-prev")
  const nextButton = document.querySelector(".portal-next")

  if (portalSlider && prevButton && nextButton) {
    const slides = document.querySelectorAll(".portal-slide")
    const slideWidth = slides[0].offsetWidth
    const visibleSlides = getVisibleSlides()
    let currentIndex = 0

    function getVisibleSlides() {
      if (window.innerWidth < 576) return 1
      if (window.innerWidth < 768) return 2
      if (window.innerWidth < 992) return 3
      return 4
    }

    function updateSliderPosition() {
      const offset = -currentIndex * (slideWidth + 16) // 16px is the gap
      portalSlider.style.transform = `translateX(${offset}px)`
    }

    prevButton.addEventListener("click", () => {
      if (currentIndex > 0) {
        currentIndex--
        updateSliderPosition()
      }
    })

    nextButton.addEventListener("click", () => {
      if (currentIndex < slides.length - visibleSlides) {
        currentIndex++
        updateSliderPosition()
      }
    })

    // Update visible slides on window resize
    window.addEventListener("resize", () => {
      const newVisibleSlides = getVisibleSlides()
      if (newVisibleSlides !== visibleSlides) {
        const visibleSlides = newVisibleSlides
        if (currentIndex > slides.length - visibleSlides) {
          currentIndex = slides.length - visibleSlides
          updateSliderPosition()
        }
      }
    })

    // Initial setup
    updateSliderPosition()
  }

  // Gallery and video item interactions
  const galleryItems = document.querySelectorAll(".gallery-item")
  const videoItems = document.querySelectorAll(".video-item")

  galleryItems.forEach((item) => {
    item.addEventListener("mouseenter", () => {
      item.querySelector(".gallery-overlay").style.opacity = "1"
      if (item.querySelector(".gallery-action")) {
        item.querySelector(".gallery-action").style.opacity = "1"
      }
    })

    item.addEventListener("mouseleave", () => {
      item.querySelector(".gallery-overlay").style.opacity = "0"
      if (item.querySelector(".gallery-action")) {
        item.querySelector(".gallery-action").style.opacity = "0"
      }
    })
  })

  videoItems.forEach((item) => {
    item.addEventListener("mouseenter", () => {
      item.querySelector(".video-overlay").style.opacity = "1"
      const playButton = item.querySelector(".video-play-button")
      playButton.style.transform = "translate(-50%, -50%) scale(1.1)"
    })

    item.addEventListener("mouseleave", () => {
      item.querySelector(".video-overlay").style.opacity = "0"
      const playButton = item.querySelector(".video-play-button")
      playButton.style.transform = "translate(-50%, -50%) scale(1)"
    })
  })

  // Enhanced table row interactions
  const tableRows = document.querySelectorAll(".table-hover tbody tr")

  tableRows.forEach((row) => {
    // Add elevation effect on hover
    row.addEventListener("mouseenter", function () {
      this.classList.add("elevation-1")
    })

    row.addEventListener("mouseleave", function () {
      this.classList.remove("elevation-1")
    })

    // Add click effect
    row.addEventListener("click", function (e) {
      // Don't trigger if clicking on a button or link
      if (!e.target.closest("a") && !e.target.closest("button")) {
        const viewButton = this.querySelector(".btn-outline-primary")
        if (viewButton) {
          viewButton.click()
        }
      }
    })
  })

  // Enhanced tab switching animations
  const tabButtons = document.querySelectorAll("#infoTabs .nav-link")

  tabButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Remove active class from all tabs
      tabButtons.forEach((btn) => {
        if (btn !== this) {
          btn.classList.remove("active")
          if (btn.classList.contains("bg-warning")) {
            btn.classList.remove("bg-warning", "bg-opacity-75", "text-dark")
            btn.classList.add("bg-primary", "text-white")
          }
        }
      })

      // Add active class to clicked tab
      this.classList.add("active")
      if (this.id === "whats-new-tab") {
        this.classList.remove("bg-primary", "text-white")
        this.classList.add("bg-warning", "bg-opacity-75", "text-dark")
      }
    })
  })

  // Add pulse effect to download buttons
  const downloadButtons = document.querySelectorAll(".btn-primary")

  downloadButtons.forEach((button) => {
    button.addEventListener("click", function () {
      this.classList.add("btn-pulse")
      setTimeout(() => {
        this.classList.remove("btn-pulse")
      }, 1000)
    })
  })
})
