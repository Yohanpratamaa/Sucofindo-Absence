// Smart Presence Login JavaScript

class LoginManager {
    constructor() {
        this.init();
        this.startRealTimeClock();
    }

    init() {
        this.setupEventListeners();
        this.setupAnimations();
        this.setupFormValidation();
        this.setupPasswordToggle();
        this.autoFocusUsername();
    }

    // Real-time clock function
    startRealTimeClock() {
        const updateTime = () => {
            const now = new Date();

            // Format time
            const time = now.toLocaleTimeString("id-ID", {
                hour: "2-digit",
                minute: "2-digit",
                hour12: false,
            });

            // Format date
            const dateOptions = {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
            };
            const date = now.toLocaleDateString("id-ID", dateOptions);

            // Update elements
            const timeElement = document.getElementById("current-time");
            const dateElement = document.getElementById("current-date");

            if (timeElement) {
                timeElement.textContent = time;
            }

            if (dateElement) {
                dateElement.textContent = date;
            }
        };

        // Update immediately and then every second
        updateTime();
        setInterval(updateTime, 1000);
    }

    setupEventListeners() {
        const form = document.querySelector("form");
        const submitBtn = document.querySelector('button[type="submit"]');

        if (form) {
            form.addEventListener("submit", this.handleFormSubmit.bind(this));
        }

        // Add input animations
        const inputs = document.querySelectorAll("input");
        inputs.forEach((input) => {
            input.addEventListener("focus", this.handleInputFocus.bind(this));
            input.addEventListener("blur", this.handleInputBlur.bind(this));
            input.addEventListener("input", this.handleInputChange.bind(this));
        });
    }

    setupAnimations() {
        // Fade in elements on load
        window.addEventListener("load", () => {
            const animatedElements = document.querySelectorAll(".fade-in");
            animatedElements.forEach((el, index) => {
                setTimeout(() => {
                    el.style.opacity = "1";
                    el.style.transform = "translateY(0)";
                }, index * 150);
            });
        });

        // Character animation
        const character = document.querySelector(".character-bounce");
        if (character) {
            setInterval(() => {
                character.classList.add("character-bounce");
                setTimeout(() => {
                    character.classList.remove("character-bounce");
                }, 2000);
            }, 5000);
        }

        // Floating elements animation
        const floatingElements = document.querySelectorAll(".floating");
        floatingElements.forEach((el, index) => {
            el.style.animationDelay = `${index * 0.5}s`;
        });
    }

    setupFormValidation() {
        const emailInput = document.getElementById("email");
        const passwordInput = document.getElementById("password");

        if (emailInput) {
            emailInput.addEventListener("blur", () => {
                this.validateEmail(emailInput);
            });
        }

        if (passwordInput) {
            passwordInput.addEventListener("blur", () => {
                this.validatePassword(passwordInput);
            });
        }
    }

    setupPasswordToggle() {
        const toggleBtn = document.querySelector(
            '[onclick="togglePassword()"]'
        );
        if (toggleBtn) {
            toggleBtn.onclick = (e) => {
                e.preventDefault();
                this.togglePasswordVisibility();
            };
        }
    }

    autoFocusUsername() {
        const emailInput = document.getElementById("email");
        if (emailInput && !emailInput.value) {
            setTimeout(() => {
                emailInput.focus();
            }, 500);
        }
    }

    handleFormSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const submitBtn = form.querySelector('button[type="submit"]');
        const emailInput = form.querySelector("#email");
        const passwordInput = form.querySelector("#password");

        // Validate inputs
        const isEmailValid = this.validateEmail(emailInput);
        const isPasswordValid = this.validatePassword(passwordInput);

        if (!isEmailValid || !isPasswordValid) {
            this.showAlert("Please correct the errors and try again.", "error");
            return;
        }

        // Show loading state
        this.setLoadingState(submitBtn, true);

