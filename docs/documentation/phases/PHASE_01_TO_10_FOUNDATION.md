# Fases 0–10 — Fundamentos y ERP base

## Objetivo

Establecer la plataforma técnica y los módulos administrativos esenciales de un colegio.

## Módulos implementados

- Configuración Laravel, Inertia, React, roles (Fases 0–2).  
- Layout intranet y navegación (Fase 3).  
- Sitio público inicial (Fase 4).  
- Estudiantes, apoderados, estructura académica, matrículas (5–8).  
- Pensiones, pagos, comprobantes (9–10).

## Áreas principales

`app/Models/Student.php`, `Enrollment`, `Payment`, migraciones académicas y financieras, portales base.

## Rutas principales

- `/intranet/*` — gestión administrativa.  
- Rutas públicas institucionales.

## Pruebas

Feature tests en `tests/Feature/Intranet/`, BDD `students`, `enrollments`, `payments`.

## Resultado

Sistema operable para secretaría y administración académico-administrativa.

## Valor para el colegio

Digitalización del núcleo operativo: matrícula, cobranza y datos maestros confiables.
