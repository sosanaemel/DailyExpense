// =======================
// textInput
// =======================
const textInput = document.getElementById('textInput');
const submitBtn = document.querySelector('button[name="input_submit"]');

submitBtn.addEventListener('click', () => {
    if (textInput.value.trim() === "") {
        alert("Please enter something first ✍🏻");
    }
});

const voiceBtn = document.getElementById('voiceBtn');

if (voiceBtn) {
    voiceBtn.addEventListener('click', () => {
        if ('webkitSpeechRecognition' in window) {
            const recognition = new webkitSpeechRecognition();
            recognition.lang = 'en-US';
            recognition.start();

            recognition.onresult = function(event){
                textInput.value = event.results[0][0].transcript;
            };
        } else {
            alert("Your browser does not support voice input.");
        }
    });
}




// =======================
// Save Budget — WORKING
// =======================
document.addEventListener("DOMContentLoaded", () => {

    const widgetInput = document.getElementById('widgetInput');
    const widgetSave = document.getElementById('widgetSave');
    const widgetMsg = document.getElementById('widgetMsg');

    widgetSave.addEventListener('click', () => {

        const value = widgetInput.value;

        if (!value || value <= 0) {
            alert("Enter a valid budget 💰");
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
            if (data.trim() === "OK") {
                widgetMsg.style.display = "block";
                widgetInput.value = "";

                setTimeout(() => {
                    widgetMsg.style.display = "none";
                }, 2000);
            } else {
                alert("Save failed ❌");
            }
        })
        .catch(err => console.error(err));
    });

});
