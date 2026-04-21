// --- 1. PREVENT SCROLL GLITCH ON REFRESH ---
if ("scrollRestoration" in history) {
	history.scrollRestoration = "manual";
}
window.scrollTo(0, 0);

// --- 2. HEADER SCROLL EFFECT (OPTIMIZED) ---
const header = document.querySelector(".header");
let ticking = false;

window.addEventListener(
	"scroll",
	() => {
		if (!ticking) {
			window.requestAnimationFrame(() => {
				if (window.scrollY > 50) {
					header.classList.add("scrolled");
				} else {
					header.classList.remove("scrolled");
				}
				ticking = false;
			});
			ticking = true;
		}
	},
	{ passive: true },
);

// --- 3. MOBILE MENU (HAMBURGER) ---
const mobileToggle = document.getElementById("mobile-toggle");
const navMenu = document.getElementById("nav-menu");
const closeMenu = document.querySelector(".close-menu");
const navLinks = document.querySelectorAll(".nav-link");

mobileToggle.addEventListener("click", () => {
	navMenu.classList.add("active");
});

closeMenu.addEventListener("click", () => {
	navMenu.classList.remove("active");
});

navLinks.forEach((link) => {
	link.addEventListener("click", () => {
		navMenu.classList.remove("active");
	});
});

// --- 4. SCROLL ANIMATION (FADE IN) ---
const observerOptions = {
	threshold: 0.1,
};

const observer = new IntersectionObserver((entries) => {
	entries.forEach((entry) => {
		if (entry.isIntersecting) {
			entry.target.classList.add("show-element");
		}
	});
}, observerOptions);

const hiddenElements = document.querySelectorAll(".hidden-element");
hiddenElements.forEach((el) => observer.observe(el));

// --- 5. MODAL VIDEO POP-UP ---
document.addEventListener("DOMContentLoaded", () => {
	const modal = document.getElementById("video-modal");
	const btnOther = document.getElementById("btn-other-instruments");
	const closeModal = document.querySelector(".close-modal");
	const iframe = document.getElementById("demo-video");

	if (btnOther && modal) {
		btnOther.addEventListener("click", (e) => {
			e.preventDefault();
			modal.classList.add("active");
		});
	}

	if (closeModal) {
		closeModal.addEventListener("click", () => {
			modal.classList.remove("active");
			if (iframe) iframe.src = iframe.src;
		});
	}

	window.addEventListener("click", (e) => {
		if (e.target === modal) {
			modal.classList.remove("active");
			if (iframe) iframe.src = iframe.src;
		}
	});
});

// --- 6. FILTER LOGIC UNTUK MENTOR ---
const filterDropdown = document.getElementById("mentor-filter");
const tutorCards = document.querySelectorAll(".tutor-card");

if (filterDropdown) {
	const applyFilter = (selectedCategory) => {
		const seenCategories = new Set();

		tutorCards.forEach((card) => {
			card.classList.remove("show-element");
			const cardCategory = card.getAttribute("data-category");

			let shouldShow = false;

			if (selectedCategory === "all") {
				if (!seenCategories.has(cardCategory)) {
					shouldShow = true;
					seenCategories.add(cardCategory);
				}
			} else if (cardCategory === selectedCategory) {
				shouldShow = true;
			}

			if (shouldShow) {
				card.style.display = "block";
				setTimeout(() => card.classList.add("show-element"), 50);
			} else {
				card.style.display = "none";
			}
		});
	};

	filterDropdown.addEventListener("change", (e) => {
		applyFilter(e.target.value);
	});

	applyFilter(filterDropdown.value);
}
