class NepaliDateConverter {
    constructor() {
        this.nepaliMonths = [
            'बैशाख', 'जेठ', 'असार', 'साउन', 'भदौ', 'असोज',
            'कार्तिक', 'मंसिर', 'पुष', 'माघ', 'फागुन', 'चैत'
        ];
        this.englishMonths = [
            'Baisakh', 'Jestha', 'Ashar', 'Shrawan', 'Bhadra', 'Ashwin',
            'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra'
        ];
        this.init();
    }

    // Wait for NepaliFunctions to be available
    waitForNepaliFunctions(callback, maxAttempts = 50) {
        let attempts = 0;
        const checkInterval = setInterval(() => {
            attempts++;
            if (window.NepaliFunctions && window.NepaliFunctions.AD2BS && window.NepaliFunctions.BS2AD) {
                clearInterval(checkInterval);
                callback();
            } else if (attempts >= maxAttempts) {
                clearInterval(checkInterval);
                console.error('NepaliFunctions not available after maximum attempts');
            }
        }, 100);
    }

    convertAdToBs(adDate, sourceFormat = 'YYYY-MM-DD', returnFormat = 'readable') {
        if (!adDate || !window.NepaliFunctions || !window.NepaliFunctions.AD2BS) {
            console.error('NepaliFunctions.AD2BS not available or invalid AD date:', adDate);
            return null;
        }

        try {
            let bsResult;
            if (typeof adDate === 'string') {
                bsResult = window.NepaliFunctions.AD2BS(adDate, sourceFormat, 'YYYY-MM-DD');
            } else if (adDate instanceof Date) {
                const dateStr = `${adDate.getFullYear()}-${String(adDate.getMonth() + 1).padStart(2, '0')}-${String(adDate.getDate()).padStart(2, '0')}`;
                bsResult = window.NepaliFunctions.AD2BS(dateStr, 'YYYY-MM-DD', 'YYYY-MM-DD');
            } else if (typeof adDate === 'object' && adDate.year && adDate.month && adDate.day) {
                bsResult = window.NepaliFunctions.AD2BS(adDate, null, 'YYYY-MM-DD');
            } else {
                return null;
            }

            if (!bsResult) return null;

            let bsYear, bsMonth, bsDay;
            if (typeof bsResult === 'string') {
                [bsYear, bsMonth, bsDay] = bsResult.split('-').map(Number);
            } else if (typeof bsResult === 'object') {
                bsYear = bsResult.year;
                bsMonth = bsResult.month;
                bsDay = bsResult.day;
            }

            const formatted = `${bsYear}-${String(bsMonth).padStart(2, '0')}-${String(bsDay).padStart(2, '0')}`;
            const nepaliFormatted = this.toNepaliNumber(formatted);
            const readableFormat = `${this.englishMonths[bsMonth - 1]} ${bsDay}, ${bsYear}`;

            return {
                year: bsYear,
                month: bsMonth,
                day: bsDay,
                formatted,
                nepaliFormatted,
                readable: readableFormat,
                monthName: this.englishMonths[bsMonth - 1],
                monthNameNepali: this.nepaliMonths[bsMonth - 1],
                originalAd: adDate
            };
        } catch (error) {
            console.error('AD to BS conversion error:', error, adDate);
            return null;
        }
    }

    convertBsToAd(bsDate, sourceFormat = 'YYYY-MM-DD', returnFormat = 'YYYY-MM-DD') {
        if (!bsDate || !window.NepaliFunctions || !window.NepaliFunctions.BS2AD) {
            console.error('NepaliFunctions.BS2AD not available or invalid BS date:', bsDate);
            return null;
        }

        try {
            let adResult;
            if (typeof bsDate === 'string' && bsDate.match(/^\d{8}$/)) {
                // Handle 8-digit BS integer
                const year = parseInt(bsDate.substring(0, 4));
                const month = parseInt(bsDate.substring(4, 6));
                const day = parseInt(bsDate.substring(6, 8));
                adResult = window.NepaliFunctions.BS2AD({year, month, day}, null, 'YYYY-MM-DD');
            } else if (typeof bsDate === 'string' && bsDate.match(/^\d{4}-\d{2}-\d{2}$/)) {
                adResult = window.NepaliFunctions.BS2AD(bsDate, 'YYYY-MM-DD', 'YYYY-MM-DD');
            } else if (typeof bsDate === 'object' && bsDate.year && bsDate.month && bsDate.day) {
                adResult = window.NepaliFunctions.BS2AD(bsDate, null, 'YYYY-MM-DD');
            } else {
                return null;
            }

            if (!adResult) return null;

            let adYear, adMonth, adDay;
            if (typeof adResult === 'string') {
                [adYear, adMonth, adDay] = adResult.split('-').map(Number);
            } else if (typeof adResult === 'object') {
                adYear = adResult.year;
                adMonth = adResult.month;
                adDay = adResult.day;
            }

            const formatted = returnFormat === 'YYYY-MM-DD'
                ? `${adYear}-${String(adMonth).padStart(2, '0')}-${String(adDay).padStart(2, '0')}`
                : adResult;

            return {
                year: adYear,
                month: adMonth,
                day: adDay,
                formatted,
                originalBs: bsDate
            };
        } catch (error) {
            console.error('BS to AD conversion error:', error, bsDate);
            return null;
        }
    }

    toNepaliNumber(number) {
        const nepaliDigits = ['०', '१', '२', '३', '४', '५', '६', '७', '८', '९'];
        return String(number).replace(/[0-9]/g, (digit) => nepaliDigits[parseInt(digit)]);
    }

