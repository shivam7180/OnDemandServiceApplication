/**
 * AuraCraft Core Client Script
 * Handles UI interactions, modal controls, responsive menus, toast notifications,
 * and automatic HTML5 browser geolocation fetching.
 */

const AuraCraft = {
  // 1. Mobile Menu Toggle
  initNavigation() {
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const navLinks = document.getElementById('navLinks');

    if (mobileToggle && navLinks) {
      mobileToggle.addEventListener('click', () => {
        navLinks.classList.toggle('open');
        const isOpen = navLinks.classList.contains('open');
        mobileToggle.setAttribute('aria-expanded', isOpen);
        mobileToggle.innerHTML = isOpen ? '✕' : '☰';
      });

      // Close menu on link click
      navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
          navLinks.classList.remove('open');
          mobileToggle.innerHTML = '☰';
        });
      });
    }
  },

  // 2. Tab Switcher
  initTabs() {
    document.querySelectorAll('[data-tab-group]').forEach(group => {
      const tabs = group.querySelectorAll('[data-tab-target]');
      const contents = group.querySelectorAll('[data-tab-content]');

      tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
          e.preventDefault();
          const target = tab.getAttribute('data-tab-target');

          tabs.forEach(t => t.classList.remove('active'));
          contents.forEach(c => c.style.display = 'none');

          tab.classList.add('active');
          const activeContent = group.querySelector(`[data-tab-content="${target}"]`);
          if (activeContent) {
            activeContent.style.display = 'block';
          }
        });
      });
    });
  },

  // 3. Modal Manager
  openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  },

  closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
      modal.classList.remove('active');
      document.body.style.overflow = '';
    }
  },

  initModals() {
    document.querySelectorAll('[data-open-modal]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const target = btn.getAttribute('data-open-modal');
        this.openModal(target);
      });
    });

    document.querySelectorAll('.modal-close, [data-close-modal]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const modal = btn.closest('.modal-overlay');
        if (modal) {
          this.closeModal(modal.id);
        }
      });
    });

    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
          this.closeModal(overlay.id);
        }
      });
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(m => this.closeModal(m.id));
      }
    });
  },

  // 4. Toast Notification
  showToast(message, type = 'success') {
    let container = document.getElementById('toastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toastContainer';
      container.className = 'toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    let icon = '✓';
    if (type === 'error') icon = '✕';
    if (type === 'warning') icon = '⚠';
    if (type === 'info') icon = 'ℹ';

    toast.innerHTML = `
      <span style="font-weight: bold; font-size: 1.1rem;">${icon}</span>
      <div>${message}</div>
    `;

    container.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(100%)';
      toast.style.transition = 'all 0.3s ease';
      setTimeout(() => toast.remove(), 300);
    }, 4000);
  },

  // 5. Automatic Browser Geolocation Fetcher
  detectLocation(inputElement, latElement, lngElement, statusElement) {
    if (!navigator.geolocation) {
      this.showToast('Geolocation is not supported by your browser', 'warning');
      return;
    }

    if (statusElement) {
      statusElement.textContent = 'Detecting satellite position...';
      statusElement.style.color = 'var(--terracotta)';
    }
    this.showToast('Requesting GPS location permission...', 'info');

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        if (latElement) latElement.value = lat;
        if (lngElement) lngElement.value = lng;

        // Human-readable coordinate label
        if (inputElement) {
          inputElement.value = `📍 Current Location (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
          inputElement.style.borderColor = 'var(--status-success)';
          inputElement.style.backgroundColor = '#F4FAF4';
        }

        if (statusElement) {
          statusElement.textContent = '✓ GPS coordinates locked';
          statusElement.style.color = 'var(--status-success)';
        }

        this.showToast('Location fetched successfully from device GPS!', 'success');

        // Store in localStorage for persistence across pages
        localStorage.setItem('user_lat', lat);
        localStorage.setItem('user_lng', lng);
        localStorage.setItem('user_location_label', `📍 Current Location (${lat.toFixed(4)}, ${lng.toFixed(4)})`);
      },
      (error) => {
        let msg = 'Unable to retrieve location';
        if (error.code === error.PERMISSION_DENIED) {
          msg = 'Location permission was denied. Please allow location access in your browser or type address manually.';
        } else if (error.code === error.TIMEOUT) {
          msg = 'Location request timed out. Please try again.';
        }
        if (statusElement) {
          statusElement.textContent = '⚠ Permission required';
          statusElement.style.color = 'var(--status-warning)';
        }
        this.showToast(msg, 'warning');
      },
      { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
    );
  }
};

document.addEventListener('DOMContentLoaded', () => {
  AuraCraft.initNavigation();
  AuraCraft.initTabs();
  AuraCraft.initModals();
});
