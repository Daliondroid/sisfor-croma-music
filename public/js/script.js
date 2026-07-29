/**
 * CROMA MUSIC - LANDING PAGE INTERACTION SCRIPT
 */

document.addEventListener("DOMContentLoaded", () => {
	// --- 1. HEADER SCROLL & BACK TO TOP EFFECT ---
	const header = document.querySelector(".header");
	const backToTopBtn = document.getElementById("back-to-top");
	let ticking = false;

	const handleScroll = () => {
		if (!ticking) {
			window.requestAnimationFrame(() => {
				const scrollPos = window.scrollY;

				// Header scroll state
				if (scrollPos > 40) {
					header.classList.add("scrolled");
				} else {
					header.classList.remove("scrolled");
				}

				// Back to top button visibility
				if (backToTopBtn) {
					if (scrollPos > 300) {
						backToTopBtn.classList.add("show");
					} else {
						backToTopBtn.classList.remove("show");
					}
				}

				ticking = false;
			});
			ticking = true;
		}
	};

	window.addEventListener("scroll", handleScroll, { passive: true });
	handleScroll(); // Initial check

	// --- 2. SCROLL ANIMATION (INTERSECTION OBSERVER) ---
	const observerOptions = {
		threshold: 0.12,
		rootMargin: "0px 0px -40px 0px",
	};

	const observer = new IntersectionObserver((entries, obs) => {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
				entry.target.classList.add("show-element");
				obs.unobserve(entry.target);
			}
		});
	}, observerOptions);

	const hiddenElements = document.querySelectorAll(".hidden-element");
	hiddenElements.forEach((el) => observer.observe(el));

	// --- 3. CONTINUOUS SEAMLESS AUTO-SCROLLING CAROUSEL ---
	const carouselTrack = document.getElementById("carousel-track");

	if (carouselTrack) {
		let isHovered = false;
		let currentX = 0;
		const scrollSpeed = 0.8; // Smooth pixels per frame

		const stepScroll = () => {
			if (!isHovered) {
				currentX += scrollSpeed;
				const halfWidth = carouselTrack.scrollWidth / 2;

				if (halfWidth > 0 && currentX >= halfWidth) {
					currentX %= halfWidth; // Seamless mathematical loop without any jump
				}

				carouselTrack.style.transform = `translate3d(-${currentX}px, 0, 0)`;
			}
			requestAnimationFrame(stepScroll);
		};

		// Pause auto-scroll when user hovers or touches carousel
		carouselTrack.addEventListener("mouseenter", () => (isHovered = true));
		carouselTrack.addEventListener("mouseleave", () => (isHovered = false));
		carouselTrack.addEventListener("touchstart", () => (isHovered = true), { passive: true });
		carouselTrack.addEventListener("touchend", () => (isHovered = false), { passive: true });

		// Start auto scroll loop
		requestAnimationFrame(stepScroll);
	}

	// --- 3. DYNAMIC VIDEO MODAL POP-UP ---
	const modal = document.getElementById("video-modal");
	const btnOther = document.getElementById("btn-other-instruments");
	const closeModal = document.getElementById("close-modal");
	const iframe = document.getElementById("demo-video");

	const openModal = () => {
		if (modal && iframe) {
			const videoSrc = iframe.getAttribute("data-src");
			if (videoSrc && !iframe.src) {
				iframe.src = videoSrc;
			}
			modal.classList.add("active");
			document.body.style.overflow = "hidden"; // Prevent background scroll
		}
	};

	const closeModalWindow = () => {
		if (modal && iframe) {
			modal.classList.remove("active");
			document.body.style.overflow = "";
			iframe.src = ""; // Stop video playback & free memory
		}
	};

	if (btnOther) btnOther.addEventListener("click", openModal);
	if (closeModal) closeModal.addEventListener("click", closeModalWindow);

	if (modal) {
		modal.addEventListener("click", (e) => {
			if (e.target === modal) {
				closeModalWindow();
			}
		});
	}

	// KEYBOARD NAVIGATION (ESCAPE KEY)
	window.addEventListener("keydown", (e) => {
		if (e.key === "Escape") {
			closeModalWindow();
		}
	});

	// --- 4. ACCESSIBLE MENTOR PILL FILTER & FEATURED SPOTLIGHT LOGIC ---
	const filterPills = document.querySelectorAll(".filter-pill");
	const tutorCards = document.querySelectorAll(".tutor-card");
	const toggleAllBtn = document.getElementById("toggle-all-mentors");
	const toggleWrapper = document.getElementById("mentor-toggle-wrapper");
	const toggleText = document.getElementById("toggle-text");
	const toggleIcon = document.getElementById("toggle-icon");

	if (filterPills.length > 0 && tutorCards.length > 0) {
		let showAllMentors = false;
		let currentCategory = "all";

		const updateMentorVisibility = () => {
			tutorCards.forEach((card) => {
				const cardCategory = card.getAttribute("data-category");
				const isFeatured = card.getAttribute("data-featured") === "true";

				let shouldShow = false;

				if (currentCategory === "all") {
					shouldShow = showAllMentors ? true : isFeatured;
				} else {
					shouldShow = cardCategory === currentCategory;
				}

				if (shouldShow) {
					card.style.display = "";
					requestAnimationFrame(() => {
						card.classList.add("show-element");
					});
				} else {
					card.style.display = "none";
					card.classList.remove("show-element");
				}
			});

			// Show toggle button only when viewing 'all' category
			if (toggleWrapper) {
				if (currentCategory === "all") {
					toggleWrapper.style.display = "block";
					if (toggleText && toggleIcon && toggleAllBtn) {
						if (showAllMentors) {
							toggleText.textContent = "Lihat Lebih Sedikit";
							toggleIcon.style.transform = "rotate(180deg)";
							toggleAllBtn.setAttribute("aria-expanded", "true");
						} else {
							toggleText.textContent = "Lihat Semua Mentor (24)";
							toggleIcon.style.transform = "rotate(0deg)";
							toggleAllBtn.setAttribute("aria-expanded", "false");
						}
					}
				} else {
					toggleWrapper.style.display = "none";
				}
			}
		};

		filterPills.forEach((pill) => {
			pill.addEventListener("click", () => {
				const selectedCategory = pill.getAttribute("data-filter");
				currentCategory = selectedCategory;

				filterPills.forEach((p) => {
					const isSelected = p.getAttribute("data-filter") === selectedCategory;
					p.classList.toggle("active", isSelected);
					p.setAttribute("aria-selected", isSelected ? "true" : "false");
				});

				updateMentorVisibility();
			});
		});

		if (toggleAllBtn) {
			toggleAllBtn.addEventListener("click", () => {
				showAllMentors = !showAllMentors;
				updateMentorVisibility();
			});
		}

		// Initial display (8 featured mentors)
		updateMentorVisibility();
	}
});
