# Tablas núcleo

## students

Ficha del alumno: código, nombres, documento, vínculo opcional `user_id` para portal.

## guardians + guardian_student

Apoderados y relación con estudiantes (prioridad, contacto de emergencia).

## enrollments

Matrícula por año y sección; estado (matriculado, retirado, etc.).

## academic_years, educational_levels, grades, sections

Estructura organizativa del colegio.

## users

Credenciales; roles vía Spatie.

## Relaciones clave

Un estudiante tiene muchas matrículas (histórico); una matrícula activa por año típicamente.
