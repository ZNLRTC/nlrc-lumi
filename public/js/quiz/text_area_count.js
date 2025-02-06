document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector(".form");
    /*--------------------------------------------------------------
    FOR CHAR COUNT
    --------------------------------------------------------------*/
    var textareas = form.querySelectorAll("textarea");

    textareas.forEach(function(textarea) {
        var hiddenElement = textarea.nextElementSibling;

        if (hiddenElement?.classList.contains("count")) {
            var charCount = hiddenElement.querySelector(".char-count");

            function updateCharCount() { charCount.textContent = textarea.value.length; }

            textarea.addEventListener("input", () => {
                textarea.value = textarea.value.substring(0, 1000);
                updateCharCount();
            });

            textarea.addEventListener("blur", () => hiddenElement.style.display = "none" );
        }
    });
});