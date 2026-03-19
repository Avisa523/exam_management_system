document.addEventListener('DOMContentLoaded', function() {
    const menuDots = document.getElementById('menuDots');
    const dotsDropdown = document.getElementById('dotsDropdown');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    // Show/hide dropdown for Change Password + Logout
    menuDots.addEventListener('click', function(e){
        dotsDropdown.classList.toggle('show');
        e.stopPropagation();
    });

    window.addEventListener('click', function(){
        dotsDropdown.classList.remove('show');
    });

    // Sidebar push effect
    menuDots.addEventListener('dblclick', function(){
        sidebar.classList.toggle('open');
        mainContent.classList.toggle('push');
    });
});


// Multi-step form navigation
let currentStep = 0;
const steps = document.querySelectorAll(".form-step");

function showStep(index){
    steps.forEach((s,i)=>s.style.display = i===index?'block':'none');
}
function nextStep(){ if(currentStep < steps.length-1){ currentStep++; showStep(currentStep); } }
function prevStep(){ if(currentStep > 0){ currentStep--; showStep(currentStep); } }

// Role toggle
function toggleRoleForms(){
    const role = document.getElementById("role").value;

    // Hide all forms first
    document.getElementById("student-form").style.display = "none";
    document.getElementById("teacher-form").style.display = "none";
    document.getElementById("admin-form").style.display = "none";
    document.getElementById("student-docs").style.display = "none";
    document.getElementById("teacher-docs").style.display = "none";
    document.getElementById("admin-docs").style.display = "none";

    // Show selected role
    if(role==='student'){ 
        document.getElementById("student-form").style.display="block";
        document.getElementById("student-docs").style.display="block";
    } else if(role==='teacher'){
        document.getElementById("teacher-form").style.display="block";
        document.getElementById("teacher-docs").style.display="block";
    } else if(role==='admin'){
        document.getElementById("admin-form").style.display="block";
        document.getElementById("admin-docs").style.display="block";
    }
}

// Initialize first step
showStep(currentStep);

document.addEventListener('DOMContentLoaded', () => {
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

    // Initialize first step
    showStep(currentStep);

    // Expose to global so buttons can access
    window.nextStep = nextStep;
    window.prevStep = prevStep;
});