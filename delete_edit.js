
const selectAll = document.getElementById('selectAll');
const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
const editBtn = document.getElementById('editSelected');
const deleteBtn = document.getElementById('deleteSelected');

// Select All functionality
selectAll.addEventListener('change', () => {
        rowCheckboxes.forEach(cb => cb.checked = selectAll.checked);
    });

// Delete selected rows
deleteBtn.addEventListener('click', () => {
    const selectedIds = [...rowCheckboxes].filter(cb => cb.checked).map(cb => cb.closest('tr').dataset.id);
    if(selectedIds.length === 0) return alert('Select at least one row.');

    if(confirm('Are you sure you want to delete selected rows?')){
        const formData = new FormData();
        formData.append('delete_ids', JSON.stringify(selectedIds));

       fetch('delete_inputs.php', {method:'POST', body: formData})
        .then(res => res.json())
        .then(data => {
            if(data.success){
                selectedIds.forEach(id => document.querySelector(`tr[data-id='${id}']`).remove());
            }
        });
    }
});

// Edit selected rows (simplest: prompt each row)
editBtn.addEventListener('click', () => {
    const selectedRows = [...rowCheckboxes].filter(cb => cb.checked).map(cb => cb.closest('tr'));
    if(selectedRows.length === 0) return alert('Select at least one row.');

    selectedRows.forEach(row => {
        const moneyCell = row.children[1];
        const categoryCell = row.children[2];
        const newMoney = prompt('Edit Money:', moneyCell.textContent);
        const newCategory = prompt('Edit Category:', categoryCell.textContent);

        if(newMoney !== null && newCategory !== null){
            const formData = new FormData();
            formData.append('id', row.dataset.id);
            formData.append('money', newMoney);
            formData.append('category', newCategory);

           fetch('edit_inputs.php', { method:'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    moneyCell.textContent = newMoney;
                    categoryCell.textContent = newCategory;
                }
            });
        }
    });
});

