<?php
// Your code here!

function esquinas($vecinos, $rondas)
{
    $cant = 4;
    return ($cant * $vecinos) * $rondas;
}


function bordes($vecinos, $rondas, $n)
{
    $cant = 4*($n-2);
    return ($cant * $vecinos) * $rondas;
}

function internos($vecinos, $rondas, $n)
{
    $cant = ($n-2) * ($n-2); # Equivale a ($n-2) al cuadrado.
    return ($cant * $vecinos) * $rondas;
}

echo "Sin Diagonales:\n";

for($n=4; $n<10; $n++) {

    $rondas = 2*($n-1);
    $esquinas = esquinas(2, $rondas);
    $bordes = bordes(3, $rondas, $n);
    $internos = internos(4, $rondas, $n);
    
    $mensajes = $esquinas + $bordes + $internos;
    if ($n % 2 == 0)
        echo "Con n=$n: $mensajes mensajes\n";
    
}

echo "Con Diagonales:\n";

for($n=4; $n<10; $n++) {

    $rondas = $n-1;
    $esquinas = esquinas(3, $rondas);
    $bordes = bordes(5, $rondas, $n);
    $internos = internos(8, $rondas, $n);
    
    $mensajes = $esquinas + $bordes + $internos;
    if ($n % 2 == 0)
        echo "Con n=$n: $mensajes mensajes\n";
    
}


?>