document.addEventListener('DOMContentLoaded', function(){

    const categories = JSON.parse(document.getElementById('categories-data').textContent);
    const totals = JSON.parse(document.getElementById('totals-data').textContent);

    new Chart(document.getElementById('categoryChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: categories,
            datasets: [{
                label: 'Total per Category',
                data: totals,
                backgroundColor: ['#f09dafff','#a5dbffff','#fadf9dff','#99f7f7ff','#b498ecff','#d1a982ff']
            }]
        },
        options: { responsive: true }
    });
});