        // Simulate delay for better UX
        setTimeout(() => {
            form.submit();
        }, 800);
    }

    handleInputFocus(e) {
        const input = e.target;
        input.parentElement.classList.add("input-focused");

        // Add subtle animation
        input.style.transform = "translateY(-1px)";
        input.style.transition = "all 0.3s ease";
    }

    handleInputBlur(e) {
        const input = e.target;
        input.parentElement.classList.remove("input-focused");

        // Reset animation
        input.style.transform = "translateY(0)";
    }

    handleInputChange(e) {
        const input = e.target;

        // Remove error states on typing
        input.classList.remove("input-error");

        // Add success state for filled inputs
        if (input.value.trim() !== "") {
            input.classList.add("input-filled");
        } else {
            input.classList.remove("input-filled");
        }
    }

    validateEmail(input) {
        const email = input.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!email) {
            this.setInputError(input, "Email is required");
            return false;
        }

        if (!emailRegex.test(email)) {
            this.setInputError(input, "Please enter a valid email address");
            return false;
        }

        this.setInputSuccess(input);
        return true;
    }

    validatePassword(input) {
        const password = input.value;

        if (!password) {
            this.setInputError(input, "Password is required");
            return false;
        }

        if (password.length < 6) {
            this.setInputError(input, "Password must be at least 6 characters");
            return false;
        }

        this.setInputSuccess(input);
        return true;
    }

    setInputError(input, message) {
        input.classList.remove("input-success");
        input.classList.add("input-error");

        // Remove existing error message
        const existingError =
            input.parentElement.querySelector(".error-message");
        if (existingError) {
            existingError.remove();
        }

        // Add new error message
        const errorDiv = document.createElement("div");
        errorDiv.className = "error-message text-red-500 text-sm mt-1";
        errorDiv.textContent = message;
        input.parentElement.appendChild(errorDiv);
    }

    setInputSuccess(input) {
        input.classList.remove("input-error");
        input.classList.add("input-success");

        // Remove error message
        const existingError =
            input.parentElement.querySelector(".error-message");
        if (existingError) {
            existingError.remove();
        }
    }

    togglePasswordVisibility() {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eye-icon");

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }

        // Add subtle animation
        eyeIcon.style.transform = "scale(0.8)";
        setTimeout(() => {
            eyeIcon.style.transform = "scale(1)";
        }, 150);
    }

    setLoadingState(button, isLoading) {
        if (isLoading) {
            button.disabled = true;
            button.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Signing In...
            `;
        } else {
            button.disabled = false;
            button.innerHTML = "Sign In";
        }
    }

    showAlert(message, type = "info") {
        // Remove existing alerts
        const existingAlert = document.querySelector(".custom-alert");
        if (existingAlert) {
            existingAlert.remove();
        }

        const alertDiv = document.createElement("div");
        alertDiv.className = `custom-alert alert-slide fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 ${
            type === "error"
                ? "bg-red-500 text-white"
                : type === "success"
                ? "bg-green-500 text-white"
                : "bg-blue-500 text-white"
        }`;

        alertDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${
                    type === "error"
                        ? "fa-exclamation-circle"
                        : type === "success"
                        ? "fa-check-circle"
                        : "fa-info-circle"
                } mr-2"></i>
                <span>${message}</span>
                <button class="ml-4 text-white hover:text-gray-200" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(alertDiv);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Keyboard shortcuts
    setupKeyboardShortcuts() {
        document.addEventListener("keydown", (e) => {
            // Enter to submit form
            if (e.key === "Enter" && e.target.tagName !== "BUTTON") {
                const form = document.querySelector("form");
                if (form) {
                    form.dispatchEvent(new Event("submit"));
                }
            }

            // Escape to clear inputs
            if (e.key === "Escape") {
                const inputs = document.querySelectorAll("input");
                inputs.forEach((input) => {
                    if (input.type !== "submit") {
                        input.value = "";
                        input.classList.remove(
                            "input-error",
                            "input-success",
                            "input-filled"
                        );
                    }
                });
            }
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
    new LoginManager();
});

// Additional utility functions
window.loginUtils = {
    // Check if user is on mobile
    isMobile: () => {
        return window.innerWidth <= 768;
    },

    // Shake effect for errors
    shakeElement: (element) => {
        element.style.animation = "shake 0.5s";
        setTimeout(() => {
            element.style.animation = "";
        }, 500);
    },

    // Smooth scroll to element
    scrollToElement: (element) => {
        element.scrollIntoView({
            behavior: "smooth",
            block: "center",
        });
    },
};

// Add shake animation to CSS
const shakeCSS = `
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
    20%, 40%, 60%, 80% { transform: translateX(10px); }
}
`;

const style = document.createElement("style");
style.textContent = shakeCSS;
document.head.appendChild(style);
