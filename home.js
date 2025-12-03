// =======================
// Voice Input
// =======================
const voiceBtn = document.getElementById('voiceBtn');
const textInput = document.getElementById('textInput');

voiceBtn.addEventListener('click', () => {
    if ('webkitSpeechRecognition' in window) {
        const recognition = new webkitSpeechRecognition();
        recognition.lang = 'en-US';
        recognition.start();

        recognition.onresult = function(event){
            const transcript = event.results[0][0].transcript;
            textInput.value = transcript;
        }
    } else {
        alert("Your browser does not support voice input.");
    }
});


// =======================
// Save Budget — WORKING
// =======================
const widgetInput = document.getElementById('widgetInput');
const widgetSave = document.getElementById('widgetSave');
const widgetMsg = document.getElementById('moneyForm');

widgetSave.addEventListener('click', () => {

    const value = widgetInput.value.trim();

    if (value === "") {
        console.log("Empty input");
        return;
    }

    const formData = new FormData();
    formData.append("widget_value", value);
    formData.append("widget_submit", "1");

    fetch("home.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {

        widgetMsg.style.display = "block";
        widgetInput.value = "";

        setTimeout(() => {
            widgetMsg.style.display = "none";
        }, 2000);
    })
    .catch(err => console.error("Fetch Error:", err));
});

if(currentBudget <= 0){
    document.getElementById("textInput").disabled = true;
    document.getElementById("budgetZeroMsg").style.display = "block";
}
