/**
 * ============================================================================
 * BACAin - app.js
 * Interaktivitas: Pencarian/filter langsung, kalkulasi sewa, dialog konfirmasi, notifikasi
 * Dikerjakan oleh: Syakhish (Front-end)
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // Format angka ke format Rupiah
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(number);
    }

    // ========================================================================
    // 1. Mobile Navigation Menu Toggle
    // ========================================================================
    const navToggle = document.getElementById('nav-toggle');
    const navMenu = document.getElementById('nav-menu');

    if (navToggle && navMenu) {
        navToggle.addEventListener('click', function () {
            const isExpanded = navToggle.getAttribute('aria-expanded') === 'true';
            navToggle.setAttribute('aria-expanded', !isExpanded);
            navMenu.classList.toggle('is-active');
        });
    }

    // ========================================================================
    // 2. Alert & Flash Notification Dismiss
    // ========================================================================
    const alertCloseButtons = document.querySelectorAll('.alert-close');
    alertCloseButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const alert = btn.closest('.alert');
            if (alert) {
                alert.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function () {
                    alert.remove();
                }, 300);
            }
        });
    });

    // Auto-dismiss alert setelah 6 detik
    const flashAlerts = document.querySelectorAll('.flash-messages .alert');
    flashAlerts.forEach(function (alert) {
        setTimeout(function () {
            if (alert && alert.parentElement) {
                alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function () {
                    alert.remove();
                }, 500);
            }
        }, 6000);
    });

    // ========================================================================
    // 3. Live Search & Filter Instan di Katalog
    // ========================================================================
    const searchInput = document.getElementById('search-input');
    const bookGrid = document.getElementById('catalog-book-grid');

    if (searchInput && bookGrid) {
        const bookCards = bookGrid.querySelectorAll('.book-card');

        searchInput.addEventListener('input', function () {
            const query = searchInput.value.trim().toLowerCase();
            let visibleCount = 0;

            bookCards.forEach(function (card) {
                const title = card.getAttribute('data-title') || '';
                const author = card.getAttribute('data-author') || '';

                if (query === '' || title.includes(query) || author.includes(query)) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Update live result count jika elemen tersedia
            const resultsCountEl = document.querySelector('.results-count');
            if (resultsCountEl) {
                resultsCountEl.innerHTML = `Menampilkan <strong>${visibleCount}</strong> buku secara langsung`;
            }
        });
    }

    // ========================================================================
    // 4. Kalkulator Estimasi Biaya Sewa Dinamis (Book Detail)
    // ========================================================================
    const calculatorCard = document.getElementById('rental-calculator');
    if (calculatorCard) {
        const basePrice = parseFloat(calculatorCard.getAttribute('data-base-price') || '0');
        const bookType = calculatorCard.getAttribute('data-book-type') || 'default';
        const pages = parseInt(calculatorCard.getAttribute('data-pages') || '0', 10);
        const chapters = parseInt(calculatorCard.getAttribute('data-chapters') || '0', 10);
        const isColor = calculatorCard.getAttribute('data-is-color') === '1';
        const sizeMb = parseFloat(calculatorCard.getAttribute('data-size-mb') || '0');

        const daysInput = document.getElementById('rent-days');
        const daysLabel = document.getElementById('calc-days-label');
        const specialLabel = document.getElementById('calc-special-label');
        const totalDisplay = document.getElementById('calc-total-display');
        const presetButtons = document.querySelectorAll('.btn-preset');

        function updateRentalPrice() {
            let days = parseInt(daysInput.value, 10);
            if (isNaN(days) || days < 1) {
                days = 1;
            }

            // Hitung komponen khusus sesuai polimorfisme backend
            let specialCost = 0;
            let specialText = '-';

            if (bookType === 'novel') {
                specialCost = pages * 20.0;
                specialText = `${pages} hlm × Rp 20 = Rp ${specialCost.toLocaleString('id-ID')}`;
            } else if (bookType === 'komik') {
                const ratePerCh = isColor ? 750.0 : 500.0;
                specialCost = chapters * ratePerCh;
                specialText = `${chapters} ch × Rp ${ratePerCh} = Rp ${specialCost.toLocaleString('id-ID')}`;
            } else if (bookType === 'ebook') {
                specialCost = sizeMb * 50.0;
                specialText = `${sizeMb} MB × Rp 50 = Rp ${specialCost.toLocaleString('id-ID')}`;
            }

            const total = (basePrice * days) + specialCost;

            if (daysLabel) {
                daysLabel.textContent = days;
            }
            if (specialLabel) {
                specialLabel.textContent = specialText;
            }
            if (totalDisplay) {
                totalDisplay.textContent = formatRupiah(total);
            }

            // Update active preset button highlight
            presetButtons.forEach(function (btn) {
                const presetDays = parseInt(btn.getAttribute('data-days'), 10);
                if (presetDays === days) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });
        }

        // Listener input hari
        if (daysInput) {
            daysInput.addEventListener('input', updateRentalPrice);
            daysInput.addEventListener('change', updateRentalPrice);
        }

        // Listener preset buttons
        presetButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const chosenDays = btn.getAttribute('data-days');
                if (daysInput) {
                    daysInput.value = chosenDays;
                    updateRentalPrice();
                }
            });
        });

        // Initial run
        updateRentalPrice();

        // Konfirmasi sewa buku sebelum submit
        const rentForm = document.getElementById('form-rental');
        if (rentForm) {
            rentForm.addEventListener('submit', function (e) {
                const days = daysInput ? daysInput.value : 7;
                const bookTitle = document.querySelector('.detail-title') 
                    ? document.querySelector('.detail-title').textContent.trim() 
                    : 'buku ini';
                const totalCost = totalDisplay ? totalDisplay.textContent : '';

                const confirmMsg = `Konfirmasi Peminjaman:\n\n` +
                    `Buku: ${bookTitle}\n` +
                    `Durasi: ${days} Hari\n` +
                    `Estimasi Total: ${totalCost}\n\n` +
                    `Lanjutkan proses penyewaan?`;

                if (!confirm(confirmMsg)) {
                    e.preventDefault();
                }
            });
        }
    }
});
