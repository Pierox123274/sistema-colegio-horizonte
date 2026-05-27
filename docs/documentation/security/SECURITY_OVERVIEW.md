# Seguridad — Visión general

## Pilares

1. **Autenticación** — sesiones seguras, Breeze.  
2. **Autorización** — roles Spatie + policies.  
3. **Auditoría** — `audit_logs` inmutable operacionalmente.  
4. **Transporte** — HTTPS en producción, headers (`SecurityHeadersMiddleware`).  
5. **Datos** — no exponer PII en logs de IA; hashes en metadata.

## Documentos relacionados

- [ROLE_PERMISSION_MATRIX.md](./ROLE_PERMISSION_MATRIX.md)  
- [AUDIT_TRAIL_DOCUMENTATION.md](./AUDIT_TRAIL_DOCUMENTATION.md)  
- [PRODUCTION_SECURITY_CHECKLIST.md](./PRODUCTION_SECURITY_CHECKLIST.md)  
- `docs/SECURITY_POLICY.md` (repositorio)
