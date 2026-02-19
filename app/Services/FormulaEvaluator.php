<?php

namespace App\Services;

use InvalidArgumentException;

class FormulaEvaluator
{
    /**
     * Evalua una formula matematica reemplazando variables con sus valores.
     *
     * @param string $formula Ej: "(ancho * largo) / 10000"
     * @param array $variables Ej: ['ancho' => 30, 'largo' => 40]
     * @return float Resultado de la evaluacion
     * @throws InvalidArgumentException Si la formula es invalida o faltan variables
     */
    public static function evaluate(string $formula, array $variables): float
    {
        if (empty(trim($formula))) {
            throw new InvalidArgumentException('La formula esta vacia.');
        }

        $expression = $formula;

        // Ordenar variables por longitud desc para evitar reemplazos parciales
        // Ej: "largo_total" se reemplaza antes que "largo"
        uksort($variables, fn($a, $b) => strlen($b) - strlen($a));

        foreach ($variables as $name => $value) {
            $numericValue = (float) $value;
            $expression = str_replace($name, (string) $numericValue, $expression);
        }

        // Eliminar espacios
        $sanitized = preg_replace('/\s+/', '', $expression);

        // Validar: solo debe contener digitos, puntos, operadores y parentesis
        if (!preg_match('/^[\d\+\-\*\/\(\)\.]+$/', $sanitized)) {
            // Encontrar que caracteres invalidos quedaron (probablemente variables sin valor)
            $invalid = preg_replace('/[\d\+\-\*\/\(\)\.\s]/', '', $expression);
            throw new InvalidArgumentException(
                "Formula contiene variables sin valor: \"{$invalid}\". Formula procesada: \"{$expression}\""
            );
        }

        // Validar parentesis balanceados
        $depth = 0;
        for ($i = 0; $i < strlen($sanitized); $i++) {
            if ($sanitized[$i] === '(') $depth++;
            if ($sanitized[$i] === ')') $depth--;
            if ($depth < 0) {
                throw new InvalidArgumentException('Formula tiene parentesis desbalanceados.');
            }
        }
        if ($depth !== 0) {
            throw new InvalidArgumentException('Formula tiene parentesis desbalanceados.');
        }

        // Evaluar de forma segura
        try {
            $result = eval("return {$sanitized};");
        } catch (\Throwable $e) {
            throw new InvalidArgumentException(
                "Error evaluando formula: {$e->getMessage()}. Formula: \"{$formula}\""
            );
        }

        if (!is_numeric($result) || is_nan($result) || is_infinite($result)) {
            throw new InvalidArgumentException(
                "Formula produjo un resultado invalido (division por cero u otro error). Formula: \"{$formula}\""
            );
        }

        return round((float) $result, 6);
    }

    /**
     * Verifica si una formula puede ser evaluada con las variables dadas.
     */
    public static function canEvaluate(string $formula, array $variables): bool
    {
        try {
            static::evaluate($formula, $variables);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Extrae los nombres de variables usados en una formula.
     * Variables son secuencias de letras/guiones bajos que no son numeros.
     */
    public static function extractVariables(string $formula): array
    {
        preg_match_all('/[a-zA-Z_][a-zA-Z0-9_]*/', $formula, $matches);
        return array_unique($matches[0]);
    }

    /**
     * Construye el array de variables combinando campos del producto y de la MP.
     *
     * @param array $camposProductoValues Valores de campos_producto del producto (materias_config[mp_id])
     * @param array $camposValoresMP Valores de campos_inventario de la MP (campos_valores)
     * @return array Variables combinadas para evaluar la formula
     */
    public static function buildVariables(array $camposProductoValues, array $camposValoresMP = []): array
    {
        // Campos de la MP tienen prioridad menor (el producto puede sobreescribir)
        return array_merge($camposValoresMP, $camposProductoValues);
    }

    /**
     * Calcula el consumo de una materia prima para un producto.
     *
     * @param string $formula Formula de consumo de la MP
     * @param array $materiasConfig Config del producto para esta MP (materias_config[mp_id])
     * @param array $camposValoresMP Valores propios de la MP (campos_valores)
     * @return float Consumo por unidad de producto
     */
    public static function calcularConsumo(string $formula, array $materiasConfig, array $camposValoresMP = []): float
    {
        $variables = static::buildVariables($materiasConfig, $camposValoresMP);
        return static::evaluate($formula, $variables);
    }
}
