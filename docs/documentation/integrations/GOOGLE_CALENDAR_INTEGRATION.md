# Google Calendar

## Actual

Export de eventos → URL “Añadir a Google Calendar” + contenido ICS.  
Sync al crear videoclase (`CalendarIntegrationService`).

## Variables

```
INTEGRATION_CALENDAR_ENABLED=true
GOOGLE_CALENDAR_ENABLED=false
GOOGLE_CALENDAR_CLIENT_ID=
GOOGLE_CALENDAR_CLIENT_SECRET=
GOOGLE_CALENDAR_OAUTH_READY=false
```

## Futuro

OAuth bidireccional cuando `GOOGLE_CALENDAR_OAUTH_READY=true`.
