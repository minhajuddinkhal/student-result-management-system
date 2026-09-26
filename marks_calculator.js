// Static/js/marks_calculator.js - Dynamic component builder and marks validation

function addAssessmentRow(termType) {
    const tableBody = document.getElementById(termType + '-components-body');
    if (!tableBody) return;

    const rowCount = tableBody.querySelectorAll('tr').length;
    const newRow = document.createElement('tr');
    
    newRow.innerHTML = `
        <td>
            <input type="hidden" name="comp_term[]" value="${termType}">
            <input type="text" name="comp_name[]" class="form-control form-control-sm" placeholder="e.g. Quiz ${rowCount + 1}, Viva, Exam" required>
        </td>
        <td>
            <input type="number" step="0.5" name="comp_max[]" class="form-control form-control-sm comp-max-input" value="20" min="1" required>
        </td>
        <td>
            <input type="number" step="0.5" name="comp_weight[]" class="form-control form-control-sm comp-weight-input" value="10" min="0.5" required onchange="calculateTotalWeights()">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove(); calculateTotalWeights();">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tableBody.appendChild(newRow);
    calculateTotalWeights();
}

function calculateTotalWeights() {
    const midInputs = document.querySelectorAll('#mid-components-body .comp-weight-input');
    const finalInputs = document.querySelectorAll('#final-components-body .comp-weight-input');

    let midSum = 0;
    midInputs.forEach(i => midSum += parseFloat(i.value || 0));

    let finalSum = 0;
    finalInputs.forEach(i => finalSum += parseFloat(i.value || 0));

    const midTarget = parseFloat(document.getElementById('mid_weight')?.value || 40);
    const finalTarget = parseFloat(document.getElementById('final_weight')?.value || 60);

    const midSumDisplay = document.getElementById('mid-sum-display');
    if (midSumDisplay) {
        midSumDisplay.textContent = midSum.toFixed(1) + '%';
        midSumDisplay.className = (Math.abs(midSum - midTarget) < 0.01) ? 'badge bg-success' : 'badge bg-warning text-dark';
    }

    const finalSumDisplay = document.getElementById('final-sum-display');
    if (finalSumDisplay) {
        finalSumDisplay.textContent = finalSum.toFixed(1) + '%';
        finalSumDisplay.className = (Math.abs(finalSum - finalTarget) < 0.01) ? 'badge bg-success' : 'badge bg-warning text-dark';
    }

    const grandSumDisplay = document.getElementById('grand-sum-display');
    const grandSum = midSum + finalSum;
    if (grandSumDisplay) {
        grandSumDisplay.textContent = grandSum.toFixed(1) + '% / 100%';
        grandSumDisplay.className = (Math.abs(grandSum - 100) < 0.01) ? 'alert alert-success py-2' : 'alert alert-danger py-2';
    }
}