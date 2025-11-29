const moneyForm = document.getElementById('moneyForm');
const moneyInput = document.getElementById('moneyInput');
const widgetFixed = document.getElementById('widgetFixed');

moneyForm.addEventListener('submit', function(){
    e.preventDefault();

    const amount = moneyInput.value;

    const formData = new FormData();
    formData.append('money_input', amount);

    fetch('update_widget.php', { method:'POST', body: formData })

    .then(res => res.json())
    .then(data => {
        console.log(data); // مهم عشان نعرف فين المشكلة

        if(data.success){
            widgetFixed.textContent = data.new_widget_value + " 💸";
            moneyInput.value = '';
        }
    })
    .catch(err => console.log("Fetch Error: ", err));
});
