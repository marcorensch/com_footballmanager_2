// JS for game officials styling

document.addEventListener("DOMContentLoaded", () => {
    const officialsRow = document.getElementById('officials-row');
    const customOfficialsRow = document.getElementById('custom-officials-row');
    // add class to all children of officials-row
    officialsRow.querySelectorAll('.control-group').forEach((el) => {
        el.classList.add('col-lg-4');
    });

    // add class to all children of custom-officials-row
    customOfficialsRow.querySelectorAll('#custom-officials-row .subform-wrapper').forEach((el) => {
        el.classList.add('row');
    });
    customOfficialsRow.querySelectorAll('.controls .control-group').forEach((el) => {
        el.classList.add('col-lg-4');
    });

});