    convertBsInteger(bsInteger, returnFormat = 'readable') {
        if (!bsInteger || String(bsInteger).length !== 8) {
            console.error('Invalid BS integer date:', bsInteger);
            return null;
        }

        const dateStr = String(bsInteger);
        const year = parseInt(dateStr.substring(0, 4));
        const month = parseInt(dateStr.substring(4, 6));
        const day = parseInt(dateStr.substring(6, 8));
        const formatted = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const nepaliFormatted = this.toNepaliNumber(formatted);
        const readableFormat = `${this.englishMonths[month - 1]} ${day}, ${year}`;

        return {
            year,
            month,
            day,
            formatted,
            nepaliFormatted,
            readable: readableFormat,
            monthName: this.englishMonths[month - 1],
            monthNameNepali: this.nepaliMonths[month - 1],
            originalBsInteger: bsInteger
        };
    }

    convertTimestamps() {
        document.querySelectorAll('.nepali-date-display').forEach(wrapper => {
            // Skip if already processed
            if (wrapper.dataset.processed === 'true') {
                return;
            }

            const bsDateElement = wrapper.querySelector('.bs-date');
            const adDateElement = wrapper.querySelector('.ad-date');
            const toggleElement = wrapper.querySelector('.date-toggle');
            const format = wrapper.dataset.format || 'formatted';

            if (!bsDateElement || !adDateElement || !toggleElement) {
                console.warn('Missing bs-date, ad-date, or date-toggle elements in nepali-date-display:', wrapper);
                return;
            }

            let bsDate = wrapper.dataset.originalBs || bsDateElement.textContent.trim();
            let adDate = wrapper.dataset.originalAd || adDateElement.textContent.trim();

            // Handle AD to BS conversion
            if (wrapper.dataset.convertDate && !bsDate) {
                const adConverted = this.convertAdToBs(wrapper.dataset.convertDate, 'YYYY-MM-DD', format);
                if (adConverted) {
                    bsDate = format === 'readable' ? adConverted.readable : adConverted.formatted;
                } else {
                    bsDate = wrapper.dataset.convertDate; // Fallback to AD if conversion fails
                }
                adDate = wrapper.dataset.convertDate;
            }
            // Handle BS integer to AD and BS
            else if (wrapper.dataset.bsInteger) {
                const bsInteger = parseInt(wrapper.dataset.bsInteger);
                const bsConverted = this.convertBsInteger(bsInteger, format);
                if (bsConverted) {
                    bsDate = format === 'readable' ? bsConverted.readable : bsConverted.formatted;
                    const adConverted = this.convertBsToAd(bsInteger.toString());
                    adDate = adConverted ? adConverted.formatted : 'N/A';
                } else {
                    bsDate = 'N/A';
                    adDate = 'N/A';
                }
            }
            // Handle existing BS date
            else if (bsDate && bsDate.match(/^\d{4}-\d{2}-\d{2}$/)) {
                const adConverted = this.convertBsToAd(bsDate, 'YYYY-MM-DD', 'YYYY-MM-DD');
                adDate = adConverted ? adConverted.formatted : 'N/A';
            }
            // Handle existing AD date
            else if (adDate && adDate.match(/^\d{4}-\d{2}-\d{2}$/)) {
                const bsConverted = this.convertAdToBs(adDate, 'YYYY-MM-DD', format);
                if (bsConverted) {
                    bsDate = format === 'readable' ? bsConverted.readable : bsConverted.formatted;
                } else {
                    bsDate = 'N/A';
                }
            } else {
                console.warn('Invalid date format in element:', wrapper);
                return;
            }

            // Set initial values
            bsDateElement.textContent = bsDate;
            adDateElement.textContent = adDate || 'N/A';
            wrapper.dataset.originalBs = bsDate;
            wrapper.dataset.originalAd = adDate;
            wrapper.dataset.currentType = 'BS';
            wrapper.dataset.processed = 'true';
            toggleElement.textContent = 'AD';

            // Clear any existing click handlers
            const newToggleElement = toggleElement.cloneNode(true);
            toggleElement.parentNode.replaceChild(newToggleElement, toggleElement);

            // Add toggle handler
            newToggleElement.addEventListener('click', () => {
                const currentType = wrapper.dataset.currentType || 'BS';
                if (currentType === 'BS') {
                    bsDateElement.style.display = 'none';
                    adDateElement.style.display = 'inline';
                    newToggleElement.textContent = 'BS';
                    wrapper.dataset.currentType = 'AD';
                    wrapper.title = `BS: ${wrapper.dataset.originalBs}`;
                } else {
                    bsDateElement.style.display = 'inline';
                    adDateElement.style.display = 'none';
                    newToggleElement.textContent = 'AD';
                    wrapper.dataset.currentType = 'BS';
                    wrapper.title = `AD: ${wrapper.dataset.originalAd}`;
                }
            });
        });
    }

    init() {
        // Wait for NepaliFunctions to be available before initializing
        this.waitForNepaliFunctions(() => {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.convertTimestamps());
            } else {
                this.convertTimestamps();
            }

            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach(node => {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                const wrappers = node.querySelectorAll('.nepali-date-display');
                                if (wrappers.length) this.convertTimestamps();
                            }
                        });
                    }
                });
            });

            observer.observe(document.body, { childList: true, subtree: true });
        });
    }
}

// Initialize when script loads
window.NepaliDateConverter = new NepaliDateConverter();