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
				} else {
					card.style.display = "none";
				}
			});

			// Show toggle button only when viewing 'all' category
			if (toggleWrapper) {
				if (currentCategory === "all") {
					toggleWrapper.style.display = "block";
					if (toggleText && toggleAllBtn) {
						if (showAllMentors) {
							toggleText.textContent = "Lihat Lebih Sedikit";
							toggleAllBtn.setAttribute("aria-expanded", "true");
						} else {
							toggleText.textContent = "Lihat Semua Mentor (24)";
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

	// --- 5. MOBILE NAVIGATION PANEL ---
	const mobileNavToggle = document.getElementById('mobile-nav-toggle');
	const mobileNavPanel  = document.getElementById('mobile-nav-panel');
	const mobileNavIcon   = document.getElementById('mobile-nav-icon');

	if (mobileNavToggle && mobileNavPanel) {
		const openPanel = () => {
			mobileNavPanel.classList.add('open');
			mobileNavToggle.setAttribute('aria-expanded', 'true');
			mobileNavPanel.setAttribute('aria-hidden', 'false');
			if (mobileNavIcon) mobileNavIcon.className = 'fa-solid fa-xmark';
			document.body.style.overflow = 'hidden';
		};

		const closePanel = () => {
			mobileNavPanel.classList.remove('open');
			mobileNavToggle.setAttribute('aria-expanded', 'false');
			mobileNavPanel.setAttribute('aria-hidden', 'true');
			if (mobileNavIcon) mobileNavIcon.className = 'fa-solid fa-bars';
			document.body.style.overflow = '';
		};

		mobileNavToggle.addEventListener('click', () => {
			const isOpen = mobileNavPanel.classList.contains('open');
			isOpen ? closePanel() : openPanel();
		});

		// Close on any panel link click
		mobileNavPanel.querySelectorAll('a').forEach(link => {
			link.addEventListener('click', closePanel);
		});

		// Close on Escape key
		document.addEventListener('keydown', (e) => {
			if (e.key === 'Escape' && mobileNavPanel.classList.contains('open')) {
				closePanel();
				mobileNavToggle.focus();
			}
		});
	}

	// --- 6. INSTRUMENT CATALOG REAL-TIME SEARCH & CATEGORY FILTER ---
	const instSearchInput = document.getElementById("instrument-search-input");
	const instFilterPills = document.querySelectorAll(".instrument-filter-pills .filter-pill");
	const instrumentCards = document.querySelectorAll(".instrument-card");

	if (instrumentCards.length > 0) {
		let selectedCategory = "all";
		let searchQuery = "";

		const filterInstruments = () => {
			instrumentCards.forEach((card) => {
				const category = card.getAttribute("data-category") || "";
				const title = card.getAttribute("data-title") || "";

				const matchesCategory = selectedCategory === "all" || category.toLowerCase() === selectedCategory.toLowerCase();
				const matchesSearch = title.toLowerCase().includes(searchQuery.toLowerCase());

				if (matchesCategory && matchesSearch) {
					card.style.display = "";
				} else {
					card.style.display = "none";
				}
			});
		};

		if (instSearchInput) {
			instSearchInput.addEventListener("input", (e) => {
				searchQuery = e.target.value.trim();
				filterInstruments();
			});
		}

		instFilterPills.forEach((pill) => {
			pill.addEventListener("click", () => {
				selectedCategory = pill.getAttribute("data-category");

				instFilterPills.forEach((p) => {
					const isSelected = p.getAttribute("data-category") === selectedCategory;
					p.classList.toggle("active", isSelected);
					p.setAttribute("aria-selected", isSelected ? "true" : "false");
				});

				filterInstruments();
			});
		});
	}
});
