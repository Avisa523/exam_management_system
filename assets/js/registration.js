// Registration Form Multi-Step Navigation

document.addEventListener("DOMContentLoaded", function() {
    let currentStep = 0;

    const steps = document.querySelectorAll(".form-step");
    const indicators = document.querySelectorAll(".step");

    function showStep(index){
        steps.forEach((s,i)=>s.classList.toggle("active", i===index));
        indicators.forEach((ind,i)=>ind.classList.toggle("active", i===index));
    }

    function nextStep(){
        if(currentStep < steps.length-1){
            currentStep++;
            showStep(currentStep);
        }
    }

    function prevStep(){
        if(currentStep > 0){
            currentStep--;
            showStep(currentStep);
        }
    }

    showStep(currentStep);

    window.nextStep = nextStep;
    window.prevStep = prevStep;
});

function validateFileSize(input, maxMB) {
    const files = input.files;
    const maxSize = maxMB * 1024 * 1024; // Convert MB to bytes

    for (let i = 0; i < files.length; i++) {
        if (files[i].size > maxSize) {
            alert(`File "${files[i].name}" exceeds ${maxMB} MB.`);
            input.value = ''; // Clear the input
            return false;
        }
    }
    return true;
}
