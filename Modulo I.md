# Módulo II

:dart: **Clases 1 y 2:** *Conceptos básicos. Comunicación y sincronización. Interferencia.*

- [Módulo II](#módulo-ii)
  - [Patrones de resolución](#patrones-de-resolución)
  - [Programa concurrente: estados, acciones e historias](#programa-concurrente-estados-acciones-e-historias)
  - [Acciones atómicas](#acciones-atómicas)
  - [Propiedad ASV](#propiedad-asv)
  - [Sentencia Await](#sentencia-await)
    - [Ejemplo](#ejemplo)
  - [Propiedades](#propiedades)
  - [Scheduling y Fairness](#scheduling-y-fairness)
    - [Fairness incondicional](#fairness-incondicional)
    - [Fairness débil](#fairness-débil)
    - [Fairness fuerte](#fairness-fuerte)
    - [Ejemplo](#ejemplo-1)
  - [Problemas básicos](#problemas-básicos)


## Patrones de resolución

:construction::construction::construction::construction::construction::construction::construction:

## Programa concurrente: estados, acciones e historias

El **_estado_**, de un programa concurrente, consiste en los valores de las variables del programa en un instante de tiempo dado. Pudiendo ser, variables explícitas (las definidas por el programador) como variables implícitas (*program counter*, *stack pointer*, etc ...).

La ejecución, del programa concurrente, puede ser vista como el intercalado de secuencias de **_acciones atómicas_** ejecutadas por cada proceso. Así, cada ejecución particular del programa puede ser vista como una **_historia (trace)_**: S<sub>1</sub> &rarr; S<sub>2</sub> &rarr; ... S<sub>n</sub>.

Cada ejecución de un programa concurrente, produce una historia. Inclusive, hasta en los programas más triviales, el número de historias posibles puede ser enorme. Por ejemplo; para el siguiente programa existen 2 (dos) historias posibles: 

```
var x = 5; // (a)

co
    x = 0; // (b)
    x = x + 1; // (c)
oc
```

|#| Historia | Valor X |
|:-:|:--------:|:-------:|
|1|A &rarr; B &rarr; C | `x = 1` |
|2|A &rarr; C &rarr; B | `x = 0` |

## Acciones atómicas

Una **_acción atómica_**, es una acción que realiza un cambio de estado indivisible. Esto significa que, cualquier estado intermedio que pueda existir en la implementación de la acción no debe ser visible a los otros procesos.

Las acciones atómicas, pueden ser **condicionales** o **incondicionales**. Según tengan, o no, una condición de guarda B que demore su ejecución hasta que la misma sea verdadera.

Una **_acción atómica de grano fino_**, es aquella implementada por el hardware (CPU) en el cual el programa concurrente es ejecutado.

En un programa secuencial, las sentencias de asignación parecen ser atómicas. Esto es porque ningún estado intermedio es visible al programa. En los programas concurrentes, no se puede afirmar lo mismo. Una sentencia de asignación, puede estar implementada por una secuencia de instrucciones máquina de grano fino. Por ejemplo, para el siguiente programa.

```
var y = 0;
var z = 0;

co 
    x = y + z; // (a) load x,y; (b) sum x,z;
    y = 1; // (c) 
    z = 2; // (d)
oc
```

Asumiendo que la sentencia  `x = y + z`, a nivel máquina, es implementada con las instrucciones atómicas `load` y `sum`; podemos sostener que su ejecución no será atómica, ya que eventualmente podrá ser interrumpida. Esto se ve en, algunas de, las posibles historias del programa.

|#| Historia | Valor X |
|:-:|:--------:|:-------:|
|1|A &rarr; B &rarr; C &rarr; D | `x = 0` |
|2|C &rarr; A &rarr; B &rarr; D | `x = 1` |
|3|A &rarr; C &rarr; D &rarr; B | `x = 2` |
|4|C &rarr; D &rarr; A &rarr; B | `x = 3` |


En todas las historias, la ejecución de B antecede a la de A (`x = y + z`). Lo cual, es deseable. Excepto en la historia número 3, donde se observa una interferencia.

La **_interferencia_**, es un evento negativo. El cual se produce, cuando un proceso toma una acción que invalida las suposiciones hechas por otro proceso. Técnicas para evitar la interferencia, pueden ser:

1. Variables disjuntas (disjoint variables)
2. Afirmaciones debilitadas (weakened assertions)
3. Invariantes globales (global invariants)
4. Sincronización (synchronization)

## Propiedad ASV

Una forma de detectar que una sentencia no posee interferencias y, por consiguiente, su ejecución parecerá atómica es verificar que la misma posea, a lo sumo, una referencia crítica.

Una **_referencia crítica_**, en una expresión, es la referencia a una variable que es modificada por otro proceso. Toda expresión que contenga a lo sumo una referencia crítica, cumple con la **_propiedad  "a lo sumo una vez"_** y su ejecución parecerá atómica.

Por ejemplo, la sentencia de asignación _x = expr_ satisface la propiedad si se cumple alguna de las siguientes condiciones:

1. Si _expr_ contiene a lo sumo una referencia crítica, en cuyo caso _x_ no podrá ser leída por otro proceso.
2. Si _expr_ no contiene referencias críticas, en cuyo caso _x_ podrá ser leída por otro proceso.

```
    Program Uno;        |   Program Dos;        |   Program Tres;
                        |                       |
    var x = 0;          |   var x = 0;          |   var x = 0;
    var y = 0;          |   var y = 0;          |   var y = 0;
                        |                       |
    co  x = x + 1; (a)  |   co  x = y + 1; (a)  |   co  x = y + 1; (a)
    //  y = y + 1; (b)  |   //  y = y + 1; (b)  |   //  y = x + 1; (b)
    oc                  |   oc                  |   oc
```

:construction::construction::construction::construction::construction::construction:

:construction:  VERIFICAR HISOTIRAS Y CONSULTAR POR QUE HAY INTERFERENCIA EN 3

:construction::construction::construction::construction::construction::construction:

:white_check_mark: **Program Uno** 
- La sentencia del proceso A, no posee referencias críticas.
- La sentencia del proceso B, no posee referencias críticas.

:white_check_mark: **Program Dos**
- La sentencia del proceso A, contiene una referencia crítica en `y + 1`. No obstante, `x` no es leída por otro proceso.
- La sentencia del proceso B, no posee referencias críticas.

:x: **Program Tres**
- La sentencia del proceso A, contiene una referencia crítica en `y + 1` y `x` es leída por otro proceso.
- La sentencia del proceso B, contiene una referencia crítica en `x + 1` e `y` es leída por otro proceso.

:pushpin: La propiedad también aplica para expresiones que, no sean de asignación.

:pushpin: Se asume que, en el proceso A del **program dos**, leer `y` antes o despues de su actualización es indistinto.

## Sentencia Await

Si una expresión o sentencia de asignación, no cumplen con la propiedad ASV será necesario que la ejecutemos de forma atómica. Aunque, en general, necesitaremos ejecutar secuencias de instrucciones como una única acción atómica.

En ambos casos, necesitaremos utilizar algún mecanismo de sincronización que nos permita construir **acciones atómicas de grano grueso**. Las cuales, no son más que una secuencia de acciones atómicas de grano fino que parecen ser indivisibles.

Podemos especificar acciones atómicas, a través de parentesis angulares `<` y `>`. Como también, especificar sincronización a través de la sentencia `await`:

```
< await (B) S; >
```

- B es una condición booleana que, especifica una condición de demora.
- S es un conjunto de setencias que eventualmente terminarán, y que su estado interno no será visible por otros procesos.

Por ejemplo, en el siguiente programa; B demora hasta que `x` contenga un valor positivo entonces, decrementa `x`.

```
< await ( a > 0 ) a = a-1; >
```

Con la sentencia **await** podemos construir acciones atómicas de grano grueso arbitrarias. Por ejemplo, el programa anterior es un ejemplo de la operación P de un semáforo a. No obstante, es complejo implementar esta sentencia en su forma general.

La forma general del **await** especifica ambas formas de sincronización (**exlusión mutua** y **por condicón**). No obstante podemos utilizar su abreviación para, especificarlas por separado.

Para especificar sincronización por **exclusión mutua** podemos emplear `<S>`. El siguiente ejemplo, incrementa a `x` e `y` de forma atómica:

```
< x = x + 1; y = y + 1;>
```

:bulb: La acción atómica anterior, es **incondicional**.

Para especificar sincronización por **condición**, podemos emplear `<await(B)>`. El siguiente ejemplo, demora la ejecución del proceso hasta que `count > 0`:

```
< await (count > 0) >
```

:bulb: La acción atómica anterior, es **condicional**.

Por último, si la condicón de guarda satisface ASV. Podremos implementar el **await** mediante **busy waiting** o **spin loop**

```
BusyWaiting
    do (not B)
        skip;
    od
End.

SpinLoop
    while (not B);
End.
```

### Ejemplo

Productor/consumidor con buffer de tamaño N (queue).

```
int size = 0;
Queue queue;

Process Productor
    while(true)
        < await(size < N) queue.push(dato) >
    end;
End.

Process Consumidor
    while(true)
        < await(size > 0) queue.pop(dato); size--; >
    end;
End.
```

- El productor podrá colocar datos, siempre y cuando la cola no este llena (condición).
- El consumidor podrá leer datos, siempre y cuando la cola no este vacía (condición).
- La lectura y escritura se deben ejecutar de forma atómica para evitar inconsitencias (mutex).

¿Qué pasaría si, el buffer es un arreglo en lugar de una cola?

:construction:

## Propiedades

Una propiedad, en un programa concurrente, es un atributo que debe ser cierto en cada historia posible del programa. Existen 2 (dos) grandes categorías, de propiedades.

La primera, es la de **seguridad (safety)**. La cual, debe asegurar que nada malo ocurra durante la ejecución; para garantizar que el estado final del programa sea el correcto. Dos propiedades fundamentales, dentro de esta categoría, son:

1. **Exclusión mutua:** El evento negativo, en este caso, sería que 2 (dos) o más programas; accedan a la misma sección crítica en el mismo instante de tiempo.
2. **Ausencia de deadlock:** El evento negativo, en este caso, sería que un proceso quede demorado, esperando por una condición que nunca será verdadera.

La segunda, es la de **vida (liveness)**. La cual, debe asegurar que eventualmente algo bueno ocurrirá; para garantizar la correcta terminación del programa. Por ejemplo:
1. Que todo proceso,eventualmente, acceda a su sección crítica.
2. Que todo mensaje, eventualmente, sea recibido por su receptor.

## Scheduling y Fairness

La mayoría de las propiedades de vida, de un programa concurrente, dependen del **fairness**. El cual, es un concepto, que se ocupa de garantizar que todos los procesos tienen chances de proceder sin importar lo que hagan los demás.

Para que los procesos avancen, acciones atómicas candidatas deben ser elegidas para su ejecución. Dicha tarea de selección, es llevada a cabo por una política de planificación (**scheduling policy**). Según la bibliografía, existen 3 (tres) grados de fairness que toda política de planificación debería proveer.

:bulb: El programa es ejecutado en una arquitectura monoprocesador. 

### Fairness incondicional

Una política de planificación cumple con este grado si cada acción atómica, incondicional elegible, eventualmente es ejecutada.

```
var continue = true;
co  while (continue); (P)
/   continue = false; (Q)
oc 
```

Una política donde se conceda CPU, a los procesos, hasta que estos terminan o son demorados; no sería incondicionalmente fair. Ya que si (P) ingresa primero entonces, (Q) no tendría oportunidad de ser ejecutada.

Por el contrario, Round Robin si resulta una política incondicionalmente fair. Ya que, (Q) eventualmente podrá acceder a CPU.

### Fairness débil

Una política de planificación cumple este grado si, es incondicionalmente fair y cada acción atómica condicional eventualmente es ejecutada. Asumiendo que su condición, se volverá verdadera y permanecerá así hasta ser vista por el proceso ejecutando la acción.

### Fairness fuerte

Una política de planificación cumple este grado si, es incondicionalmente fair y cada acción atómica condicional eventualmente es ejecutada. Asumiendo que su condición, se volverá verdadera con infinita frecuencia hasta ser vista por el proceso ejecutando la acción.

:warning: No es posible idear un planificador que sea práctico y fuertemente fair. 

### Ejemplo

¿Este programa termina?

```
var continue = true;
var try = false;

co  while (continue) { try = true; try = false; } (P)
/   < await(try) continue = false > (Q)
oc
```

Con una política débilmente fair, el programa podría no terminar. Ya que, `try` no se mantiene verdadera hasta ser vista por (Q).

Con una política fuertemente fair, el programa podría terminar. Ya que, `try` se convierte en verdadera con infinita frecuencia.

## Problemas básicos

:construction::construction::construction::construction::construction::construction::construction: