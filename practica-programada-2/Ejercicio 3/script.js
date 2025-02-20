let productos = [];

function agregarProducto() {
    let nombre = document.getElementById("nombre").value;
    let precio = parseFloat(document.getElementById("precio").value);
    let categoria = document.getElementById("categoria").value;

    if (nombre === "" || isNaN(precio) || precio <= 0) {
        alert("Por favor, ingrese un nombre y un precio valido.");
        return;
    }

    let producto = { nombre, precio, categoria };
    productos.push(producto);

    document.getElementById("nombre").value = "";
    document.getElementById("precio").value = "";

    mostrarProductos();
}

function mostrarProductos(filtro = "Todos") {
    let lista = document.getElementById("listaProductos");
    lista.innerHTML = "";

    productos.forEach((producto, index) => {
        if (filtro === "Todos" || producto.categoria === filtro) {
            let item = document.createElement("li");
            item.innerHTML = `${producto.nombre} - ₡${producto.precio} (${producto.categoria}) 
            <button class="eliminar" onclick="eliminarProducto(${index})">X</button>`;

            lista.appendChild(item);
        }
    });
}

function eliminarProducto(index) {
    productos.splice(index, 1);
    mostrarProductos();
}

function filtrarProductos() {
    let categoriaSeleccionada = document.getElementById("filtroCategoria").value;
    mostrarProductos(categoriaSeleccionada);
}
