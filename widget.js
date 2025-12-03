moneyForm.addEventListener('submit', function(e){
    e.preventDefault();

    const amount = moneyInput.value;

    const formData = new FormData();
    formData.append('money_input', amount);

    fetch('update_widget.php', { method:'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        
        if(data.success){
            widgetFixed.textContent = data.new_widget_value + " 💸";
            moneyInput.value = '';
        } 
        else {
            alert(data.message); // أهم جزء
        }

    })
    .catch(err => console.log("Fetch Error: ", err));
});
