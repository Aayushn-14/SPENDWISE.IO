

     function scrollToSection(sectionId) { 
     const section = document.getElementById(sectionId);
     if (section) 
     {
        section.scrollIntoView({ behavior: 'smooth' });
     }
  } 
  
  function validateForm() {
    var fileInput = document.getElementById('fileInput');
    var selectedFile = fileInput.files[0];
    var analysisMethod = document.getElementById('analysisMethod').value;
    var uploadMessage = document.getElementById('uploadMessage');

    // Check if a file is selected
    if (!selectedFile) {
        uploadMessage.innerText = "! Please select a file before submitting.";
        uploadMessage.style.color = "white"; // Change text color to red for emphasis
        return false; // Prevent form submission
    } else {
        uploadMessage.innerText = ""; // Clear any previous message
    }

    // Additional checks for analysis method (if required)
    if (analysisMethod === "") {
        // Display a message or perform necessary action
        uploadMessage.innerText = "! Please select an analysis method.";
        return false; // Prevent form submission
    }

    return true; // Allow form submission
}