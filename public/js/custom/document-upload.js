document.addEventListener('DOMContentLoaded', async function() {
    console.log('LOG: DocumentUpload script initialized.');

    const progressBar = document.querySelector('.completion-progress');
    const valueContainer = document.querySelector('.progress-percent');
    const darkModeSwitches = document.querySelectorAll('[x-ref="switchButton"]');

    const progressEndValue = valueContainer.getAttribute('data-completion-progress');
    var progressValue = 0;

    var isDarkMode = false;

    [...darkModeSwitches].forEach((btn) => {
        btn.addEventListener('click', function() {
            isDarkMode = !isDarkMode;

            if (isDarkMode) {
                toggleDarkModeStyles('add');
            } else {
                toggleDarkModeStyles('remove');
            }

            setProgressBarStyles(isDarkMode);
        });
    });

    // Prevent memory leak just in case the element cannot be found
    if (progressBar) {
        if (progressBar.classList.contains('dark')) {
            isDarkMode = true;
            toggleDarkModeStyles('add');
        }

        if (progressEndValue == 0) {
            setProgressBarStyles(isDarkMode);
        } else {
            let progress = setInterval(() => {
                progressValue++;
                console.log(`Progress value: ${progressValue}`);

                valueContainer.textContent = `${progressValue}%`;

                setProgressBarStyles(isDarkMode);

                if (progressValue == progressEndValue) {
                    clearInterval(progress);
                }
            }, 100);
        }
    }

    /**
     * @param {boolean} isDarkMode - Whether the page is in dark mode or not
     * @returns void
    */
    function setProgressBarStyles(isDarkMode) {
        const progressBarColors = [
            { innerCircleColor: '#727cf5', backdropCircleColor: '#cadcff' }, // Light mode
            { innerCircleColor: '#e2e8f0', backdropCircleColor: '#334155' } // Dark mode
        ];
        const colorsToUse = isDarkMode ? progressBarColors[1] : progressBarColors[0];

        // Access CSS variables in document-upload.css
        progressBar.style.setProperty("--progress-value", progressValue);
        progressBar.style.setProperty("--inner-circle-color", colorsToUse.innerCircleColor);
        progressBar.style.setProperty("--backdrop-circle-color", colorsToUse.backdropCircleColor);

        /* Non-CSS variables version below, will comment for now for backup purposes
        progressBar.style.background = `conic-gradient(
            ${colorsToUse.innerCircleColor} ${progressValue * 3.6}deg,
            ${colorsToUse.backdropCircleColor} ${progressValue * 3.6}deg
        )`;
        */
    }

    /**
     * @param {string} type - Must be either add or remove
     * @returns void
    */
    function toggleDarkModeStyles(type) {
        if (type == 'add') {
            progressBar.classList.add('dark');
            valueContainer.classList.add('dark');
        } else {
            progressBar.classList.remove('dark');
            valueContainer.classList.remove('dark');
        }
    }
});
