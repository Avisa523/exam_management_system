// Registration Form Multi-Step Navigation
let currentStep = 0;

const steps = document.querySelectorAll(".form-step");
const indicators = document.querySelectorAll(".step");

function showStep(index){
    steps.forEach((s,i)=>s.classList.toggle("active",i===index));
    indicators.forEach((ind,i)=>ind.classList.toggle("active",i===index));
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