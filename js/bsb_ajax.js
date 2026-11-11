/* [LDM] - [v2026-11-11] Prevent BSB upload background images from excedding server file size upload limit */
jQuery(document).ready(function($) {

    // Listen to all change events in the document during the capturing phase
    document.addEventListener('change', function(event) {
        
        // Only process on BSB page
        const myElement = document.getElementById('root');
        
        // Check if the changing element is a file input
        if (event.target && event.target.type === 'file' && myElement && myElement.contains(event.target)) {
            const input = event.target;
            const files = input.files;
    
            if (files.length > 0) {
                
                const maxSizeInBytes = 1.49 * 1024 * 1024; // 1.49 MB limit (set by server)
                const file = files[0];
                console.log('BSB upload size check: file.size = ' + file.size);
                if (file.size > maxSizeInBytes) {
                    // 1. Cancel the selection by clearing the input
                    input.value = ''; 
    
                    // 2. Alert the user or handle the UI error
                    alert(`The chosen image "${file.name}" is too large (${(file.size / 1024 / 1024).toFixed(2)} MB). \nFile size must be less than 1.5 MB.`);
                    
                    // 3. Stop other scripts from processing this file
                    event.stopImmediatePropagation();
                    event.preventDefault();
                }
            }
        }
    }, true); // The 'true' enables the capturing phase to catch events early

});
