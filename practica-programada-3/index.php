<?php

$transacciones = [];

function registrarTransaccion($id, $descripcion, $monto) {
    global $transacciones;
    $transacciones[] = [
        'id' => $id,
        'descripcion' => $descripcion,
        'monto' => $monto
    ];
}

function generarEstadoDeCuenta() {
    global $transacciones;
    
    $montoTotalContado = 0;
    foreach ($transacciones as $transaccion) {
        $montoTotalContado += $transaccion['monto'];
    }
    
    $interes = 0.026 * $montoTotalContado;
    $montoConInteres = $montoTotalContado + $interes;
    $cashback = 0.001 * $montoTotalContado;
    $montoFinal = $montoConInteres - $cashback;
    
    $estadoCuenta = "ESTADO DE CUENTA\n";
    $estadoCuenta .= "------------------------------\n";
    foreach ($transacciones as $transaccion) {
        $estadoCuenta .= "ID: {$transaccion['id']} - {$transaccion['descripcion']} - Monto: ₡{$transaccion['monto']}\n";
    }
    $estadoCuenta .= "------------------------------\n";
    $estadoCuenta .= "Monto Total Contado: ₡$montoTotalContado\n";
    $estadoCuenta .= "Monto con Interés (2.6%): ₡$montoConInteres\n";
    $estadoCuenta .= "Cashback (0.1%): ₡$cashback\n";
    $estadoCuenta .= "Monto Final a Pagar: ₡$montoFinal\n";
    
    echo nl2br($estadoCuenta);
    
    file_put_contents("estado_cuenta.txt", $estadoCuenta);
}

registrarTransaccion(1, "Compra en supermercado", 15000);
registrarTransaccion(2, "Pago de servicios", 23000);
registrarTransaccion(3, "Cena en restaurante", 18000);

generarEstadoDeCuenta();

?>
