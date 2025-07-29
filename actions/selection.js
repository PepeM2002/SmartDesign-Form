fetch('../actions/tratamientos.json')
    .then(response => response.json())
    .then(data => {
        const select = document.getElementById('tratamiento-select');
        data.tratamientos.forEach((tratamiento, index) => {
            const option = document.createElement('option');
            option.value = index;
            option.textContent = tratamiento;
            select.appendChild(option);
        });
    })
    .catch(error => {
        console.error('Error al cargar tratamientos:', error);
    });