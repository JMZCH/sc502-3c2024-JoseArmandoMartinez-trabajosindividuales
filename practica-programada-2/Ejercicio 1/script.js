function calcular() {
    let salarioBruto = parseFloat(document.getElementById("salario").value);

    if (isNaN(salarioBruto) || salarioBruto <= 0) {
        alert("Por favor ingrese un salario válido.");
        return;
    }

    let cargasSociales = salarioBruto * 0.105;

    let impuestoRenta = 0;
    if (salarioBruto > 941000) {
        impuestoRenta = (salarioBruto - 941000) * 0.15;
    }
    if (salarioBruto > 1383000) {
        impuestoRenta = (salarioBruto - 1383000) * 0.2 + (1383000 - 941000) * 0.15;
    }

    let salarioNeto = salarioBruto - cargasSociales - impuestoRenta;

    document.getElementById("cargasSociales").innerText = cargasSociales.toFixed(2);
    document.getElementById("impuestoRenta").innerText = impuestoRenta.toFixed(2);
    document.getElementById("salarioNeto").innerText = salarioNeto.toFixed(2);
}
