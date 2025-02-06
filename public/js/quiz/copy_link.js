document.addEventListener("DOMContentLoaded", () => {
    Livewire.on('copyLink', (linkToCopy) => {
        const tempInput = document.createElement("input");
        tempInput.value = linkToCopy; 
        document.body.appendChild(tempInput);
        tempInput.select(); 
        document.execCommand("copy");
        document.body.removeChild(tempInput);

        alert("Link copied to clipboard!");
    });
});