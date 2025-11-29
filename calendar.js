const datePicker = document.getElementById('datePicker');

// فلترة الجدول فقط
datePicker.addEventListener('change', () => {
    const selectedDate = datePicker.value;
    if (!selectedDate) return;

    const rows = document.querySelectorAll("#inputsTable tbody tr");

    rows.forEach(row => {
        row.style.display = (row.dataset.date === selectedDate) ? '' : 'none';
    });

});
const chartDiv = document.getElementById('chart');
const days = JSON.parse(chartDiv.getAttribute('data-days'));
const dailyTotals = JSON.parse(chartDiv.getAttribute('data-totals'));

new Chart(document.getElementById('myChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: days,           // كل يوم موجود في قاعدة البيانات
        datasets: [{
            label: 'Expenses',
            data: dailyTotals,  // مجموع كل يوم
            backgroundColor: '#36A2EB'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});



// فلترة اليوم الافتراضية
const today = new Date().toISOString().slice(0, 10);
document.querySelectorAll('#inputsTable tbody tr').forEach(row => {
    row.style.display = (row.dataset.date === today) ? '' : 'none';
});



