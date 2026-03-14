/**
 * Accessibility JavaScript functionality
 * Implements dark mode, high contrast mode, font size adjustment, and other accessibility features
 */

document.addEventListener("DOMContentLoaded", () => {
    const darkModeToggle = document.getElementById("dark-mode-toggle");
    const highContrastToggle = document.getElementById("high-contrast-toggle");
    const decreaseFontBtn = document.getElementById("decrease-font");
    const increaseFontBtn = document.getElementById("increase-font");
    const screenReaderBtn = document.getElementById("screen-reader");
    const languageToggle = document.getElementById("language-toggle");

    // Check for saved preferences
    const savedTheme = localStorage.getItem("theme");
    const savedContrast = localStorage.getItem("highContrast");
    const savedFontSize = localStorage.getItem("fontSize");
    const savedReducedMotion = localStorage.getItem("reducedMotion");

    // Apply saved theme
    if (savedTheme === "dark") {
        document.body.classList.add("dark-mode");
        darkModeToggle.innerHTML = '<i class="fas fa-sun small"></i>';
        darkModeToggle.setAttribute("aria-label", "Switch to light mode");
    }

    // Apply saved contrast
    if (savedContrast === "high") {
        document.body.classList.add("high-contrast");
        highContrastToggle.classList.add("active");
        highContrastToggle.setAttribute("aria-label", "Disable high contrast");
    }

    // Apply saved font size
    if (savedFontSize) {
        document.body.classList.remove(
            "font-size-sm",
            "font-size-md",
            "font-size-lg",
            "font-size-xl"
        );
        document.body.classList.add(savedFontSize);
    } else {
        document.body.classList.add("font-size-md");
    }

    // Apply saved motion preference
    if (savedReducedMotion === "reduced") {
        document.body.classList.add("reduced-motion");
    }

    // Dark mode toggle
    if (darkModeToggle) {
        darkModeToggle.addEventListener("click", () => {
            document.body.classList.toggle("dark-mode");

            if (document.body.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
                darkModeToggle.innerHTML = '<i class="fas fa-sun small"></i>';
                darkModeToggle.setAttribute(
                    "aria-label",
                    "Switch to light mode"
                );
            } else {
                localStorage.setItem("theme", "light");
                darkModeToggle.innerHTML = '<i class="fas fa-moon small"></i>';
                darkModeToggle.setAttribute(
                    "aria-label",
                    "Switch to dark mode"
                );
            }

            // Announce theme change to screen readers
            announceToScreenReader(
                `${
                    document.body.classList.contains("dark-mode")
                        ? "Dark"
                        : "Light"
                } mode enabled`
            );
        });
    }

    // High contrast toggle
    if (highContrastToggle) {
        highContrastToggle.addEventListener("click", () => {
            document.body.classList.toggle("high-contrast");
            highContrastToggle.classList.toggle("active");

            if (document.body.classList.contains("high-contrast")) {
                localStorage.setItem("highContrast", "high");
                highContrastToggle.setAttribute(
                    "aria-label",
                    "Disable high contrast"
                );
            } else {
                localStorage.setItem("highContrast", "normal");
                highContrastToggle.setAttribute(
                    "aria-label",
                    "Enable high contrast"
                );
            }

            // Announce contrast change to screen readers
            announceToScreenReader(
                `High contrast mode ${
                    document.body.classList.contains("high-contrast")
                        ? "enabled"
                        : "disabled"
                }`
            );
        });
    }

    // Font size adjustment
    if (decreaseFontBtn) {
        decreaseFontBtn.addEventListener("click", () => {
            if (document.body.classList.contains("font-size-xl")) {
                document.body.classList.remove("font-size-xl");
                document.body.classList.add("font-size-lg");
                localStorage.setItem("fontSize", "font-size-lg");
                announceToScreenReader("Font size decreased to large");
            } else if (document.body.classList.contains("font-size-lg")) {
                document.body.classList.remove("font-size-lg");
                document.body.classList.add("font-size-md");
                localStorage.setItem("fontSize", "font-size-md");
                announceToScreenReader("Font size decreased to medium");
            } else if (document.body.classList.contains("font-size-md")) {
                document.body.classList.remove("font-size-md");
                document.body.classList.add("font-size-sm");
                localStorage.setItem("fontSize", "font-size-sm");
                announceToScreenReader("Font size decreased to small");
            }
        });
    }

    if (increaseFontBtn) {
        increaseFontBtn.addEventListener("click", () => {
            if (document.body.classList.contains("font-size-sm")) {
                document.body.classList.remove("font-size-sm");
                document.body.classList.add("font-size-md");
                localStorage.setItem("fontSize", "font-size-md");
                announceToScreenReader("Font size increased to medium");
            } else if (document.body.classList.contains("font-size-md")) {
                document.body.classList.remove("font-size-md");
                document.body.classList.add("font-size-lg");
                localStorage.setItem("fontSize", "font-size-lg");
                announceToScreenReader("Font size increased to large");
            } else if (document.body.classList.contains("font-size-lg")) {
                document.body.classList.remove("font-size-lg");
                document.body.classList.add("font-size-xl");
                localStorage.setItem("fontSize", "font-size-xl");
                announceToScreenReader("Font size increased to extra large");
            } else if (!document.body.classList.contains("font-size-xl")) {
                document.body.classList.add("font-size-md");
                localStorage.setItem("fontSize", "font-size-md");
                announceToScreenReader("Font size set to medium");
            }
        });
    }

    // Screen reader information
    if (screenReaderBtn) {
        let bootstrap;
        try {
            bootstrap = require("bootstrap");
        } catch (e) {
            bootstrap = window.bootstrap;
        }
        screenReaderBtn.addEventListener("click", () => {
            const modal = new bootstrap.Modal(
                document.getElementById("screenReaderModal")
            );
            modal.show();
        });
    }

    // Language toggle
    if (languageToggle) {
        languageToggle.addEventListener("click", () => {
            const currentLang =
                languageToggle.querySelector("span").textContent;
            if (currentLang === "हिंदी") {
                languageToggle.querySelector("span").textContent = "English";
                languageToggle.setAttribute("aria-label", "Switch to English");
                // Here you would implement the actual language change
                announceToScreenReader("Language changed to Hindi");
            } else {
                languageToggle.querySelector("span").textContent = "हिंदी";
                languageToggle.setAttribute("aria-label", "Switch to Hindi");
                // Here you would implement the actual language change
                announceToScreenReader("Language changed to English");
            }
        });
    }

    // Ticker control
    const tickerControl = document.querySelector(".ticker-control");
    const tickerContent = document.querySelector(".ticker-content");

    if (tickerControl && tickerContent) {
        tickerControl.addEventListener("click", () => {
            tickerContent.classList.toggle("paused");

            if (tickerContent.classList.contains("paused")) {
                tickerControl.innerHTML = '<i class="fas fa-play"></i>';
                tickerControl.setAttribute("aria-label", "Play announcements");
                announceToScreenReader("Announcements paused");
            } else {
                tickerControl.innerHTML = '<i class="fas fa-pause"></i>';
                tickerControl.setAttribute("aria-label", "Pause announcements");
                announceToScreenReader("Announcements playing");
            }
        });
    }

    // Detect keyboard navigation for enhanced focus styles
    function handleFirstTab(e) {
        if (e.keyCode === 9) {
            // Tab key
            document.body.classList.add("keyboard-focus");
            window.removeEventListener("keydown", handleFirstTab);
        }
    }
    window.addEventListener("keydown", handleFirstTab);

    // Helper function to announce changes to screen readers
    function announceToScreenReader(message) {
        const announcement = document.createElement("div");
        announcement.setAttribute("aria-live", "polite");
        announcement.setAttribute("aria-atomic", "true");
        announcement.classList.add("sr-only");
        announcement.textContent = message;
        document.body.appendChild(announcement);

        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }
});


