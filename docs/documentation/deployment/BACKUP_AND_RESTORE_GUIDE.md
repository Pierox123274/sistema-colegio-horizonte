# Respaldos y restauración

## Automático

Job diario `CreateInstitutionalBackupJob` → `storage/app/backups/`.

## Manual

```bash
php artisan institution:create-backup
```

## Retención

`institution:prune-old-backups` según `DEVOPS_BACKUP_MAX_FILES`.

## Restauración

1. Modo mantenimiento.  
2. Restaurar BD desde dump del ZIP.  
3. Restaurar `storage/app/public` si aplica.  
4. Verificar `.env` y `php artisan migrate:status`.
